<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Backpack\CRUD\CrudTrait;
use ClickNow\Money\Money;
use Config;
use Mockery\Exception;
use RegionCode;

class Product extends Model
{
    use CrudTrait, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        static::creating(function (Product $game) {
            // if a uuid is not set on create, then make one
            if (!isset($game->uuid)) {
                $game->uuid = (string)Str::uuid();
            }
        });
        self::created(function($model) {
            foreach(regions() as $regionMain) {
                foreach(regions() as $regionSub) {
                    if ($regionMain != $regionSub && isset($model->attributes["cover_$regionMain"]) && !isset($model->attributes["cover_$regionSub"])) {
                        $model->attributes["cover_$regionSub"] = $model->attributes["cover_$regionMain"];
                    }
                }
            }

            $model->attributes['grouping_game_id'] = $model->attributes['id'];
            $model->attributes['rating_game_id'] = $model->attributes['id'];
            $model->save();
        });
        self::updated(function ($model) {
            $save = false;
            foreach(regions() as $regionMain) {
                foreach(regions() as $regionSub) {
                    if ($regionMain != $regionSub && isset($model->attributes["cover_$regionMain"]) && !isset($model->attributes["cover_$regionSub"])) {
                        $model->attributes["cover_$regionSub"] = $model->attributes["cover_$regionMain"];
                        $save = true;
                    }
                }
            }
            if ($save) {
                $model->save();
            }
        });
    }
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'games';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
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
        'cover_us_url',
        'cover_jp_url',
        'cover_eu_url',
        'type',
        'extra_images',
        'average_rating',
        'average_duration',
        'average_difficulty',
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
        'company',
        'model_number',
        'esrb',
        'components',
        'player_count',
        'multiplayer_online_no_limit',
        'cover_pa',
        'name_pa',
        'upc_pa',
        'catalog_number_pa',
        'cover_pa_url',
        'pa',
    ];
    protected $hidden = [];
    protected $dates = ['release_date','deleted_at'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get all of the game's comments.
     */
    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function company()
    {
        return $this->hasOne('App\Models\AccessoriesHardwareCompanies', 'id', 'id');
    }

    public function developers()
    {
        return $this->belongsToMany('App\Models\Developer', 'developer_game', 'game_id');
    }

    public function franchises()
    {
        return $this->belongsToMany('App\Models\Franchise', 'franchise_game', 'game_id');
    }

    public function genres()
    {
        return $this->belongsToMany('App\Models\Genre', 'game_genre', 'game_id')->orderBy('priority', 'asc')->orderBy('name', 'asc');
    }

    public function publishers()
    {
        return $this->belongsToMany('App\Models\Publisher', 'game_publisher', 'game_id')->withPivot('region');
    }

    public function publishersUs()
    {
        return $this->belongsToMany('App\Models\Publisher', 'game_publisher', 'game_id')->where('game_publisher.region', '=', 'us');
    }

    public function publishersJp()
    {
        return $this->belongsToMany('App\Models\Publisher', 'game_publisher', 'game_id')->where('game_publisher.region', '=', 'jp');;
    }

    public function publishersEU()
    {
        return $this->belongsToMany('App\Models\Publisher', 'game_publisher', 'game_id')->where('game_publisher.region', '=', 'eu');
    }

    public function publishersPA()
    {
        return $this->belongsToMany('App\Models\Publisher', 'game_publisher', 'game_id')->where('game_publisher.region', '=', 'pa');
    }

    public function platform()
    {
        return $this->belongsTo('App\Models\Platform');
    }

    // TODO: deprecate this relation
    public function genre()
    {
        return $this->belongsTo('App\Models\Genre');
    }

    public function giantbomb()
    {
        return $this->belongsTo('App\Models\Giantbomb');
    }

    public function metacritic()
    {
        return $this->hasOne('App\Models\Metacritic', 'game_id', 'id');
    }

    public function wishlist()
    {
        return $this->hasOne('App\Models\Wishlist', 'game_id', 'id')->where('user_id', \Auth::id());
    }

    public function havelist()
    {
        return $this->hasOne('App\Models\HaveList', 'game_id', 'id')->where('user_id', \Auth::id())->where('region', session()->get('region.code'));
    }

    public function collection()
    {
        return $this->hasMany('App\Models\HaveList', 'game_id', 'id')->where('user_id', \Auth::id());
    }

    public function completedlist()
    {
        return $this->hasOne('App\Models\CompletedList', 'game_id', 'id')->where('user_id', \Auth::id());
    }

    public function heartbeat()
    {
        return $this->hasMany('App\Models\Wishlist', 'game_id', 'id');
    }

    public function listings()
    {
        return $this->hasMany('App\Models\Listing', 'game_id', 'id')->orderBy('price')->where('status', null)->whereHas('user', function ($query) {$query->where('status',1);})->orWhere('status', 0)->whereHas('user', function ($query) {$query->where('status',1);});
    }

    public function listingsCount()
    {
        return $this->hasOne('App\Models\Listing', 'game_id', 'id')
            ->selectRaw('game_id, count(*) as aggregate')
            ->groupBy('game_id')->where('status', null)->whereHas('user', function ($query) {$query->where('status',1);})->orWhere('status', 0)->whereHas('user', function ($query) {$query->where('status',1);});
    }

    public function wishlistCount()
    {
        return $this->hasOne('App\Models\Wishlist', 'game_id', 'id')
            ->selectRaw('game_id, count(*) as aggregate')
            ->groupBy('game_id');
    }

    public function cheapestListing()
    {
        return $this->hasOne('App\Models\Listing', 'game_id', 'id')
            ->selectRaw('game_id, count(*) as aggregate')
            ->groupBy('game_id')->where('status', null)->whereHas('user', function ($query) {$query->where('status',1);})->orWhere('status', 0)->whereHas('user', function ($query) {$query->where('status',1);});
    }

    public function highestListing()
    {
        return $this->hasOne('App\Models\Listing', 'game_id', 'id')
            ->selectRaw('game_id, max(price) as aggregate')
            ->groupBy('game_id')->where('status', null)->where('sell', 1)->whereHas('user', function ($query) {$query->where('status',1);})->orWhere('status', 0)->where('sell', 1)->whereHas('user', function ($query) {$query->where('status',1);});
    }

    public function averagePrice()
    {
        return $this->hasOne('App\Models\Listing', 'game_id', 'id')
            ->selectRaw('game_id, avg(price) as aggregate')
            ->groupBy('game_id')->where('status', '>', '0')->where('sell', 1)->whereHas('user', function ($query) {$query->where('status',1);});
    }

    // This is a list of all games that can be trade for the game
    public function tradegames()
    {
        return $this->belongsToMany('App\Models\Listing', 'game_trade')->withPivot('listing_game_id', 'price', 'price_type')->with('game')->withTrashed();;
    }

    public function altGroup()
    {
        return $this->hasMany("App\Models\Product", 'grouping_game_id', "grouping_game_id")
            ->select('games.*', 'platforms.name as platform_name', \DB::raw('CONCAT(games.name, " <", platforms.name, ">") AS name_and_platform'))
            ->join('platforms', 'games.platform_id', '=','platforms.id')
            ->whereNotNull('games.'.session('region.code'));
    }

    public function altGroupRegionless()
    {
        return $this->hasMany("App\Models\Product", 'grouping_game_id', "grouping_game_id")
            ->select('games.*', 'platforms.name as platform_name', \DB::raw('CONCAT(games.name, " <", platforms.name, ">") AS name_and_platform'))
            ->join('platforms', 'games.platform_id', '=','platforms.id');
    }

    public function ratingsGroup()
    {
        return $this->hasMany("App\Models\Product", 'rating_game_id', "rating_game_id")
            ->select('games.*', 'platforms.name as platform_name', \DB::raw('CONCAT(games.name, " <", platforms.name, ">") AS name_and_platform'))
            ->join('platforms', 'games.platform_id', '=','platforms.id');
    }

    public function customLists()
    {
        return $this->hasManyThrough('App\Models\CustomList', 'App\Models\CustomListItem', 'game_id', 'id', 'id', 'custom_list_id');
    }

    public function publishersName($region)
    {
        $publishers = $this->publishers()->get();
        $names = [];
        foreach ($publishers as $publisher) {
            if ($publisher->pivot->region == $region) {
                $names[] = $publisher->name;
            }
        }
        return $names;
    }

    public function ratings()
    {
        if (auth()->check()) {
            return $this->hasMany("App\Models\Rating", 'game_id', "rating_game_id")
                ->select('game_rating.*', 'friends.status')
                ->leftJoin("friends", function($join) {
                    $join->on('game_rating.user_id', '=', 'friends.friend_id')
                        ->where('friends.user_id', '=', auth()->id())
                        ->where('status', '=', 'friend');
                })
                ->orderBy('friends.status', 'desc')
                ->orderBy("created_at", "desc");

        }
        return $this->hasMany("App\Models\Rating", 'game_id', "rating_game_id")->orderBy("created_at", "desc");
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
    public function getTrueNameAttribute()
    {
        return $this->attributes['name'];
    }

    public function getAverageDurationAttribute()
    {
        if (is_null($this->attributes['average_duration'])) {
            return null;
        }
        return (object) [
            'formatted' => floor($this->attributes['average_duration'] / 60).":".date('i', mktime(0, $this->attributes['average_duration'])),
            'hours' => floor($this->attributes['average_duration'] / 60),
            'minutes' => $this->attributes['average_duration'] % 60,
            'unformatted' => $this->attributes['average_duration'],
        ];
    }

    public function getNameAttribute()
    {
        $regionName = $this->attributes['name_'.session('region.abbr')];
        if (isset($regionName)) {
            return $regionName;
        }
        return $this->attributes['name'];
    }

    public function getOtherPlatformsAttribute()
    {
        return json_decode($this->attributes['other_platforms']);
    }

    public function getAltGroupAttribute()
    {
        $altProducts = $this->altGroup()->get();
        foreach ($altProducts as $key => $product) {
            if ($product->id == $this->attributes['id']) {
                unset($altProducts[$key]);
            }
        }
        return $altProducts;
    }

    public function getAltGroupRegionlessAttribute()
    {
        $altProducts = $this->altGroup()->get();
        foreach ($altProducts as $key => $product) {
            if ($product->id == $this->attributes['id']) {
                unset($altProducts[$key]);
            }
        }
        return $altProducts;
    }

    public function getRatingsGroupAttribute()
    {
        $ratingsGroup = $this->ratingsGroup()->get();
        foreach ($ratingsGroup as $key => $product) {
            if ($product->id == $this->attributes['id']) {
                unset($ratingsGroup[$key]);
            }
        }
        return $ratingsGroup;
    }

    public function getCoverUsUrlAttribute()
    {
        return $this->attributes['cover_us'];
    }


    public function getCoverJpUrlAttribute()
    {
        return $this->attributes['cover_jp'];
    }


    public function getCoverEuUrlAttribute()
    {
        return $this->attributes['cover_eu'];
    }

    public function getCoverPaUrlAttribute()
    {
        return $this->attributes['cover_pa'];
    }

    public function getExtraImagesAttribute()
    {
        return json_decode($this->attributes['extra_images'], true);
    }

    public function setTrueNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    // convert a url string to be an image in s3
    public function convertUrl($game, $url, $region)
    {
        if (!in_array($region, regions())) {
            throw new \Exception('Invalid Region');
        }
        $destination_path = env('S3_DESTINATION')."games/$region";
        // skip if url is empty
        if (empty($url)) {
            return;
        }
        $file = file_get_contents($url);
        $image = \Image::make($file)->encode('jpg', 90);
        if (!isset($game->attributes['uuid'])) {
            $game->attributes['uuid'] = (string) Str::uuid();
        }
        $filename = $game->attributes['uuid'].'.jpg';
        \Storage::disk('s3')->put($destination_path.'/'.$filename, $image->stream());
        $game->attributes["cover_$region"] = env('S3_BUCKET_URL').$destination_path.'/'.$filename.'?lastmod='.time();
    }

    public function setCoverUsUrlAttribute($url)
    {
        if (is_null($url) || isset($this->original['cover_us']) && $this->original['cover_us'] == $url) {
            return;
        }
        $this->convertUrl($this, $url, 'us');
    }

    public function setCoverJpUrlAttribute($url)
    {
        if (is_null($url) || isset($this->original['cover_jp']) && $this->original['cover_jp'] == $url) {
            return;
        }
        $this->convertUrl($this, $url, 'jp');
    }

    public function setCoverEuUrlAttribute($url)
    {
        if (is_null($url) || isset($this->original['cover_eu']) && $this->original['cover_eu'] == $url) {
            return;
        }
        $this->convertUrl($this, $url, 'eu');
    }

    public function setCoverPaUrlAttribute($url)
    {
        if (is_null($url) || isset($this->original['cover_pa']) && $this->original['cover_pa'] == $url) {
            return;
        }
        $this->convertUrl($this, $url, 'pa');
    }

    public function setExtraImagesAttribute($url_list)
    {
        if (!is_array($url_list))
            return;

        // This workaround can be removed when Laravel >= 5.8 & Backpack >= 3.6
        $this->attributes['extra_images'] = ($url_list === [0]) ? '' : json_encode($url_list);
        // replace with: $this->attributes['extra_images'] = json_encode($url_list);
        // see: https://github.com/Laravel-Backpack/CRUD/issues/2397#issuecomment-577786222
    }

    // Convert an uploaded image to upload to s3
    public function convertImage($game, $region, $value)
    {
        if (!in_array($region, regions())) {
            throw new \Exception('Invalid Region');
        }
        $attribute_name = "cover_$region";
        $disk = "s3";
        $destination_path = env('S3_DESTINATION')."games/$region";
        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($game->{$attribute_name});

            // set null in the database column
            $game->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);
            // 1. Generate a filename.
            if (!isset($game->attributes['uuid'])) {
                $game->attributes['uuid'] = (string) Str::uuid();
            }
            $filename = $game->attributes['uuid'].'.jpg';
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());
            // 3. Save the path to the database
            $game->attributes[$attribute_name] = env('S3_BUCKET_URL').$destination_path.'/'.$filename.'?lastmod='.time();
        }
    }

    public function setCoverUsAttribute($value)
    {
        if (is_null($value)) {
            return;
        }
        return $this->convertImage($this, 'us', $value);
    }

    public function setCoverJpAttribute($value)
    {
        if (is_null($value)) {
            return;
        }
        return $this->convertImage($this, 'jp', $value);
    }

    public function setCoverEuAttribute($value)
    {
        if (is_null($value)) {
            return;
        }
        return $this->convertImage($this, 'eu', $value);
    }

    public function setCoverPaAttribute($value)
    {
        if (is_null($value)) {
            return;
        }
        return $this->convertImage($this, 'pa', $value);
    }

    /*
    |
    | Save cover to database
    |
    */
    public function setCoverAttribute($value)
    {
        $attribute_name = "cover";
        $disk = "local";
        $destination_path = "public/games";

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value);
            // 1. Generate a filename.
            $filename = time().'-'.$this->id.'.jpg';
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

            // Delete old image
            if (!is_null($this->getAttribute('cover'))) {
                \Storage::disk($disk)->delete('/public/games/' . $this->getAttribute('cover'));
            }

            // 3. Save the path to the database
            $this->attributes[$attribute_name] = $filename;
            // if string was sent
        } else {
            $this->attributes[$attribute_name] = $value;
        }
    }

    /*
    |
    | Helper Class for count listings
    |
    */
    public function getListingsCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (! array_key_exists('listingsCount', $this->relations)) {
            $this->load('listingsCount');
        }

        $related = $this->getRelation('listingsCount');

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }

    /*
    |
    | Helper Class for count wishlist
    |
    */
    public function getWishlistCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (! array_key_exists('wishlistCount', $this->relations)) {
            $this->load('wishlistCount');
        }

        $related = $this->getRelation('wishlistCount');

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }

    /*
    |
    | Helper Class for cheapest listings
    |
    */
    public function getCheapestListingAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (! array_key_exists('cheapestListing', $this->relations)) {
            $this->load('cheapestListing');
        }

        $related = $this->getRelation('cheapestListing');

        // format cheapest price
        if ($related) {
            $cheapest_price = money($related->aggregate, Config::get('settings.currency'))->format();
        };

        // then return the price directly
        return ($related) ?  $cheapest_price : 0;
    }

    /*
    |
    | Helper Class for averagePrice
    |
    */
    public function getAveragePrice($currency = true)
    {
        // if relation is not loaded already, let's do it first
        if (! array_key_exists('averagePrice', $this->relations)) {
            $this->load('averagePrice');
        }

        $related = $this->getRelation('averagePrice');

        // then return the count directly
        return ($related) ? money($related->aggregate / 1, Config::get('settings.currency'))->format($currency, Config::get('settings.decimal_place')) : 0;
    }

    /*
    |
    | Helper Class for lowest price
    |
    */
    public function getLowestPriceAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (! array_key_exists('cheapestListing', $this->relations)) {
            $this->load('cheapestListing');
        }

        $related = $this->getRelation('cheapestListing');

        // then return the price directly
        return ($related) ? number_format($related->aggregate / currency(Config::get('settings.currency'))->getSubunit(), 2, '.', '') : 0;
    }

    /*
    |
    | Helper Class for highest price
    |
    */
    public function getHighestPriceAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (! array_key_exists('highestListing', $this->relations)) {
            $this->load('highestListing');
        }

        $related = $this->getRelation('highestListing');

        // then return the price directly
        return ($related) ? number_format($related->aggregate / currency(Config::get('settings.currency'))->getSubunit(), 2, '.', '') : 0;
    }

    /*
    |
    | Get Cover Image
    |
    */
    public function getImageCoverAttribute()
    {
        $cover = 'cover_'.session('region.abbr');
        if (!is_null($this->$cover)) {
            return $this->$cover;
        } else {
            return null;
        }
    }

    /*
    |
    | Get Carousel Image
    |
    */
    public function getImageCarouselAttribute()
    {
        $cover = 'cover_'.session('region.abbr');
        if (!is_null($this->$cover)) {
            return asset('images/carousel/' . $this->$cover);
        } else {
            return asset('images/carousel/no_cover.jpg');
        }
    }

    /*
    |
    | Get Square (Tiny) Image
    |
    */
    public function getImageSquareTinyAttribute()
    {
        $cover = 'cover_'.session('region.abbr');
        if (!is_null($this->$cover)) {
            return $this->$cover;
        } else {
            return asset('images/square_tiny/no_cover.jpg');
        }
    }

    /*
    |
    | Get Square Image
    |
    */
    public function getImageSquareAttribute()
    {
        $cover = 'cover_'.session('region.abbr');
        if (!is_null($this->$cover)) {
            return 'https://images.igdb.com/igdb/image/upload/t_cover_small/'.$this->$cover;
        } else {
            return asset('images/square/no_cover.jpg');
        }
    }

    /*
    |
    | Get URL
    |
    */
    public function getUrlSlugAttribute()
    {
        if ($this->type === "game") {
            return url('games/' . str_slug($this->name) . '-' . $this->platform->acronym . '-' . $this->id);
        } else {
            return url('hardware/' . str_slug($this->name) . '-' . $this->platform->acronym . '-' . $this->id);
        }
    }


    public function getComponentsAttribute()
    {
        if (isset($this->attributes['components'])) {
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
    | Get Image for backend
    |
    */
    public function getImageAdmin()
    {
        if (!is_null($this->fresh()->cover)) {
            return "<img src='" . asset('uploads/game/square_tiny/' . $this->fresh()->cover)  . "' height='50' />";
        } elseif (!is_null($this->fresh()->giantbomb_id)) {
            return '<img src="http://www.giantbomb.com/api/image/square_avatar/' . $this->fresh()->giantbomb->image . '" />';
        } else {
            return "<img src='" . asset('uploads/game/square_tiny/no_cover.jpg') . "' height='50' />";
        }
    }

    /*
    |
    | Get Console label for backend
    |
    */
    public function getConsoleAdmin()
    {
        return '<span class="label" style="width:100%; border:1px solid lightgray; background-color: '. $this->fresh()->platform->color . '; color:'.$this->fresh()->platform->text_color.'">' . $this->fresh()->platform->name .'</span>';
    }

    /*
    |
    | Get Name with cover and release year for backend
    |
    */
    public function getNameAdmin()
    {
        return '<div class="user-block">
					<img src="' . $this->fresh()->getImageSquareTinyAttribute() . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->getUrlSlugAttribute() .'" target="_blank">' . $this->fresh()->true_name . '</a></span>
					<span class="description">' . ($this->fresh()->release_date ? '<i class="fa fa-calendar"></i> ' . $this->fresh()->release_date->format('Y') . '&nbsp;/&nbsp;' : '') . 'ID: <strong>' . $this->fresh()->id . '</strong></span>
				</div>';
    }

    /*
    |
    | Get Listings count and cheapest listing for backend
    |
    */
    public function getListingsAdmin()
    {
        if ($this->fresh()->getListingsCountAttribute() > 0) {
            if ($this->getCheapestListingAttribute() == '0') {
                return '<div class="block"><span class="label label-success">' . $this->fresh()->getListingsCountAttribute() .'</span></div> <span class="text-muted text-xs"><i class="fa fa-exchange"></i> Trade only</span>';
            } else {
                return '<div class="block"><span class="label label-success">' . $this->fresh()->getListingsCountAttribute() .'</span></div> <span class="text-muted text-xs"><i class="fa fa-shopping-basket"></i> starting from ' . $this->fresh()->getCheapestListingAttribute() . '</span>';
            }
        } else {
            return '<span class="label label-danger">' . $this->fresh()->getListingsCountAttribute() .'</span>';
        }
    }

    /*
    |
    | Get Console label for backend
    |
    */
    public function getPlatformAdmin()
    {
        return '<span class="label" style="border:1px solid lightgray; background-color: '. $this->fresh()->platform->color . '; color:'.$this->fresh()->platform->text_color.'">' . $this->fresh()->platform->name .'</span>';
    }

    public function esrbUrl()
    {
        if ($this->esrb) {
            return env("S3_BUCKET_URL").'ratings/'.$this->esrb.'.png';
        }
        return false;
    }

    public function pegiUrl()
    {
        if ($this->pegi) {
            return env("S3_BUCKET_URL").'ratings/'.$this->pegi.'.png';
        }
        return false;
    }

    // save function for games also available on other platforms
    public static function saveAltGroup($primary_id, $altIds)
    {
        // Get the product with the original group_game_id
        $product = Product::find($primary_id);
        // Reset the group to only be in their group for any that was removed
        Product::where('grouping_game_id', $product->grouping_game_id)->update(['grouping_game_id' => \DB::raw('id')]);
        if (isset($altIds)) {
            // Update the groups grouping_game_id to be the current product id
            Product::whereIn('id', $altIds)->update(['grouping_game_id' => $product->id]);
        }
    }

    // save function for games also available on other platforms
    public static function saveRatingsGroup($primary_id, $ratingsGameIds)
    {
        // Get the product with the original rating_game_id
        $product = Product::with('ratingsGroup')->find($primary_id);
        $productRatingsToUpdate = $ratingsGameIds;
        $productRatingsToUpdate[] = $product->id;
        $oldRatingsGroup = $product->ratingsGroup();
        foreach ($oldRatingsGroup as $productRating) {
            if (!in_array($productRating->id, $ratingsGameIds)) {
                $productRatingsToUpdate[] = $productRating->id;
            }
        }
        // Reset the rating group to only be in their group for any that was removed
        Product::where('rating_game_id', $product->rating_game_id)->update(['rating_game_id' => \DB::raw('id')]);
        if (isset($ratingsGameIds)) {
            // Update the groups rating_group_id to be the current product id
            Product::whereIn('id', $ratingsGameIds)->update(['rating_game_id' => $product->id]);
        }
        // Update Ratings to be correct within their ratings group
        $products = Product::whereIn('id', $productRatingsToUpdate)->get();
        foreach ($products as $product) {
            $rating = Rating::selectRaw("AVG(rating) as average_rating")->join('games', 'games.id', '=', 'game_rating.game_id')->where('games.rating_game_id', $product->rating_game_id)->groupBy('games.rating_game_id')->first();
            if (!isset($rating->average_rating)) {
                $average_rating = null;
            } else {
                $average_rating = $rating->average_rating;
            }
            Product::where('rating_game_id', $product->rating_game_id)->update(['average_rating' => $average_rating]);
        }
    }
}
