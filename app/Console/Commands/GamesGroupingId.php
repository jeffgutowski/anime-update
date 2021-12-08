<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Product;

class GamesGroupingId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Groups games that have the same name by the lowest id';

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
        $games = Product::select('id', 'name', 'grouping_game_id')->get();
        $bar = $this->output->createProgressBar(count($games));
        $bar->start();
        foreach ($games as $game) {
            // Get the first game with the same name. Capitalization will be ignored.
            $group = Product::where("name", "LIKE", $game->name)->first();
            // set the grouping game id and rating game id to be the same
            $game->grouping_game_id = $group->id;
            $game->rating_game_id = $group->id;
            $game->save();
            $bar->advance();
        }
        $bar->finish();
    }
}
