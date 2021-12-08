<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImageColumnNamesOnAccessoriesHardwareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `image_us` `cover_us` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `image_eu` `cover_eu` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `image_jp` `cover_jp` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `release_ntsc_u` `ntsc_u` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `release_ntsc_j` `ntsc_j` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `release_pal` `pal` VARCHAR(255)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `cover_us` `image_us` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `cover_eu` `image_eu` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `cover_jp`  `image_jp` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `ntsc_u` `release_ntsc_u` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `ntsc_j` `release_ntsc_j` VARCHAR(255)");
        DB::statement("ALTER TABLE `accessories_hardware` CHANGE `pal` `release_pal` VARCHAR(255)");
    }
}
