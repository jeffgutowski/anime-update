<?php

namespace App\Http\Controllers;

use App\Models\Trophy;
use App\Models\UserTrophy;
use App\Models\Game;
use App\Models\Product;
use App\Models\HaveList;
use Auth;
use DB;

class HaveListController extends ListController
{
    public $model = 'App\Models\HaveList';
    public $list_name = 'collection';

    public function create()
    {
        // Check if user is logged in
        if (!(Auth::check())) {
            return response()->json(['Unauthorized'], 401);
        }

        $product = Product::find(request('product_id'));
        $this->product = $product;

        // Check if game exists
        if (is_null($product)) {
            return response()->json(['Product does not exist'], 422);
        }

        // check current session for if the product is available
        if ($product->{session()->get('region.code')}) {
            // if available set region to current session
            $region = session()->get('region.code');
        } else {
            // if not available find a region that it is available in
            foreach (["ntsc_u", "pal", "ntsc_j"] as $format) {
                if ($product->{$format}) {
                    $region = $format;
                    break;
                }
            }
        }

        $list = HaveList::create(['game_id' => $product->id, 'user_id' => Auth::id(), 'region' => $region, 'quantity' => 1]);
        $this->calculateTrophies();
        return response()->json(['list' => $list]);
    }

    public function update()
    {
        // Check if user is logged in
        if (!(Auth::check())) {
            return response()->json(['Unauthorized'], 401);
        }
        $list = HaveList::where('id', request('id'))->where('user_id', auth()->user()->id)->update(request()->all());
        return response()->json(['list' => $list, 'request' => request()->all()]);
    }

    public function remove($id)
    {
        $list = HaveList::where('id', $id)->where('user_id', auth()->user()->id)->first();
        $this->product = Product::find($list->game_id);
        $list->delete();
        $this->calculateTrophies();
        return $list;
    }

    public function delete($slug)
    {
        $return = parent::delete($slug);
        $this->calculateTrophies();
        return $return;
    }

    private function calculateTrophies()
    {
        if ($this->product->type != 'game') {
            return;
        }
        DB::beginTransaction();
        $totalCount = Game::select('rating_game_id')->join('game_have_lists', 'game_have_lists.game_id', '=', 'games.id')->groupBy('rating_game_id', 'platform_id')->where('game_have_lists.user_id', Auth::id())->whereNotNull(session()->get('region.code'))->get()->count();
        $trophy = Trophy::where('type', 'collector')->where('platform_id', null)->where('region', null)->where('threshold', '<=', $totalCount)->orderBy('threshold', 'desc')->first();
        $next = Trophy::where('type', 'collector')->where('platform_id', null)->where('region', null)->where('threshold', '>', $trophy->threshold)->orderBy('threshold', 'asc')->first();
        $userTrophy = UserTrophy::updateOrCreate([
            'user_id' => Auth::id(),
            'type' => 'collector',
            'region' => null,
            'platform_id' => null
        ], [
            'trophy_id' => $trophy->id,
            'next_trophy_id' => isset($next->id) ? $next->id : null,
            'count' => $totalCount,
        ]);

        $platformCount = Game::select('rating_game_id')
            ->join('game_have_lists', 'game_have_lists.game_id', '=', 'games.id')
            ->groupBy('rating_game_id')
            ->where('platform_id', $this->product->platform_id)
            ->where('game_have_lists.user_id', Auth::id())
            ->whereNotNull(session()->get('region.code'))
            ->get()->count();
        $platformTrophy = Trophy::where('type', 'collector')->where('platform_id', $this->product->platform_id)->where('region', session()->get("region.code"))->where('threshold', '<=', $platformCount)->orderBy('threshold', 'desc')->first();
        if ($platformTrophy) {
            $platformNext = Trophy::where('type', 'collector')->where('platform_id', $this->product->platform_id)->where('region', session()->get("region.code"))->where('threshold', '>', $platformTrophy->threshold)->orderBy('threshold', 'asc')->first();
            $platformUserTrophy = UserTrophy::updateOrCreate([
                'user_id' => Auth::id(),
                'type' => 'collector',
                'region' => session()->get('region.code'),
                'platform_id' => $this->product->platform_id
            ], [
                'trophy_id' => $platformTrophy->id,
                'next_trophy_id' => isset($platformNext->id) ? $platformNext->id : null,
                'count' => $platformCount,
            ]);
        } else {
            // if no trophy is found then delete any that went under the threshold
            UserTrophy::where('user_id', Auth::id())->where('platform_id', $this->product->platform_id)->where('type', 'collector')->where('region', session()->get("region.code"))->delete();

        }
        // Cleanup any user trophies where count is 0
        UserTrophy::where('user_id', Auth::id())->where('count', 0)->delete();
        DB::commit();
    }
}
