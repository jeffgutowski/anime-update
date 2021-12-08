{{-- START GAME --}}
<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-20">
  {{-- Start Game Cover --}}
  <div class="card game-cover-wrapper hvr-grow-shadow"  style="margin-bottom: 0px;">
    {{-- Show "New!" label if item or price is not older than 1 day --}}
    @if(Carbon\Carbon::now()->subDays(1) < $listing->created_at )
      <div class="item-new {{ $listing->product->cover_generator ? 'with-platform' : ''  }}">{{ trans('listings.general.new') }}</div>
    @endif
    {{-- Pacman Loader for background image - show only when cover exists --}}

  @if($listing->product->image_cover)
      {{--
    <div class="loader pacman-loader cover-loader"></div> --}}
    {{-- Show game name, when no cover exist --}}
    @else
      <div class="no-cover-name">{{$listing->product->name}}</div>
    @endif

    <a href="{{ $listing->url_slug }}">

      {{-- Check if game is on the wishlist --}}
      @if(Auth::check())
        {{-- Check if game id is in wishlist of user --}}
        @if(Auth::user()->wishlists()->contains('game_id', $listing->product->id))
          {{-- (Heart icon) On your Wishlist --}}
          <div class="on-wishlist {{ $listing->product->cover_generator ? 'with-platform' : ''  }} {{ Carbon\Carbon::now()->subDays(1) < $listing->created_at ? 'with-new' : ''  }}">
            <i class="fas fa-heart"></i> {{ trans('wishlist.on_wishlist') }}
          </div>
        @endif
      @endif

      {{-- Payment icon --}}
      @if($listing->payment)
      <div class="animation-scale-up payment-enabled">
        <i class="fa fa-shield-check" aria-hidden="true"></i>
      </div>
      @endif

      {{-- Digital download icon --}}
      @if($listing->digital)
      <div class="animation-scale-up digital-download {{ $listing->payment ? 'with-payment' : '' }}">
        <i class="fa fa-download" aria-hidden="true"></i>
      </div>
      @endif

      {{-- Generated game cover with platform on top --}}
      @if($listing->product->cover_generator)
        <div class="lazy game-cover gen"  data-original="{{$listing->product->image_cover}}"></div>
        <div class="game-platform-gen" style="background-color: {{$listing->product->platform->color}}; text-align: {{$listing->product->platform->cover_position}};">
          {{-- Check if platform logo setting is enabled --}}
          @if( config('settings.platform_logo') )
            <img src="{{ $listing->product->platform->cover_image }}" alt="{{$listing->product->platform->name}} Logo">
          @else
            <span>{{$listing->product->platform->name}}</span>
          @endif
        </div>
      {{-- Normal game cover --}}
      @else
        <div class="lazy game-cover"  data-original="{{$listing->product->image_cover}}"></div>
      @endif
      {{-- Item name --}}
      @if($listing->product->image_cover)
      <div class="item-name">
        {{ $listing->product->name }} @if($listing->limited_edition)<div><i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}</div>@endif
      </div>
      @elseif($listing->limited_edition)
      <div class="item-name">
        <i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}<span>
      </div>
      @endif
      @if($listing->picture)
      <div class="lazy item-image" data-original="{{ $listing->picture_square }}"></div>
      @endif
    </a>
  </div>
  {{-- End Game Cover --}}


  <div class="listing-details" style="margin-top: 5px;">
    @if($listing->sell)
      <div>
        <div class="wrapper-price">
          <div class="display-price listing-cost">
            @if($listing->game->platform->cartridge)
              <span class="game-media-price cartridge-price">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512">
                  <title>Game Retro Cartridge</title><path fill="#FFF" fill-rule="evenodd" d="M480,0 C497.673112,-3.24649801e-15 512,14.326888 512,32 L512,432 C512,440.836556 504.836556,448 496,448 C487.163444,448 480,455.163444 480,464 L480,480 C480,497.673112 465.673112,512 448,512 L64,512 C46.326888,512 32,497.673112 32,480 L32,464 C32,455.163444 24.836556,448 16,448 C7.163444,448 -9.08412536e-13,440.836556 -9.09494702e-13,432 L-9.09494702e-13,32 C-9.11659034e-13,14.326888 14.326888,3.24649801e-15 32,0 L480,0 Z M96,32 L32,32 L32,416 L64,416 L64,480 L448,480 L448,416 L480,416 L480,32 L448,32 L448,336 C448,344.836556 440.836556,352 432,352 L272,352 C263.163444,352 256,344.836556 256,336 L256,32 L192,32 L192,80 C192,88.836556 184.836556,96 176,96 L112,96 C103.163444,96 96,88.836556 96,80 L96,32 Z M320,416 C328.836556,416 336,423.163444 336,432 C336,440.836556 328.836556,448 320,448 L192,448 C183.163444,448 176,440.836556 176,432 C176,423.163444 183.163444,416 192,416 L320,416 Z M176,320 C184.836556,320 192,327.163444 192,336 C192,344.836556 184.836556,352 176,352 L112,352 C103.163444,352 96,344.836556 96,336 C96,327.163444 103.163444,320 112,320 L176,320 Z M416,256 L288,256 L288,304 C288,312.836556 295.163444,320 304,320 L304,320 L400,320 C408.836556,320 416,312.836556 416,304 L416,304 L416,256 Z M176,256 C184.836556,256 192,263.163444 192,272 C192,280.836556 184.836556,288 176,288 L112,288 C103.163444,288 96,280.836556 96,272 C96,263.163444 103.163444,256 112,256 L176,256 Z M176,192 C184.836556,192 192,199.163444 192,208 C192,216.836556 184.836556,224 176,224 L112,224 C103.163444,224 96,216.836556 96,208 C96,199.163444 103.163444,192 112,192 L176,192 Z M176,128 C184.836556,128 192,135.163444 192,144 C192,152.836556 184.836556,160 176,160 L112,160 C103.163444,160 96,152.836556 96,144 C96,135.163444 103.163444,128 112,128 L176,128 Z"/>
                </svg>
              </span>
            @elseif($listing->game->platform->disc)
              <span class="game-media-price"><i class="fas fa-compact-disc"></i></span>
            @endif
            {{$listing->getPrice()}}</div>
          <div class="display-price listing-shipping"><i class="fa fa-truck shipping-price"></i>
            @if($listing->delivery_price === 0)
              <span style="font-size: 14px; position: relative; bottom:2px">Free Shipping</span>
            @else
              + {{$listing->getDeliveryPrice()}}
            @endif
          </div>
        </div>
      </div>

    @else
      <div>
      </div>
    @endif

      @if((request('rs') == null && ($listing->product->average_rating || $listing->product->average_difficulty || $listing->product->average_duration)) || (request('rs') == 'my' && ($listing->rating || $listing->difficulty || $listing->duration)))
        <div class="game-popularity">
          @if($listing->product->average_rating)
            <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="{{ucfirst($listing->product->type)." Rating"}}" style="margin-left: -5px; margin-right: -5px">
            <span class="fa-stack fa-1x">
              <span class="fa fa-star fa-stack-1x fa-lg icon-rating" style="color: orange"></span>
              <span class="fa-stack-1x avg-rating-number-sm">{{ request('rs') == 'my' ? $listing->rating : $listing->product->average_rating }}</span>
            </span>
            </a>
          @endif
          @if($listing->product->type == 'game')
            @if($listing->product->average_difficulty)
              <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="Difficulty Rating" style="margin-left: -5px; margin-right: -5px">
              <span class="fa-stack fa-1x">
                  <span class="fa fa-star fa-stack-1x fa-lg icon-rating" style="color: crimson"></span>
                  <span class="fa-stack-1x avg-rating-number-sm">{{ request('rs') == 'my' ? $listing->difficulty : $listing->product->average_difficulty }}</span>
              </span>
              </a>
            @endif
            @if($listing->product->average_duration)
              <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="Duration" style="margin-left: -5px; margin-right: -5px">
              <span class="fa-stack fa-1x">
                  <span class="fa fa-stopwatch fa-stack-1x fa-lg icon-rating" style="color: royalblue"></span>
                  <span class="fa-stack-1x avg-rating-number-sm">{{ request('rs') == 'my' ? $listing->duration : $listing->product->average_duration->hours }}</span>
              </span>
              </a>
            @endif
          @endif
        </div>
      @endif
  </div>

  {{-- Start User info --}}
  <div class="game-user-details" style="margin-top: 0px !important; text-align: left">
    {{-- User avtar and name --}}
    <a href="{{ $listing->user->url }}" class="user-link">
      <span class="avatar avatar-xs @if($listing->user->isOnline()) avatar-online @else avatar-offline @endif">
        <img src="{{$listing->user->avatar_square_tiny}}" alt="{{$listing->user->name}}'s Avatar" style="max-height: 21px"><i></i>
      </span>
      {{$listing->user->name}}
    </a>
  </div>
  {{-- End User info --}}
</div>
{{-- End GAME --}}
