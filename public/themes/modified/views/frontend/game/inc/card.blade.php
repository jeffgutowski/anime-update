{{-- START GAME --}}
<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-20">
  {{-- Start Game Cover --}}
  <div class="card game-cover-wrapper hvr-grow-shadow" style="margin-bottom: 5px; max-height: 203px; overflow:hidden">
    {{-- Show "New!" label if item or price is not older than 1 day --}}
    @if(Carbon\Carbon::now()->subDays(1) < $game->created_at && false) {{-- New Label is broken for many things --}}
      <div class="item-new {{ $game->cover_generator ? 'with-platform' : ''  }}">{{ trans('listings.general.new') }}</div>
    @endif

    <a href="{{ $game->url_slug }}">

      {{-- Check if game is on the wishlist --}}
      @if(Auth::check())
        {{-- Check if game id is in wishlist of user --}}
        @if(Auth::user()->wishlists()->contains('game_id', $game->id))
          {{-- (Heart icon) On your Wishlist --}}
          <div class="on-wishlist {{ $game->cover_generator ? 'with-platform' : ''  }} {{ Carbon\Carbon::now()->subDays(1) < $game->created_at ? 'with-new' : ''  }}">
            <i class="fas fa-heart"></i> {{ trans('wishlist.on_wishlist') }}
          </div>
        @endif
      @endif

      {{-- Generated game cover with platform on top --}}
      <div style="border-radius: 5px 5px 0px 0px; overflow: hidden;">
      @include('frontend.game.inc.cover')
      </div>
      {{-- Item name --}}
      @if($game->image_cover)
      <div class="item-name">
        {{ $game->name }}
      </div>
      @endif
    </a>
  </div>
  {{-- End Game Cover --}}

  <div class="listing-details flex-center-space">
    @if($game->listingsCount > 0 || $game->wishlistCount > 0)
    <div class="listing-active-wrapper">
      @if($game->listingsCount > 0)
      <div class="listing-active @if($game->wishlistCount > 0) with-game-popularity @endif">
          <i class="fa {{ $game->listingsCount == 1 ? 'fa-tag' : 'fa-tags' }}"></i> {{ $game->listingsCount }}
      </div>
      @endif
{{-- Commenting out wishlist count because it won't fit all ratings
      @if($game->wishlistCount > 0)
      <div class="game-popularity @if($game->listingsCount > 0) with-listing-active @endif">
        <i class="fa fa-heartbeat"></i> {{ $game->wishlistCount }}
      </div>
      @endif
--}}
    </div>
    @else
    <div></div>
    @endif
    @if($game->average_rating || $game->average_difficulty || $game->average_duration)
      <div class="game-popularity">
        @if(request('rs') == 'my' ? $game->rating : $game->average_rating)
          <a href="#" class="condition-tip ratings-details-sm" data-toggle="tooltip" data-placement="right" title="{{ucfirst($game->type)." Rating"}}">
            <span class="fa-stack fa-1x">
              <span class="fa fa-star fa-stack-1x fa-lg icon-rating" style="color: orange"></span>
              <span class="fa-stack-1x avg-rating-number-sm">
                  {{ request('rs') == 'my' ? $game->rating : $game->average_rating }}
              </span>
            </span>
          </a>
        @endif
        @if($game->type == 'game')
          @if(request('rs') == 'my' ? $game->difficulty : $game->average_difficulty)
            <a href="#" class="condition-tip ratings-details-sm" data-toggle="tooltip" data-placement="right" title="Difficulty Rating">
              <span class="fa-stack fa-1x">
                  <span class="fa fa-shield fa-stack-1x fa-lg icon-rating" style="color: crimson"></span>
                  <span class="fa-stack-1x avg-rating-number-sm">
                        {{ request('rs') == 'my' ? $game->difficulty : $game->average_difficulty }}
                  </span>
              </span>
            </a>
          @endif
          @if(request('rs') == 'my' ? $game->duration : $game->average_duration)
            <a href="#" class="condition-tip ratings-details-sm" data-toggle="tooltip" data-placement="right" title="Duration" style="margin-left: -5px; margin-right: -5px">
              <span class="fa-stack fa-1x">
                  <span class="fa fa-stopwatch fa-stack-1x fa-lg icon-rating" style="color: royalblue"></span>
                  <span class="fa-stack-1x avg-rating-number-sm">
                      {{ request('rs') == 'my' ? floor($game->duration / 60) : $game->average_duration->hours }}
                  </span>
              </span>
            </a>
          @endif
        @endif
      </div>
    @endif
  </div>

</div>
{{-- End GAME --}}
