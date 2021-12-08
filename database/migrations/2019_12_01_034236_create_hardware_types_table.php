<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateHardwareTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories_hardware_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->unique();
        });

        DB::table('accessories_hardware_types')->insert([
            ['name' => 'Console', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
            ['name' => 'Controller', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
            ['name' => 'Memory Card', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accessories_hardware_types');
    }
}
