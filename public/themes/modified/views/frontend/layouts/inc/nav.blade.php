{{-- Progress bar for ajax loading --}}
<nav class="site-navbar navbar navbar-dark navbar-fixed-top navbar-inverse"
role="navigation" style="{{ (config('settings.landing_page') && !Auth::check() && Request::is('/') || Request::is('games/*') && !Request::is('games/add')) || Request::is('games') || Request::is('user/*') || Request::is('login') || Request::is('password/reset/*') || Request::is('offer/*') || Request::is('listings') || (Request::is('listings/*') && !Request::is('listings/add') && !Request::is('listings/*/add') && !Request::is('listings/*/edit') ) ? 'background: linear-gradient(0deg, rgba(34,33,33,0) 0%, rgba(34,33,33,0.8) 100%);' : 'background-color: rgba(34,33,33,1);' }} -webkit-transition: all .3s ease 0s; -o-transition: all .3s ease 0s; transition: all .3s ease 0s; z-index: 20;">
@if(config('app.env') !== 'production')
<div style="z-index:-10; position:fixed; height:60px; left:0px; top:0px; width:100%; color:white; text-align:center; font-weight:bold;">{{strtoupper(config('app.env'))}} SITE</div>
@endif
  {{-- Start header --}}
  <div class="navbar-header">

    {{-- Toggle offcanvas navigation (Mobile only) --}}
    <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided navbar-toggle offcanvas-toggle"
   data-toggle="offcanvas" data-target="#js-bootstrap-offcanvas" id="offcanvas-toggle">
      <span class="sr-only">{{ trans('general.nav.toggle_nav') }}</span>
      <span class="hamburger-bar"></span>
    </button>

    {{-- Toggle sub navigation (Mobile only) --}}
    <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
    data-toggle="collapse">
      <i class="icon fa fa-ellipsis-h" aria-hidden="true"></i>
    </button>
    {{-- Logo --}}
    <a class="navbar-brand navbar-brand-center" href="{{ url('') }}">
      <img src="{{ asset(config('settings.logo')) }}?v={{hash_file('md5', asset(config('settings.logo')))}}"
      title="Logo" class="hires">
    </a>

  </div>
  {{-- End header --}}
  @php
    // Get all platforms
    // TODO: Cache Platforms
    $platforms = \Cache::remember('platforms:'.session('region.code'), config('cache.timeout.lg'), function() {
        return \App\Models\Platform::orderBy('name', 'asc')->where(session('region.code'), true)->get();
    });
    $hardwares = \Cache::remember('accessories_hardware_types:'.session('region.code'), config('cache.timeout.lg'), function() {
        return \DB::table('accessories_hardware_types')->select('id', 'name', 'slug')->get();
    });
  @endphp
  
  {{-- Start Navigation --}}
  <div class="navigation">

    <div class="navbar-container navbar-offcanvas navbar-offcanvas-touch" id="js-bootstrap-offcanvas" style="margin-left: 0px; margin-right: 0px; padding-left: 0px; padding-right: 0px;">

      <ul class="site-menu" data-plugin="menu">
        {{-- Close button (only offcanvas menu) --}}
        <li class="site-menu-item hidden-md-up">
          <a href="javascript:void(0)" data-toggle="offcanvas" data-target="#js-bootstrap-offcanvas" id="offcanvas-toggle" class="offcanvas-toggle">
            <i class="site-menu-icon fa fa-times" aria-hidden="true"></i>
            <span class="site-menu-title">{{ trans('general.close') }}</span>
          </a>
        </li>
        {{-- Start listings nav --}}
        <li class="site-menu-item has-sub {{ Request::is('listings/*') || ( URL::current() == url('listings') ) ? 'active': '' }}">
          <a href="javascript:void(0)" data-toggle="dropdown">
            <span class="site-menu-title"><i class="site-menu-icon fa fa-tags" aria-hidden="true"></i> {{ trans('general.listings') }}</span><span class="site-menu-arrow"></span>
          </a>
          <div class="dropdown-menu site-menu-games dropdown-listings">
              <div>
                <ul style="padding-left:10px;padding-top:1px">
                <li class="site-menu-item {{ ( URL::current() == url('listings/') ) ? 'active' : null }}">
                  <a href="{{ url('listings/')}}">
                    <span class="site-menu-title">{{ trans('listings.general.all_listings') }}</span>
                  </a>
                </li>
                @foreach($platforms as $platform)
                  <li class="site-menu-item {{ ( URL::current() == url('listings/'.$platform->acronym) ) ? 'active' : null }}">
                    <a href="{{ url('listings/?p='.$platform->id)}}">
                      <span class="site-menu-title">{{ $platform->name }}</span>
                    </a>
                  </li>
                @endforeach
              </div>
          </div>
        </li>
        {{-- End listings nav --}}
        {{-- Games navbar --}}
        <li class="site-menu-item {{ Request::is('games/*') || ( URL::current() == url('games') ) ? 'active': '' }}">
          <a href="javascript:void(0)" data-toggle="dropdown">
            <span class="site-menu-title"><i class="site-menu-icon fa fa-gamepad" aria-hidden="true"></i> {{ trans('general.games') }}</span><span class="site-menu-arrow"></span>
          </a>
          <div class="dropdown-menu site-menu-games dropdown-games">
              <div>
                <ul style="padding-left:10px;padding-top:1px">
                <li class="site-menu-item">
                  <a href="{{ url('games/platform/all')}}">
                    <span class="site-menu-title">{{ trans('games.overview.all_games') }}</span>
                  </a>
                </li>
                @foreach($platforms as $platform)
                  <li class="site-menu-item">
                    <a href="{{ url('games/?p='.$platform->id)}}">
                      <span class="site-menu-title">{{ $platform->name }}</span>
                    </a>
                  </li>
                @endforeach
              </div>
          </div>
        </li>

        {{-- Hardware navbar --}}
        <li class="site-menu-item {{ Request::is('hardware/*') || ( URL::current() == url('hardware') ) ? 'active': '' }}">
          <a href="javascript:void(0)" data-toggle="dropdown">
            <span class="site-menu-title"><i class="site-menu-icon fa fa-hdd" aria-hidden="true"></i> {{ trans('general.hardware') }}</span><span class="site-menu-arrow"></span>
          </a>
          <div class="dropdown-menu site-menu-hardware dropdown-hardware">
            <div>
              <ul style="padding-left:10px;padding-top:1px;overflow-y:hidden;">
                <li class="site-menu-item">
                  <a href="{{ url('/hardware')}}">
                    <span class="site-menu-title">{{ trans('games.overview.all_hardware') }}</span>
                  </a>
                </li>
                @foreach($hardwares as $hardware)
                  <li class="site-menu-item">
                    <a href="{{ url('hardware/?t='.$hardware->slug)}}">
                      <span class="site-menu-title">{{ $hardware->name }}</span>
                    </a>
                  </li>
              @endforeach
            </div>
          </div>
        </li>



        {{-- Search navbar --}}
        <li class="site-menu-item">
          <a href="javascript:void(0)" data-toggle="collapse" data-target="#site-navbar-search" role="button" id="navbar-search-open">
            <i class="site-menu-icon fa fa-search hidden-sm-down" aria-hidden="true"></i>
            <span class="site-menu-title hidden-md-down">{{ trans('general.nav.search') }}</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="navbar-container container-fluid userbar">
      <!-- Navbar Collapse -->
      <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
        <!-- Navbar Toolbar -->

        {{-- Start Search toggle for mobile view --}}
        <button type="button" class="navbar-toggler collapsed float-left" data-target="#site-navbar-search"
        data-toggle="collapse">
          <span class="sr-only">{{ trans('general.nav.toggle_search') }}</span>
          <i class="icon fa fa-search" aria-hidden="true"></i>
        </button>
        {{-- End Search toggle for mobile view --}}

        <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
          {{-- Start User nav --}}
          @if(Auth::check())
            <li class="nav-item dropdown">
            <a class="nav-link" href="{{ url('/cart') }}" title="Cart" role="button">
              {{-- Icon --}}
              <div class="quick-icon">
                <i class="fa fa-shopping-cart"></i>
                <span class='badge' id='cart-count'>{{Cart::count()}}</span>
              </div>
            </a>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link" href="{{ url('dash/quick-shop') }}" title="Quick Shop" role="button" >
                {{-- Icon --}}
                <div class="quick-icon">
                  <i class="fas fa-store-alt"></i>
                </div>
              </a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link" href="{{ url('messages') }}" title="Messages" role="button" >
              <i class="fas @if(!Request::is('messages') && $unreadMessagesCount>0) fa-envelope-open @else fa-envelope @endif"></i>
              {{-- Count unread notifications --}}
              @if(!Request::is('messages') && $unreadMessagesCount>0)
                <span id="unread-messages" class="badge badge-danger badge-sm up">{{$unreadMessagesCount}}</span>
              @endif
            </a>
          </li>
          <li class="nav-item dropdown" id="dropdown-notifications">
            <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" title="Notifications" role="button" >
              <i class="icon fa fa-bell @if(count(Auth::user()->unreadNotifications)>0) faa-shake animated @endif" aria-hidden="true"></i>
              {{-- Count unread notifications --}}
              @if(count(Auth::user()->unreadNotifications)>0)
                <span class="badge badge-danger badge-sm up">{{count(Auth::user()->unreadNotifications)}}</span>
              @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-nofications">
              <li class="dropdown-notifications-loading">
                <i class="fa fa-refresh fa-spin fa-2x" aria-hidden="true"></i>
              </li>
              <li class="dropdown-notifications-content">
              </li>
              {{-- Subscribe to push notifications --}}
              @if(config('settings.onesignal'))
              <li class="dropdown-notifications-push-subscribe" id="subscribe-push" style="display:none;">
                <a href="#" id="subscribe-push-link">
                  <i class="fa fa-dot-circle-o" aria-hidden="true"></i>
              {{ trans('general.nav.user.notifications_push_subscribe') }}
                </a>
              </li>
              @endif
              {{-- Show all notifications --}}
              <li class="dropdown-notifications-showall">
                <a href="{{ url('dash/notifications')}}">
                  <i class="fa fa-bell"></i> {{ trans('general.nav.user.notifications_all') }}
                </a>
              </li>
            </ul>
          </li>
            <li class="nav-item dropdown">
              <a class="nav-link navbar-avatar flex-center" href="{{Auth::user()->url}}">
                <span class="avatar avatar-online" style="width:35px">
                  <img src="{{Auth::user()->avatar_square_tiny}}" alt="{{Auth::user()->name}}" border="0" width="75" style="position:relative;right:2px">
                </span>
              </a>
            </li>
          <li class="nav-item dropdown">
            <a class="nav-link navbar-avatar flex-center" data-toggle="dropdown" href="#" aria-expanded="false"
            data-animation="scale-up" role="button">
              <span class="m-l-5 m-r-10"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
            </a>
            <div class="dropdown-menu" role="menu" style="">
              @can('access_backend')
              <a class="dropdown-item" href="{{url('admin')}}" role="menuitem"><i class="icon fa fa-id-badge" aria-hidden="true"></i> {{ trans('general.nav.user.admin') }}</a>
              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              @endcan
              @if(config('settings.payment'))
              <a class="dropdown-item" href="{{url('dash/balance')}}" role="menuitem"><i class="icon far fa-money-bill" aria-hidden="true"></i> <strong>{{ money(abs(filter_var(number_format( Auth::user()->balance,2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</strong></a>
              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              @endif
              <a class="dropdown-item" href="{{url('dash')}}" role="menuitem"><i class="icon fa fa-tachometer" aria-hidden="true"></i> {{ trans('general.nav.user.dashboard') }}</a>
              <a class="dropdown-item" href="{{url('dash/listings')}}" role="menuitem"><i class="icon fa fa-tags" aria-hidden="true"></i> {{ trans('general.nav.user.listings') }}</a>
              <a class="dropdown-item" href="{{url('dash/offers')}}" role="menuitem"><i class="icon fa fa-briefcase" aria-hidden="true"></i> {{ trans('general.nav.user.offers') }}</a>
              <a class="dropdown-item" href="{{url('friends')}}" role="menuitem"><i class="icon fa fa-user-friends" aria-hidden="true"></i> {{ trans('general.nav.user.friends') }}</a>

              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              <a class="dropdown-item" href="{{url('dash/quick-shop')}}" role="menuitem"><i class="icon fa fa-store-alt" aria-hidden="true"></i> Quick Shop</a>
              <a class="dropdown-item" href="{{Auth::user()->url."?frag=wishlist#wishlist"}}" role="menuitem"><i class="icon fa fa-heart" aria-hidden="true"></i> {{ trans('wishlist.wishlist') }}</a>
              <a class="dropdown-item" href="{{Auth::user()->url."?frag=collection#collection"}}" role="menuitem"><i class="icon fa fa-clipboard-list" aria-hidden="true"></i> Collection</a>
              <a class="dropdown-item" href="{{Auth::user()->url."?frag=completedlist#completedlist"}}" role="menuitem"><i class="icon fa fa-clipboard-check" aria-hidden="true"></i> Games I've Completed</a>
              <a class="dropdown-item" href="{{Auth::user()->url."?frag=trophies#trophies"}}" role="menuitem"><i class="icon fa fa-trophy" aria-hidden="true"></i> Trophies</a>

              <a class="dropdown-item" href="{{Auth::user()->url."?frag=customlists#customlists"}}" role="menuitem"><i class="icon fa fa-list-ol" aria-hidden="true"></i> Lists</a>
              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              <a class="dropdown-item" href="{{ url('dash/notifications') }}" role="menuitem"><i class="icon fa fa-bell" aria-hidden="true"></i> {{ trans('general.nav.user.notifications') }}</a>
              <a class="dropdown-item" href="{{ url('dash/settings') }}" role="menuitem"><i class="icon fa fa-wrench" aria-hidden="true"></i> {{ trans('general.nav.user.settings') }}</a>
              <a class="dropdown-item" href="{{Auth::user()->url}}" role="menuitem">
                <span class="avatar avatar-online" style="width:22px">
                  <img src="{{Auth::user()->avatar_square_tiny}}" alt="{{Auth::user()->name}}" border="0" width="75" style="position:relative;right:3px;max-width: 22px; max-height: 22px;">
                </span>
                {{ trans('general.nav.user.profile') }}</a>
              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              <a class="dropdown-item" href="{{url('logout')}}" role="menuitem"><i class="icon fa fa-power-off" aria-hidden="true"></i> {{ trans('general.nav.user.logout') }}</a>
            </div>
          </li>

          @endif

          @if(Auth::check())
          
          {{-- Add Listing Button --}}
          <a href="{{url('listings/add')}}" aria-expanded="false" role="button" class="btn btn-orange btn-round navbar-btn navbar-right" style="font-weight: 500;">
            <i class="fa fa-plus"></i><span class="hidden-md-down"> {{ trans('general.nav.listing_add') }}</span>
          </a>
          @endif

          @if(!Auth::check())
          {{-- Sign Up Button --}}
          <a data-toggle="modal" data-target="#RegModal" href="javascript:void(0)" aria-expanded="false" role="button" class="btn btn-orange btn-round navbar-btn navbar-right" style="font-weight: 500; border-radius: 0px 50px 50px 0px;">
            <i class="fa fa-user-plus"></i>
          </a>
          {{-- Sign in Button --}}
          <a data-toggle="modal" data-target="#LoginModal" href="javascript:void(0)" aria-expanded="false" role="button" class="btn btn-success btn-round navbar-btn navbar-right" style="font-weight: 500; border-radius: 50px 0px 0px 50px">
            <i class="fa fa-sign-in"></i> {{ trans('auth.login') }}
          </a>
          @endif
          {{-- End User nav --}}
        </ul>
      </div>
      <!-- End Navbar Collapse -->
    </div>

    <!-- Site Navbar Seach -->
    <div class="collapse navbar-search-overlap" id="site-navbar-search" style="width: 100%;">
      <form role="search" id="search">
        <div class="form-group">
          <div class="input-search input-search-fix">
            <i class="input-search-icon fa fa-search" aria-hidden="true" id="loadingcomplete"></i>
            <i class="input-search-icon fa fa-sync fa-spin" aria-hidden="true" id="loadingsearch" style="display: none; margin-top: -8px !important;"></i>
            <input type="text" class="gs-search-bar form-control" name="input" placeholder="{{ trans('general.nav.search') }}" id="navbar-search" autocomplete="off">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="button" class="input-search-close icon fa fa-times" data-target="#site-navbar-search"
            data-toggle="collapse" aria-label="Close" id="search-close"></button>
          </div>
        </div>
      </form>
    </div>
    <!-- End Site Navbar Seach -->
  </div>
  {{-- End Navigation --}}
</nav>
