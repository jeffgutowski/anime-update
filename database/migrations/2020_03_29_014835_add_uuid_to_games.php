<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

use App\Models\Game;

class AddUuidToGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->uuid('uuid')->index();
        });

        $games = Game::withTrashed()->get();
        DB::beginTransaction();
        foreach($games as $game) {
            $game->uuid = (string) Str::uuid();
            $game->save();
        }
        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
