<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyNumberOfMulitplayer extends Migration
{
    public function up()
    {
        $games = App\Models\Game::where("player_count", "!=", null)->get();
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('multiplayer_local');
            $table->dropColumn('multiplayer_lan');
            $table->dropColumn('multiplayer_online');
        });
        Schema::table('games', function (Blueprint $table) {
            $table->unsignedTinyInteger('multiplayer_local')->nullable();
            $table->unsignedTinyInteger('multiplayer_lan')->nullable();
            $table->unsignedTinyInteger('multiplayer_online')->nullable();
            $table->boolean('multiplayer_online_no_limit');
        });
        foreach ($games as $game) {
            $game->multiplayer_local = $game->multiplayer_local ? $game->player_count : null;
            $game->multiplayer_lan = $game->multiplayer_lan ? $game->player_count : null;
            $game->multiplayer_online = $game->multiplayer_online ? $game->player_count : null;
            $game->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('multiplayer_local');
            $table->dropColumn('multiplayer_lan');
            $table->dropColumn('multiplayer_online');
            $table->dropColumn('multiplayer_online_no_limit');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->boolean('multiplayer_local');
            $table->boolean('multiplayer_lan');
            $table->boolean('multiplayer_online');
        });
    }
}
