<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTrophiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_trophies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->unsigned();
            $table->string("type");
            $table->string("region")->nullable();
            $table->integer('platform_id')->nullable()->unsigned();
            $table->integer("count")->unsigned();
            $table->integer("trophy_id")->unsigned();
            $table->integer("next_trophy_id")->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::table('user_trophies', function (Blueprint $table) {
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->foreign('trophy_id')->references('id')->on('trophies')->onDelete('cascade');
            $table->foreign('next_trophy_id')->references('id')->on('trophies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_trophies');
    }
}
