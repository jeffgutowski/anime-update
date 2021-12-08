<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->string('youtube_id', 12)->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('order_by', 4)->default("asc");
            $table->boolean('custom_item_thumbnails');
            $table->boolean('show_order_number');
            $table->boolean('public');
            $table->integer('clicks')->unsigned()->index()->default('0');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('custom_lists');
    }
}
