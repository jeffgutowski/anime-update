<?php

use Illuminate\Database\Seeder;
use App\Services\PlatformService;
use Carbon\Carbon;

class PlatformsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('platforms')->delete();
        $platforms = PlatformService::index();
        $insert = [];
        foreach ($platforms as $platform) {
            $color = '#000000';
            $position = 'left';
            $textColor = '#ffffff';
            switch ($platform->id) {
                case 4: // N64
                    $color = '#fdbf2d';
                    break;
                case 5: // Wii
                    $color = 'white';
                    $position = 'right';
                    $textColor = '#9b9b9b';
                    break;
                case 7: // PS1
                    $color = '#4081bc';
                    break;
                case 8: // PS2
                    $color = '#140c7a';
                    break;
                case 9: // PS3
                    $color = '#326db3';
                    break;
                case 11: // Xbox
                    $color = '#93c83e';
                    break;
                case 12: // Xbox 360
                    $color = '#a4c955';
                    break;
                case 20: // Nintendo DS
                    $color = '#929497';
                    break;
                case 21: // Gamecube
                    $color = '#663399';
                    $position = 'center';
                    break;
                case 23: // Dreamcast
                    $color = '#4365a2';
                    break;
                case 24: // GBA
                    $color = '#1f00cc';
                    break;
                case 37: // Nintendo 3DS
                    $color = '#c90f17';
                    break;
                case 38: // PSP
                    $color = '#8e92af';
                    break;
                case 41: // Wii U
                    $color = '#009ac7';
                    $position = 'center';
                    break;
                case 46: // PS Vita
                    $color = '#1654bd';
                    break;
                case 48: // PS4
                    $color = '#003791';
                    break;
                case 49: // Xbox One
                    $color = '#107c10';
                    $position = 'center';
                    break;
                case 130: // Nintendo Switch
                    $color = '#e60012';
                    break;
                default:
                    $color = '#000000';
                    $position = 'left';
                    $textColor = '#ffffff';
            }
            $insert[] = [
                'id' => $platform->id,
                'name' => $platform->name,
                'description' => isset($platform->summary) ? $platform->summary : null,
                'color' => $color,
                'text_color' => $textColor,
                'acronym' => isset($platform->abbreviation) ? $platform->abbreviation : null,
                'cover_position' => $position,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('platforms')->insert($insert);
    }
}
