<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Services\PlatformService;
use Illuminate\Console\Command;
use DB;

class GamesUpc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:upc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save UPC information on games from external csv';

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
        ini_set('memory_limit', -1);
        $count = 0;
        // Get the price guide csv and turn it into an object
        $games = csvToObject(storage_path('app/public/csv/price-guide.csv'));
        $bar = $this->output->createProgressBar(count($games));
        $bar->start();
        foreach ($games as $key => $game) {
            // find the appropriate platform for the game
            $platform = $this->getPlatform($game->console_name);
            if (isset($platform) && $game->upc !== "") {
                // try to find the game if it is an exact match
                $dbGame = Game::where('name', $game->product_name)
                    ->where('platform_id', $platform->id)
                    ->whereNull('catalog_number')
                    ->first();
                if (isset($dbGame)) {
                    // update the catalog number if it is an exact match
                    $dbGame->update(['catalog_number' => $game->upc]);
                    $count++;
                } elseif ($game->genre !== 'Systems') {
                    // try a fuzzy match instead
                    $query = "SELECT id, MATCH (name) AGAINST (?) AS rank FROM games 
                        WHERE MATCH (name) AGAINST (?)
                        AND platform_id = ? 
                        AND catalog_number IS NULL
                        ORDER BY rank DESC
                        LIMIT 1";
                    // There is a try/catch because some product names have a + symbol that breaks the sql
                    try {
                        $fuzzyMatch = DB::select($query, array($game->product_name, $game->product_name, $platform->id));
                    } catch (Exception $e) {
                        continue;
                    }
                    // If the fuzzy match rank is greater then 20, it is close enough
                    if (isset($fuzzyMatch[0]) && $fuzzyMatch[0]->rank > 20) {
                        // update the game's catalog number
                        $dbGame = Game::where('id', $fuzzyMatch[0]->id)->update(['catalog_number' => $game->upc]);
                        $count++;
                    }
                }
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info("\r\nAdding UPC numbers complete. Added $count UPC numbers.");
    }

    private function getPlatform($platformName)
    {
        $platform = null;
        switch ($platformName) {
            case "Neo Geo":
                $platform = (object)['id' => 80];
                break;
            case "Gamecube":
                $platform = (object)['id' => 21];
                break;
            case "Commodore 64":
                $platform = (object)['id' => 15];
                break;
            case "Sega Dreamcast":
                $platform = (object)['id' => 23];
                break;
            case "Sega Genesis":
                $platform = (object)['id' => 29];
                break;
            case "TurboGrafx-16":
                $platform = (object)['id' => 86];
                break;
        }
        if (!isset($platform)) {
            $platform = PlatformService::find('name', $platformName);
        }
        if (!isset($platform)) {
            $platform = PlatformService::find('abbreviation', strtolower($platformName));
        }
        if (!isset($platform)) {
            $platform = PlatformService::find('alternative_name', strtolower($platformName));
        }
        if (!isset($platform)) {
            $platform = PlatformService::find('name', str_replace('Playstation', 'PlayStation', $platformName));
        }
        if (!isset($platform)) {
            $platform = PlatformService::find('name', str_replace('GameBoy', 'Game Boy', $platformName));
        }
        return $platform;
    }
}
