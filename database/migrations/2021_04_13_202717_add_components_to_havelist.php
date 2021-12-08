<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Product;
use App\Models\HaveList;

class AddComponentsToHavelist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_have_lists', function (Blueprint $table) {
            $table->unsignedInteger('quantity');
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
            $table->boolean('complete');
        });
        $lists = HaveList::all();
        foreach($lists as $item) {
            $game = Product::where('id', $item->game_id)->first();
            foreach(config('components.all') as $component) {
                if ($game->{$component}) {
                    $item->{$component} = true;
                }
            }
            $item->quantity = 1;
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_have_lists', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->dropColumn('box');
            $table->dropColumn('case');
            $table->dropColumn('manual');
            $table->dropColumn('disc');
            $table->dropColumn('case_art');
            $table->dropColumn('cartridge');
            $table->dropColumn('cartridge_holder');
            $table->dropColumn('clamshell');
            $table->dropColumn('box_or_case');
            $table->dropColumn('art_or_holder');
            $table->dropColumn('case_sticker');
            $table->dropColumn('insert');
            $table->dropColumn('styrofoam');
            $table->dropColumn('complete');
        });
    }
}
