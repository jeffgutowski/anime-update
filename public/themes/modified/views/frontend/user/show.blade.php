@extends(Theme::getLayout())
@include('frontend.user.subheader')

@section('content')
@yield('user-content')


<!-- Nav tabs -->
<div class="m-b-40 m-t-40 subheader-tabs-wrapper flex-center-space profile-header">
  {{-- Start Nav tabs --}}
  <ul class="subheader-tabs" role="tablist">
    <li class="nav-item">
      <a href="?frag=listings#listings" data-target="#listings" role="tab" class="subheader-link">
        <i class="fa fa-tags profile-link" aria-hidden="true"></i><span class="hidden-md-down"> {{ trans('users.profile.listings') }}</span>
      </a>
    </li>
      <li class="nav-item">
          <a href="?frag=about#about" data-target="#about" role="tab" class="subheader-link">
              <i class="fa fa-user profile-link" aria-hidden="true"></i><span class="hidden-md-down"> About Me </span>
          </a>
      </li>
      @if(auth()->id() == $user->id)
          <li class="nav-item">
              <a href="?frag=wishlist#wishlist" data-target="#wishlist" role="tab" class="subheader-link">
                  <i class="fa fa-heart profile-link" aria-hidden="true"></i><span class="hidden-md-down"> {{ trans('wishlist.wishlist') }}</span>
              </a>
          </li>
      @endif
      <li class="nav-item">
          <a href="?frag=collection#collection" data-target="#collection" role="tab" class="subheader-link">
              <i class="fa fa-clipboard-list profile-link" aria-hidden="true"></i><span class="hidden-md-down"> Collection </span>
          </a>
      </li>
      <li class="nav-item">
          <a href="?frag=hardware#hardware" data-target="#hardware" role="tab" class="subheader-link">
              <i class="fa fa-hdd profile-link" aria-hidden="true"></i><span class="hidden-md-down"> Hardware Collection </span>
          </a>
      </li>
      <li class="nav-item">
          <a href="?frag=completedlist#completedlist" data-target="#completedlist" role="tab" class="subheader-link">
              <i class="fa fa-clipboard-check" aria-hidden="true"></i><span class="hidden-md-down">
              @if(auth()->check() && auth()->user()->id == $user->id)
                  Games I've Completed
              @else
                  Completed Games
              @endif
              </span>
          </a>
      </li>
      <li class="nav-item">
          <a href="?frag=customlists#customlists" data-target="#customlists" role="tab" class="subheader-link">
              <i class="fa fa-list-ol profile-link" aria-hidden="true"></i><span class="hidden-md-down"> Lists </span>
          </a>
      </li>
      <li class="nav-item">
          <a href="?frag=trophies#trophies" data-target="#trophies" role="tab" class="subheader-link">
              <i class="fa fa-trophy profile-link" aria-hidden="true"></i><span class="hidden-md-down"> Trophies </span>
          </a>
      </li>
    @if($user->ratings->count() > 0)
    <li class="nav-item">
      <a data-toggle="tab" href="?frag=ratings#ratings" data-target="#ratings" role="tab" class="subheader-link">
        <i class="fa fa-thumbs-up profile-link" aria-hidden="true"></i><span class="hidden-md-down"> {{ trans('users.profile.ratings') }}</span>
      </a>
    </li>
    @endif
    {{-- Check if logged in user is user --}}
    @if(!(Auth::check() && Auth::user()->id == $user->id))
    <li class="nav-item" style="white-space:nowrap;">
      <a href="javascript:void(0)" data-toggle="modal" data-target="{{ Auth::check() ? '#NewMessage' : '#LoginModal' }}" class="subheader-link">
        <i class="fas fa-envelope-open profile-link" aria-hidden="true"></i><span class="hidden-md-down"> {{ trans('messenger.send_message') }}</span>
      </a>
    </li>
    @endif
  </ul>
</div>
<div class="tab-content subheader-margin">
    <div class="tab-pane fade" id="listings" role="tabpanel">
@if(!is_null($listings))
<div id="listings-wrapper">
    @include('frontend.game.filterControls')
