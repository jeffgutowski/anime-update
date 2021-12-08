<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueCompositeKeyOnGamesPivotTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_publisher', function (Blueprint $table) {
            $table->unique(['game_id', 'publisher_id']);
        });
        Schema::table('developer_game', function (Blueprint $table) {
            $table->unique(['game_id', 'developer_id']);
        });
        DB::statement('DELETE t1 FROM game_genre t1, game_genre t2 WHERE t1.id < t2.id AND t1.game_id = t2.game_id AND t1.genre_id = t2.genre_id;');
        Schema::table('game_genre', function (Blueprint $table) {
            $table->unique(['game_id', 'genre_id']);
        });
        $developers = DB::table('developers')->where('name', 'LIKE', '%duplicate%')->pluck('id');
        DB::table('developer_game')->whereIn('developer_id', $developers)->delete();
        DB::table('developers')->where('name', 'LIKE', '%duplicate%')->delete();
        $publishers = DB::table('publishers')->where('name', 'LIKE', '%duplicate%')->pluck('id');
        DB::table('game_publisher')->whereIn('publisher_id', $publishers)->delete();
        DB::table('publishers')->where('name', 'LIKE', '%duplicate%')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_publisher', function (Blueprint $table) {
            $table->dropUnique(['game_id', 'publisher_id']);
        });
        Schema::table('developer_game', function (Blueprint $table) {
            $table->dropUnique(['game_id', 'developer_id']);
        });
        Schema::table('game_genre', function (Blueprint $table) {
            $table->dropUnique(['game_id', 'genre_od']);
        });
    }
}
