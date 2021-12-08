<?php

namespace App\Models;

use App\Scopes\GameScope;
use App\Scopes\HardwareScope;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class AccessoriesHardware extends Product
{
    use CrudTrait;

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new HardwareScope());
    }
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'description',
        'cover_generator',
        'cover',
        'release_date',
        'publisher',
        'developer',
        'pegi',
        'platform_id',
        'other_platforms',
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
        'type',
        'company',
        'model_number',
        'components'
    ];

    protected $casts = [
        'other_platforms' => 'array',
    ];

    public function getUrlSlugAttribute()
    {
        return url('hardware/' . str_slug($this->name) . '-' . $this->platform->acronym . '-' . $this->id);
    }

}
