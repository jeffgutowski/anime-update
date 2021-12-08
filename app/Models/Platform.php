<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platform extends Model
{
    use CrudTrait, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'platforms';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'color',
        'description',
        'acronym',
        'cover_position',
        'text_color',
        'ntsc_u',
        'ntsc_j',
        'pal',
        'cover_image',
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
        'image'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function games()
    {
        return $this->hasMany('App\Models\Game');
    }

    public function gamesCount()
    {
        return $this->hasOne('App\Models\Game')
            ->selectRaw('platform_id, count(*) as aggregate')
            ->groupBy('platform_id')->withoutGlobalScopes();
    }

    public function digitals()
    {
        return $this->belongsToMany('App\Models\Digital');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |
    | Get URL
    |
    */
    public function getUrlAttribute()
    {
        return url('listings/' . str_slug($this->acronym));
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    /*
    |
    | Helper Class for count games
    |
    */
    public function getGamesCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (! array_key_exists('gamesCount', $this->relations)) {
            $this->load('gamesCount');
        }

        $related = $this->getRelation('gamesCount');

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }

    /*
    |
    | Get Listings count and cheapest listing for backend
    |
    */
    public function getGamesAdmin()
    {
        if ($this->getGamesCountAttribute() > 0) {
            return '<span class="label label-success">' . $this->getGamesCountAttribute() .'</span>';
        } else {
            return '<span class="label label-danger">0</span>';
        }
    }

    /*
|
| Get Console label for backend
|
*/
    public function getCover()
    {
        return '<span class="label" style="width:100%; border:1px solid lightgray; background-color: '. $this->fresh()->color . '; color:'.$this->fresh()->text_color.'">' . $this->fresh()->name .'</span>';
    }


    /*
    |
    | Set Cover Image to url when saved.
    |
    */
    public function setCoverImageAttribute($value)
    {
        if (is_null($value)) {
            return;
        }
        $destination_path = env('S3_DESTINATION')."platforms";
        $filename = $this->attributes['acronym'].'.png';
        $image = \Image::make($value)->encode('png', 90);
        \Storage::disk('s3')->put($destination_path.'/'.$filename, $image);
        $this->attributes["cover_image"] = env('S3_BUCKET_URL').$destination_path.'/'.$filename.'?lastmod='.time();
    }
}
