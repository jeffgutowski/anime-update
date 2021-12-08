<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGameRantings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->integer('grouping_game_id')->index();
            $table->integer('rating_game_id')->index();
            $table->decimal('average_rating', 2, 1)->nullable()->index();
        });
        Schema::create('game_rating', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->index();
            $table->integer('user_id')->index();
            $table->decimal('rating', 2, 1)->index();
            $table->timestamps();
        });
       DB::statement("UPDATE games SET grouping_game_id = id, rating_game_id = id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('grouping_game_id');
            $table->dropColumn('rating_game_id');
            $table->dropColumn('average_rating');
        });
        Schema::table('game_rating', function (Blueprint $table) {
            $table->dropIfExists();
        });
    }
}
