<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccessoriesHardwareType as ProductType;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Game;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Redirect;
use SEO;
use Cache;
use App\Traits\ParameterRouteCache;

class WishlistController extends Controller
{
    use ParameterRouteCache;

    /**
     * Index wishlist.
     *
     * @return mixed
     */
    public function index()
    {
      // Check if user is logged in
      if (!(Auth::check())) {
          return redirect()->route('frontend.auth.login');
      }
        $products = Wishlist::query()
            ->select('game_wishlists.*', 'games.name', 'listings.condition', 'listings.price', "listings.id as id", "listings.created_at as created_at", "game_wishlists.id as wishlist_id")
            ->leftJoin('listings', 'listings.game_id', '=', 'game_wishlists.game_id')
            ->whereNull('listings.deleted_at')
            ->where('game_wishlists.user_id', Auth::id())->join('games', 'games.id', '=', 'game_wishlists.game_id')
            ->with('game','listings','listings.game','listings.game.platform','listings.user');
        $filterRepo = new \App\Repositories\FiltersRepository;
        $filterRepo->order = "listings.created_at";
        $filterRepo->direction = "desc";
        if (!request()->has("o") || strpos(request('o'), 'listings.created_at')) {
            $filterRepo->groupBy = null;
        }

        $products = $filterRepo->filter($products, request()->only($filterRepo->filtersList), 'product');

        $response = [
            'wishlists' => $products,
            'filters' => $filterRepo,
            'genres' => Cache::remember('genres', config('cache.timeout.lg'), function() { return Genre::orderBy('name', 'asc')->get(); }),
            'platforms' => Cache::remember('platforms:'.session('region.code'), config('cache.timeout.lg'), function() { return Platform::orderBy('name', 'asc')->where(session('region.code'), true)->get(); }),
            'product_types' => Cache::remember('product_types', config('cache.timeout.lg'), function() { return ProductType::get(); }),
            'pagination_links' => $this->paginationLinks($products),
            'pagination_obj' => $products
        ];
      // SEO Page Title
      SEO::setTitle(trans('wishlist.wishlist'). ' - ' . config('settings.page_name') . ' » ' . config('settings.sub_title'));

      return view('frontend.wishlist.index', $response);
    }

    /**
     * Wishlist sorted by newest
     *
     * @return mixed
     */
    public function new()
    {
        // Check if user is logged in
        if (!(Auth::check())) {
            return redirect()->route('frontend.auth.login');
        }

        $selects = [
            '*',
            'game_wishlists.id as id',
            'games.id as game_id',
            'games.name as name',
            'users.name as user_name',
            'listings.id as listing_id',
            'listings.created_at as created_at',
            'platforms.name as platform_name'
        ];

        // Get all wishlist entries from this user
        $wishlists =  Wishlist::select($selects)
            ->where('game_wishlists.user_id', Auth::id())
            ->join('listings', 'listings.game_id', '=', 'game_wishlists.game_id')
            ->whereNull('listings.deleted_at')
            ->join('games', 'games.id', 'game_wishlists.game_id')
            ->join('platforms', 'platforms.id', '=', 'games.platform_id')
            ->join('users', 'users.id', '=', 'listings.user_id')
            ->orderBy('listings.created_at', 'desc')->paginate('25');
        // SEO Page Title
        SEO::setTitle(trans('wishlist.wishlist'). ' - ' . config('settings.page_name') . ' » ' . config('settings.sub_title'));

        return view('frontend.wishlist.index', ['wishlists' => $wishlists]);
    }



    /**
     * Add item to wishlist.
     *
     * @return mixed
     */
    public function add($slug)
    {
      // Check if user is logged in
      if (!(Auth::check())) {
          return redirect()->route('frontend.auth.login');
      }

      // Get game id from slug string
      $game_id = ltrim(strrchr($slug,'-'),'-');
      $game = Product::find($game_id);

      // Check if game exists
      if (is_null($game)) {
          return abort('404');
      }

      // Check if game is already in the wishlist
      $wishlist_check = Wishlist::where('game_id', $game->id)->where('user_id', Auth::id())->first();

      if (isset($wishlist_check)) {
          // show a error message
          \Alert::error('<i class="fas fa-times m-r-5"></i> ' . trans('wishlist.alert.exists', ['game_name' => str_replace("'", '', $game->name)]))->flash();

          return redirect()->back();

      }

      // Get all input values
      $input = Input::all();

      // Create new wishlist
      $wishlist = new Wishlist;
      // Set game id
      $wishlist->game_id = $game->id;
      // Set user id
      $wishlist->user_id = Auth::id();

      // Check if user want to get a notification
      if (Input::has('wishlist-notification')) {
          $wishlist->notification = true;
      }

      // Save wishlist
      $wishlist->save();

      // show a success message
      \Alert::success('<i class="fas fa-heart m-r-5"></i>' . trans('wishlist.alert.added', ['game_name' => str_replace("'", '', $game->name)]))->flash();

      return redirect()->back();
    }

    /**
     * Add item to wishlist.
     *
     * @return mixed
     */
    public function update($slug)
    {
      // Check if user is logged in
      if (!(Auth::check())) {
          return redirect()->route('frontend.auth.login');
      }

      // Get game id from slug string
      $game_id = ltrim(strrchr($slug,'-'),'-');
      $game = Game::find($game_id);

      // Check if game exists
      if (is_null($game)) {
          return abort('404');
      }

      // Check if item is in wishlist
      $wishlist = Wishlist::where('game_id', $game->id)->where('user_id', Auth::id())->first();

      if (!isset($wishlist)) {
          return abort('404');
      }

      // Get all input values
      $input = Input::all();

      // Set game id
      $wishlist->game_id = $game->id;
      // Set user id
      $wishlist->user_id = Auth::id();

      // Check if user want to get a notification
      if (Input::has('wishlist-notification')) {
          $wishlist->notification = true;
          // Max price for the notification
          $max_price = filter_var($input['wishlist_price'], FILTER_SANITIZE_NUMBER_INT);
          if ($max_price > 0) {
              $wishlist->max_price = $max_price;
          } else {
              $wishlist->max_price = null;
          }
      } else {
          $wishlist->notification = false;
          $wishlist->max_price = null;
      }

      // Save wishlist
      $wishlist->save();

      // show a success message
      \Alert::success('<i class="fas fa-heart m-r-5"></i> ' . trans('wishlist.alert.saved', ['game_name' => str_replace("'", '', $game->name)]))->flash();

      return redirect()->back();
    }

    /**
     * Remove item from wishlist.
     *
     * @param $id
     * @return mixed
     */
    public function delete($slug)
    {
        // Check if user is logged in
        if (!(Auth::check())) {
            return redirect()->route('frontend.auth.login');
        }

        // Get game id from slug string
        $game_id = ltrim(strrchr($slug,'-'),'-');
        $game = Product::find($game_id);

        // Check if game exists
        if (is_null($game)) {
            return abort('404');
        }

        // Check if game is already in the wishlist
        $wishlist = Wishlist::where('game_id', $game->id)->where('user_id', Auth::id())->first();

        // Check if wishlist entry exists, otherwise abort with a 404 error
        if (isset($wishlist)) {
            $wishlist->delete();
        } else {
            return abort('404');
        }

        // show a success message
        \Alert::error('<i class="far fa-heart"></i> ' . trans('wishlist.alert.removed', ['game_name' => str_replace("'", '', $game->name)]))->flash();

        return redirect()->back();
    }
}
