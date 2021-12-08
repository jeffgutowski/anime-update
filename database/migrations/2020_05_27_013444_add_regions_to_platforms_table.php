<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegionsToPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->boolean('ntsc_u');
            $table->boolean('ntsc_j');
            $table->boolean('pal');
        });
        DB::table('platforms')->update(['ntsc_u' => true, 'ntsc_j' => true, 'pal' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn('ntsc_u');
            $table->dropColumn('ntsc_j');
            $table->dropColumn('pal');
        });
    }
}
