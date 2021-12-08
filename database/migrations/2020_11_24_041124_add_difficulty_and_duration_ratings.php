<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDifficultyAndDurationRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_rating', function (Blueprint $table) {
            $table->decimal('difficulty', 2, 1)->nullable()->index();
            $table->integer('duration')->unsigned()->nullable()->index();
        });
        Schema::table('games', function (Blueprint $table) {
            $table->decimal('average_difficulty', 2, 1)->nullable()->index();
            $table->integer('average_duration')->nullable()->unsigned()->index();
        });
        DB::statement("ALTER TABLE game_rating MODIFY rating decimal(2,1) null");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_rating', function (Blueprint $table) {
            $table->dropColumn('difficulty');
            $table->dropColumn('duration');
        });
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('average_difficulty');
            $table->dropColumn('average_duration');
        });
    }
}
