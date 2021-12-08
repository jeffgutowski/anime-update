<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocaleCoversToGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('cover_us')->nullable();
            $table->string('cover_jp')->nullable();
            $table->string('cover_eu')->nullable();
        });
        DB::table('games')->update([
            'cover_us' => DB::raw( "CONCAT('https://images.igdb.com/igdb/image/upload/t_cover_big/', cover)"),
            'cover_jp' => DB::raw( "CONCAT('https://images.igdb.com/igdb/image/upload/t_cover_big/', cover)"),
            'cover_eu' => DB::raw( "CONCAT('https://images.igdb.com/igdb/image/upload/t_cover_big/', cover)"),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('cover_us');
            $table->dropColumn('cover_jp');
            $table->dropColumn('cover_eu');
        });
    }
}
