<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Product;

class PlayasiaRegion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->addColumn('date','pa')->nullable();
            $table->addColumn('text','cover_pa')->nullable();
            $table->addColumn('text','name_pa')->nullable();
            $table->addColumn('text','upc_pa')->nullable();
            $table->addColumn('text','catalog_number_pa')->nullable();

        });

        Schema::table('platforms', function (Blueprint $table) {
            $table->addColumn('boolean','pa');
        });

        DB::table('platforms')
            ->whereIn("acronym", [
                "3ds",
                "nds", "switch", "ps2",
                "ps3", "ps4", "ps5",
                "psvita",
                "psp",
                "xboxone", "XboxSeriesX",
                "xbox360",
            ])
            ->update(["pa" => true]);

        Product::query()->update(["cover_pa" => DB::raw("cover_us")]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('pa');
            $table->dropColumn('cover_pa');
            $table->dropColumn('name_pa');
            $table->dropColumn('upc_pa');
            $table->dropColumn('catalog_number_pa');
        });

        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn('pa');
        });
    }
}
