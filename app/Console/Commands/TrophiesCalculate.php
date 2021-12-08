<?php

namespace App\Console\Commands;

use DB;
use App\Models\Game;
use App\Models\Platform;
use App\Models\Trophy;
use App\Models\UserTrophy;
use App\Models\User;
use Illuminate\Console\Command;

class TrophiesCalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trophies:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates all trophies for all users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            UserTrophy::where('user_id', $user->id)->delete();
            print_r($user->id." ".$user->name."\r\n");
            DB::beginTransaction();
            // Collection Trophies
            $totalCount = Game::select('rating_game_id')->join('game_have_lists', 'game_have_lists.game_id', '=', 'games.id')->groupBy('rating_game_id', 'platform_id')->where('game_have_lists.user_id', $user->id)->get()->count();
            $trophy = Trophy::where('type', 'collector')->where('platform_id', null)->where('region', null)->where('threshold', '<=', $totalCount)->orderBy('threshold', 'desc')->first();
            $next = Trophy::where('type', 'collector')->where('platform_id', null)->where('region', null)->where('threshold', '>', $trophy->threshold)->orderBy('threshold', 'asc')->first();
            $userTrophy = UserTrophy::updateOrCreate([
                'user_id' => $user->id,
                'type' => 'collector',
                'region' => null,
                'platform_id' => null
            ], [
                'trophy_id' => $trophy->id,
                'next_trophy_id' => isset($next->id) ? $next->id : null,
                'count' => $totalCount,
            ]);

            $platforms = Platform::all();
            foreach ($platforms as $platform) {
                foreach (regionCodes() as $region) {
                    $platformCount = Game::select('rating_game_id')
                        ->join('game_have_lists', 'game_have_lists.game_id', '=', 'games.id')
                        ->groupBy('rating_game_id')
                        ->where('platform_id', $platform->id)
                        ->where('game_have_lists.user_id', $user->id)
                        ->whereNotNull($region)
                        ->get()->count();
                    $platformTrophy = Trophy::where('type', 'collector')->where('platform_id', $platform->id)->where('region', $region)->where('threshold', '<=', $platformCount)->orderBy('threshold', 'desc')->first();
                    if ($platformTrophy) {
                        $platformNext = Trophy::where('type', 'collector')->where('platform_id', $platform->id)->where('region', $region)->where('threshold', '>', $platformTrophy->threshold)->orderBy('threshold', 'asc')->first();
                        $platformUserTrophy = UserTrophy::updateOrCreate([
                            'user_id' => $user->id,
                            'type' => 'collector',
                            'region' => $region,
                            'platform_id' => $platform->id
                        ], [
                            'trophy_id' => $platformTrophy->id,
                            'next_trophy_id' => isset($platformNext->id) ? $platformNext->id : null,
                            'count' => $platformCount,
                        ]);
                    }

                }
            }

            // Completed Games Trophies
            $totalCount = Game::select('rating_game_id')->join('game_completed_lists', 'game_completed_lists.game_id', '=', 'games.id')->where('game_completed_lists.user_id', $user->id)->groupBy('rating_game_id', 'platform_id')->get()->count();
            $trophy = Trophy::where('type', 'gamer')->where('platform_id', null)->where('region', null)->where('threshold', '<=', $totalCount)->orderBy('threshold', 'desc')->first();
            $next = Trophy::where('type', 'gamer')->where('platform_id', null)->where('region', null)->where('threshold', '>', $trophy->threshold)->orderBy('threshold', 'asc')->first();
            $userTrophy = UserTrophy::updateOrCreate([
                'user_id' => $user->id,
                'type' => 'gamer',
                'region' => null,
                'platform_id' => null
            ], [
                'trophy_id' => $trophy->id,
                'next_trophy_id' => isset($next->id) ? $next->id : null,
                'count' => $totalCount,
            ]);

            $platforms = Platform::all();
            foreach ($platforms as $platform) {
                foreach (regionCodes() as $region) {
                    $platformCount = Game::select('rating_game_id')
                        ->join('game_completed_lists', 'game_completed_lists.game_id', '=', 'games.id')
                        ->groupBy('rating_game_id')
                        ->where('platform_id', $platform->id)
                        ->where('game_completed_lists.user_id', $user->id)
                        ->whereNotNull($region)
                        ->get()->count();
                    $platformTrophy = Trophy::where('type', 'gamer')->where('platform_id', $platform->id)->where('region', $region)->where('threshold', '<=', $totalCount)->orderBy('threshold', 'desc')->first();
                    $platformNext = Trophy::where('type', 'gamer')->where('platform_id', $platform->id)->where('region', $region)->where('threshold', '>', $trophy->threshold)->orderBy('threshold', 'asc')->first();
                    if ($platformTrophy) {
                        $platformUserTrophy = UserTrophy::updateOrCreate([
                            'user_id' => $user->id,
                            'type' => 'gamer',
                            'region' => $region,
                            'platform_id' => $platform->id
                        ], [
                            'trophy_id' => $platformTrophy->id,
                            'next_trophy_id' => isset($platformNext->id) ? $platformNext->id : null,
                            'count' => $platformCount,
                        ]);
                    }
                }
            }
            DB::commit();
            // Cleanup any user trophies where that user count is 0
            UserTrophy::where('user_id', $user->id)->where('count', 0)->delete();

        }
    }
}
