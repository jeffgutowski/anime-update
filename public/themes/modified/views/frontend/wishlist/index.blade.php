@extends(Theme::getLayout())
@section('content')
  @include('frontend.game.filterControls')
  @include('frontend.game.filters')
  {{-- Pagination Link before wishlist entries --}}
  {{ $wishlists->links() }}

  @forelse($wishlists as $wishlist)
    {{-- Start Listing --}}
    <section class="panel">

      {{-- Start Listing Header --}}
      <div class="panel-heading listing-heading">
        <div class="flex-center-space">
          <div class="flex-center">
            {{-- Game Cover --}}
            <div class="m-r-10">
              <span>
                <img style="max-height: 100px;" src="{{$wishlist->product->image_cover}}" alt="{{ $wishlist->product->name }}">
              </span>
            </div>
            {{-- Game Name + platform --}}
            <div>
              <div class="title">{{ $wishlist->product->name }}</div>
              <span class="platform-label" style="background-color:{{ $wishlist->product->platform->color }}; color:{{$wishlist->product->platform->text_color}}"> {{ $wishlist->product->platform->name }} </span>
                @if($wishlist->product->average_rating || $wishlist->product->average_difficulty || $wishlist->product->average_duration)
                  <div style="font-size: 20px">
                    @if($wishlist->product->average_rating)
                      <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="{{ucfirst($wishlist->product->type)." Rating"}}" style="margin-left: -1px; margin-right: -1px">
                                      <span class="fa-stack fa-1x" style="">
                                        <span class="fa fa-star fa-stack-1x fa-lg icon-rating" style="color: orange"></span>
                                        <span class="fa-stack-1x avg-rating-number-sm">{{$wishlist->product->average_rating}}</span>
                                      </span>
                      </a>
                    @endif
                    @if($wishlist->product->type == 'game')
                      @if($wishlist->product->average_difficulty)
                        <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="Difficulty Rating" style="margin-left: -1px; margin-right: -1px">
                                        <span class="fa-stack fa-1x">
                                            <span class="fa fa-shield fa-stack-1x fa-lg icon-rating" style="color: crimson"></span>
                                            <span class="fa-stack-1x avg-rating-number-sm">{{$wishlist->product->average_difficulty}}</span>
                                        </span>
                        </a>
                      @endif
                      @if($wishlist->product->average_duration)
                        <a href="#" class="condition-tip" data-toggle="tooltip" data-placement="right" title="Duration" style="margin-left: -1px; margin-right: -1px">
                                        <span class="fa-stack fa-1x">
                                            <span class="fa fa-stopwatch fa-stack-1x fa-lg icon-rating" style="color: royalblue"></span>
                                            <span class="fa-stack-1x avg-rating-number-sm">{{$wishlist->product->average_duration->hours}}</span>
                                        </span>
                        </a>
                      @endif
                    @endif
                  </div>
                @endif
            </div>
          </div>
        </div>
      </div>
      {{-- End Listing Header --}}

      <div class="listing-body">
      @if(!request()->has("o") || strpos(request('o'), 'listings.created_at'))
          @if(isset($wishlist->id))
          <div class="listing {{ (isset($wishlist->max_price) && $wishlist->price > $wishlist->max_price) ? 'grayscale' : '' }}">

            {{-- Sell details (price) for listing --}}
            @if($wishlist->price)
              {{-- Secure payment badge --}}
              @if($wishlist->payment)
                <div class="secure-payment-details">
                  <i class="fa fa-shield-check" aria-hidden="true"></i>
                </div>
              @endif
              <div class="sell-details">
                {{ money($wishlist->price, Config::get('settings.currency'))->format(true, Config::get('settings.decimal_place')) }}
              </div>
            @endif
            {{-- Show listing details --}}
            <div class="listing-detail-wrapper">
              <div class="listing-detail">
                {{-- Condition --}}
                <div class="value condition">
                  <div class="value-title">
                    {{ trans('listings.general.condition') }}
                  </div>
                  <div class="text">
                    {{  trans('listings.general.conditions.'.$wishlist->condition) }}
                  </div>
                </div>
              </div>
            </div>

            {{-- Details Button --}}
            <a href="{{  url('listings/' . str_slug($wishlist->name) . '-' . $wishlist->acronym . '-' . strtolower($wishlist->user_name) . '-' . $wishlist->id) }}">
              <div class="details-button">
                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                <span class="hidden-sm-down"> {{ trans('listings.overview.subheader.details') }}</span>
              </div>
            </a>
          </div>
          {{-- End Listing Details --}}
        @else
          <div class="listing-no-offers">
            <i class="far fa-frown" aria-hidden="true"></i> {{ trans('listings.general.no_listings') }}
          </div>
        @endif
      @else
        @forelse($wishlist->listings as $listing)
          @php $trade_list = json_decode($listing->trade_list); @endphp
            {{-- Start Listing Details --}}
            <div class="listing {{ (isset($wishlist->max_price) && $listing->price > $wishlist->max_price) ? 'grayscale' : '' }}">

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
              {{-- Show trade icon when user accept tradde --}}

              {{-- Show listing details --}}
              <div class="listing-detail-wrapper">
                <div class="listing-detail">
                  {{-- Condition --}}
                  <div class="value condition">
                    <div class="value-title">
                      {{ trans('listings.general.condition') }}
                    </div>
                    <div class="text">
                      {{$listing->condition_string}}
                    </div>
                  </div>
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
        @empty
        <div class="listing-no-offers">
          <i class="far fa-frown" aria-hidden="true"></i> {{ trans('listings.general.no_listings') }}
        </div>
        @endforelse
      @endif
      </div>

      {{-- Start Listing Footer --}}

      <div class="panel-footer">
        {{-- Cheapest Listing created at --}}
        <div class="listing-footer-time">
          @if (count($wishlist->listings) > 0)
            @if(!request()->has("o") || strpos(request('o'), 'listings.created_at'))
              {{ $wishlist->created_at->diffForHumans() }} <br/>
            @else
              <strong><i class="fa fa-tags"></i> {{ $wishlist->listings->count() }} {{ trans('general.listings') }}</strong>
            @endif
          @endif
        </div>
        {{-- Footer Buttons --}}
        <div>
          <a href="{{ $wishlist->product->url_slug }}/wishlist/delete" class="button additional delete-wishlist">
            <i class="fa fa-trash" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.delete') }}</span>
          </a><a href="javascript:void(0);" data-toggle="modal" data-target="#EditWishlist_{{$wishlist->wishlist_id}}" class="button additional">
            <i class="fa fa-edit" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.edit') }}</span>
          </a>
            <a href="{{ $wishlist->product->url_slug }}" class="button">
              <i class="fa fa-gamepad" aria-hidden="true"></i><span class="hidden-sm-down"> Game</span>
            </a>
        </div>

      </div>
      {{-- End Listing Footer --}}
    </section>
    {{-- End Listing --}}
    {{-- Include modal for wishlist --}}
    @include('frontend.wishlist.inc.modal-wishlist', ['game' => $wishlist->game])
  @empty
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
  @endforelse

  {{-- Pagination Link after wishlist entries --}}
  {{ $wishlists->links() }}


  @section('after-scripts')

  <script type="text/javascript">
  $(document).ready(function(){



    {{-- Delete submit --}}
    $(".delete-wishlist").click( function(){
      $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
      $(this).addClass('loading');
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

  });
  </script>
  @endsection

@stop
