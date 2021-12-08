<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Platform;

class AddComponentsColumnsOnPlatforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->boolean('box');
            $table->boolean('case');
            $table->boolean('manual');
            $table->boolean('disc');
            $table->boolean('case_art');
            $table->boolean('cartridge');
            $table->boolean('cartridge_holder');
            $table->boolean('clamshell');
            $table->boolean('box_or_case');
            $table->boolean('art_or_holder');
            $table->boolean('case_sticker');
            $table->boolean('insert');
            $table->boolean('styrofoam');
        });

        $config = config('platform-components');
        // TODO: Reminder to get data for PS Vita
        // Update each platform to have their default components
        foreach ($config as $acronym => $components) {
            $platform = Platform::where('acronym', $acronym)->first();
            foreach ($components as $component) {
                $platform->$component = true;
            }
            $platform->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platforms', function (Blueprint $table) {
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
