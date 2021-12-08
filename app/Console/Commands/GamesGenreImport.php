<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;
use App\Services\PlatformService;
use Carbon\Carbon;
use DB;
use File;

class GamesGenreImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:genre';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Genres to Games';

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
        // DB::table('game_genre')->where('genre_id', 35)->update(['genre_id' => 8]);
        // DB::table('genres')->where('id', 35)->delete();
        // DB::table('genres')->where('id', 8)->update(['name' => 'Platformer']);
        // DB::table('genres')->where('id', 34)->update(['name' => 'Action']);
        // $games = DB::table('game_genre')->where('genre_id', 34)->get();
        // foreach ($games as $game) {
        //     DB::table('game_genre')->insert(['game_id' => $game->game_id, 'genre_id' => 31]);
        // }
        $completed = DB::table('game_genre')->groupBy('game_id')->pluck('game_id')->toArray();

        ini_set('memory_limit', '-1');
        $platforms = PlatformService::index();
        foreach ($platforms as $platform) {
            if ($platform->id !== 6 && $platform->id !== 14) {
                $this->line($platform->name);
                $games = json_decode(File::get(storage_path('app/public/json/games/'.$platform->abbreviation.'.json')));
                $bar = $this->output->createProgressBar(count($games));
                $bar->start();
                DB::beginTransaction();
                foreach ($games as $import) {
                    $game = Game::where('platform_id', $platform->id)->where('name', $import->name)->first();
                    if (isset($game->name) && isset($import->genres) && !in_array($game->id, $completed)) {
                        $game->genres()->sync($import->genres);
                    }
                    $bar->advance();
                }
                DB::commit();
                $bar->finish();
                $this->line("\n");
            }
        }
        $this->info('Import Genres Complete');
    }
}
