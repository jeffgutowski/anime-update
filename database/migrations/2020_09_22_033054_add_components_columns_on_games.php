<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Game;
use App\Models\Platform;

class AddComponentsColumnsOnGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->boolean('box')->nullable();
            $table->boolean('case')->nullable();
            $table->boolean('manual')->nullable();
            $table->boolean('disc')->nullable();
            $table->boolean('case_art')->nullable();
            $table->boolean('cartridge')->nullable();
            $table->boolean('cartridge_holder')->nullable();
            $table->boolean('clamshell')->nullable();
            $table->boolean('box_or_case')->nullable();
            $table->boolean('art_or_holder')->nullable();
            $table->boolean('case_sticker')->nullable();
            $table->boolean('insert')->nullable();
            $table->boolean('styrofoam')->nullable();
        });
        $config = config('platform-components');
        // Update each game to have their default components based on platform
        foreach ($config as $acronym => $components) {
            $updates = [];
            foreach ($components as $component) {
                $updates[$component] = true;
            }
            $platform = Platform::where('acronym', $acronym)->first();
            // Update games to have their default components
            Game::where('platform_id', $platform->id)->update($updates);

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
            $table->dropColumn('box');
            $table->dropColumn('case');
            $table->dropColumn('manual');
            $table->dropColumn('disc');
            $table->dropColumn('case_art');
            $table->dropColumn('cartridge');
            $table->dropColumn('cartridge_holder');
            $table->dropColumn('clamshell');
            $table->dropColumn('box_or_case');
            $table->dropColumn('art_or_holder');
            $table->dropColumn('case_sticker');
            $table->dropColumn('insert');
            $table->dropColumn('styrofoam');
        });
    }
}
