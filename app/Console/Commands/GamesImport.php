<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;
use App\Services\PlatformService;
use Carbon\Carbon;
use DB;
use File;

class GamesImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import games into database from json file';

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
        $platforms = PlatformService::index();
        foreach ($platforms as $platform) {
            $this->line($platform->name);
            $games = json_decode(File::get(storage_path('app/public/json/games/'.$platform->abbreviation.'.json')));
            $bar = $this->output->createProgressBar(count($games));
            $bar->start();
            DB::beginTransaction();
            foreach ($games as $import) {
                $releaseDate = $import->release_dates;
                $author = isset($import->involved_companies) ? $import->involved_companies : null;
                $cover = isset($import->cover) && isset($import->cover->image_id) ? $import->cover->image_id.'.jpg' : null;
                $genre = isset($import->genres) ? $import->genres[0] : null;
                $regions = [];
                $releaseDate = null;
                foreach ($import->release_dates as $release_date) {
                    if ($release_date->platform === $platform->id && isset($release_date->region) && isset($release_date->date)) {
                        $regionReleaseDate = date('Y-m-d', $release_date->date);
                        if (is_null($releaseDate)) {
                            $releaseDate = $regionReleaseDate;
                        } elseif ($releaseDate > $regionReleaseDate) {
                            $releaseDate = $regionReleaseDate;
                        }
                        switch ($release_date->region) {
                            case 2: // north_america
                                $regions['ntsc_u'] = $regionReleaseDate;
                                break;
                            case 1: // europe
                            case 3: // australia
                            case 4: // new_zealand
                                $regions['pal'] = $regionReleaseDate;
                                break;
                            case 5: // japan
                            case 7: // asia
                                $regions['ntsc_j'] = $regionReleaseDate;
                                break;
                            case 6: // china
                                $regions['ntsc_c'] = $regionReleaseDate;
                                break;
                            case 8: // worldwide
                                $regions = [
                                    'ntsc_u' => $regionReleaseDate,
                                    'ntsc_j' => $regionReleaseDate,
                                    'ntsc_c' => $regionReleaseDate,
                                    'pal' => $regionReleaseDate,
                                ];
                        }
                    }
                    if (isset($import->first_release_date)) {
                        $releaseDate = $import->first_release_date;
                    }
                }
                $game = Game::withTrashed()->updateOrCreate([
                    'platform_id' => $platform->id,
                    'name' => $import->name,
                ], array_merge([
                    'igdb_id' => $import->id,
                    'description' => isset($import->summary) ? $import->summary : '',
                    'cover_generator' => 1,
                    'cover' => $cover,
                    'release_date' => $releaseDate,
                    'igdb_created_at' => $import->created_at,
                    'genre_id' => $genre,
                ], $regions));
                $bar->advance();
            }
            DB::commit();
            $bar->finish();
            $this->line("\n");
        }
        DB::statement("update games games1, games games2 set games1.deleted_at = '".date('Y-m-d H:i:s')."' WHERE games1.id < games2.id and games1.name = games2.name and games1.platform_id = games2.platform_id;");
        DB::statement("update games set created_at = '".Carbon::now()->subDay()->toDateTimeString()."'");
        $this->info('Import Complete');
    }
}
