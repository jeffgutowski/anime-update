@extends(Theme::getLayout())

{{-- Add Game Subheader --}}
@include('frontend.game.subheader')

@section('content')
{{-- Content from Subheader --}}
@yield('game-content')
{{-- Start Subheader tabs --}}

<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 col-xxl-10">
  {{-- Start Item Content --}}
  <div class="item-content">
      <div style="margin-top: 50px">
          <h2 class="text-center"><i class="fa fa-tags"></i> Listings</h2>
          <hr style="border-color: grey;">
      </div>
    <div class="subheader-tabs-wrapper flex-center-space" hidden>
      {{-- Nav tabs --}}
      <div class="no-flex-shrink">
        <ul class="subheader-tabs" role="tablist">
          {{-- Listings tab --}}
          <li class="nav-item">
            <a data-toggle="tab" href="#listings" data-target="#listings" role="tab" class="subheader-link">
              <i class="fa fa-tags" aria-hidden="true"></i><span class="hidden-xs-down"> {{ trans('listings.general.listings') }}</span>
            </a>
          </li>
          {{-- Comments tab --}}
          @if(config('settings.comment_game'))
          <li class="nav-item">
            <a data-toggle="tab" href="#comments" data-target="#comments" role="tab" class="subheader-link">
              <i class="fa fa-comments" aria-hidden="true"></i><span class="{{ config('settings.comment_game') ? 'hidden-sm-down' : 'hidden-xs-down'}}"> {{ trans('comments.comments') }}</span>
            </a>
          </li>
          @endif
        </ul>
      </div>
      {{-- Share buttons --}}
      @if(false) {{-- Hide Social Media Buttons --}}
      <div @if(config('settings.comment_game')) class="subheader-social-comments" @endif>
        {{-- Facebook share --}}
        <a href="https://www.facebook.com/dialog/share?
    app_id={{config('settings.facebook_client_id')}}&display=popup&href={{URL::current()}}&redirect_uri={{ url('self.close.html')}}" onclick="window.open(this.href, 'facebookwindow','left=20,top=20,width=600,height=400,toolbar=0,resizable=1'); return false;" class="btn btn-icon btn-round btn-lg social-facebook m-r-5">
          <i class="icon fab fa-facebook-f" aria-hidden="true"></i>
        </a>
        {{-- Twitter share --}}
        <a href="http://twitter.com/intent/tweet?text={{trans('general.share.twitter_game', ['game_name' => $game->name, 'platform' => $game->platform->name, 'page_name' => config('settings.page_name')])}} &#8921; {{URL::current()}}" onclick="window.open(this.href, 'twitterwindow','left=20,top=20,width=600,height=300,toolbar=0,resizable=1'); return false;" class="btn btn-icon btn-round btn-lg social-twitter m-r-5">
          <i class="icon fab fa-twitter" aria-hidden="true"></i>
        </a>
      </div>
      @endif
    </div>
    {{-- End Subheader tabs --}}

    {{-- Start tabs content --}}
    <div class="tab-content subheader-margin m-t-40">

      {{-- Load Google AdSense --}}
      @if(config('settings.google_adsense'))
        @include('frontend.ads.google')
      @endif



      @if(config('settings.comment_game'))
      {{-- Start comments tab --}}
      <div class="tab-pane fade" id="comments" role="tabpanel">
        @php $item_type = 'game'; $item_id = $game->id; @endphp
        @include('frontend.comments.form')
      </div>
      {{-- End comments tab --}}
      @endif

      {{-- Start media tab --}}
      <div class="tab-pane fade" id="media" role="tabpanel">
      </div>
      {{-- End media tab --}}


      {{-- Start listings tab --}}
      <div class="tab-pane fade" id="listings" role="tabpanel">
      @if(count($game->listings))
          <div style="text-align: center; margin-top:-15px;">
              @if(Auth::check())
                  <a href="{{ url('listings/' . str_slug($game->name) . '-' . $game->platform->acronym . '-' . $game->id . '/add' ) }}" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
              @else
                  <a href="javascript:void(0);" data-toggle="modal" data-target="#LoginModal" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
              @endif
          </div>
      @endif
      {{-- Start Listings --}}
      @forelse($game->listings as $listing)
      @php $trade_list = json_decode($listing->trade_list); @endphp
        {{-- Start Listing Details --}}
        <div class="listing hvr-grow-shadow2">

          {{-- Sell details (price) for listing --}}
          @if($listing->sell == 1)
            {{-- Secure payment badge --}}
            @if($listing->payment)
            <div class="secure-payment-details">
              <i class="fa fa-shield-check" aria-hidden="true"></i>
            </div>
            @endif
          <div class="sell-details">
            {{ $listing->getPrice() }}
          </div>
          @endif
          {{-- Show listing details --}}
          <div class="listing-detail-wrapper">
            <div class="listing-detail">
              {{-- Digital Download --}}
              @if($listing->digital)
              <div class="value condition">
                <div class="value-title">
                  {{ trans('listings.general.digital_download') }}
                </div>
                <div class="text">
                  {{$listing->product->platform->digitals->where('id',$listing->digital)->first()->name}}
                </div>
              </div>
              @else
              {{-- Condition --}}
              <div class="value condition">
                <div class="value-title">
                  {{ trans('listings.general.condition') }}
                </div>
                <div class="text">
                  {{$listing->condition_string}}
                </div>
              </div>
              @endif
              {{-- Pickup --}}
              <div class="value pickup">
                <div class="value-title">
                  {{ trans('listings.general.pickup') }}
                </div>
                @if($listing->pickup == 1)
                  <div class="vicon">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                  </div>
                @else
                  <div class="vicon disabled">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                  </div>
                @endif
              </div>
              {{-- Delivery --}}
              <div class="value">
                <div class="value-title">
                  {{ trans('listings.general.delivery') }}
                </div>
                @if($listing->delivery)
                  <div class="vicon">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                  </div>
                @else
                  <div class="vicon disabled">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                  </div>
                @endif
              </div>
              {{-- Limited Edition --}}
              @if($listing->limited_edition)
              <div class="value limited-edition condition">
                <div class="value-title">
                  {{ trans('listings.form.details.limited') }}
                </div>
                <div class="text">
                  {{$listing->limited_edition}}
                </div>
              </div>
              @endif
            </div>
          </div>

          {{-- Details Button --}}
          <a href="{{ $listing->url_slug }}">
            <div class="details-button">
              <i class="fa fa-arrow-right" aria-hidden="true"></i>
              <span class="hidden-sm-down"> {{ trans('listings.overview.subheader.details') }}</span>
            </div>
          </a>
        </div>
        {{-- End Listing Details --}}
        {{-- Start user info and creation date --}}
        <div class="listing-user-details flex-center-space">
          <div>
            <a href="{{$listing->user->url}}" class="user-link">
              <span class="avatar avatar-xs @if($listing->user->isOnline()) avatar-online @else avatar-offline @endif">
                <img src="{{ $listing->user->avatar_square_tiny }}" alt="{{$listing->user->name}}'s Avatar"><i></i>
              </span>
              {{$listing->user->name}}
            </a> {{$listing->created_at->diffForHumans()}}
          </div>
          <div class="no-flex-shrink">
            <span class="profile-location small">
            @if($listing->user->location)
              {{$listing->user->location->place}} <img src="{{ asset('img/flags/' .   $listing->user->location->country_abbreviation . '.svg') }}" height="14"/>@if($listing->distance !== false)<i class="fa fa-location-arrow m-l-10" aria-hidden="true"></i> {{$listing->distance}} {{config('settings.distance_unit')}}@endif
            @endif
            </span>
          </div>
        </div>
        {{-- End user info and creation date --}}
      @empty
        {{-- Start empty list message --}}
        <div class="no-listings">
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
            <a href="{{ url('listings/' . str_slug($game->name) . '-' . $game->platform->acronym . '-' . $game->id . '/add' ) }}" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
            @else
            <a href="javascript:void(0);" data-toggle="modal" data-target="#LoginModal" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
            @endif
          </div>
        </div>
        {{-- End empty list message --}}
        @endforelse
        {{-- End Listings --}}
        {{-- Site Action for adding new listing --}}
        <div class="site-action">
          @if(Auth::check())
          <button type="button" onclick="location.href='{{ url('listings/' . str_slug($game->name) . '-' . $game->platform->acronym . '-' . $game->id . '/add' ) }}';" class="site-action-toggle btn-raised btn btn-orange btn-floating animation-scale-up">
            <i class="front-icon fa fa-plus" aria-hidden="true"></i>
          </button>
          @else
          <button type="button" data-toggle="modal" data-target="#LoginModal" class="site-action-toggle btn-raised btn btn-orange btn-floating animation-scale-up">
            <i class="front-icon fa fa-plus" aria-hidden="true"></i>
          </button>
          @endif
        </div>
        {{-- End Site Action --}}

      </div>
      {{-- End listings tab --}}

      {{-- Admin quick toggles - no translation, it's just for the admin --}}
      @can('edit_games')

      <div class="form-inline m-t-50">
        {{-- Edit game (redirect to admin panel) --}}
        @if(app('request')->segment(1) === "games")
          <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/game/' . $game->id . '/edit') . (session('region.code') == 'pal' ? '#eu' : '') . (session('region.code') == 'ntsc_j' ? '#jp' : '')}}" class="btn btn-dark m-r-5 m-t-10" target="_blank"><i class="fa fa-edit"></i> {{ trans('general.edit') }}</a>
        @elseif(app('request')->segment(1) === "hardware")
          <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/accessorieshardware/' . $game->id . '/edit') . (session('region.code') == 'pal' ? '#eu' : '') . (session('region.code') == 'ntsc_j' ? '#jp' : '') }}" class="btn btn-dark m-r-5 m-t-10" target="_blank"><i class="fa fa-edit"></i> {{ trans('general.edit') }}</a>
        @endif
        @if(isset($game->metacritic))
        {{-- Refresh metacritic data --}}
        <a href="{{ url('games/' . $game->id . '/refresh/metacritic') }}" class="btn btn-dark m-r-5 m-t-10" id="refresh-metacritic"><i class="fa fa-sync"></i> Refresh Metacritic</a>
        @endif
      </div>

      @endcan

    </div>
    <div style="margin-top: 50px;">
        <h2 class="text-center"><i class="fa fa-list-ol"></i> Lists</h2>
        <hr style="border-color: grey;">
        @include("frontend.custom_lists.public")
    </div>
      {{-- End tabs content --}}
  </div>
</div>
</div>
<link rel="stylesheet" href="{{ asset('css/magnific-popup.min.css') }}">

{{-- Include modal for wishlist --}}
@include('frontend.wishlist.inc.modal-wishlist')
@include('frontend.wishlist.inc.modal-havelist')

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('game', $game) !!}
@endsection
{{-- End Breadcrumbs --}}

@section('after-scripts')


<script src="//cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

{{-- Load comment script --}}
@if(config('settings.comment_game'))
  @yield('comments-script')
@endif

<script type="text/javascript">
$(document).ready(function(){


  @can('edit_games')
    {{-- Metacritic refresh submit --}}
    $('#refresh-metacritic').click( function(){
      $('#refresh-metacritic').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Refreshing');
      $('#refresh-metacritic').addClass('loading');
    });

    {{-- Change giantbomb submit --}}
    $('#change-giantbomb').click( function(){
      $('#change-giantbomb').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Fetching new data');
      $('#change-giantbomb').addClass('loading');
      $('#change-giantbomb').submit();
    });
  @endcan

    {{-- Init masonry --}}
  var $grid = $('.grid').masonry({
    itemSelector: '.grid-item',
    columnWidth: '.grid-item',
    percentPosition: true
  });

  {{-- layout Masonry after each image loads --}}
  $grid.imagesLoaded().progress( function() {
    $grid.masonry('layout');
  });

  {{-- Popup for gallery --}}
  $('.game-gallery').magnificPopup({
      type: 'image',
      tClose: '{{ trans('games.gallery.close') }}',
      tLoading: '{{ trans('games.gallery.loading') }}',
      gallery: {
        tPrev: '{{ trans('games.gallery.prev') }}',
        tNext: '{{ trans('games.gallery.next') }}',
        tCounter: '%curr% {{ trans('games.gallery.counter') }} %total%',
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
      },
      image: {
          tError: '{{ trans('games.gallery.error') }}'
      },
      mainClass: 'mfp-zoom-in',
      removalDelay: 300, //delay removal by X to allow out-animation
      callbacks: {
          beforeOpen: function() {
              $('#portfolio a').each(function(){
                  $(this).attr('title', $(this).find('img').attr('alt'));
              });
          },
          open: function() {
              //overwrite default prev + next function. Add timeout for css3 crossfade animation
              $.magnificPopup.instance.next = function() {
                  var self = this;
                  self.wrap.removeClass('mfp-image-loaded');
                  setTimeout(function() { $.magnificPopup.proto.next.call(self); }, 120);
              };
              $.magnificPopup.instance.prev = function() {
                  var self = this;
                  self.wrap.removeClass('mfp-image-loaded');
                  setTimeout(function() { $.magnificPopup.proto.prev.call(self); }, 120);
              };
          },
          imageLoadComplete: function() {
              var self = this;
              setTimeout(function() { self.wrap.addClass('mfp-image-loaded'); }, 16);
          }
      }
   });

  {{-- Popover for trade list games with grayscale animation --}}
  $('[data-toggle="popover"]').popover({
      html: true,
      trigger: 'manual',
      placement: 'top',
      offset: '10px 3px',
      template: '<div class="popover trade-list-game"><div class="popover-arrow"></div><h3 class="popover-title" role="tooltip"></h3><div class="popover-content"></div></div>'
  }).on("mouseenter", function () {
    $(this).popover('toggle');
      $( ".avatar", this ).removeClass('gray');
  }).on("mouseleave", function () {
    $(this).popover('toggle');
      $( ".avatar", this ).addClass('gray');
  });

  $('[data-toggle="popover"]').popover().click(function(e) {
      $(this).popover('toggle');
      $( ".img-circle", this ).css({'filter': '', 'filter': '', '-webkit-filter': ''});
  });



  {{-- JS to enable links on tab --}}
  var hash = document.location.hash;
  var prefix = "!";
  if (hash) {
      hash = hash.replace(prefix,'');
      var hashPieces = hash.split('?');
      activeTab = $('[role="tablist"] [data-target="' + hashPieces[0] + '"]');
      activeTab && activeTab.tab('show');

      var $this = activeTab,
      loadurl = $this.attr('href'),
      targ = $this.attr('data-target');


      if( !$.trim( $(targ).html() ).length ){

        $.ajax({
            url: loadurl,
            type: 'GET',
            beforeSend: function() {
                // TODO: show your spinner
                $('#loading').show();
            },
            complete: function() {
                // TODO: hide your spinner
                $('#loading').hide();
            },
            success: function(result) {
              $(targ).html(result);
            }
        });


      }


  }else{
      activeTab = $('[role="tablist"] [data-target="#listings"]');
      activeTab && activeTab.tab('show');
  }

  {{-- Change hash on page reload --}}
  $('[role="tablist"] a').on('shown.bs.tab', function (e) {
      var $this = $(this),
      loadurl = $this.attr('href'),
      targ = $this.attr('data-target');


      if( !$.trim( $(targ).html() ).length ){


        $.ajax({
            url: loadurl,
            type: 'GET',
            beforeSend: function() {
                // TODO: show your spinner
                $('#loading').show();
            },
            complete: function() {
                // TODO: hide your spinner
                $('#loading').hide();
            },
            success: function(result) {
              $(targ).html(result);
            }
        });


      }
      window.location.hash = targ.replace("#", "#" + prefix);
  });

    window.rating = {
        "stars": 5,
        "half": true,
        "color": "orange",
        "value": {{ isset($user_rating->rating) ? $user_rating->rating : 0 }},
        "emptyStar": "far fa-star fa-2x",
        "halfStar": "fas fa-star-half-alt fa-2x",
        "filledStar": "fas fa-star fa-2x",
        click: function(rating, $el){
            $("#user-rating").css("color", "orange");
            $.ajax({
                url: "/rating",
                data: {
                    "_token": "{{ csrf_token() }}",
                    rating: rating.stars,
                    game_id: {{$game->id}},
                },
                method: "POST",
                success: function (response) {
                    $('.delete-game-rating').css("visibility", "visible");
                }
            });
        }
    };
    $("#user-rating").rating(window.rating);

    window.difficulty = {
        "stars": 5,
        "half": true,
        "color": "crimson",
        "value": {{ isset($user_rating->difficulty) ? $user_rating->difficulty : 0 }},
        "emptyStar": "far fa-shield fa-2x",
        "halfStar": "fas fa-shield-alt fa-2x",
        "filledStar": "fas fa-shield fa-2x",
        click: function(rating, $el){
            $("#user-difficulty").css("color", "crimson");
            $.ajax({
                url: "/rating/difficulty",
                data: {
                    "_token": "{{ csrf_token() }}",
                    difficulty: rating.stars,
                    game_id: {{$game->id}},
                },
                method: "POST",
                success: function (response) {
                    $('.delete-difficulty-rating').css("visibility", "visible");
                }
            });
        }
    };
    $("#user-difficulty").rating(window.difficulty);

    $("#hour-rating, #minute-rating").on("keyup", debounce(function () {
        let hours = $('#hour-rating').val()
        if (hours < 0) {
            hours = 0
        }
        if (hours > 999) {
            hours = 999
        }
        $('#hour-rating').val(hours)

        let minutes = $('#minute-rating').val()
        if (minutes < 0) {
            minutes = 0
        }
        if (minutes > 59) {
            minutes = 59
        }
        $('#minute-rating').val(minutes)
        $.ajax({
            url: "/rating/duration",
            data: {
                "_token": "{{ csrf_token() }}",
                duration: (Number(hours) * 60 + Number(minutes)),
                game_id: {{$game->id}},
            },
            method: "POST",
            success: function (response) {
                $('.delete-duration-rating').css("visibility", "visible");
            }
        });

    }, 500));

    if ({{isset($user_rating->rating) ? 1 : 0}}) {
        $('.delete-game-rating').css("visibility", "visible");
    }
    if ({{isset($user_rating->difficulty) ? 1 : 0}}) {
        $('.delete-difficulty-rating').css("visibility", "visible");
    }
    if ({{isset($user_rating->duration) ? 1 : 0}}) {
        $('.delete-duration-rating').css("visibility", "visible");
    }

    $("#user-rating").hover(function(){
        $(this).css("color", "white");
    }, function(){
        $(this).css("color", "orange");
    });

    $("#user-difficulty").hover(function(){
        $(this).css("color", "white");
    }, function(){
        $(this).css("color", "crimson");
    });

});

function delete_rating() {
    $.ajax({
        url: "/rating/{{$game->id}}",
        data: {
            "_token": "{{ csrf_token() }}",
        },
        method: "DELETE",
        success: function (response) {
            $("#user-rating").empty();
            $("#rating-container").prepend($("<span id='user-rating'></span>"));
            window.rating.value = 0;
            $("#user-rating").rating(window.rating);
            $('.delete-game-rating').css("visibility", "hidden");
        }
    });
}

function delete_difficulty() {
    $.ajax({
        url: "/rating/difficulty/{{$game->id}}",
        data: {
            "_token": "{{ csrf_token() }}",
        },
        method: "DELETE",
        success: function (response) {
            $("#user-difficulty").empty();
            $("#difficulty-container").prepend($("<span id='user-difficulty'></span>"));
            window.difficulty.value = 0;
            $("#user-difficulty").rating(window.difficulty);
            $('.delete-difficulty-rating').css("visibility", "hidden");
        }
    });
}

function delete_duration() {
    $.ajax({
        url: "/rating/duration/{{$game->id}}",
        data: {
            "_token": "{{ csrf_token() }}",
        },
        method: "DELETE",
        success: function (response) {
            $('.hour-rating').val(null)
            $('.minute-rating').val(null)
            $('.delete-duration-rating').css("visibility", "hidden");
        }
    });
}
</script>
@endsection



@stop
