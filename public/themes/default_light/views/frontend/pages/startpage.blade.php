@extends(Theme::getLayout())

@section('subheader')

  <div style="position: relative">

    <div class="page-top-background @if(config('settings.frontpage_carousel')) with-carousel @endif">
      <div class="background-overlay" id="parallax"></div>
    </div>

  </div>

  {{-- Load carousel --}}
  @if(config('settings.frontpage_carousel'))
    @include('default_light::frontend.pages.inc.slider')
  @endif



@stop

@section('content')

{{-- Load Google AdSense --}}
@if(config('settings.google_adsense'))
  @include('frontend.ads.google')
@endif

  {{-- Title "Newest Listings" --}}
  <div class="panels-title border-bottom flex-center-space">
    {{-- Title with active listings count --}}
    <div>
      <i class="fa fa-tags" aria-hidden="true"></i> {{ trans('listings.general.newest_listings') }}
    </div>
    {{-- Show all link --}}
    <div>
      @if(count($listings) == 24)
      <a href="{{ url('listings') }}" class="btn btn-dark f-w-500"><i class="fa fa-angle-right m-r-5" aria-hidden="true"></i><span class="hidden-xs-down">{{ trans('users.dash.show_all') }}</span></a>
      @endif
    </div>
  </div>

  {{-- START LISTINGS --}}
  <div class="row">

    @forelse($listings as $listing)
    {{-- START GAME --}}
    <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-20">

      {{-- Start Game Cover --}}
      <div class="card game-cover-wrapper hvr-grow-shadow"  style="margin-bottom: 0px;">
        {{-- Show "New!" label if item or price is not older than 1 day --}}
        @if(Carbon\Carbon::now()->subDays(1) < $listing->created_at )
          <div class="item-new {{ $listing->game->cover_generator ? 'with-platform' : ''  }}">{{ trans('listings.general.new') }}</div>
        @endif
        {{-- Pacman Loader for background image - show only when cover exists --}}
        @if($listing->game->image_cover)
          {{--
        <div class="loader pacman-loader cover-loader"></div> --}}
        {{-- Show game name, when no cover exist --}}
        @else
        <div class="no-cover-name">{{$listing->game->name}}</div>
        @endif

        <a href="{{ $listing->url_slug }}">

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

          {{-- Pickup icon --}}
          @if($listing->pickup)
          <div class="pickup-icon {{ $listing->digital ? 'with-digital' : '' }} {{ $listing->payment ? 'with-payment' : '' }}">
            <i class="far fa-handshake" aria-hidden="true"></i>
          </div>
          @endif

          {{-- Delivery icon --}}
          @if($listing->delivery)
          <div class="delivery-icon {{ $listing->pickup ? 'with-pickup' : '' }} {{ $listing->digital ? 'with-digital' : '' }} {{ $listing->payment ? 'with-payment' : '' }}">
            <i class="fa fa-truck" aria-hidden="true"></i>
          </div>
          @endif

          {{-- Generated game cover with platform on top --}}
          @if($listing->game->cover_generator)
            <div class="lazy game-cover gen"  data-original="{{$listing->game->image_cover}}"></div>
            <div class="game-platform-gen" style="background-color: {{$listing->game->platform->color}}; text-align: {{$listing->game->platform->cover_position}};">
              {{-- Check if platform logo setting is enabled --}}
              @if( config('settings.platform_logo') )
                <img src="{{ asset('logos/' . $listing->game->platform->acronym . '_tiny.png/') }}" alt="{{$listing->game->platform->name}} Logo">
              @else
                <span>{{$listing->game->platform->name}}</span>
              @endif
            </div>
          {{-- Normal game cover --}}
          @else
            <div class="lazy game-cover"  data-original="{{$listing->game->image_cover}}"></div>
          @endif
          {{-- Item name --}}
          @if($listing->game->image_cover)
          <div class="item-name">
            {{ $listing->game->name }} @if($listing->limited_edition)<span><i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}<span>@endif
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


      <div class="listing-details flex-center-space" style="margin-top: 5px;">
        @if($listing->sell)
        <div class="listing-price">
          {{ $listing->getPrice() }}
        </div>
        @else
        <div>
        </div>
        @endif
        @if($listing->trade)
        <div class="listing-trade @if($listing->sell) with-price @endif" class="no-flex-shrink">
            <i class="fa fa-exchange"></i>
        </div>
        @endif
      </div>

      {{-- Start User info --}}
      <div class="game-user-details" style="margin-top: 0px !important; ">
        {{-- Distance --}}
        @if($listing->distance !== false)
        <span class="distance">
          <i class="fa fa-location-arrow" aria-hidden="true"></i> {{$listing->distance}} {{config('settings.distance_unit')}}
        </span>
        @endif
        {{-- User avtar and name --}}
        <a href="{{ $listing->user->url }}" class="user-link">
          <span class="avatar avatar-xs @if($listing->user->isOnline()) avatar-online @else avatar-offline @endif">
            <img src="{{$listing->user->avatar_square_tiny}}" alt="{{$listing->user->name}}'s Avatar"><i></i>
          </span>
          {{$listing->user->name}}
        </a>
      </div>
      {{-- End User info --}}
    </div>
    {{-- End GAME --}}
    @empty
      {{-- Start empty list message --}}
      <div class="empty-list add-button">
        {{-- Icon --}}
        <div class="icon">
          <i class="far fa-frown" aria-hidden="true"></i>
        </div>
        {{-- Text --}}
        <div class="text">
          {{ trans('listings.general.no_listings') }}
        </div>
        {{-- Create listing button --}}
        @if(Auth::check())
        <a href="{{ url('listings/add' ) }}" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
        @else
        <a href="javascript:void(0);" data-toggle="modal" data-target="#LoginModal" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
        @endif
      </div>
      {{-- End empty list message --}}
    @endforelse


    {{-- Show more link on bottom --}}
    @if(count($listings) == 24)
      <div class="text-center m-b-10 m-t-10">
        <a href="{{ url('listings') }}" class="btn btn-dark f-w-500">{{ trans('listings.general.show_all') }}</a>
      </div>
    @endif

  </div>
  {{-- END LISTINGS --}}


