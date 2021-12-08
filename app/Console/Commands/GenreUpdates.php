<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use App\Models\Game;
use App\Models\Genre;
use DB;

class GenreUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'genres:update {--add} {--remove} {--modify} {--listorder} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates genres with a specific ruleset';

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
        if ($this->option('add') || $this->option('all')) {
            $this->add();
        }
        if ($this->option('remove') || $this->option('all')) {
            $this->remove();
        }
        if ($this->option('modify') || $this->option('all')) {
            $this->modify();
        }
        if ($this->option('listorder') || $this->option('all')) {
            $this->listorder();
        }
    }

    public function add()
    {
        $this->line("--add");
        // associate genres
        $genres = [
            'Basketball' => ['nba ', 'basketball', 'hoops'],
            'Football (US Football)' => ['nfl ', ' nfl', 'football', 'madden'],
            'Baseball' => ['baseball', 'mlb ', ' mlb'],
            'Hockey' => ['hockey', 'nhl ', 'nhl'],
            'Tennis' => ['tennis'],
            'Bowling' => ['bowling'],
            'Boxing' => ['boxing'],
            'Soccer (Football)' => ['soccer', ' fifa', 'fifa '],
            'Golf' => ['golf', 'pga ', ' pga', 'tiger woods'],
            'Wrestling' => ['wwe', 'wwf'],
            'Fishing' => ['fishing'],
            'Hunting' => ['fishing', 'deer hunter', "cabela's"],
            'Racing' => ['atv', 'horse', 'f1 ', ' f1', 'racing', 'horse racing', 'BMX', 'Burnout', 'Cars', 'Dirt', 'Gran Turismo', 'Midnight Club', 'GP', 'Gran Prix', 'MotoGP', 'MX ', ' MX', 'MXGP', 'Nascar', 'Need for Speed', 'Rally', 'SSX', 'Test Drive', 'Tony Hawk', "Tony Hawk's", 'WRC'],
            'Horse Racing' => ['horse'],
            'Volleyball' => ['volley ball', 'volleyball'],
            'Sports' => ['wwe', 'wwf', 'fishing', 'deer hunter', ' atv', 'atv ', 'horse', "cabela's", 'bowling', 'volley ball', 'volleyball', 'hoops', 'f1 ', ' f1', 'fifa', 'BMX', 'Burnout', 'Cars', 'Dirt', 'Gran Turismo', 'Midnight Club', 'GP', 'Gran Prix', 'MotoGP', 'MX ', ' MX', 'MXGP', 'Nascar', 'Need for Speed', 'Rally', 'SSX', 'Test Drive', 'Tony Hawk', "Tony Hawk's", 'WRC'],
        ];
        foreach ($genres as $genre_name => $names)
        {
            $genre = Genre::where('name', '=', $genre_name)->first();
            $games = Game::where(function($query) use ($names) {
                foreach ($names as $name) {
                    $query->orWhere('name', 'LIKE', "%$name%");
                }
            })
            ->where("name", "NOT LIKE", "Hakunetsu Pro%")
            ->get();
            $this->line($genre_name);
            $bar = $this->output->createProgressBar(count($games));
            $bar->start();
            foreach ($games as $game) {
                try {
                    $game->genres()->attach($genre);
                } catch (\Exception $e) {

                }
                $bar->advance();
            }
            $bar->finish();
            $this->output->newLine();
        }
    }

    public function remove()
    {
        $this->line("--remove");
        // delete genre
        $this->line("Indie");
        $genre = Genre::where('name', '=', "Indie")->first();
        if ($genre) {
            Game::where('genre_id', '=', $genre->id)->withTrashed()->update(['genre_id' => null]);
            DB::table('game_genre')->where('genre_id', '=', $genre->id)->delete();
            $genre->delete();
        }
    }

    public function modify()
    {
        $this->line("--modify");
        $genres = [
            "First-Person Shooter (FPS)" => ["Shooter", "Action", "Adventure"],
            "Third-Person Shooter (TPS)" => ["Shooter", "Action", "Adventure"],
            "Role-playing (RPG)" => ["Adventure"],
            "Wrestling" => ["Sports", "Fighting"],
            "Platformer" => ["Action", "Adventure"],
        ];
        foreach ($genres as $genre_name => $names) {
            $games = Game::whereHas('genres', function($query) use ($genre_name) {
                $query->where('name', "=", $genre_name);
            })->get();
            $modifing_genres = Genre::whereIn("name", $names)->get();
            $this->line($genre_name);
            $bar = $this->output->createProgressBar(count($games));
            $bar->start();
            foreach ($games as $game) {
                foreach ($modifing_genres as $m_genre) {
                    try {
                        $game->genres()->attach($m_genre);
                    } catch (\Exception $e) {

                    }
                }

                $bar->advance();
            }
            $bar->finish();
            $this->output->newLine();
        }


        // remove action from fighting
        $games = Game::whereHas('genres', function($query) use ($genre_name) {
            $query->where('name', "=", "Fighting");
        })->get();
        $genre = Genre::where('name', 'Action')->first();
        $this->line("Fighting");
        $bar = $this->output->createProgressBar(count($games));
        $bar->start();
        foreach ($games as $game) {
            try {
                $game->genres()->detach($genre);
            } catch (\Exception $e) {

            }

            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine();
    }

    public function listorder()
    {
        $this->line("--listorder");
        $order = [
            "Action",
            "Adventure",
            "Fighting",
            "Racing",
            "Farming",
            "Strategy",
            "Shooter",
            "Visual Novel",
            "Role-playing (RPG)",
            "Arcade",
            "Flight",
            "Space",
            "Simulator",
            "Party/Mini Game",
            "Board Game",
            "Gambling",
            "Card Game",
            "Dance",
            "Music",
            "Puzzle",
            "Quiz/Trivia",
            "Pinball",
            "Educational",
            "Hunting",
            "Fishing",
            "Vehicular Combat",
            "Sports",
            "Action-Simulated",
            "Point-and-Click",
            "Tactical",
            "Beat 'em Up",
            "Hack and Slash",
            "Platformer",
            "Dungeon Crawl",
            "Combat",
            "Dating",
            "Roguelike",
            "Sandbox",
            "Battle Royale",
            "Light Gun",
            "Rail Shooter",
            "First-Person Shooter (FPS)",
            "Third-Person Shooter (TPS)",
            "Run and Gun",
            "Shoot 'em Up",
            "Turn-Based",
            "Open World",
            "Japanese-Style Role-Playing Game (JRPG)",
            "Western-Style Role-Playing Game (WRPG)",
            "Massive Multiplayer Online (MMO)",
            "Multiplayer Online Battle Arena (MOBA)",
            "Real Time Strategy (RTS)",
            "Business Tycoon",
            "Stealth",
            "Survival",
            "Horror",
            "Rhythm",
            "Tower Defense",
            "Baseball",
            "Basketball",
            "Bowling",
            "Boxing",
            "Football (US Football)",
            "Golf",
            "Hockey",
            "Horse Racing",
            "Rugby",
            "Skateboard",
            "Skiing / Snowboarding",
            "Soccer (Football)",
            "Tennis",
            "Wrestling",
            "Miscellaneous",
        ];
        $bar = $this->output->createProgressBar(count($order));
        $bar->start();
        foreach ($order as $key => $name) {
            Genre::where("name", $name)->update(['priority' => $key]);
            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine();
    }
}
