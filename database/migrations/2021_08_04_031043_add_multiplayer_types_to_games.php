<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultiplayerTypesToGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->boolean('multiplayer_local');
            $table->boolean('multiplayer_lan');
            $table->boolean('multiplayer_online');
        });
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
        });
    }
}
