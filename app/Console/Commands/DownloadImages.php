<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mockery\Exception;

class DownloadImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $date = date('Y-03-27 00:00:00');
        $games = \App\Models\Game::select('id', 'cover_us')
            ->where('updated_at', '>', $date)
            ->get();
        $bar = $this->output->createProgressBar(count($games));
        $bar->start();
        foreach ($games as $game) {
            $file_name = basename($game->cover_us);
            $path_info = pathinfo($file_name);
            $name = str_pad($game->id, 7, "0", STR_PAD_LEFT);
            try {
                if (isset($path_info['extension'])) {
                    $extension = explode("?", $path_info['extension']);
                    file_put_contents("public/images/us/".$name.".".strtolower($extension[0]), file_get_contents($game->cover_us));
                } else {
                    file_put_contents("public/images/us/".$name.".jpg", file_get_contents($game->cover_us));
                }
            } catch (\Exception $e) {
                logger()->debug([$game->id]);
            }
            $bar->advance();
        }
        $bar->finish();
        echo "\n";
    }
}
