<?php
namespace App\Http\Controllers;

use App\Models\CustomList;
use App\Models\Wishlist;
use App\Traits\ParameterRouteCache;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Game;
use App\Models\AccessoriesHardware as Hardware;
use App\Models\Friend;
use App\Models\Product;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\AccessoriesHardwareType as ProductType;
use App\Models\Country;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Http\Requests\Frontend\User\UpdateProfileRequest;
use App\Http\Requests\Frontend\User\ChangePasswordRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Repositories\UserRepository;
use Validator;
use Redirect;
use Session;
use SEO;
use Theme;
use Searchy;
use Auth;
use Cache;

class UserController
{
    use ParameterRouteCache;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * UserController constructor.
     *
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Settings form
     *
     * @param  string|null  $system
     * @return mixed
     */
    public function settingsForm()
    {
        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        // Page title
        SEO::setTitle(trans('users.dash.settings.settings') . ' - ' . config('settings.page_name'));

        $platforms = \Cache::remember('platforms:'.session('region.code'), config('cache.timeout.lg'), function() {
            return \App\Models\Platform::orderBy('name', 'asc')->where(session('region.code'), true)->get();
        });

        $genres = \Cache::remember('genres', config('cache.timeout.lg'), function() {
            return Genre::orderBy('name', 'asc')->get();
        });

        return view('frontend.user.settings.profile', ['user' => auth()->user(), 'location' => auth()->user()->location, 'platforms' => $platforms, 'genres' => $genres]);
    }

    /**
     * Save settings
     *
     * @param UpdateProfileRequest $request
     * @return mixed
     */
    public function settingsSave(UpdateProfileRequest $request)
    {
        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        $this->user->updateProfile(auth()->id(), $request);
        return redirect()->route('dashboard.settings');
    }

    /**
     * Password form
     *
     * @return view
     */
    public function passwordForm()
    {
        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        // Page title
        SEO::setTitle(trans('users.dash.settings.password_heading') . ' - ' . config('settings.page_name'));

        return view('frontend.user.settings.password', ['user' => auth()->user()]);
    }

