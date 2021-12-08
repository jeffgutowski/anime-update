<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Platform;

class CreatePlatformCoverField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('cover_image')->nullable();
        });
        DB::beginTransaction();
        Platform::where("acronym", "atari2600")->update(["color" => "#bbbbbb", "text_color" => "#ed1c24"]);
        Platform::where("acronym", "atari5200")->update(["color" => "#bbbbbb", "text_color" => "#153a96"]);
        Platform::where("acronym", "atari7800")->update(["color" => "#bbbbbb", "text_color" => "#982025"]);
        Platform::where("acronym", "C16")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "C64")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "CD-i")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "gb")->update(["color" => "#c4c4c4", "text_color" => "#0c005d", "cover_position" => "left"]);
        Platform::where("acronym", "gbc")->update(["color" => "#f5f5f5", "text_color" => "#0c005d", "cover_position" => "left"]);
        Platform::where("acronym", "Jaguar")->update(["color" => "#000000", "text_color" => "#ff0000"]);
        Platform::where("acronym", "Jaguar CD")->update(["color" => "#000000", "text_color" => "#ff0000", "acronym" => "JaguarCD"]);
        Platform::where("acronym", "Lynx")->update(["color" => "#000000", "text_color" => "#ffd42a"]);
        Platform::where("acronym", "neogeocd")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "neogeocolor")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "neogeopocket")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "nes")->update(["color" => "#ffffff", "text_color" => "#fe0202"]);
        Platform::where("acronym", "new3ds")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "sega32")->update(["color" => "#fdad00", "text_color" => "#b1140d"]);
        Platform::where("acronym", "segacd")->update(["color" => "#0880ce", "text_color" => "#ffffff"]);
        Platform::where("acronym", "gamegear")->update(["color" => "#d32dbf", "text_color" => "#ffffff"]);
        Platform::where("acronym", "smd")->update(["color" => "#fc101c", "text_color" => "#ffffff"]);
        Platform::where("acronym", "sms")->update(["color" => "#ffffff", "text_color" => "#fc101c"]);
        Platform::where("acronym", "saturn")->update(["color" => "#ebe3d8", "text_color" => "#000000"]);
        Platform::where("acronym", "snes")->update(["color" => "#000000", "text_color" => "#fc0c18"]);
        Platform::where("acronym", "turbografx16cd")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "turbografx16")->update(["color" => "#ffffff", "text_color" => "#000000"]);
        Platform::where("acronym", "virtualboy")->update(["color" => "#000000", "text_color" => "#fc0c18"]);
        Platform::where("id", ">", 0)->update(["cover_image" => DB::raw('CONCAT("https://game-seeker.s3.amazonaws.com/platforms/", platforms.acronym, ".png")')]);
        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
}
