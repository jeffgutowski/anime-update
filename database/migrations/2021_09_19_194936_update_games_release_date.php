<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateGamesReleaseDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Product::where("id", "!=", "0")->update(['release_date' => DB::raw("coalesce(LEAST(ntsc_u, ntsc_j, pal, pa), ntsc_u, ntsc_j, pal, pa)")]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // update from a backup database. Let us hope it doesn't have to come to it
    }
}
