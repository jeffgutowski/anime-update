<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomListGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_list_game', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('custom_list_id');
            $table->unsignedInteger('game_id');
            $table->text("description");
            $table->integer("order_number");
            $table->string("thumbnail");
            $table->timestamps();
            $table->foreign('custom_list_id')->references('id')->on('custom_lists')->onDelete('cascade');
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
        Schema::dropIfExists('custom_list_game');
    }
}