</div>
{{-- End Filter / Sort options --}}
  {{-- START LISTINGS --}}
    <div class="row">
      @forelse($listings as $listing)
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
                {{ $listing->product->name }} @if($listing->limited_edition)<span><i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}<span>@endif
              </div>
              @elseif($listing->limited_edition)
              <div class="item-name">
                <i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}
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
                @if((request('rs') == null && ($listing->product->average_rating || $listing->product->average_difficulty || $listing->product->average_duration)) || (request('rs') == 'my' && ($listing->rating || $listing->difficulty || $listing->duration)))
                    <div class="game-popularity">
                        @if(request('rs') == 'my' ? $listing->rating : $listing->product->average_rating)
                            <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="{{ucfirst($listing->product->type)." Rating"}}" style="margin-left: -5px; margin-right: -5px">
                                <span class="fa-stack fa-1x">
                                  <span class="fa fa-star fa-stack-1x fa-lg icon-rating" style="color: orange"></span>
                                  <span class="fa-stack-1x avg-rating-number-sm">{{ request('rs') == 'my' ? $listing->rating : $listing->product->average_rating }}</span>
                                </span>
                            </a>
                        @endif
                        @if($listing->product->type == 'game')
                            @if(request('rs') == 'my' ? $listing->difficulty : $listing->product->average_difficulty)
                                <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="Difficulty Rating" style="margin-left: -5px; margin-right: -5px">
                                  <span class="fa-stack fa-1x">
                                      <span class="fa fa-shield fa-stack-1x fa-lg icon-rating" style="color: crimson"></span>
                                      <span class="fa-stack-1x avg-rating-number-sm">{{ request('rs') == 'my' ? $listing->difficulty : $listing->product->average_difficulty }}</span>
                                  </span>
                                </a>
                            @endif
                            @if(request('rs') == 'my' ? $listing->duration : (isset($listing->product->average_duration->hours) ? $listing->product->average_duration->hours : false ))
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
        </div>
        {{-- End GAME --}}
    @empty
      <div class="no-listings">
        {{-- Start empty list message --}}
        <div class="empty-list">
          {{-- Icon --}}
          <div class="icon">
            <i class="far fa-frown" aria-hidden="true"></i>
          </div>
          {{-- Text --}}
          <div class="text">
            {{ trans('listings.general.no_listings') }}
          </div>
        </div>
        {{-- End empty list message --}}
      </div>
    @endforelse

    </div>
  {{-- END LISTINGS --}}
  {{ $listings->links() }}
@endif
  </div>
  <div class="tab-pane fade" id="about" role="tabpanel">
      <div class="about-me"> {!! $user->about_me !!}</div>
      <div style="color: white; border: 1px solid #929292; padding: 10px; border-radius: 5px">
          <div><b>Favorite Game:</b> {!! $user->favorite_game !!}</div>
          <div><b>Favorite Genre:</b> {!! isset($user->favoriteGenre->name) ? $user->favoriteGenre->name : null !!}</div>
          <div><b>Favorite Platform:</b> {!! isset($user->favoritePlatform->name) ? $user->favoritePlatform->name : null !!}</div>
          <div><b>Favorite Developer:</b> {!! $user->favorite_developer !!}</div>
          <div><b>Favorite Publisher:</b> {!! $user->favorite_publisher !!}</div>
      </div>
  </div>
    @if(auth()->id() == $user->id)
        <div class="tab-pane fade" id="wishlist" role="tabpanel">
            @if(!is_null($wishlist))
                @php $games = $wishlist; $fragment = "wishlist"; @endphp
                @include('frontend.game.filterControls')
                @include('frontend.game.content')
            @endif
        </div>
    @endif
  <div class="tab-pane fade" id="collection" role="tabpanel">
      @if(!is_null($have_list))
          @php $games = $have_list; $fragment = "collection"; @endphp
          @include('frontend.game.filterControls')
          @include('frontend.game.content')
      @endif
  </div>
  <div class="tab-pane fade" id="hardware" role="tabpanel">
    @if(!is_null($hardware_list))
        @php $games = $hardware_list; $fragment = "hardware"; @endphp
        @include('frontend.game.filterControls')
        @include('frontend.game.content')
    @endif
  </div>
  <div class="tab-pane fade" id="completedlist" role="tabpanel">
      @if(!is_null($completed_list))
          @php $games = $completed_list; $fragment = "completedlist"; @endphp
          @include('frontend.game.filterControls')
          @include('frontend.game.content')
      @endif
  </div>
    <div class="tab-pane fade" id="trophies" role="tabpanel">
        @if(!is_null($trophies) && $trophies->count() > 0)
            @foreach($trophies as $trophy)
                <div class="trophy" >
                    <div class="trophy-header">
                        <i class="trophy-icon {{$trophy->type == 'collector' ? 'fa fa-compact-disc' : 'fa fa-gamepad'}}" style="{{isset($trophy->platform) ? 'background:'.$trophy->platform->color.';'.'color:'.$trophy->platform->text_color.';' : ''}}"></i> {{$trophy->trophy->name}} {{ romanNumerals($trophy->trophy->tier) }}
                    </div>
                    @if(isset($trophy->nextTrophy))
                    <div class="progress">
                        <div class="progress-bar" style="width:{{$trophy->count/$trophy->nextTrophy->threshold*100}}%"></div>
                    </div>
                        <div class="trophy-exp">{{$trophy->count}}/{{$trophy->nextTrophy->threshold}}</div>
                        <div class="next-level">Next: {{$trophy->nextTrophy->name}} {{ romanNumerals($trophy->nextTrophy->tier) }}</div>
                    @else
                        <div class="progress-bar" style="width:100%"></div>
                        <div class="trophy-exp">{{$trophy->trophy->threshold}}/{{$trophy->trophy->threshold}}</div>
                    @endif
                </div>
            @endforeach
        @else
            No Trophies
        @endif
    </div>
  {{-- START RATINGS --}}
  <div class="tab-pane fade" id="ratings" role="tabpanel">
    @forelse ($ratings as $rating)
      @php
        if($rating->rating == 2){
          $bg = 'bg-success';
          $icon = 'fa-thumbs-up';
        }else if($rating->rating == 1){
          $bg = 'bg-dark';
          $icon = 'fa-minus';
        }else{
          $bg = 'bg-danger';
          $icon = 'fa-thumbs-down';
        }
      @endphp
      {{-- Start Rating --}}
      <section class="panel rating-panel hvr-grow-shadow2 {{$bg}}">
        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
        <div class="background-color" style="border-radius: 5px;"></div>

        <div class="panel-body">
          {{-- Rating icon --}}
          <i class="fa {{$icon}} rating-icon" aria-hidden="true"></i>
          {{-- User avatar --}}
          <span class="avatar">
            <img src="{{$rating->user_from->avatar_square}}" alt="{{$rating->user_from->name}}'s Avatar">
          </span>
          {{-- Notice --}}
          <div>
            <span class="from-user">{{ trans('users.profile.rating_from', ['username' => $rating->user_from->name]) }}</span>
            {{-- Rating notice --}}
            @if($rating->notice)
              {{-- Head text with username from rater--}}
              <span class="notice"><i class="fa fa-quote-left" aria-hidden="true"></i> {{$rating->notice}} <i class="fa fa-quote-right" aria-hidden="true"></i></span>
            @else
              {{-- No notice --}}
              <span class="notice">{{ trans('offers.status_complete.no_notice') }}</span>
            @endif
          </div>

        </div>
      </section>
      {{-- End Rating --}}
    @empty
      <div style="text-align: center;">
        {{-- No Ratings --}}
        <div style="text-align: center; display: block;">
          <span class="fa-stack fa-lg" style="font-size: 50px;text-align: center;">
            <i class="fa fa-thumbs-up fa-stack-1x"></i>
            <i class="fa fa-ban fa-stack-2x text-danger"></i>
          </span>
        </div>
        <span class="no-ratings">{{ trans('users.general.no_ratings') }}</span>
      </div>
    @endforelse
  </div>
  {{-- END RATINGS --}}
    <div class="tab-pane fade" id="customlists" role="tabpanel">
        {{-- Check if auth user is veiwing own list and show the editable index if so --}}
        @if($custom_lists && auth()->check() && auth()->id() == $user->id)
            @include("frontend.custom_lists.index")
        @elseif($custom_lists)
            {{-- Else show public index --}}
            @include("frontend.custom_lists.public")
        @endif
    </div>
  {{-- Start Edit / Delete when user has permission --}}
  @if(Auth::check() && Auth::user()->hasPermission('edit_users'))
  <div>
    @if($user->isActive())
      <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user/' . $user->id . '/ban') }}" class="btn btn-danger m-r-5"><i class="fa fa-trash"></i> Ban</a>
    @else
      <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user/' . $user->id . '/ban') }}" class="btn btn-success m-r-5"><i class="fa fa-check-circle"></i> Unban</a>
    @endif
    <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user/' . $user->id . '/edit') }}" class="btn btn-dark" target="_blank"><i class="fa fa-edit"></i> {{ trans('general.edit') }}</a>
  </div>
  @endif

