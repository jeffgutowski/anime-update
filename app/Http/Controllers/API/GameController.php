<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Publisher;
use App\Models\Developer;
use App\Services\PlatformService;
use Storage;
use GuzzleHttp\Client;
use DB;

class GameController extends Controller
{
    public function add(Request $request, $json = null)
    {
        // Accept only ajax requests
        if (!app('request')->ajax()) {
            return abort('404');
        }

        // Ignore user aborts and allow the script
        // to run forever
        ignore_user_abort(true);
        set_time_limit(0);


        $import = json_decode($request->input('game'));
        $releaseDate = $import->release_dates;
        $platform = PlatformService::find('name', $import->platform); //TODO: find better way to get platform;
        $author = isset($import->involved_companies) ? $import->involved_companies : null;
        $cover = isset($import->cover) && isset($import->cover->image_id) ? $import->cover->image_id.'.jpg' : null;
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
            'igdb_id' => $import->id,
            'platform_id' => $platform->id,
        ], array_merge([
            'name' => $import->name,
            'description' => isset($import->summary) ? $import->summary : '',
            'cover_generator' => 1,
            'cover' => $cover,
            'cover_us' => 'https://images.igdb.com/igdb/image/upload/t_cover_big/'.$cover,
            'cover_jp' => 'https://images.igdb.com/igdb/image/upload/t_cover_big/'.$cover,
            'cover_eu' => 'https://images.igdb.com/igdb/image/upload/t_cover_big/'.$cover,
            'release_date' => $releaseDate,
            'igdb_created_at' => $import->created_at,
        ], $regions));
        if(isset($import->genres)) {
            $game->genres()->sync($import->genres);
        }
        if(isset($import->involved_companies)) {
            DB::beginTransaction();
            foreach ($import->involved_companies as $company) {
                // igdb return company name weird
                $companyName = $company->company->name;
                if ($company->developer && strpos($companyName, 'duplicate') === false) {
                    $developer = Developer::firstOrCreate(['name' => $companyName]);
                    $game->developers()->attach($developer->id);
                }
                if ($company->publisher && strpos($companyName, 'duplicate') === false) {
                    $publisher = Publisher::firstOrCreate(['name' => $companyName]);
                    $game->publishers()->attach($publisher->id);
                }
            }
            DB::commit();
        }
        // TODO: stop saving images to the local disk
        // $client = new Client();
        // $image = $client->request('GET', 'https://images.igdb.com/igdb/image/upload/t_cover_big/'.$cover.'.jpg');
        // Storage::disk('local')->put('public/games/'.$cover.'.jpg', $image->getBody()->getContents());

        return response()->json('-'.$game->id, 200);
    }
}
