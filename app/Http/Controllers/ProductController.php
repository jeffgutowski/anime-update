<?php
namespace App\Http\Controllers;

use App\Models\CustomList;
use App\Repositories\FilterRepository\FiltersRepository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;
use ClickNow\Money\Money;
use App\Models\Developer;
use App\Models\Game;
use App\Models\Product;
use App\Models\Publisher;
use App\Models\AccessoriesHardware as Hardware;
use App\Models\AccessoriesHardwareType as ProductType;
use App\Models\AccessoriesHardwareCompanies as Company;
use App\Models\Giantbomb;
use App\Models\Platform;
use App\Models\Genre;
use App\Services\IgdbService;
use App\Services\PlatformService;
use GuzzleHttp\Client;
use Searchy;
use Redirect;
use Request;
use Config;
use SEO;
use Session;
use Theme;
use Cache;
use App\Traits\ParameterRouteCache;

class ProductController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ParameterRouteCache;

    /**
     * Index all games
     *
     * @return Response
     */
    public function index()
    {
        $class = self::getRouteClass();
        $products = $class::query();
        $products->select("*");
        $filterRepo = new \App\Repositories\FiltersRepository;
        $cache = Cache::remember($this->cachekey, config('cache.md'), function() use ($filterRepo, $products) {
            $products = $filterRepo->filter($products, request()->only($filterRepo->filtersList), 'product');
            return (object) [
                'products' => $products,
                'filterRepo' => $filterRepo,
                'paginationLinks' => $this->paginationLinks($products),
            ];
        });
        if ($filterRepo->pageErrorRedirection) {
            return redirect()->to($filterRepo->pageErrorRedirection);
        }

        // Page title
        SEO::setTitle(trans('general.title.games_all', ['page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title')]));

        // Page description
        SEO::setDescription(trans('general.description.games_all', ['games_count' => $cache->products->total(), 'page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title')]));

        $response = [
            'games' => $cache->products,
            'filters' => $cache->filterRepo,
            'genres' => Cache::remember('genres', config('cache.timeout.lg'), function() { return Genre::orderBy('name', 'asc')->get(); }),
            'platforms' => Cache::remember('platforms:'.session('region.code'), config('cache.timeout.lg'), function() { return Platform::orderBy('name', 'asc')->where(session('region.code'), true)->get(); }),
            'product_types' => Cache::remember('product_types', config('cache.timeout.lg'), function() { return ProductType::get(); }),
            'pagination_links' => $cache->paginationLinks,
            'pagination_obj' => $cache->products,
        ];
        // Check if ajax request
        if (Request::ajax()) {
            return view('frontend.game.ajax.index', $response);
        } else {
            return view('frontend.game.index', $response);
        }
    }

    public function platform($platform = null)
    {
        if (!is_null($platform) && $platform !== 'all') {
            $platform = Platform::where('acronym', $platform)->first();
            if (isset($platform->id)) {
                session()->put('listingsPlatformFilter', [$platform->id]);
                return redirect('/games');
            }
        } elseif (!is_null($platform) && $platform === 'all') {
            session()->forget('listingsPlatformFilter');
            return redirect('/games');
        }
        return abort(404);
    }

    /**
     * Display game infos with all listing
     *
     * @param  string   $slug
     * @return Response
     */
    public function show($slug)
    {
        $class = self::getRouteClass();

        // Get game id from slug string
        $game_id = ltrim(strrchr($slug,'-'),'-');
        $game = $class::with('listings', 'altGroup', 'wishlist', 'havelist', 'completedlist', 'ratings.user')->find($game_id);

        // Check if game exists
        if (is_null($game)) {
            return abort('404');
        }

        // Check if slug is right
        $slug_check = str_slug($game->name) . '-' . $game->platform->acronym . '-' . $game->id;

        // Redirect to correct slug link
        if ($slug_check != $slug) {
            if ($game->type === 'game') {
                return Redirect::to(url('games/' . $slug_check));
            } else {
                return Redirect::to(url('hardware/' . $slug_check));
            }
        }

        // Page title & description
        SEO::setTitle(trans('general.title.game', ['game_name' => $game->name, 'platform' => $game->platform->name,'page_name' => config('settings.page_name')]));
        SEO::setDescription( (strlen($game->description) > 147) ? substr($game->description, 0, 147) . '...' : $game->description );

        // Get different platforms for the game
        $other_platforms = [];
        if (explode('\\', $class)[2] == 'AccessoriesHardware') {
            $others = $game->other_platforms ?: [];
            $other_platforms = Platform::whereIn('id', $others)->get();
        }

        // Get image size for og
        if ($game->image_cover) {
          // Check if image is corrupted
          try {
              $imgsize = getimagesize($game->image_cover);
              SEO::opengraph()->addImage(['url' => $game->image_cover, ['height' => $imgsize[1], 'width' => $imgsize[0]]]);
              // Twitter Card Image
              SEO::twitter()->setImage($game->image_cover);
          } catch(\Exception $e) {
              // Delete corrupted image
              // $disk = "local";
              // \Storage::disk($disk)->delete('/public/games/' . $game->cover );
              // $game->cover = null;
              // $game->save();
          }
        }

        $extra_images = [];
        if ($game->extra_images) {
            $prefix = "https://".env("AWS_BUCKET").".s3.".env("AWS_REGION").".amazonaws.com";
            foreach ($game->extra_images as $image) {
                $extra_images[] = str_replace('S3BUCKET', $prefix, $image);
            }
        }

        if (auth()->user()) {
            $user_rating = \DB::table('game_rating')->where("user_id", auth()->user()->id)->where("game_id", $game->id)->first();
        } else {
            $user_rating = null;
        }

        $customLists = CustomList::whereHas('items', function ($query) use ($game) {
            $query->where('game_id', $game->id);
        })
        ->orderBy('clicks', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(12);

        return view('frontend.game.show', [
            'game'                => $game,
            'different_platforms' => $game->altGroup,
            'other_platforms'     => $other_platforms,
            'extra_images'        => $extra_images,
            'user_rating'         => $user_rating,
            'custom_lists'        => $customLists
        ]);
    }

    /**
     * Get media (images & videos) tab in game and listing overview
     *
     * @param  int  $id
     * @return Response
     */
    public function showMedia($id)
    {
        $class = self::getRouteClass();
        $game = $class::with('giantbomb')->find($id);

        // Accept only ajax requests
        if (!Request::ajax()) {
            // redirect to game if no AJAX request
            if ($game) {
                return Redirect::to(url($game->url_slug . '#!media'));
            } else {
                return abort('404');
            }
        }

        // Check if game exist
        if (!$game) {
            return abort('404');
        }

        // Get images from giantbomb
        if ($game->giantbomb_id != 0) {
            $images = json_decode($game->giantbomb->images);
            $videos = json_decode($game->giantbomb->videos);
        } else {
            $images = NULL;
            $videos = NULL;
        }

        // don't loose backUrl session if one is set
        if (Session::has('backUrl')) {
            Session::keep('backUrl');
        }

        return view('frontend.game.showMedia', ['game' => $game,'images' =>$images,'videos' =>$videos]);
    }

    /**
     * Get available trade games for the specific game in the tab in game overview
     *
     * @param  int  $id
     * @return Response
     */
    public function showTrade($id)
    {
        $class = self::getRouteClass();

        $game = $class::find($id);

        // Accept only ajax requests
        if (!Request::ajax()) {
            // redirect to game if no AJAX request
            if ($game) {
                return Redirect::to(url($game->url_slug . '#!trade'));
            } else {
                return abort('404');
            }
        }

        // Check if game exist
        if (!$game) {
            return abort('404');
        }

        // help to check if trade games was removed in the next step
        $removed_games = false;

        // Remove not active listings
        foreach ($game->tradegames as $listing) {
            // check if listing is removed or not active
            if ($listing->status == 1 || $listing->status == 2 || $listing->deleted_at) {
                \DB::table('game_trade')->where('listing_id', $listing->id)->where('game_id', $game->id)->delete();
                $removed_games = true;
            }
        }

        if ($removed_games) {
            // Refresh game model
            $game = $game->fresh();
        }

        return view('frontend.game.showTrade', ['tradegames' => $game->tradegames]);
    }

    /**
     * Form for adding a new game
     *
     * @return Response
     */
    public function add()
    {

        // Check if user can add games to the system
        if (!Config::get('settings.user_add_item') && !(\Auth::user()->can('edit_games'))) {
            return abort(404);
        }

        // Page title
        SEO::setTitle(trans('general.title.game_add', ['page_name' => config('settings.page_name')]));

        return view('frontend.game.add', ['platforms' => Platform::all()]);
    }

    /**
     * Search games
     *
     * @param  int  $id
     * @return Response
     */
    public function search($value)
    {

        // get all inpus
        $input = Input::all();

        // Number of items per page
        $perPage = 36;

        $words = explode(' ', $value);
        foreach ($words as &$word) {
            if (strlen($word) > 3) {
                $word = '+'.$word;
            }
        }
        $string = implode(' ', $words);
        // search for games
        $products = Product::selectRaw("*, MATCH (name, name_us, name_jp, name_eu) AGAINST ('$string') AS `rank`")
            ->whereRaw("MATCH (name, name_us, name_jp, name_eu) AGAINST ('$string')")
            ->whereNotNull(session('region.code'))
            ->orderBy('rank', 'desc')->limit(180)->get()->toArray();
        $games = Product::hydrate($products);
        $games->load('platform');

        // Get the current page from the url if it's not set default to 1
        $page = Input::get('page', 1);

        // Number of items per page
        $perPage = 36;

        // Start displaying items from this number;
        $offSet = ($page * $perPage) - $perPage; // Start displaying items from this number

        // Get only the items you need using array_slice (only get 10 items since that's what you need)
        //$itemsForCurrentPage = array_slice($deals_query->toArray(), $offSet, $perPage, true);

        // Page title
        SEO::setTitle(trans('general.title.search_result', ['page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title'),'value' => $value]));

        // and return to typeahead
        return view('frontend.game.searchindex', ['games' => new \Illuminate\Pagination\LengthAwarePaginator($games->forPage($page,$perPage), count($games), $perPage, $page, ['path' => Request::url()]), 'value' => $value]);
    }

    private function databaseCheck($game, $database, $platform)
    {
        foreach ($database as $result) {
            if ($result->igdb_id === $game->id && $result->platform_id === $platform->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Search with json response
     *
     * @param  String  $value
     * @return JSON
     */
    public function searchJson($value)
    {
        // Accept only ajax requests
        if(!Request::ajax()){
            return abort('404');
        }

        $words = explode(' ', $value);
        foreach ($words as &$word) {
            if (strlen($word) > 3) {
                $word = '+'.$word;
            }
        }
        $string = implode(' ', $words);

        $games = Product::selectRaw("*, MATCH (games.name, games.name_us, games.name_jp, games.name_eu) AGAINST (?) AS `rank`")
            ->whereRaw("MATCH (games.name, games.name_us, games.name_jp, games.name_eu) AGAINST (?)")
            ->setBindings([$string, $string])
            ->whereNotNull(session('region.code'))
            ->orderBy('rank', 'desc')
            ->limit(6)->get();

        $games->load('platform','listingsCount','cheapestListing');

        $data = array();
        foreach ($games as $game) {
            $image_name = substr($game->cover, 0, -4);
            $data[" " . $game->id]['id'] = $game->id;
            $data[" " . $game->id]['name'] = $game->name;
            $data[" " . $game->id]['pic'] = $game->image_square_tiny;
            $data[" " . $game->id]['platform_name'] = $game->platform->name;
            $data[" " . $game->id]['platform_color'] = $game->platform->color;
            $data[" " . $game->id]['platform_text_color'] = $game->platform->text_color;
            $data[" " . $game->id]['platform_acronym'] = $game->platform->acronym;
            $data[" " . $game->id]['platform_digital'] = $game->platform->digitals->count() > 0 ? true : false;
            $data[" " . $game->id]['listings'] = $game->listings_count;
            $data[" " . $game->id]['release_year'] = substr($game->{session('region.code')}, 0, 4);
            $data[" " . $game->id]['cheapest_listing'] = $game->cheapest_listing;
            $data[" " . $game->id]['url'] = $game->url_slug;
            $data[" " . $game->id]['avgprice'] = $game->getAveragePrice();
            $data[" " . $game->id]['avgprice_string'] = trans('listings.form.sell.avgprice', ['game_name' => $game->name, 'avgprice' => $game->getAveragePrice() ]);
            $data[" " . $game->id]['ntsc_u'] = $game->ntsc_u;
            $data[" " . $game->id]['pal'] = $game->pal;
            $data[" " . $game->id]['ntsc_j'] = $game->ntsc_j;
            $data[" " . $game->id]['pa'] = $game->pa;
            $data[" " . $game->id]['components'] = $game->components;

            foreach (config('components.all') as $component) {
                $data[" " . $game->id]["$component"] = $game->$component;
            }
        }

        // and return to typeahead
        return response()->json($data);
    }

    private static function getRouteClass()
    {
        $request = app('request');
        $classes = [
            'hardware' => Hardware::class,
            'games' => Game::class,
            'search' => Product::class,
            'product' => Product::class
        ];
        return $classes[($request->segments()[0])];
    }
}
