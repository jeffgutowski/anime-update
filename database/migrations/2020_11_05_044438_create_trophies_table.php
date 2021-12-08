<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;
use App\Models\Trophy;
use App\Models\Platform;
use App\Models\Game;

class CreateTrophiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trophies', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->integer('threshold')->index()->unsigned();
            $table->integer('tier')->nullable();
            $table->string('type');
            $table->integer('platform_id')->nullable()->unsigned();
            $table->string('region')->nullable();
            $table->timestamps();
            $table->foreign('platform_id')->references('id')->on('platforms');

        });
        $now = Carbon::now();
        DB::table('trophies')->insert([
            ['name' => 'Newbie Collector', 'threshold' => 0, 'tier' => 1, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Newbie Collector', 'threshold' => 10, 'tier' => 2, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Newbie Collector', 'threshold' => 25, 'tier' => 3, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Collector', 'threshold' => 50, 'tier' => 1, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Collector', 'threshold' => 100, 'tier' => 2, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Collector', 'threshold' => 150, 'tier' => 3, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Competitive Collector', 'threshold' => 200, 'tier' => 1, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Competitive Collector', 'threshold' => 250, 'tier' => 2, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Competitive Collector', 'threshold' => 325, 'tier' => 3, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Champion Collector', 'threshold' => 400, 'tier' => 1, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Champion Collector', 'threshold' => 450, 'tier' => 2, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Champion Collector', 'threshold' => 525, 'tier' => 3, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ultimate Collector', 'threshold' => 600, 'tier' => 1, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ultimate Collector', 'threshold' => 750, 'tier' => 2, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ultimate Collector', 'threshold' => 875, 'tier' => 3, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Legendary Collector', 'threshold' => 1000, 'tier' => 1, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Legendary Collector', 'threshold' => 1500, 'tier' => 2, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Legendary Collector', 'threshold' => 3000, 'tier' => 3, 'type' => 'collector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Newbie Gamer', 'threshold' => 0, 'tier' => 1, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Newbie Gamer', 'threshold' => 3, 'tier' => 2, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Newbie Gamer', 'threshold' => 6, 'tier' => 3, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Gamer', 'threshold' => 10, 'tier' => 1, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Gamer', 'threshold' => 20, 'tier' => 2, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Gamer', 'threshold' => 35, 'tier' => 3, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Competitive Gamer', 'threshold' => 50, 'tier' => 1, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Competitive Gamer', 'threshold' => 65, 'tier' => 2, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Competitive Gamer', 'threshold' => 80, 'tier' => 3, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Champion Gamer', 'threshold' => 100, 'tier' => 1, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Champion Gamer', 'threshold' => 125, 'tier' => 2, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Champion Gamer', 'threshold' => 160, 'tier' => 3, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ultimate Gamer', 'threshold' => 200, 'tier' => 1, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ultimate Gamer', 'threshold' => 240, 'tier' => 2, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ultimate Gamer', 'threshold' => 275, 'tier' => 3, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Legendary Gamer', 'threshold' => 300, 'tier' => 1, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Legendary Gamer', 'threshold' => 400, 'tier' => 2, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Legendary Gamer', 'threshold' => 500, 'tier' => 3, 'type' => 'gamer', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $platforms = Platform::where('name', '!=', 'Multi-Platform')->get();
        DB::beginTransaction();
            foreach ($platforms as $platform) {
                foreach (['ntsc_u', 'ntsc_j', 'pal'] as $region) {
                    $count = Game::select('grouping_game_id')->where('platform_id', $platform->id)->groupBy('grouping_game_id')->whereNotNull($region)->get()->count();
                    $count = $count > 0 ? $count : 1;
                    Trophy::create(['name' => $platform->name.' Gamer', 'threshold' => 0, 'type' => 'gamer', 'platform_id' => $platform->id, 'region' => $region]);
                    Trophy::create(['name' => $platform->name.' Gamer Completist', 'threshold' => $count, 'type' => 'gamer', 'platform_id' => $platform->id, 'region' => $region]);
                    Trophy::create(['name' => $platform->name.' Collector', 'threshold' => 0, 'type' => 'collector', 'platform_id' => $platform->id, 'region' => $region]);
                    Trophy::create(['name' => $platform->name.' Collector Completist', 'threshold' => $count, 'type' => 'collector', 'platform_id' => $platform->id, 'region' => $region]);
                }
            }
        DB::commit();
        DB::table('permissions')->insert(['name' => 'edit_trophies', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        $permission = DB::table('permissions')->where('name', 'edit_trophies')->first();
        DB::table('permission_roles')->insert(['permission_id' => $permission->id, 'role_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trophies');
        DB::table('permissions')->where('name', 'edit_trophies')->delete();
    }
}
