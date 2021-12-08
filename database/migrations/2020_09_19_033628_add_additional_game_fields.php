<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalGameFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('name_us')->nullable()->index();
            $table->string('name_jp')->nullable()->index();
            $table->string('name_eu')->nullable()->index();
            $table->string('upc_us')->nullable()->index();
            $table->string('upc_jp')->nullable()->index();
            $table->string('upc_eu')->nullable()->index();
            $table->string('catalog_number_us')->nullable()->index();
            $table->string('catalog_number_jp')->nullable()->index();
            $table->string('catalog_number_eu')->nullable()->index();
        });
        DB::statement('UPDATE games set `upc_us` = `upc`, `catalog_number_us` = `catalog_number`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('name_us');
            $table->dropColumn('name_jp');
            $table->dropColumn('name_eu');
            $table->dropColumn('upc_us');
            $table->dropColumn('upc_jp');
            $table->dropColumn('upc_eu');
            $table->dropColumn('catalog_number_us');
            $table->dropColumn('catalog_number_jp');
            $table->dropColumn('catalog_number_eu');
        });
    }
}
