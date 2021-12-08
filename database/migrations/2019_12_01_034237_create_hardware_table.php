<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHardwareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories_hardware', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('company')->nullable()->default(null);
            $table->foreign('company')->references('name')->on('accessories_hardware_companies');
            $table->string('type')->nullable()->default(null);
            $table->foreign('type')->references('name')->on('accessories_hardware_types');
            $table->integer('platform_id')->unsigned()->index();
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->string('model_number')->nullable();
            $table->string('upc')->nullable();
            $table->date('release_ntsc_u')->nullable();
            $table->date('release_ntsc_j')->nullable();
            $table->date('release_pal')->nullable();
            $table->string('image_us')->nullable();
            $table->string('image_jp')->nullable();
            $table->string('image_eu')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accessories_hardware');
    }
}
