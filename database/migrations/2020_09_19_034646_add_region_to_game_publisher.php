<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegionToGamePublisher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_publisher', function (Blueprint $table) {
            $table->string('region')->nullable()->index();
            $table->dropForeign(['game_id']);
            $table->dropForeign(['publisher_id']);
            $table->dropUnique(['game_id', 'publisher_id']);
            $table->unique(['game_id', 'publisher_id', 'region']);
            $table->foreign('game_id')->references('id')->on('games');
            $table->foreign('publisher_id')->references('id')->on('publishers');
        });
        DB::statement('ALTER TABLE game_publisher MODIFY COLUMN region ENUM("us", "eu", "jp")');
        DB::statement('UPDATE game_publisher set game_publisher.region = "us" WHERE region IS NULL');
        // Clone Publishers into jp region
        $gamePublisherPivot = \DB::table('game_publisher')->get();
        $insert = [];
        foreach ($gamePublisherPivot as $pivot) {
            $insert[] = ['game_id' => $pivot->game_id, 'publisher_id' => $pivot->publisher_id, 'region' => 'jp'];
            if (count($insert) > 1000) {
                \DB::table('game_publisher')->insert($insert);
                $insert = [];
            }
        }
        if (count($insert) > 0) {
            \DB::table('game_publisher')->insert($insert);
        }
        // Clone Publishers into eu region
        $insert = [];
        foreach ($gamePublisherPivot as $pivot) {
            $insert[] = ['game_id' => $pivot->game_id, 'publisher_id' => $pivot->publisher_id, 'region' => 'eu'];
            if (count($insert) > 1000) {
                \DB::table('game_publisher')->insert($insert);
                $insert = [];
            }
        }
        if (count($insert) > 0) {
            \DB::table('game_publisher')->insert($insert);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DELETE FROM game_publisher WHERE region != "us"');
        Schema::table('game_publisher', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropForeign(['publisher_id']);
            $table->dropUnique(['game_id', 'publisher_id', 'region']);
            $table->unique(['game_id', 'publisher_id']);
            $table->foreign('game_id')->references('id')->on('games');
            $table->foreign('publisher_id')->references('id')->on('publishers');
            $table->dropColumn('region');
        });
    }
}
