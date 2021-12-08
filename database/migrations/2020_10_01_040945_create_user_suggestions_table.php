<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suggestions', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('reviewed_at')->nullable();
            $table->integer('reviewer_id')->unsigned()->nullable();
            $table->boolean('approved')->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('company')->nullable();
            $table->string('model_number')->nullable();
            $table->text('description')->nullable();
            $table->integer('platform_id')->unsigned()->nullable();
            $table->date('ntsc_u')->nullable();
            $table->date('ntsc_j')->nullable();
            $table->date('pal')->nullable();
            $table->string('cover_us')->nullable();
            $table->string('cover_jp')->nullable();
            $table->string('cover_eu')->nullable();
            $table->string('name_us')->nullable();
            $table->string('name_jp')->nullable();
            $table->string('name_eu')->nullable();
            $table->string('upc_us')->nullable();
            $table->string('upc_jp')->nullable();
            $table->string('upc_eu')->nullable();
            $table->string('catalog_number_us')->nullable();
            $table->string('catalog_number_jp')->nullable();
            $table->string('catalog_number_eu')->nullable();
            $table->boolean('box')->nullable();
            $table->boolean('case')->nullable();
            $table->boolean('manual')->nullable();
            $table->boolean('disc')->nullable();
            $table->boolean('case_art')->nullable();
            $table->boolean('cartridge')->nullable();
            $table->boolean('cartridge_holder')->nullable();
            $table->boolean('clamshell')->nullable();
            $table->boolean('box_or_case')->nullable();
            $table->boolean('art_or_holder')->nullable();
            $table->boolean('case_sticker')->nullable();
            $table->boolean('insert')->nullable();
            $table->boolean('styrofoam')->nullable();
            $table->json('publishers_us')->nullable();
            $table->json('publishers_eu')->nullable();
            $table->json('publishers_jp')->nullable();
            $table->json('developers')->nullable();
            $table->json('genres')->nullable();
            $table->text('comments')->nullable();
            $table->text('review_comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suggestions');
    }
}
