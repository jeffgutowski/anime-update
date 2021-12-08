<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User_Location;

class AddRegionCodeToUserLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_locations', function (Blueprint $table) {
            $table->string('region_code')->nullable();
        });
        DB::table('user_locations')->update(array('region_code' => 'NTSC-U'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_locations', function (Blueprint $table) {
            $table->dropColumn('region_code');
        });
    }
}
