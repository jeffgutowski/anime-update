<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFranchiseGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franchise_game', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('franchise_id')->unsigned();
            $table->foreign('franchise_id')->references('id')->on('franchises');
            $table->integer('game_id')->unsigned();
            $table->foreign('game_id')->references('id')->on('games');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('franchise_game');
    }
}
