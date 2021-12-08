<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use App\Models\Game;

class MergeAccessoriesHardwareIntoGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('type');
            $table->string('company')->nullable();
            $table->string('model_number')->nullable();
        });
        DB::table('accessories_hardware_types')->insert(['id' => 0, 'name' => 'Game', 'slug' => 'game']);
        DB::table('games')->update(['type' => 'game']);
        $hardwares = DB::table('accessories_hardware')->get();
        foreach ($hardwares as $hardware) {
            unset($hardware->id);
            $hardware->type = slugify($hardware->type);
            $hardware->uuid = (string) Str::uuid();
            DB::table('games')->insert((array) $hardware);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Too complicated. Just reset database.
    }
}