    /**
     * Change password
     *
     * @param ChangePasswordRequest $request
     * @return mixed
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        $this->user->changePassword($request->all());
        return Redirect::to('dash/settings/password');
    }

    /**
     * User profile
     *
     * @param ChangePasswordRequest $request
     * @return view
     */
    public function show($slug)
    {
        // Get user from slug string
        $user = User::with('location')->where('name', $slug)->first();
        // Check if user exists
        if (is_null($user)) {
            return Redirect::to('/');
        }
        $filterRepo = new \App\Repositories\FiltersRepository;
        $pagination_links = null;
        $pagination_obj = null;
        $listings = null;
        if (in_array(request()->get('frag'), ['', 'listings'])) {
            $listings = Listing::query();
            $listings->select(['listings.*']);
            $listings->where('listings.user_id', '=', $user->id);
            if (request('re')) {
                $listings->where('listings.region', request('re'));
            }
            $listings->join('games', function($join) {
                $join->on('games.id', '=', 'listings.game_id');
            })->where(function($query) {
                $query->where('status', 0)
                    ->orWhere('status', null);
            });
            $filterRepo = new \App\Repositories\FiltersRepository;
            $filterRepo->region = request('re', 'all');
            $filterRepo->order = 'listings.created_at';
            $filterRepo->direction = 'desc';
            $listings = $filterRepo->filter($listings, request()->only($filterRepo->filtersList), 'listing', 'listing');
            if ($filterRepo->pageErrorRedirection) {
                return redirect()->to($filterRepo->pageErrorRedirection);
            }
            $pagination_links = $this->paginationLinks($listings);
            $pagination_obj = $listings;
        }

        $wishlist = null;
        if (request()->get('frag') == 'wishlist' && auth()->id() == $user->id) {
            $wishlist = Game::query()
                ->join('game_wishlists', 'games.id', '=', 'game_wishlists.game_id')
                ->where('game_wishlists.user_id', Auth::id());
            $wishlist->select('*', 'games.id as id');
            $filterRepo = new \App\Repositories\FiltersRepository();
            $filterRepo->region = request('re', 'all');
            $wishlist = $filterRepo->filter($wishlist, request()->only($filterRepo->filtersList), 'product', 'wishlist');
            if ($filterRepo->pageErrorRedirection) {
                return redirect()->to($filterRepo->pageErrorRedirection);
            }
            $pagination_links = $this->paginationLinks($wishlist);
            $pagination_obj = $wishlist;
        }

        $have_list = null;
        if (request()->get('frag') == 'collection') {
            $have_list = Game::query();
            $have_list->join('game_have_lists', function($join) use ($user){
                $join->on('game_have_lists.game_id', '=', 'games.id')
                    ->where('game_have_lists.user_id', '=', $user->id);
                if (request('re') != 'all' && !is_null(request('re')) ) {
                    $join->where('game_have_lists.region', '=', request('re'));
                }
            });
            $have_list->select('*', 'games.id as id');
            $filterRepo = new \App\Repositories\FiltersRepository();
            $filterRepo->region = request('re', 'all');
            $have_list = $filterRepo->filter($have_list, request()->only($filterRepo->filtersList), 'product', 'have-page');
            if ($filterRepo->pageErrorRedirection) {
                return redirect()->to($filterRepo->pageErrorRedirection);
            }
            $pagination_links = $this->paginationLinks($have_list);
            $pagination_obj = $have_list;
        }
        $hardware_list = null;
        if (request()->get('frag') == 'hardware') {
            $hardware_list = Hardware::query();
            $hardware_list->join('game_have_lists', function($join) use ($user){
                $join->on('game_have_lists.game_id', '=', 'games.id')
                    ->where('game_have_lists.user_id', '=', $user->id);
                if (request('re') != 'all' && !is_null(request('re')) ) {
                    $join->where('game_have_lists.region', '=', request('re'));
                }
            });
            $hardware_list->select('*', 'games.id as id');
            $filterRepo = new \App\Repositories\FiltersRepository;
            $filterRepo->region = request('re', 'all');
            $hardware_list = $filterRepo->filter($hardware_list, request()->only($filterRepo->filtersList), 'product', 'have-page');
            if ($filterRepo->pageErrorRedirection) {
                return redirect()->to($filterRepo->pageErrorRedirection);
            }
            $pagination_links = $this->paginationLinks($hardware_list);
            $pagination_obj = $hardware_list;
        }
        $completed_list = null;
        if (request()->get('frag') == 'completedlist') {
            $completed_list = Game::query();
            $completed_list->join('game_completed_lists', function($join) use ($user) {
                $join->on('game_completed_lists.game_id', '=', 'games.id')
                    ->where('game_completed_lists.user_id', '=', $user->id);
            });
            $completed_list->select('*', 'games.id as id');
            $filterRepo = new \App\Repositories\FiltersRepository;
            $filterRepo->region = request('re', 'all');
            $completed_list = $filterRepo->filter($completed_list, request()->only($filterRepo->filtersList), 'product', 'completed-page');
            if ($filterRepo->pageErrorRedirection) {
                return redirect()->to($filterRepo->pageErrorRedirection);
            }
            $pagination_links = $this->paginationLinks($completed_list);
            $pagination_obj = $completed_list;
        }
        $custom_lists = null;
        if (request()->get('frag') == 'customlists') {
            $custom_lists = CustomList::where('user_id', $user->id)
                ->orderBy('order_number', 'asc')
                ->where(function($query) use ($user) {
                    if (!auth()->check() || auth()->id() != $user->id) {
                        $query->where('public', true);
                    }
                })
                ->paginate('36', ['*'], 'custom_list_page');
        }

        $trophies = null;
        if (request()->get('frag') == 'trophies') {
            $location = 'ntsc_u';
            if (isset($user->location->region_code)) {
                $location = str_replace('-','_', strtolower($user->location->region_code));
            }
            $user->load(['trophies' => function ($query) use ($location) {
                $query->with('trophy', 'nextTrophy')->where(function($where) use ($location) {
                    $where->where('region', $location)->orWhereNull('region');
                });
            }], 'trophies.platform');
            $trophies = $user->trophies;
        }

        $listingsCount = !is_null($listings) ? $listings->count() : 0;

        $response = [
            'user' => $user,
            'user_name' => $user->name,
            'friend' => auth()->check() ? Friend::where('user_id', auth()->id())->where('friend_id', $user->id)->first() : null,
            'listings_count' => $listingsCount,
            'filters' => $filterRepo,
            'genres' => Cache::remember('genres', config('cache.timeout.lg'), function() { return Genre::orderBy('name', 'asc')->get(); }),
            'platforms' => Cache::remember('platforms:'.session('region.code'), config('cache.timeout.lg'), function() { return Platform::orderBy('name', 'asc')->where(session('region.code'), true)->get(); }),
            'product_types' => Cache::remember('product_types', config('cache.timeout.lg'), function() { return ProductType::get(); }),
            'listings' => $listings,
            'wishlist' => $wishlist,
            'system' => null,
            'ratings' => [],
            'have_list' => $have_list,
            'hardware_list' => $hardware_list,
            'completed_list' => $completed_list,
            'trophies' => $trophies,
            'custom_lists' => $custom_lists,
            'pagination_links' => $pagination_links,
            'pagination_obj' => $pagination_obj,
        ];


        // Page title
        SEO::setTitle(trans('general.title.profile', ['page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title'), 'user_name' => $user->name]));

