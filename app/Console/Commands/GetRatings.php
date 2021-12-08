<?php

namespace App\Console\Commands;

use App\Models\Game;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GetRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:ratings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get ratings for all games';

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
        $offset = 0;

        $bar = $this->output->createProgressBar(Game::where('igdb_id', '=', null)->count());
        $bar->start();

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


            $games = Game::where('igdb_id', '=', null)->limit(500)->offset($offset)->get();
            if (count($games) == 0) {
                break;
            }
            foreach ($games as $game) {
                $name = $game->name;
                $str_offset = strpos($name, '(');
                if ($str_offset) {
                    $name = substr($name, 0, $str_offset - 1);
                }
                $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);
                usleep(250000);
                $client = new Client();
                try {
                    $request = $client->request('POST', "https://api.igdb.com/v4/games", [
                        'headers' => [
                            'Client-ID' => 'i2ghld19i0jxhml3o4g9t35xdgqe01',
                            'Authorization' => 'Bearer '.$token->access_token,
                            'Content-Type' => 'text/plain',
                        ],
                        'body' => 'fields *, name, age_ratings.*; where name = "'.$name.'" & platforms.id = {'.$game->platform_id.'};'
                    ]);
                } catch (\Exception $e) {
                    logger()->debug($e->getMessage());
                    $bar->advance();
                    continue;
                }
                $response = json_decode($request->getBody()->getContents());
                if (count($response) == 1) {
                    $igdb_game = $response[0];
                } elseif (count($response) > 1) {
                    foreach ($response as $key => $item) {
                        if (isset($item->platforms)) {
                            foreach ($item->platforms as $platform) {
                                if ($platform == $game->platform_id) {
                                    $igdb_game = $response[$key];
                                    break;
                                }
                            }
                        }
                    }
                }
                if (!isset($igdb_game)) {
                    $bar->advance();
                    continue;
                }
                Game::where('id', $game->id)->update(["igdb_id" => $igdb_game->id]);
                if (isset($igdb_game->age_ratings)) {
                    foreach ($igdb_game->age_ratings as $rating) {
                        Game::where('id', $game->id)->update([$this->ratings_category[$rating->category] => $this->ratings[$rating->rating]]);
                    }
                }
                $bar->advance();
            }
            $offset += 500;
        }
        $this->line("\r");
    }
}
