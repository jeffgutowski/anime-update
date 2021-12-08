<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\AccessoriesHardwareType;

class AddSlugToAccessoriesHardwareTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accessories_hardware_types', function (Blueprint $table) {
            $table->string('slug');
        });
        $types = AccessoriesHardwareType::get();
        foreach ($types as $type) {
            $type->slug = slugify($type->name);
            $type->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accessories_hardware_types', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
