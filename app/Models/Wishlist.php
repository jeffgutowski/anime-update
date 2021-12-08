<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Config;
use ClickNow\Money\Money;
use ClickNow\Money\Currency;

class Wishlist extends Model
{
    use CrudTrait;
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;

     /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'game_wishlists';
    protected $primaryKey = 'id';
    // protected $appends = [];
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['game_id','user_id'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |
    | Method to get delivery price with or without symbol
    |
    */
    public function getMaxPrice($currency = true)
    {
        return money($this->max_price, Config::get('settings.currency'))->format($currency, Config::get('settings.decimal_place'));
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function game()
    {
        return $this->belongsTo('App\Models\Product', 'game_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'game_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function listings()
    {
        return $this->hasMany('App\Models\Listing', 'game_id', 'game_id')
            ->where('status', null)
            ->whereHas('user', function ($query) {$query->where('status',1);})
            ->orWhere('status', 0)
            ->whereHas('user', function ($query) {$query->where('status',1);})
            ->orderBy('price', 'ASC')
            ->limit(5);
    }


    public function genres()
    {
        return $this->belongsToMany('App\Models\Genre', 'game_genre', 'game_id', 'genre_id', 'game_id');
    }

    public function company()
    {
        return $this->hasOne('App\Models\AccessoriesHardwareCompanies', 'id', 'id');
    }

    public function developers()
    {
        return $this->belongsToMany('App\Models\Developer', 'developer_game', 'game_id', 'developer_id', 'game_id');
    }

    public function publishers()
    {
        return $this->belongsToMany('App\Models\Publisher', 'game_publisher', 'game_id', 'publisher_id', 'game_id');
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
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