@stop

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('home') !!}
@endsection
{{-- End Breadcrumbs --}}

@section('after-scripts')
    <script>
      $(document).ready(function(){

        {{-- Platform filter --}}
        $('.platform-filter').click(function(e) {
            e.preventDefault();
            $(this).toggleClass('platform-filter-active')
            if ($(this).hasClass('platform-filter-active')) {
                $(this).css('background-color', $(this).data('color') );
            } else {
                $(this).css('background-color', '');
            }
        });

        {{-- Option filter --}}
        $('.option-filter').click(function(e) {
            e.preventDefault();
            $(this).toggleClass('option-filter-active')
        });

        {{-- Submit filter options --}}
        $('#filter-submit').click(function(e) {
            e.preventDefault();
            $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
            $(this).addClass('loading');
            {{-- Collect all active platform ids --}}
            var platform_ids = [];
            $('.platform-filter-active').each(function() {
                platform_ids.push($(this).data("id"))
            });
            {{-- Collect all active options --}}
            var options = [];
            $('.option-filter-active').each(function() {
                options.push($(this).data("filter"))
            });
            $.ajax({
                url:'{{ url("listings/filter") }}',
                type: 'POST',
                data: {platformIds:platform_ids, options: options},
                {{-- Send CSRF Token over ajax --}}
                headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                success: function (data) {
                    window.location=data;
                }
            });
        });

        {{-- Order by change URL --}}
        $('#order_by').change(function () {
            var goToUrl = $(this).val();
            window.location.replace(goToUrl);
            window.location.href = goToUrl;
        });

        {{-- Load carousel JS Settings --}}
        @if(config('settings.frontpage_carousel'))
        $(".owl-carousel").on('initialize.owl.carousel',function(){
            $(".owl-carousel").addClass('carousel-loaded');
        });

        $(".owl-carousel").owlCarousel({
                autoplay: true,
                nav:false,
                dots:false,
                lazyLoad: true,
                loop: true,
                items : 4, //4 items above 1000px browser width
                responsive:{
                    0:{
                        items:1
                    },
                    500:{
                        items:2
                    },
                    900:{
                        items:3
                    },
                    1100:{
                        items:4
                    },
                    1500:{
                        items:5
                    }
                }
        });
        @endif
      });
    </script>
@stop
