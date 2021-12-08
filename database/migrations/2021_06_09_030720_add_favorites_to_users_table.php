<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFavoritesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('favorite_game')->nullable();
            $table->integer('favorite_genre_id')->unsigned()->nullable();
            $table->integer('favorite_platform_id')->unsigned()->nullable();
            $table->string('favorite_developer')->nullable();
            $table->string('favorite_publisher')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('favorite_game');
            $table->dropColumn('favorite_genre_id');
            $table->dropColumn('favorite_platform_id');
            $table->dropColumn('favorite_developer');
            $table->dropColumn('favorite_publisher');
        });
    }
}
