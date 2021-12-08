<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use DB;
use App\Models\Developer;
use App\Models\Publisher;
use App\Models\Game;
use Schema;

class GamesCompaniesImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Companies into the database';

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
        ini_set('memory_limit', '-1');
        $companies = json_decode(File::get(storage_path('app/public/json/companies/companies.json')));
        $bar = $this->output->createProgressBar(count($companies));
        $bar->start();
        $missing = [];
        foreach ($companies as $company) {
            DB::beginTransaction();
            if (isset($company->developed)) {
                $developer = Developer::firstOrCreate(['name' => $company->name]);
                foreach ($company->developed as $developed) {
                    if (isset($developed->id)) {
                        $games = Game::where('igdb_id', $developed->id)->withTrashed()->get();
                        if (!isset($games)) {
                            $games = Game::where('name', $developed->name)->withTrashed()->get();
                        }
                        if (!isset($games)) {
                            array_push($missing, $developed->id);
                        }
                        foreach ($games as $game) {
                            $game->developers()->attach($developer);
                        }
                    }
                }
            }
            if (isset($company->published)) {
                $publisher = Publisher::firstOrCreate(['name' => $company->name]);
                foreach ($company->published as $published) {
                    if (isset($published->id)) {
                        $games = Game::where('igdb_id', $published->id)->withTrashed()->get();
                        if (!isset($games)) {
                            $games = Game::where('name', $published->name)->withTrashed()->get();
                        }
                        if (!isset($games)) {
                            array_push($missing, $published->id);
                        }
                        foreach ($games as $game) {
                            $game->publishers()->attach($publisher);
                        }
                    }
                }
            }
            DB::commit();
            $bar->advance();
        }
        $bar->finish();
        DB::statement('DELETE t1 FROM game_publisher t1, game_publisher t2 WHERE t1.id < t2.id AND t1.game_id = t2.game_id AND t1.publisher_id = t2.publisher_id;');
        DB::statement('DELETE t1 FROM developer_game t1, developer_game t2 WHERE t1.id < t2.id AND t1.game_id = t2.game_id AND t1.developer_id = t2.developer_id;');
        $this->info('Finished');
    }
}
