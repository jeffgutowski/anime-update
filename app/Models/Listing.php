<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\CrudTrait;
use ClickNow\Money\Money;
use ClickNow\Money\Currency;
use Config;
use App\Traits\Geographical;
use Gloudemans\Shoppingcart\Contracts\Buyable;

class Listing extends Model implements Buyable
{
    use CrudTrait, SoftDeletes, Geographical;
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'listings';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'game_id',
        'name',
        'picture',
        'description',
        'price',
        'condition',
        'limited_edition',
        'delivery',
        'delivery_price',
        'pickup',
        'sell',
        'trade',
        'trade_list',
        'status',
        'clicks',
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
        'components',
        'region'
    ];
    // protected $hidden = [];
    protected $dates = ['deleted_at'];
    protected $appends = ['url_slug'];

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

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'game_id', 'id')->withTrashed();
    }

    public function game()
    {
        return $this->belongsTo('App\Models\Game')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function offers()
    {
        return $this->hasMany('App\Models\Offer')->orderBy('created_at', 'desc');
    }

    // This is a list of all games that can be trade for the game
    public function tradegames()
    {
        return $this->belongsToMany('App\Models\Game', 'game_trade')->withPivot('listing_game_id', 'price', 'price_type');
    }

    public function images()
    {
        return $this->hasMany('App\Models\ListingImage')->orderBy('order');
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

    /*
    |
    | Format Condition to string. 5 Condition Levels - 1 is worst -> 5 is best.
    | You can change the names of the coditions in your listings lang file.
    |
    */
    public function getConditionStringAttribute()
    {
        switch ($this->condition) {
            case 0:
                return trans('listings.general.conditions.0');
            case 1:
                return trans('listings.general.conditions.1');
            case 2:
                return trans('listings.general.conditions.2');
            case 3:
                return trans('listings.general.conditions.3');
            case 4:
                return trans('listings.general.conditions.4');
            case 5:
                return trans('listings.general.conditions.5');
            case null:
                return trans('listings.general.digital_download');
            default:
                return 'Invalid Condition';
        }
    }

    /*
    |
    | Format price attribute to given currency from settings. (without symbol)
    |
    */
    public function getPriceFormattedAttribute()
    {
        return money($this->price, Config::get('settings.currency'))->format(true, Config::get('settings.decimal_place'));
    }

    /*
    |
    | Get price in decimal format
    |
    */
    public function getPriceDecimalAttribute()
    {
        return number_format($this->price / currency(Config::get('settings.currency'))->getSubunit(), 2, '.', '');
    }

    /*
    |
    | Method to get price with or without symbol
    |
    */
    public function getPrice($currency = true)
    {
        return money($this->price, Config::get('settings.currency'))->format($currency, Config::get('settings.decimal_place'));
    }

    /*
    |
    | Convert formatted price attribute to int before saving to database.
    |
    */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = abs(filter_var($value, FILTER_SANITIZE_NUMBER_INT));
    }

    /*
    |
    | Convert formatted price attribute to int before saving to database.
    |
    */
    public function setDeliveryPriceAttribute($value)
    {
        $this->attributes['delivery_price'] = abs(filter_var($value, FILTER_SANITIZE_NUMBER_INT));
    }

    /*
    |
    | Format delivery price attribute to given currency from settings.
    |
    */
    public function getDeliveryPriceFormattedAttribute()
    {
        return money($this->delivery_price, Config::get('settings.currency'))->format(true, Config::get('settings.decimal_place'));
    }

    /*
    |
    | Method to get delivery price with or without symbol
    |
    */
    public function getDeliveryPrice($currency = true)
    {
        return money($this->delivery_price, Config::get('settings.currency'))->format($currency, Config::get('settings.decimal_place'));
    }

    /*
    |
    | Get URL
    |
    */
    public function getUrlSlugAttribute()
    {
        return url('listings/' . str_slug($this->product->name) . '-' . $this->product->platform->acronym . '-' . strtolower($this->user->name) . '-' . $this->id);
    }

    /*
    |
    | Get Original Picture
    |
    */
    public function getPictureOriginalAttribute()
    {
        if (!is_null($this->picture)) {
            return asset('images/picture/' . $this->picture);
        } else {
            return null;
        }
    }

    /*
    |
    | Get Original Picture
    |
    */
    public function getPictureSquareAttribute()
    {
        if (!is_null($this->picture)) {
            return asset('images/avatar_square/' . $this->picture);
        } else {
            return null;
        }
    }

    /*
    |
    | Get distance to listing
    |
    */
    public function getDistanceAttribute()
    {
        // get location points (lat / long)
        if ($this->user->location->latitude && $this->user->location->longitude) {
            $latitudeFrom = $this->user->location->latitude;
            $longitudeFrom = $this->user->location->longitude;
        } else {
            return false;
        }

        if (\Auth::check() && (\Auth::user()->location && \Auth::user()->location->longitude && \Auth::user()->location->latitude)) {
            if (\Auth::user()->id == $this->user->id) {
                return false;
            }
            $latitudeTo = \Auth::user()->location->latitude;
            $longitudeTo = \Auth::user()->location->longitude;
        } elseif (session()->has('latitude') && session()->has('longitude')) {
            $latitudeTo = session()->get('latitude');
            $longitudeTo = session()->get('longitude');
        } else {
            return false;
        }



        // calculate distance
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        // return distance in unit set in the admin panel
        switch (config('settings.distance_unit')) {
            case 'km':
                return round($miles * 1.609344);
            case 'mi':
                return round($miles);
            case 'nm':
                return round($miles * 0.8684);
            default:
                return round($miles);
        }

    }

    public function getComponentsAttribute()
    {
        if ($this->attributes['components']) {
            return json_decode($this->attributes['components']);
        }
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |
    | Get user for backend
    |
    */
    public function getUserAdmin()
    {
        if ($this->fresh()->user->isOnline()) {
            return '<div class="user-block">
					<img class="img-circle" src="' . $this->fresh()->user->avatar_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->user->url .'" target="_blank">' . $this->fresh()->user->name . '</a></span>
					<span class="description"><i class="fa fa-circle text-success"></i> Online</span>
				</div>';
        } else {
            return '<div class="user-block">
						<img class="img-circle" src="' . $this->fresh()->user->avatar_square_tiny . '" alt="User Image">
						<span class="username"><a href="' . $this->fresh()->user->url .'" target="_blank">' . $this->fresh()->user->name . '</a></span>
						<span class="description"><i class="fa fa-circle text-danger"></i> Offline</span>
					</div>';
        }
    }

    /*
    |
    | Get Name with cover and release year for backend
    |
    */
    public function getGameAdmin()
    {
        return '<div class="user-block">
					<img class="img-circle" src="' . $this->fresh()->product->image_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->url_slug .'" target="_blank">' . $this->fresh()->product->name . '</a></span>
					<span class="description"><span class="label" style="background-color: '. $this->fresh()->product->platform->color . '; margin-right: 10px;">' . $this->fresh()->product->platform->name .'</span><i class="fa fa-calendar"></i> ' . $this->fresh()->product->release_date->format('Y') . '</span>
				</div>';
    }

    /*
    |
    | Get html formatted status in admin panel
    |
    */
    public function getStatusAdmin()
    {
        switch ($this->fresh()->status) {
            case 0:
                return '<span class="label label-success">Active</span>';
            case 1:
                return '<span class="label label-primary">Sold</span>';
            case 2:
                return '<span class="label label-default">Complete</span>';
        }
    }

    /*
    |
    | Get Name with cover and release year for backend
    |
    */
    public function getPriceAdmin()
    {
        if ($this->fresh()->sell) {
            return '<h4 style="margin: 0px !important;"><span class="label label-success">' . $this->fresh()->getPriceFormattedAttribute() .'</span></h4>';
        } else {
            return '<h4 style="margin: 0px !important;"><span class="label label-danger"><i class="fa fa-shopping-basket"></i></span></h4>';
        }
    }

    /*
    |
    | Get Name with cover and release year for backend
    |
    */
    public function getTradeAdmin()
    {
        if ($this->fresh()->trade == 1) {
            return '<h4 style="margin: 0px !important;"><span class="label label-success"><i class="fa fa-exchange"></i></span></h4>';
        } else {
            return '<h4 style="margin: 0px !important;"><span class="label label-danger"><i class="fa fa-exchange"></i></span></h4>';
        }
    }

    /*
    |
    | Get formatted creation date for backend
    |
    */
    public function getDateAdmin()
    {
        return '<strong>' . $this->fresh()->created_at->format(Config::get('settings.date_format')) . '</strong><br>' . $this->fresh()->created_at->format(Config::get('settings.time_format'));
    }

    /**
     * Get the identifier of the Buyable item.
     *
     * @return int|string
     */
    public function getBuyableIdentifier($options = null)
    {
        return $this->id;
    }

    /**
     * Get the description or title of the Buyable item.
     *
     * @return string
     */
    public function getBuyableDescription($options = null)
    {
        return $this->description;
    }

    /**
     * Get the price of the Buyable item.
     *
     * @return float
     */
    public function getBuyablePrice($options = null)
    {
        return $this->price;
    }
}
