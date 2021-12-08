<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageToPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('image')->nullable();
        });

        // insert platform images if it exists on S3
        $platforms = \App\Models\Platform::all();
        \DB::beginTransaction();
        foreach ($platforms as $platform) {
            if ($this->exists('https://game-seeker.s3.amazonaws.com/platforms/cover/'.$platform->acronym.'.jpg')) {
                $platform->image = 'https://game-seeker.s3.amazonaws.com/platforms/cover/'.$platform->acronym.'.jpg';
                $platform->save();
            }
        }
        \DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn('image')->nullable();
        });
    }

    private function exists($url){
        $headers=get_headers($url);
        return stripos($headers[0],"200 OK")?true:false;
    }
}
