<?php

namespace App\Models;

use App\Scopes\GameScope;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Str;

class Game extends Product
{
    use CrudTrait;

    public static function boot()
    {
        parent::boot();
        static::creating(function (Game $game) {
            // if a uuid is not set on create, then make one
            if (!isset($game->uuid)) {
                $game->uuid = (string) Str::uuid();
            }
            $game->type = 'game';
        });
        self::created(function($model) {
            // After a game is created. Set the default components the game to have to be true.
            $components = Platform::select(config('components.all'))->where('id', '=', $model->attributes['platform_id'])->first();
            if (isset($components)) {
                foreach ($components->toArray() as $component => $value) {
                    if ($value == true) {
                        $model->$component = $value;
                    }
                }
            }
            $model->save();
        });
        static::addGlobalScope(new GameScope());
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'box',
        'case',
        'manual',
        'disc',
        'case_art',
        'cartridge',
        'cartridge_holder',
        'clamshell',
        'box_or_case',
        'art_or_holder',
        'case_sticker',
        'insert',
        'styrofoam',
        'description',
        'cover_generator',
        'cover',
        'release_date',
        'publisher',
        'developer',
        'pegi',
        'platform_id',
        'genre_id',
        'igdb_id',
        'ntsc_u',
        'ntsc_j',
        'ntsc_c',
        'pal',
        'igdb_created_at',
        'cover_us',
        'cover_jp',
        'cover_eu',
        'catalog_number',
        'upc',
        'extra_images',
        'cover_us_url',
        'cover_jp_url',
        'cover_eu_url',
        'extra_images',
        'average_rating',
        'grouping_game_id',
        'name_us',
        'name_eu',
        'name_jp',
        'upc_us',
        'upc_jp',
        'upc_eu',
        'catalog_number_us',
        'catalog_number_jp',
        'catalog_number_eu',
        'esrb',
        'components',
        'player_count',
        'multiplayer_local',
        'multiplayer_lan',
        'multiplayer_online',
        'multiplayer_online_no_limit',
        'cover_pa',
        'name_pa',
        'upc_pa',
        'catalog_number_pa',
        'pa',
        'cover_pa_url',
    ];


}