        // Get image size for og
        if ($user->avatar) {
            $imgsize = getimagesize($user->avatar_square);
            SEO::opengraph()->addImage(['url' => $user->avatar_square, ['height' => $imgsize[1], 'width' => $imgsize[0]]]);
        }

        // Page description
        SEO::setDescription(trans('general.description.profile', ['user_name' => $user->name, 'listings_count' => $listingsCount, 'page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title')]));

        return view('frontend.user.show', $response);
    }

    /**
     * Notifications dashboard
     *
     * @return view
     */
    public function notifications()
    {
        // Page title
        SEO::setTitle(trans('notifications.title') . ' - ' . config('settings.page_name'));

        return view('frontend.user.dash.notifications', ['user' => auth()->user()]);
    }

    /**
     * Notifications api
     *
     * @return view
     */
    public function notificationsApi()
    {
        return view('frontend.user.api.notifications', ['user' => auth()->user()]);
    }

    /**
     * Mark notification as read
     *
     * @param  Request $request
     * @return view
     */
    public function notificationsRead(Request $request)
    {
        $notification = auth()->user()->notifications()->findOrFail($request->notif_id);
        $notification->markAsRead();
        return ['success' => true, 'message' => 'Notification read'];
    }

    /**
     * Mark all notification as read
     *
     * @param  Request $request
     * @return view
     */
    public function notificationsReadAll()
    {

        $notification = auth()->user()->unreadNotifications->markAsRead();
        // show a success message
        \Alert::success('<i class="fa fa-check m-r-5"></i>' . trans('notifications.mark_all_read_alert'))->flash();
        return redirect()->back();
    }

    /**
     * Save user location
     *
     * @param  Request $request
     * @return view
     */
    public function locationSave(Request $request)
    {
        if ($this->user->updateLocation(auth()->id(), $request)) {
            $country = Country::where('name', $request->country)->first();
            session()->put('region.code', strtolower(str_replace('-', '_', $country->region_code)));
            return response(['msg' => 'Location saved'], 200);
        } else {
            return response(['msg' => 'Error, location not saved!'], 422);
        }
    }

    /**
     * User dashboard
     *
     * @param  Request $request
     * @return mixed
     */
    public function dashboard(Request $request)
    {
        // Page title
        SEO::setTitle(trans('users.dash.dashboard') . ' - ' . config('settings.page_name'));

        // Save back URL for finished form
        Session::flash('backUrl', $request->fullUrl());

        // Check if logged in
        if (!(\Auth::check())) {
            return Redirect::to('/');
        }

        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        $user = User::with('listings', 'listings.game', 'listings.game.platform', 'listings.offers', 'listings.offers.game', 'listings.offers.user', 'offers', 'offers.listing')->where('id', \Auth::user()->id)->first();

        return view('frontend.user.dash.overview', ['user' => $user]);
    }

    /**
     * Listings dashboard
     *
     * @param  Request $request
     * @param  string $sort
     * @return view
     */
    public function listings(Request $request, $sort = null)
    {
        // Page title
        SEO::setTitle(trans('general.listings') . ' - ' . config('settings.page_name'));

        // Save back URL for finished form
        Session::flash('backUrl', $request->fullUrl());

        // Check for right link, otherwise abort and send 404
        if (!($sort == null) && !($sort == 'complete') && !($sort == 'deleted')) {
            return abort('404');
        }

        // Check if logged in
        if (!(\Auth::check())) {
            return Redirect::to('/');
        }

        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        $user = User::with('listings')->where('id', \Auth::user()->id)->first();

        $listings_trashed_count = Listing::onlyTrashed()->where('user_id', $user->id)->where('deleted_at', '!=', null)->with('game', 'game.platform', 'offers', 'offers.game', 'offers.user', 'offers.user.location')->orderBy('deleted_at', 'desc')->count();

        if ($sort == 'complete') {
            $listings = Listing::where('user_id', $user->id)->where('status', 2)->with('game', 'game.platform', 'offers', 'offers.game', 'offers.user', 'offers.user.location')->orderBy('updated_at', 'desc')->paginate('10');
        } elseif ($sort == 'deleted') {
            $listings = Listing::onlyTrashed()->where('user_id', $user->id)->where('deleted_at', '!=', null)->with('game', 'game.platform', 'offers', 'offers.game', 'offers.user', 'offers.user.location')->orderBy('deleted_at', 'desc')->paginate('10');
        } else {
            $listings = Listing::where('user_id', $user->id)->where('status', null)->orWhere('status', 0)->where('user_id', $user->id)->orWhere('status', 1)->where('user_id', $user->id)->with('game', 'game.platform', 'offers', 'offers.game', 'offers.user', 'offers.user.location')->orderBy('last_offer_at', 'desc')->paginate('10');
        }

        return view('frontend.user.dash.listings', [
            'user' => $user,
            'listings' => $listings,
            'listings_trashed_count' => $listings_trashed_count,
            'pagination_links' => $this->paginationLinks($listings)
        ]);
    }

    /**
     * Offers dashboard
     *
     * @param  Request $request
     * @param  string $sort
     * @return view
     */
    public function offers(Request $request, $sort = null)
    {

        // Page title
        SEO::setTitle(trans('general.offers') . ' - ' . config('settings.page_name'));

        // Save back URL for finished form
        Session::flash('backUrl', $request->fullUrl());

        // Check for right link, otherwise abort and send 404
        if (!($sort == null) && !($sort == 'complete') && !($sort == 'declined') && !($sort == 'deleted')) {
            return abort('404');
        }

        // Check if logged in
        if (!(\Auth::check())) {
            return Redirect::to('/login');
        }

        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        $user = auth()->user();

        $offers_trashed_count = Offer::onlyTrashed()->where('user_id', $user->id)->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')->orderBy('deleted_at', 'desc')->count();


        if ($sort == 'complete') {
            $offers = Offer::where('user_id', $user->id)->where('status', 2)->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')->orderBy('closed_at', 'desc')->paginate('10');
        } elseif ($sort == 'declined') {
            $offers = Offer::where('user_id', $user->id)->where('declined', 1)->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')->orderBy('closed_at', 'desc')->paginate('10');
        } elseif ($sort == 'deleted') {
            $offers = Offer::onlyTrashed()->where('user_id', $user->id)->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')->orderBy('deleted_at', 'desc')->paginate('10');
        } else {
            $offers = Offer::where('user_id', $user->id)->where('status', null)->where('declined', 0)->orWhere('status', 0)->where('user_id', $user->id)->where('declined', 0)->orWhere('status', 1)->where('user_id', $user->id)->where('declined', 0)->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')->orderBy('updated_at', 'desc')->paginate('10');
        }

        return view('frontend.user.dash.offers', ['user' => $user,'offers' => $offers, 'offers_trashed_count' => $offers_trashed_count]);
    }

    /**
     * Ban User.
     *
     * @param  int  $id
     * @return mixed
     */
    public function ban($user_id)
    {
        // Check if user is logged in
        if (!(\Auth::check())) {
            return Redirect::to('/');
        }

        // Check if user can ban users
        if (!(\Auth::user()->can('edit_users'))) {
            return Redirect::to('/');
        }
        // Get user
        $banuser = User::findOrFail($user_id);

        // Check if admin / mod will selfban
        if (\Auth::user()->id == $banuser->id) {
            \Alert::error('<i class="fa fa-user-times m-r-5"></i> You cant ban yourself!')->flash();
            return redirect()->back();
        }

        // Ban / Unban User
        $banuser->status = $banuser->status ? '0' : '1';
        $banuser->save();

        // show a success message
        if ($banuser->status) {
            \Alert::success('<i class="fa fa-user-times m-r-5"></i> ' . $banuser->name . ' succesfully unbaned')->flash();
        } else {
            \Alert::error('<i class="fa fa-user-times m-r-5"></i> ' . $banuser->name  .' succesfully baned')->flash();
        }

        return redirect()->back();
    }

    /**
     * Save geo location from guest
     *
     * @param  Request  $request
     * @return mixed
     */
    public function guestGeoLocation(Request $request)
    {
        session()->put('latitude', $request->latitude);
        session()->put('longitude', $request->longitude);

        return 'saved';
    }

    /**
     * Balance dashboard
     *
     * @param  Request $request
     * @param  string $sort
     * @return view
     */
    public function balance()
    {
        // Page title
        SEO::setTitle(trans('payment.transactions') . ' - ' . config('settings.page_name'));

        // Check if logged in
        if (!(\Auth::check())) {
            return Redirect::to('/login');
        }

        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        $transactions = Transaction::where('user_id', \Auth::user()->id)->orderBy('id','desc')->paginate('12');

        $sale_count = Transaction::where('user_id', \Auth::user()->id)->where('type','sale')->count();

        return view('frontend.user.dash.balance', ['transactions' => $transactions, 'sale_count' => $sale_count]);
    }

    /**
     * Withdrawal dashboard
     *
     * @param  Request $request
     * @param  string $sort
     * @return view
     */
    public function withdrawal()
    {
        // Page title
        SEO::setTitle(trans('payment.withdrawal.withdrawal') . ' - ' . config('settings.page_name'));

        // Check if logged in
        if (!(\Auth::check())) {
            return Redirect::to('/login');
        }

        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        // check if user has available balance
        if ( \Auth::user()->balance <= 0) {
            \Alert::error('<i class="fa fa-times m-r-5"></i> ' . trans('payment.withdrawal.alert.no_balance') .'')->flash();
            return redirect('dash/balance');
        }

        $transactions = Transaction::where('user_id', \Auth::user()->id)->orderBy('id','desc')->get();

        $withdrawal = Withdrawal::where('user_id', \Auth::user()->id)->where('status', '1')->paginate('12');

        return view('frontend.user.dash.withdrawal', ['withdrawal' => $withdrawal, 'transactions' => $transactions]);
    }

    /**
     * Withdrawal dashboard
     *
     * @param  Request $request
     * @param  string $sort
     * @return view
     */
    public function addWithdrawal(WithdrawalRequest $request, $method = null)
    {
        if (!isset($method) || isset($method) && !($method == 'paypal' || $method == 'bank')) {
            \Alert::error('<i class="fa fa-user-times m-r-5"></i> ' . trans('payment.withdrawal.alert.failed') .'')->flash();

            return redirect()->back();
        } else {
            // Check if logged in
            if (!(\Auth::check())) {
                return Redirect::to('/login');
            }

            // check if user account is active
            if (! \Auth::user()->isActive()) {
                \Auth::logout();
                return redirect('login')->with('error', trans('auth.deactivated'));
            }

            // Check if PayPal is allowed
            if ($method == 'paypal' && !config('settings.withdrawal_paypal')) {
                \Alert::error('<i class="fa fa-user-times m-r-5"></i> ' . trans('payment.withdrawal.alert.failed') .'')->flash();

                return redirect()->back();
            }

            // Check if Bank Transfer is allowed
            if ($method == 'bank' && !config('settings.withdrawal_bank')) {
                \Alert::error('<i class="fa fa-user-times m-r-5"></i> ' . trans('payment.withdrawal.alert.failed') .'')->flash();

                return redirect()->back();
            }

            $user = \Auth::user();

            // check if user have available balance
            if ($user->balance <= 0) {
                \Alert::error('<i class="fa fa-times m-r-5"></i> ' . trans('payment.withdrawal.alert.no_balance') .'')->flash();
                return redirect('dash/balance');
            }

            $withdrawal = new Withdrawal;

            $withdrawal->user_id = $user->id;
            if ($method == 'paypal') {
                $withdrawal->payment_method = 'paypal';
                $withdrawal->payment_details = $request->paypal_email;
            }

            if ($method == 'bank') {
                $bank = [
                    'holder_name' => $request->bank_holder_name,
                    'iban' => $request->bank_iban,
                    'bic' => $request->bank_iban,
                    'bank_name' => $request->bank_name,
                ];
                $withdrawal->payment_method = 'bank';
                $withdrawal->payment_details = json_encode($bank);
            }
            $withdrawal->currency = config('settings.currency');
            $withdrawal->total = $user->balance;

            $withdrawal->save();

            // remove balance from user account
            $user->balance = 0.00;
            $user->save();

            // sale transaction
            $withdrawal_transaction = new Transaction;

            $withdrawal_transaction->type = 'withdrawal';
            $withdrawal_transaction->item_id = $withdrawal->id;
            $withdrawal_transaction->item_type = get_class($withdrawal);
            $withdrawal_transaction->user_id = $user->id;
            $withdrawal_transaction->total = $withdrawal->total;
            $withdrawal_transaction->currency = $withdrawal->currency;

            $withdrawal_transaction->save();

            \Alert::success('<i class="fa fa-check m-r-5"></i> ' . trans('payment.withdrawal.alert.successfully') .'')->flash();

            return redirect('dash/balance');

        }


    }

    /**
     * Save player id from user for web push notifications
     *
     * @param  String $func
     */
    public function push($func, Request $request)
    {
        // Check if logged in
        if (!(\Auth::check())) {
            return Redirect::to('/login');
        }

        // check if user account is active
        if (! \Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        $user = \Auth::user();

        // Subsribe user and add player id
        if ($func == 'add') {
            // Check if player id already exist
            $player_check = \DB::table('user_player_ids')->where('player_id', $request->player_id)->first();
            // Add new player id to database
            if (!$player_check) {
                \DB::table('user_player_ids')->insert(
                    ['user_id' => $user->id, 'player_id' => $request->player_id]
                );
            }
            return 'player id saved';

        // Unsubscribe user and remove player id
        } elseif ($func == 'remove') {
            \DB::table('user_player_ids')->where('player_id', $request->player_id)->delete();
            return 'player id removed';
        }

        return 'error';
    }

    /**
     * Search with json response
     *
     * @param  String  $value
     * @return JSON
     */
    public function searchJson($value)
    {
        // Check if request was sent through ajax
        if (!request()->ajax()) {
            return abort('404');
        }

        $users = User::hydrate(Searchy::users('name')->query($value)
      ->getQuery()->where('id','!=',\Auth::user()->id)->limit(10)->get()->toArray() );

        $data = array();

        foreach ($users as $user) {
            $data[" " . $user->id]['id'] = $user->id;
            $data[" " . $user->id]['name'] = $user->name;
            $data[" " . $user->id]['avatar'] = $user->avatar_square_tiny;
            $data[" " . $user->id]['status'] = $user->isOnline() ? 'online' : 'offline';
        }

        // and return to typeahead
        return response()->json($data);
    }
}
