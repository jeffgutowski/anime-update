@section('subheader')
<div class="subheader-image-bg">
  <div class="bg-image-wrapper">
    {{-- Background image of subheader --}}
    @if($game->image_cover)
      <div class="bg-image lazy" data-original="{{$game->image_cover}}"></div>
      <div style="position: absolute; height: 500px; width: 100%; top: 0; background: linear-gradient(0deg, rgba(25,24,24,1) 30%, rgba(25,24,24,0) 80%);"></div>
    {{-- Default background when image cover is missing --}}
    @else
    <div class="bg-image no-image" style="background: linear-gradient(0deg, rgba(25,24,24,1) 0%, rgba(25,24,24,1) 30%, rgba(25,24,24,0) 80%), url({{ asset('/img/game_pattern_white.png') }});"></div>
    @endif
  </div>
  {{-- background color overlay --}}
  <div class="bg-color"></div>
</div>

{{-- Listing sold overlay --}}
@if((isset($listing) && ($listing->status != 0 && !is_null($listing->status))) || isset($listing) && !$listing->user->isActive() )
  <div class="listing-sold-overlay flex-center">
    <div class="msg">
      <div class="msg bg-danger">
        <i class="fa fa-times"></i> {{ trans('listings.general.sold') }}
      </div>
      {{-- Gameover Button --}}
      <div class="m-t-20 text-center">
        <a class="gameoverview-button" href="{{ $game->url_slug }}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ trans('listings.overview.subheader.go_gameoverview') }}</a>
      </div>
    </div>
  </div>
@endif


@endsection

@section('game-content')

{{-- SEO Start --}}
<div itemscope itemtype="http://schema.org/Product" class="hidden">
  {{-- Game name --}}
  <meta itemprop="name" content="{{ $game->name }}" />
  {{-- Game cover --}}
@if($game->cover)
  <meta itemprop="image" content="{{ $game->image_cover }}" />
@endif
  {{-- Game release date --}}
@if($game->release_date)
  <meta itemprop="releaseDate" content="{{ $game->release_date->format('Y-m-d') }}" />
@endif
  {{-- Game description --}}
@if($game->description)
  <meta itemprop="description" content="{{ $game->description }}" />
@endif
@if(isset($listing) && $listing->sell)
  {{-- User rating --}}
  @if($listing->user->ratings->count() > 0)
  <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="hidden">
    <meta itemprop="ratingValue" content="{{ ($listing->user->positive_percent_ratings * 5)/100 }}" />
    <meta itemprop="reviewCount" content="{{ $listing->user->ratings->count() }}" />
  </div>
  @endif
  {{-- Listing details --}}
  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="hidden">
    <meta itemprop="url" content="{{ $listing->url_slug }}" />
    <meta itemprop="price" content="{{ $listing->price_decimal }}" />
    <meta itemprop="priceCurrency" content="{{ Config::get('settings.currency') }}" />
    <meta itemprop="availability" content="http://schema.org/InStock" />
    <meta itemprop="itemCondition" content="{{ $listing->condition == 5 ? 'http://schema.org/NewCondition' : 'http://schema.org/UsedCondition' }}" />
    <div itemprop="seller" itemscope itemtype="http://schema.org/Person" class="hidden">
      <meta itemprop="name" content="{{ $listing->user->name }}" />
      <meta itemprop="url" content="{{ $listing->user->url }}" />
    </div>
  </div>
@elseif($game->listings)
  {{-- All offers for a game --}}
  <div itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" class="hidden">
    <meta itemprop="offerCount" content="{{ $game->listings->count() }}" />
    <meta itemprop="priceCurrency" content="{{ Config::get('settings.currency') }}" />
    <meta itemprop="lowPrice" content="{{ $game->lowestPrice }}" />
    <meta itemprop="highPrice" content="{{ $game->highestPrice }}" />
  </div>
@endif
</div>
{{-- SEO End --}}

