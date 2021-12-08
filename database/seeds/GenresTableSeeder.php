<?php

use Illuminate\Database\Seeder;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('genres')->insert(json_decode('[
            {
                "id": 13,
                "name": "Simulator",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 24,
                "name": "Tactical",
                "created_at": "2011-03-24 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 26,
                "name": "Quiz/Trivia",
                "created_at": "2011-04-05 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 4,
                "name": "Fighting",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 15,
                "name": "Strategy",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 31,
                "name": "Adventure",
                "created_at": "2011-12-11 12:00:00",
                "updated_at": "2011-12-11 12:00:00"
            },
            {
                "id": 12,
                "name": "Role-playing (RPG)",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 5,
                "name": "Shooter",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 7,
                "name": "Music",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 32,
                "name": "Indie",
                "created_at": "2012-07-04 12:00:00",
                "updated_at": "2012-07-04 12:00:00"
            },
            {
                "id": 16,
                "name": "Turn-based strategy (TBS)",
                "created_at": "2011-02-14 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 30,
                "name": "Pinball",
                "created_at": "2011-11-02 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 9,
                "name": "Puzzle",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 11,
                "name": "Real Time Strategy (RTS)",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 25,
                "name": "Hack and slash/Beat \'em up",
                "created_at": "2011-04-01 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 8,
                "name": "Platform",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 10,
                "name": "Racing",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 14,
                "name": "Sport",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-07 12:00:00"
            },
            {
                "id": 33,
                "name": "Arcade",
                "created_at": "2013-10-05 12:00:00",
                "updated_at": "2013-10-05 12:00:00"
            },
            {
                "id": 2,
                "name": "Point-and-click",
                "created_at": "2011-02-13 12:00:00",
                "updated_at": "2011-12-08 12:00:00"
            }]'
        , true));
    }
}
