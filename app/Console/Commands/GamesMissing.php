<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Services\IgdbService;
use App\Services\PlatformService;
use File;
use Illuminate\Console\Command;
use Storage;

class GamesMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve missing games from list';

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
        $missing = json_decode(File::get(storage_path('app/public/json/missing.json')));
        $count = 0;
        foreach ($missing as $id) {
            $game = IgdbService::getGame($id, ['*', 'cover.*', 'release_dates.*']);
            $count++;
            if (!$game) {
                $this->error('game does not exist: '.$id);
                continue;
            }
            $this->info($id.' '.$game->name);
            if (!isset($game->platforms)) {
                $this->error('Game has no platforms: '.$id);
                app('log')->debug('game', [$game]);
                continue;
            }
            foreach ($game->platforms as $platformId) {
                $platform = PlatformService::find('id', (int) $platformId);
                if (is_null($platform)) {
                    $this->error('Missing platform. Game id: '.$id.'. Platform id: '.$platformId);
                    continue;
                }
                $this->line($platform->name);
                $games = json_decode(File::get(storage_path('app/public/json/games/'.$platform->abbreviation.'.json')), true);
                $games = array_merge($games, [$game]);
                Storage::disk('public')->put('json/games/'.$platform->abbreviation.'.json', json_encode($games, JSON_PRETTY_PRINT));
            }
            if ($count > 10) {
                $this->info('Max count reached');
                return;
            }
        }
    }
}
