<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->index('igdb_id');
        });
        Schema::table('developers', function (Blueprint $table) {
            $table->index('name');
        });
        Schema::table('publishers', function (Blueprint $table) {
            $table->index('name');
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
            $table->dropIndex(['igdb_id']);
        });
        Schema::table('developers', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
        Schema::table('publishers', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
}
