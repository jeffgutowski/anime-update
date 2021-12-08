<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCatalogNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `games` CHANGE `catalog_number` `upc` VARCHAR(14)");
        Schema::table('games', function (Blueprint $table) {
            $table->string('catalog_number')->nullable();
            $table->index('catalog_number');
            $table->index('upc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('catalog_number');
            $table->dropIndex(['upc']);
        });
        DB::statement("ALTER TABLE `games` CHANGE `upc` `catalog_number` VARCHAR(255)");
    }
}
