<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Developer;
use App\Models\Publisher;
use DB;

class DeleteUnusedPublishersDevelopers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Publishers and Developers with no associated game';

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
        $this->line("Start Developers");
        $developers = Developer::all();
        $bar = $this->output->createProgressBar(count($developers));
        $bar->start();
        foreach ($developers as $developer) {
            $dev = DB::table('developers')
                ->where('developers.id', '=', $developer->id)
                ->join('developer_game', 'developer_game.developer_id', '=', 'developers.id')
                ->join('games', 'games.id', '=', 'developer_game.game_id')
                ->whereNull('games.deleted_at')
                ->groupBy('developers.id')
                ->get();

            if (count($dev) < 1) {
                $developer->delete();
            }
            $bar->advance();
        }
        $bar->finish();
        $this->line("Developers Completed");

        $this->line("Start Publishers");
        $publishers = Publisher::all();
        $bar = $this->output->createProgressBar(count($publishers));
        $bar->start();
        foreach ($publishers as $publisher) {
            $pub = DB::table('publishers')
                ->where('publishers.id', '=', $publisher->id)
                ->join('game_publisher', 'game_publisher.publisher_id', '=', 'publishers.id')
                ->join('games', 'games.id', '=', 'game_publisher.game_id')
                ->whereNull('games.deleted_at')
                ->groupBy('publishers.id')
                ->get();

            if (count($pub) < 1) {
                $publisher->delete();
            }
            $bar->advance();
        }
        $bar->finish();
        $this->line("Publishers Completed");

    }
}
