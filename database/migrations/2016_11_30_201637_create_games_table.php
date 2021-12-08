<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->string('cover')->nullable();
            $table->boolean('cover_generator')->default('1');
            $table->text('description')->nullable();
            $table->date('release_date')->nullable();
            $table->string('publisher')->nullable();
            $table->string('developer')->nullable();
            $table->enum('pegi', ['3', '7', '12', '16', '18'])->nullable();
            $table->text('tags')->nullable();
            $table->string('source_name')->nullable();
            $table->integer('igdb_id')->nullable()->unsigned();
            $table->integer('metacritic_id')->nullable()->unsigned();
            $table->integer('giantbomb_id')->nullable()->unsigned();
            $table->integer('platform_id')->unsigned()->index();
            $table->integer('genre_id')->nullable()->unsigned();
            $table->date('ntsc_u')->nullable();
            $table->date('ntsc_j')->nullable();
            $table->date('ntsc_c')->nullable();
            $table->date('pal')->nullable();
            $table->bigInteger('igdb_created_at');
            $table->softDeletes()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('games');
    }
}