</div>
@include('frontend.game.filters')


    {{-- Include new message modal --}}
{{-- Check if logged in user is user --}}
@if(!(Auth::check() && Auth::user()->id == $user->id))
  @include('frontend.messenger.partials.modal-message')
@endif

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('profile', $user) !!}
@endsection
{{-- End Breadcrumbs --}}

@section('after-scripts')


<script src="//cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>


<script type="text/javascript">
function disableScroll() {
    scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
    window.onscroll = function() {
        window.scrollTo(scrollLeft, scrollTop);
    };
}

function enableScroll() {
    window.onscroll = function() {};
}
$(document).ready(function(){
    // disable and then renable the scroll or else user information will cut off on load
    disableScroll()
    setTimeout(function(){
        enableScroll()
    }, 200);

  {{-- Javascript to enable link to tab --}}
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

  $('#calc-similarity').on('click', function () {
      self = $(this);
      $.ajax({
          url: '/similarity',
          headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
          data: {friend_id: {{$user->id}} },
          type: 'GET',
          beforeSend: function() {
              self.html("<i class='fa fa-spinner fa-spin fa-fw'>")
          },
          complete: function() {
              setTimeout(function () {
                  self.hide()
                  $('#similarities').slideDown(300)
              }, 300)
          },
          success: function(response) {
              $('#percent_friend_owns_from_self').text(response.percent_friend_owns_from_self);
              $('#percent_self_owns_of_friend').text(response.percent_self_owns_of_friend);
              $('#percent_friend_played_from_self').text(response.percent_friend_played_from_self);
              $('#percent_self_played_of_friend').text(response.percent_self_played_of_friend);
          }
      });
  })


  {{-- Change hash for page-reload --}}
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
});
</script>
@endsection


@stop