<div class="row no-space equal">
  <div class="offset-xs-3 col-xs-6 offset-sm-0 col-sm-4 col-md-3 offset-md-0 col-lg-3 col-xxl-2 {{ isset($listing) ? 'game-cover-sticky' : '' }}">

    {{-- Start Game Cover --}}
    <div style="border-radius: 5px 5px 5px 5px; overflow: hidden;">
    @include('frontend.game.inc.cover')
  </div>
    {{-- End Game Cover --}}
    @if (isset($extra_images))
    <div class="grid">
      @foreach ($extra_images as $image)
      <div class="grid-item">
          <div class="overlay hvr-grow-shadow3">
            <a class="game-gallery" href="{{ $image }}" data-source="{{ $image }}" data-effect="mfp-zoom-in">
                <img class="lazy overlay-figure" src="{{ $image }}"
                alt="...">
                <div class="imgDescription"><div class="valign"><i class="fa fa-expand" aria-hidden="true"></i></div></div>
            </a>
          </div>
      </div>
      @endforeach
    </div>
    @endif

      <div class="rating-cushion">
          <div class="text-center">
              <div>
                @if($game->average_rating)
                  <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="{{ucfirst($game->type)." Rating"}}">
                      <span class="fa-stack fa-2x" style="margin-right: 5px">
                        <span class="fa fa-star fa-stack-2x fa-lg icon-rating" style="color: orange"></span>
                        <span class="fa-stack-1x avg-rating-number">{{$game->average_rating}}</span>
                      </span>
                  </a>
                @endif
                @if($game->type == 'game')
                    @if($game->average_difficulty)
                        <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="Difficulty Rating">
                            <span class="fa-stack fa-2x">
                                <span class="fa fa-shield fa-stack-2x fa-lg icon-rating" style="color: crimson; top: 5px"></span>
                                <span class="fa-stack-1x avg-rating-number" style="left: 0px !important;">{{$game->average_difficulty}}</span>
                            </span>
                        </a>
                    @endif
                    @if($game->average_duration)
                        <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="Duration">
                            <span class="fa-stack fa-2x">
                                <span class="fa fa-stopwatch fa-stack-2x fa-lg icon-rating" style="color: royalblue"></span>
                                {{-- Add blank space with background to fill icon --}}
                                <span style="background: royalblue">&nbsp;&nbsp;</span>
                                <span class="fa-stack-1x avg-rating-number" style="left: 0px !important; font-size: 16px">{{$game->average_duration->formatted}}</span>
                            </span>
                        </a>
                    @endif
                @endif
              </div>
            <br/>
            <div class="ratings-user-count">
                <a href="javascript:void(0);" data-toggle="modal" data-target="#community-ratings" class="btn btn-round"><span id="rating-count">{{count($game->ratings)}}</span>&nbsp;<span id="rating-text">Rating{{count($game->ratings) == 1 ? "" : "s"}}</span></a>
            </div>
          </div>
          <div class="age-rating-container">
              @if($game->esrb && session('region.code') == 'ntsc_u')
                  <img src="{{$game->esrbUrl()}}" class="age-rating-img">
              @endif
              @if($game->pegi && session('region.code') == 'pal')
                  <img src="{{$game->pegiUrl()}}" class="age-rating-img">
              @endif
          </div>
          <br/>
          <div style="text-align: center; font-weight: bold; font-size: 16px; color: white;">
              @if(isset($game->player_count) && $game->player_count == 1)
                  <i class="fa fa-user" style="margin-right: 5px"></i>1 Player Game
              @elseif(isset($game->player_count) && $game->player_count == 2)
                  <i class="fa fa-user-friends" style="margin-right: 5px"></i>1 - 2 Player Game
              @elseif(isset($game->player_count) && $game->player_count >= 3)
                  <i class="fa fa-users" style="margin-right: 5px"></i>1 - {{$game->player_count}} Player Game
              @endif

              @if($game->multiplayer_local)
                  <div>
                      <i class="fa fa-couch" style="margin-right: 5px"></i> 1 - {{$game->multiplayer_local}} Players Local
                  </div>
              @endif
              @if($game->multiplayer_lan)
                  <div>
                      <i class="fas fa-project-diagram" style="margin-right: 5px"></i>
                      @if($game->multiplayer_lan <= 2)
                          2
                      @else
                          2 - {{$game->multiplayer_lan}}
                      @endif
                      Players LAN
                  </div>
              @endif
              @if($game->multiplayer_online)
                  <div>
                      <i class="fas fa-globe" style="margin-right: 5px"></i>
                      @if($game->multiplayer_online <= 2)
                          2
                      @else
                          2 - {{$game->multiplayer_online}}
                      @endif
                      @if($game->multiplayer_online_no_limit)
                          or More
                      @endif
                      Players Online
                  </div>
              @endif
          </div>
          @if(auth()->user() && request()->segment('1') != 'listings')
              <h4 class="text-center">
                  Your Ratings
              </h4>
              <div class="text-center">
                  <div>{{ucfirst($game->type)}} Rating <i class="fa fa-trash delete-rating delete-game-rating" onclick="delete_rating()"></i></div>
                  <div id="rating-container">
                      <span id="user-rating" class="user-rating"></span>
                  </div>
              </div>
              @if($game->type == 'game')
              <div class="text-center">
                  <div>Difficulty <i class="fa fa-trash delete-rating delete-difficulty-rating" onclick="delete_difficulty()"></i></div>
                  <div id="difficulty-container">
                      <span id="user-difficulty" class="user-difficulty"></span>
                  </div>
              </div>
              <div class="text-center">
                  <div>Duration <i class="fa fa-trash delete-rating delete-duration-rating" onclick="delete_duration()"></i></div>
                  <div>
                      <label for="hour-rating" class="hour-label">Hours</label>
                      <input id="hour-rating" type="number" class="hour-rating dark-input" name="hour-rating" min="0" max="999"
                         value="{{isset($user_rating->duration) ? floor($user_rating->duration / 60) : null}}"
                      >
                  </div>
                  <div>
                      <label for="minute-rating" class="minute-label">Minutes</label>
                      <input id="minute-rating" type="number" class="minute-rating dark-input" name="minute-rating" min="0" max="59"
                         value="{{isset($user_rating->duration) ? $user_rating->duration % 60 : null}}"
                      >

                  </div>
              </div>
              @endif
          @endif
      </div>
    {{-- Show buttons only on big screens --}}
    <div class="hidden-sm-down">
      {{-- Buttons for listing --}}
      @if(isset($listing))
        {{-- Start Buy Button --}}
        @if($listing->sell)
          <a href="javascript:void(0);" data-toggle="modal" data-target="{{ Auth::check() ? '#modal-buy' : '#LoginModal' }}" class="buy-button m-b-10 @if(!isset($game->metacritic) || (isset($game->metacritic) && !($game->metacritic->score || $game->metacritic->userscore))) m-t-10 @endif flex-center-space">
            <i class="icon fa fa-shopping-cart" aria-hidden="true"></i>
            <span class="text">{{ $listing->getPrice() }}</span>
            {{-- Check if user allow price suggestions --}}
            @if($listing->sell_negotiate)
              <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
            @else
              <span></span>
            @endif
          </a>
        @endif
        {{-- End Buy Button --}}

        {{-- Start Trade Button --}}
        @if($listing->trade)
          <a href="javascript:void(0);" class="trade-button m-b-10 {{ $listing->sell ? '' : 'm-t-20'}} flex-center-space" @if($listing->trade_negotiate && !isset($trade_list)) data-toggle="modal" data-target="{{ Auth::check() ? '#modal-trade_suggestion' : '#LoginModal' }}" @else id="trade-button-subheader" @endif>
            <i class="icon fa fa-exchange" aria-hidden="true"></i><span class="text">{{ trans('listings.general.trade') }}</span>
            {{-- Check if user allow trade suggestions --}}
            @if($listing->trade_negotiate)
              <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
            @else
              <span></span>
            @endif
          </a>
        @endif
        {{-- End Trade Button --}}
        {{-- Send Message Button --}}
        {{-- Check if logged in user is listing user --}}
        @if(!(Auth::check() && Auth::user()->id == $listing->user_id))
          <div class="m-t-10">
            <a class="message-button btn-dark flex-center-space" href="javascript:void(0)" data-toggle="modal" data-target="{{ Auth::check() ? '#NewMessage' : '#LoginModal' }}"><i class="icon fas fa-envelope-open m-r-5"></i>{{ trans('messenger.send_message') }}<span></span></a>
          </div>
        @endif
        {{-- End Message Button --}}

      @else
        {{-- Load Buy Button Ref Link --}}
        @if(config('settings.buy_button_ref'))
          @include('frontend.ads.buyref')
        @endif
        {{-- Available on different platforms --}}
        @if(isset($different_platforms) && count($different_platforms)>0)
          {{-- Platform list --}}
          <h3 class="text-center">{{trans('general.also_available_on')}}</h3>
          <div class="glist">
            @foreach($different_platforms as $different_platform)
            <a href="{{ $different_platform->url_slug }}" >
              <div onMouseOver="this.style.backgroundColor='{{ $different_platform->platform->color }}'" onMouseOut="this.style.backgroundColor=''" class="gitem @if($loop->first && !config('settings.buy_button_ref') && (isset($game->metacritic) && !$game->metacritic->score && !$game->metacritic->userscore)) m-t-20 @endif" style="border: 2px solid {{$different_platform->platform->color}};">
                {{-- Check if platform logo setting is enabled --}}
                  @if(isset($different_platform->platform->cover_image))
                    <img src="{{ $different_platform->platform->cover_image }}" alt="{{$different_platform->platform->name}} Logo">
                  @else
                    <span>{{$different_platform->platform->name}}</span>
                  @endif
              </div>
            </a>
            @endforeach
          </div>
        @endif

        {{-- The 'other_platforms' field only in use by hardware, no links (single product) --}}
        @if (isset($other_platforms) && count($other_platforms) > 0)
          <div class="glist">
            <h5>Also available for...</h5>
            @foreach ($other_platforms as $other_platform)
              <div onMouseOver="this.style.backgroundColor='{{ $other_platform->color }}'" onMouseOut="this.style.backgroundColor=''" class="gitem @if($loop->first) m-t-20 @endif" style="border: 2px solid {{$other_platform->color}};">
                {{-- Check if platform logo setting is enabled --}}
                @if( config('settings.platform_logo') && file_exists(public_path().'/logos/' . $other_platform->acronym . '_tiny.png'))
                  <img src="{{ asset('logos/' . $other_platform->acronym . '_tiny.png/') }}" alt="{{$other_platform->name}} Logo">
                @else
                  <span>{{$other_platform->name}}</span>
                @endif
              </div>
            @endforeach
          </div>
        @endif

        <div class="gsummary">
          {!! $game->description !!}
        </div>
      @endif
    </div>
  </div>


  <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9 col-xxl-10">

    {{-- Start Game Details --}}
    <div class="game-details flex-center-space">
      <div class="ginfo">

        {{-- Game title with release year --}}
        <div class="flex-center-space">
          <div class="gtitle">
            @php $code = session('region.code') @endphp
            <span class="game-info" style="padding-right: 5px">{{$game->name}}</span> @if(isset($game->$code))<span class="release-year">{{date('Y', strtotime($game->$code))}}</span>@endif
          </div>
          @if($game->heartbeat->count() > 0)
          {{-- Game heartbeat --}}
          <div class="heartbeat">
            <i class="fas fa-heartbeat"></i> {{ $game->heartbeat->count() }}
          </div>
          @endif
        </div>
        {{-- Buttons related to the game --}}
        <div class="gbuttons">
          {{-- Wishlist button --}}
          @if(!isset($game->wishlist))
            <a href="javascript:void(0);" data-toggle="modal" data-target="{{ Auth::check() ? '#AddWishlist' : '#LoginModal' }}" class="btn btn-round"><i class="fas fa-heart"></i> {{ trans('wishlist.add_wishlist') }}</a>
          {{-- On your wishlist with delete button --}}
          @else
            <a href="javascript:void(0);" data-toggle="modal" data-target="#EditWishlist_{{$game->wishlist->id}}" class="on-wishlist"><i class="fas fa-heart"></i> {{ trans('wishlist.on_wishlist') }}</a><a href="{{ $game->url_slug }}/wishlist/delete" class="btn btn-round delete-wishlist">{{ trans('general.delete') }}</a>
          @endif
          <span style="padding-left:2px">
            @if(Auth::check() && isset($game->collection[0]))
                  <a href="javascript:void(0);" data-toggle="modal" data-target="#EditHavelist_{{$game->collection[0]->id}}" class="on-wishlist"><i class="fas fa-clipboard-list"></i> Edit Collection</a><a href="{{ $game->url_slug }}/havelist/delete" class="btn btn-round delete-wishlist">{{ trans('general.delete') }}</a>
            @elseif(Auth::check())
                  <a href="javascript:void(0);" data-toggle="modal" data-target="#AddHavelist" class="btn btn-round"><i class="fas fa-clipboard-list"></i> Add to Collection</a>
            @else
                <a href="javascript:void(0);" data-toggle="modal" data-target="#LoginModal" class="btn btn-round"><i class="fas fa-clipboard-list"></i> Add to Collection</a>
            @endif
          </span>
          <span style="padding-left:2px">
          @if($game->type == 'game')
            @if(Auth::check() && isset($game->completedlist))
                <a href="javascript:void(0);" data-toggle="modal" class="on-wishlist"><i class="fas fa-clipboard-check"></i> Game I've Completed</a><a href="{{ $game->url_slug }}/completedlist/delete" class="btn btn-round delete-wishlist">{{ trans('general.delete') }}</a>
            @elseif(Auth::check())
                <a href="{{ $game->url_slug }}/completedlist/add"  class="btn btn-round"><i class="fas fa-clipboard-check"></i> Add to Games I've Completed</a>
            @else
                <a href="javascript:void(0);" data-toggle="modal" data-target="#LoginModal" class="btn btn-round"><i class="fas fa-clipboard-list"></i> Add to Games I've Completed</a>
            @endif
          @endif
          </span>
          {{-- Go to gameoverview button --}}
          @if(isset($listing))
          <a href="{{ $game->url_slug }}" class="btn btn-round m-l-5"><i class="fas fa-gamepad"></i><span class="hidden-xs-down"> {{ trans('listings.overview.subheader.go_gameoverview') }}</a></span>
          @endif
        </div>
        @if(request()->segment(1) == 'games')
        <div class="game-info-section">
            <span class="game-info-label">{{trans('games.filters.genres')}}: </span>
            @foreach($game->genres as $genre)
                <span class="game-info">{{$genre->name}}</span><span class="info-wrap">@if(!$loop->last), @endif</span>
            @endforeach
        </div>
        @endif
        @if(request()->segment(1) == 'games' || isset($game->components))
        <div class="game-info-section">
            <span class="game-info-label">{{trans('listings.general.components')}}: </span>
            @if(request()->segment(1) == 'games')
                @foreach (config('components.all') as $component)
                    @if($game->$component == true)
                        <span class="game-info"><i class="fa fa-check"></i> {{ trans("games.components.$component") }}</span><span class="info-wrap">&nbsp;</span>
                    @endif
                    @if($game->$component === 0)
                        <span class="game-info"><i class="fa fa-times"></i> {{ trans("games.components.$component") }}</span><span class="info-wrap">&nbsp;</span>
                    @endif
                @endforeach
            @endif
            @if(isset($game->components))
                @foreach($game->components as $component)
                    <span class="game-info"><i class="fa fa-check"></i> {{ $component->name }}</span><span class="info-wrap">&nbsp;</span>
                @endforeach
            @endif
        </div>
        @endif
        @if(request()->segment(1) == 'games')
          <div class="game-info-section" style="margin-bottom: 3px">
                  @if(isset($game->developers))
                  <span class="game-info-label">Developers: </span>
                  @foreach($game->developers as $developer)
                      <a href="/games?d={{$developer->id}}"><span class="game-info game-info-link">{{$developer->name}}</span></a><span class="info-wrap">@if(!$loop->last), @endif</span>
                  @endforeach
              @endif
              @if(isset($game->{"publishers".ucfirst(regionCode(session('region.code'), '_', 'lower', 'code_to_country'))}))
                  <span class="game-info-label" style="margin-left: 20px">Publishers: </span>
                  @foreach($game->{"publishers".ucfirst(regionCode(session('region.code'), '_', 'lower', 'code_to_country'))} as $publisher)
                      <a href="/games?pu={{$publisher->id}}"><span class="game-info game-info-link">{{$publisher->name}}</span></a><span class="info-wrap">@if(!$loop->last), @endif</span>
                  @endforeach
              @endif
          </div>
        @endif
        <div class="hidden-md-up">
          {{-- Buttons for listing --}}
          @if(isset($listing))
            <div class="flex-center-space">
              {{-- Start Buy Button --}}
              @if($listing->sell)
              <div class="button-fix m-t-20 {{ $listing->trade ? 'm-r-5' : ''}}">
                  <a href="javascript:void(0);" data-toggle="modal" data-target="{{ Auth::check() ? '#modal-buy' : '#LoginModal' }}" class="buy-button flex-center-space">
                    <i class="icon fa fa-shopping-cart" aria-hidden="true"></i>
                    <span class="text">{{ $listing->getPrice() }}</span>
                    {{-- Check if user allow price suggestions --}}
                    @if($listing->sell_negotiate)
                      <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
                    @else
                      <span></span>
                    @endif
                  </a>
              </div>
              @endif
              {{-- End Buy Button --}}
              {{-- Start Trade Button --}}
              @if($listing->trade)
              <div class="button-fix m-t-20 {{ $listing->sell ? 'm-l-5' : ''}}">
                  <a href="javascript:void(0);" class="trade-button flex-center-space" @if($listing->trade_negotiate && !isset($trade_list)) data-toggle="modal" data-target="{{ Auth::check() ? '#modal-trade_suggestion' : '#LoginModal' }}" @else id="trade-button-subheader-mobile" @endif>
                    <i class="icon fa fa-exchange" aria-hidden="true"></i><span class="text">{{ trans('listings.general.trade') }}</span>
                    {{-- Check if user allow trade suggestions --}}
                    @if($listing->trade_negotiate)
                      <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
                    @else
                      <span></span>
                    @endif
                  </a>
              </div>
              @endif
              {{-- End Trade Button --}}
            </div>

            {{-- Send Message Button --}}
            {{-- Check if logged in user is listing user --}}
            @if(!(Auth::check() && Auth::user()->id == $listing->user_id))
              <div class="m-t-10">
                <a class="message-button btn-dark flex-center-space" href="javascript:void(0)" data-toggle="modal" data-target="{{ Auth::check() ? '#NewMessage' : '#LoginModal' }}"><i class="icon fas fa-envelope-open m-r-5"></i>{{ trans('messenger.send_message') }}<span></span></a>
              </div>
            @endif
            {{-- End Message Button --}}
          @else
            <div class="gsummary m-b-10">
              {!! $game->description !!}
            </div>
            {{-- Load Buy Button Ref Link --}}
            @if(config('settings.buy_button_ref'))
              @include('frontend.ads.buyref')
            @endif
            {{-- Available on different platforms --}}
            @if(isset($different_platforms) && count($different_platforms)>0)
              {{-- Platform list --}}
              <div class="glist">
                @foreach($different_platforms as $different_platform)
                <a href="{{ $different_platform->url_slug }}" >
                  <div onMouseOver="this.style.backgroundColor='{{ $different_platform->platform->color }}'" onMouseOut="this.style.backgroundColor=''" class="gitem" style="border: 2px solid {{$different_platform->platform->color}};">
                    {{-- Check if platform logo setting is enabled --}}
                    @if( config('settings.platform_logo') )
                      <img src="{{ asset('logos/' . $different_platform->platform->acronym . '_tiny.png/') }}" alt="{{$different_platform->platform->name}} Logo">
                    @else
                      <span>{{$different_platform->platform->name}}</span>
                    @endif
                  </div>
                </a>
                @endforeach
              </div>
            @endif
          @endif
        </div>
      </div>
    </div>
    {{-- End Game Details --}}
      {{-- Start Ratings Modal --}}
      <div class="modal fade modal-fade-in-scale-up modal-buy" id="community-ratings" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <div class="background-pattern"></div>
                      <div class="background-color"></div>
                      <div class="title">
                          <button type="button" class="close" data-dismiss="modal">
                              <span aria-hidden="true">×</span><span class="sr-only">{{ trans('listings.modal.close') }}</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel">
                              <i class="fa fa-star m-r-5" aria-hidden="true"></i>
                              Ratings
                          </h4>
                      </div>
                  </div>
                  <div class="modal-body">
                      <table id="ratings-table">
                          <thead style="border-bottom: white solid 1px">
                              <th style="width: 20%">
                                  User
                              </th>
                              <th style="width: 20%">
                                  Rating
                              </th>
                              @if($game->type == 'game')
                              <th style="width: 20%">
                                  Difficulty
                              </th>
                              <th style="width: 20%">
                                  Duration
                              </th>
                              @endif
                              <th style="width: 20%">
                                  Date
                              </th>
                          </thead>
                          <tbody id="ratings-tbody">
                          @foreach($game->ratings as $rating)
                          <tr id="rating-user-{{$rating->user->name}}">
                              <td>
                                  @if(isset($rating->status) && $rating->status == "friend")
                                      <i class='fa fa-user-friends'></i>
                                  @endif
                                  {{$rating->user->name}}
                              </td>
                              <td>
                                <i class="fa fa-star rating-star-color"></i>{{$rating->rating}}
                              </td>
                              @if($game->type == 'game')
                              <td>
                                  <i class="fa fa-star rating-difficulty-color"></i>{{$rating->difficulty}}
                              </td>
                              <td>
                                  <i class="fa fa-stopwatch rating-duration-color"></i>{{ is_null($rating->duration) ? null : floor($rating->duration / 60).":".date('i', mktime(0, $rating->duration)) }}
                              </td>
                              @endif
                              <td>
                                  {{date("m/d/Y", strtotime($rating->created_at))}}
                              </td>
                          </tr>
                          @endforeach
                          </tbody>
                      </table>
                  </div>
          </div>
      </div>
      {{-- End Ratings Modal --}}
  </div>
</div>
@stop
