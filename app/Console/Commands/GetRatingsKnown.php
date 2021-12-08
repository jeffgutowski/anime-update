<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IgdbService;
use App\Models\Game;
use GuzzleHttp\Client;

class GetRatingsKnown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:ratings-known';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Ratings for known igdb games';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $ratings = [
        1 => '3',
        2 => '7',
        3 => '12',
        4 => '16',
        5 => '18',
        6 => 'RP',
        7 => 'EC',
        8 => 'E',
        9 => 'E10',
        10 => 'T',
        11 => 'M',
        12 => 'AO',
    ];

    private $ratings_category = [
        1 => "esrb",
        2 => "pegi"
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(Game::where('igdb_id', '!=', null)->count());
        $bar->start();
        $offset = 0;
        while (true) {
            $client = new Client();
            $request = $client->request('POST', "https://id.twitch.tv/oauth2/token", [
                'form_params' => [
                    'client_id' => 'i2ghld19i0jxhml3o4g9t35xdgqe01',
                    'client_secret' => 'l5p2qov1xbrgvpw99pgcy08sz8pi70',
                    'grant_type' => 'client_credentials'
                ],
            ]);
            $token = json_decode($request->getBody()->getContents());

            $games = Game::where('igdb_id', '!=', null)->limit(500)->offset($offset)->pluck('igdb_id');
            $ids = implode(', ', $games->toArray());
            if (count($games) == 0) {
                break;
            }
            $client = new Client();
            $request = $client->request('POST', "https://api.igdb.com/v4/games", [
                'headers' => [
                    'Client-ID' => 'i2ghld19i0jxhml3o4g9t35xdgqe01',
                    'Authorization' => 'Bearer '.$token->access_token,
                    'Content-Type' => 'text/plain',
                ],
                'body' => "fields age_ratings.*; where id = ($ids); limit 500;"
            ]);
            $response = json_decode($request->getBody()->getContents());
            foreach ($response as $game) {
                if (isset($game->age_ratings)) {
                    foreach ($game->age_ratings as $rating) {
                        Game::where('igdb_id', $game->id)->update([$this->ratings_category[$rating->category] => $this->ratings[$rating->rating]]);
                    }
                }
                $bar->advance();
            }

            $offset += 500;
            $bar->setProgress($offset);
            sleep(1);
        }
        $bar->finish();
        $this->line("\r");
    }
}
