<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Developer;
use App\Models\Publisher;
use App\Models\Game;
use DB;

class DevelopersPublishersUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Publishers and Developers';

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
        $csv = csvToArray('storage/app/data/publishers_and_developers.csv');
        $bar = $this->output->createProgressBar(count($csv));
        $bar->start();
        foreach ($csv as $key => $line) {
            DB::beginTransaction();
            if (!empty($line['publisher_id']) && !empty($line['change']) && $line['change'] != $line['current']) {
                $publisherOld = Publisher::where('id', '=', $line['publisher_id'])->first();
                $publisherNew = Publisher::firstOrCreate(['name' => $line['change']]);
                foreach (['us', 'jp', 'eu'] as $region) {
                    $games = Game::select('*', 'games.id as id')->with('publishers')
                        ->join('game_publisher', 'game_publisher.game_id', '=', 'games.id')
                        ->where('publisher_id', '=', $publisherOld->id)
                        ->where('region', $region)
                        ->get();

                    foreach ($games as $game) {
                        // Check if it has the new
                        $check = DB::table('game_publisher')
                            ->where('publisher_id', '=', $publisherNew->id)
                            ->where('game_id', $game->id)
                            ->where('region', $region)
                            ->first();

                        // if not then insert
                        if (is_null($check)) {
                            DB::table('game_publisher')->insert([
                                'game_id' => $game->id,
                                'publisher_id' => $publisherNew->id,
                                'region' => $region
                            ]);
                        }
                        // delete old
                        DB::table('game_publisher')
                            ->where('publisher_id', '=', $publisherOld->id)
                            ->where('game_id', $game->id)
                            ->where('region', $region)
                            ->delete();
                    }
                }
            }
            if (!empty($line['developer_id']) && !empty($line['change'])) {
                $developerOld = Developer::where('id', '=', $line['developer_id'])->first();
                $developerNew = Developer::firstOrCreate(['name' => $line['change']]);
                $games = Game::select('*', 'games.id as id')->with('publishers')
                    ->join('developer_game', 'developer_game.game_id', '=', 'games.id')
                    ->where('developer_id', '=', $developerOld->id)
                    ->get();

                foreach ($games as $game) {
                    // Check if it has the new
                    $check = DB::table('developer_game')
                        ->where('developer_id', '=', $developerNew->id)
                        ->where('game_id', $game->id)
                        ->first();

                    // if not then insert
                    if (is_null($check)) {
                        DB::table('developer_game')->insert([
                            'game_id' => $game->id,
                            'developer_id' => $developerNew->id,
                        ]);
                    }
                    // delete old
                    DB::table('developer_game')
                        ->where('developer_id', '=', $developerOld->id)
                        ->where('game_id', $game->id)
                        ->delete();
                }
            }
            DB::commit();
            $bar->advance();
        }
        $bar->finish();
        echo "\r\n Completed \r\n";
    }
}
