<?php

namespace App\Http\Controllers;

use App\Models\Trophy;
use App\Models\UserTrophy;
use App\Models\Game;
use Auth;
use DB;

class CompletedListController extends ListController
{
    public $model = 'App\Models\CompletedList';
    public $list_name = "completed list";

    public function add($slug)
    {
        $return = parent::add($slug);
        $this->calculateTrophies();
        return $return;
    }

    public function delete($slug)
    {
        $return = parent::delete($slug);
        $this->calculateTrophies();
        return $return;
    }

    private function calculateTrophies()
    {
        DB::beginTransaction();

        $totalCount = Game::select('rating_game_id')->join('game_completed_lists', 'game_completed_lists.game_id', '=', 'games.id')->where('game_completed_lists.user_id', Auth::id())->groupBy('rating_game_id', 'platform_id')->get()->count();
        $trophy = Trophy::where('type', 'gamer')->where('platform_id', null)->where('region', null)->where('threshold', '<=', $totalCount)->orderBy('threshold', 'desc')->first();
        $next = Trophy::where('type', 'gamer')->where('platform_id', null)->where('region', null)->where('threshold', '>', $trophy->threshold)->orderBy('threshold', 'asc')->first();
        $userTrophy = UserTrophy::updateOrCreate([
            'user_id' => Auth::id(),
            'type' => 'gamer',
            'region' => null,
            'platform_id' => null
        ], [
            'trophy_id' => $trophy->id,
            'next_trophy_id' => isset($next->id) ? $next->id : null,
            'count' => $totalCount,
        ]);

        foreach (regionCodes() as $region) {
            $platformCount = Game::select('rating_game_id')
                ->join('game_completed_lists', 'game_completed_lists.game_id', '=', 'games.id')
                ->groupBy('rating_game_id')
                ->where('platform_id', $this->product->platform_id)
                ->where('game_completed_lists.user_id', Auth::id())
                ->whereNotNull($region)
                ->get()->count();
            $platformTrophy = Trophy::where('type', 'gamer')->where('platform_id', $this->product->platform_id)->where('region', $region)->where('threshold', '<=', $platformCount)->orderBy('threshold', 'desc')->first();
            if ($platformTrophy) {
                $platformNext = Trophy::where('type', 'gamer')->where('platform_id', $this->product->platform_id)->where('region', $region)->where('threshold', '>', $trophy->threshold)->orderBy('threshold', 'asc')->first();
                $platformUserTrophy = UserTrophy::updateOrCreate([
                    'user_id' => Auth::id(),
                    'type' => 'gamer',
                    'region' => $region,
                    'platform_id' => $this->product->platform_id
                ], [
                    'trophy_id' => $platformTrophy->id,
                    'next_trophy_id' => isset($platformNext->id) ? $platformNext->id : null,
                    'count' => $platformCount,
                ]);
            } else {
                // if no trophy is found then delete any that went under the threshold
                UserTrophy::where('user_id', Auth::id())->where('platform_id', $this->product->platform_id)->where('type', 'gamer')->where('region', $region)->delete();
            }
        }
        // Cleanup any user trophies where that user count is 0
        UserTrophy::where('user_id', Auth::id())->where('count', 0)->delete();
        DB::commit();
    }
}
