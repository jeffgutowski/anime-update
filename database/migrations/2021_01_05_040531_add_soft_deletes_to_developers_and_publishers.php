<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToDevelopersAndPublishers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('developers', function (Blueprint $table) {
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE developers ADD FULLTEXT search(name)');
        Schema::table('publishers', function (Blueprint $table) {
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE publishers ADD FULLTEXT search(name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('developers', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        DB::statement('ALTER TABLE developers DROP INDEX search');
        Schema::table('publishers', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        DB::statement('ALTER TABLE publishers DROP INDEX search');
    }
}
