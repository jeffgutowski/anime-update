<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameFulltextIndexOnGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
        DB::statement('ALTER TABLE games ADD FULLTEXT full(name)');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE games DROP INDEX full');
        Schema::table('games', function (Blueprint $table) {
            $table->index('name');
        });
    }
}
