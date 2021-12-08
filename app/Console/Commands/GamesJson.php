<?php

namespace App\Console\Commands;

use App\Services\IgdbService;
use App\Services\PlatformService;
use File;
use Illuminate\Console\Command;
use Storage;

class GamesJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:json {id} {--l|list=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a list of games into json';

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
        $id = $this->argument('id');
        $list = $this->arguments('list');
        ini_set('memory_limit', '-1');
        $platform = PlatformService::find('id', (int) $id);
        if (is_null($platform)) {
            $this->error('Platform '.$id.' Not Found');
            return;
        }
        $this->info($platform->name);
        $offset = 0;
        $all = false;
        $old_created_at = null;
        if (!File::exists(storage_path('app/public/json/games/'.$platform->abbreviation.'.json'))) {
            File::put(storage_path('app/public/json/games/'.$platform->abbreviation.'.json'), null);
        }
        while (!$all) {
            $games = json_decode(File::get(storage_path('app/public/json/games/'.$platform->abbreviation.'.json')), true);
            $games = !is_null($games) ? $games : [];
            if (isset($games)) {
                $created_at = end($games)['created_at'];
                if ($old_created_at === $created_at && !is_null($created_at)) {
                    app('log')->debug('duplicate created at ', [$old_created_at]);
                    $this->info('duplicate created at '.$old_created_at);
                    $created_at += 1;
                }
            }
            while ($offset <= 150) {
                if (empty($games)) {
                    $import = IgdbService::searchGames(null, ['*', 'cover.*', 'release_dates.*'], 50, $offset, 'created_at', ['release_dates.platform' => ['eq' => $platform->id], 'category' => ['eq' => 0]]);
                } else {
                    $import = IgdbService::searchGames(null, ['*', 'cover.*', 'release_dates.*'], 50, $offset, 'created_at', ['created_at' => ['gte' => $created_at] ,'release_dates.platform' => ['eq' => $platform->id], 'category' => ['eq' => 0]]);
                }
                $games = array_merge($games, (array) $import);
                $offset += 50;
                if (count($import) < 50) {
                    $this->info('Final Import Page Count: '.count($import));
                    $all = true;
                    break;
                }
            }
            $offset = 0;
            $old_created_at = $created_at;
            Storage::disk('public')->put('json/games/'.$platform->abbreviation.'.json', json_encode($games, JSON_PRETTY_PRINT));
        }
        $games = json_decode(File::get(storage_path('app/public/json/games/'.$platform->abbreviation.'.json')), true);
        $gameList = [];
        foreach ($games as $game) {
            $gameList[] = $game['name'];
        }
        $gameList = array_values(array_unique($gameList));
        sort($gameList);
        if ($list) {
            foreach ($gameList as $gameItem) {
                $this->line($gameItem);
            }
        }
        $this->info('Game Count: '.count($gameList));
    }
}
