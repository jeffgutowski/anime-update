<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateHardwareCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories_hardware_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->unique();
        });

        DB::table('accessories_hardware_companies')->insert([
            ['name' => 'Nintendo', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
            ['name' => 'Sony', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
            ['name' => 'Microsoft', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
            ['name' => 'Sega', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
            ['name' => 'Mad Catz', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
            ['name' => 'Logitech', 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()],
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
