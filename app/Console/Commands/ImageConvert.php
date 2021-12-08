<?php

namespace App\Console\Commands;

use App\Models\AccessoriesHardware;
use Illuminate\Console\Command;
use App\Models\Game;
use League\Flysystem\MountManager;
use DB;
use Log;

class ImageConvert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:convert {--s|start=0} {--l|limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts images to be on S3';

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
        $start_id = (int) $this->option('start');
        $limit = $this->option('limit');
        Log::channel('taskslog')->info("Image Convert Started");
        $failures = [];
        $games = AccessoriesHardware::where('cover_us', 'NOT LIKE', '%game-seeker.s3.amazonaws.com%')->whereNotNull('cover_us');
        if ($start_id > 0) {
            $games->where('id', '>=', $start_id);
        }
        if (isset($limit)) {
            $games->limit($limit);
        }
        $games = $games->get();

        $bar = $this->output->createProgressBar(count($games));
        $bar->start();
        foreach ($games as $game) {
            $region = 'us';
            $destination_path = env('S3_DESTINATION')."games/$region";
            try {
                $file = file_get_contents($game->cover_us, false, stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false))));
                $image = \Image::make($file)->encode('jpg', 90);
                $filename = $game->uuid.'.jpg';
                \Storage::disk('s3')->put($destination_path.'/'.$filename, $image->stream());
                $url = env('S3_BUCKET_URL').$destination_path.'/'.$filename;
                DB::statement("UPDATE games SET cover_us = ?, cover_jp = ?, cover_eu = ? where id = ?", [$url, $url, $url, $game->id]);
            } catch (\Exception $e) {
                Log::channel('taskslog')->info($game->id." ".$game->name." ".$e->getMessage());
                $failures[] = $game->id;
                continue;
            }
            $bar->advance();
        }
        $bar->finish();
        Log::channel('taskslog')->info("Failures:");
        Log::channel('taskslog')->info($failures);
        Log::channel('taskslog')->info("Image Convert Completed");
        echo "\n Completed \n";
    }
}
