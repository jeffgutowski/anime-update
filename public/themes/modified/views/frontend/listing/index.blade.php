@extends(Theme::getLayout())

@section('subheader')

<div style="position: relative;  height: 0px; ">


  <div class="page-top-background" style="position: absolute; z-index:0 !important; top: 0; width: 100%;">
    @if(!is_null($system))
    <div style="background-color: {{$system->color}}; height: 400px; margin-top: -60px; z-index: 0; position: relative;"></div>
    @endif

    <div class="background-overlay listings-overview {{!is_null($system) ? 'with-platform' : ''}}"></div>

  </div>

</div>

@stop



@section('content')
@include('frontend.layouts.inc.regions')

@if(!is_null($system))

<div style="margin-bottom: 50px;">
  {{-- Check if platform logo setting is enabled --}}
  @if( config('settings.platform_logo') )
    <img src="{{ asset('logos/' . $system->acronym . '.png/') }}" alt="" height="40">
  @else
    <span class="platform-title">{{$system->name}}</span>
  @endif
</div>

@endif

{{-- Load Google AdSense --}}
@if(config('settings.google_adsense'))
  @include('default::frontend.ads.google')
@endif
@include('frontend.game.filterControls')
@include('frontend.game.filters')
  {{-- START LISTINGS --}}
  <div class="row">

    @forelse($listings as $listing)
      @include('frontend.listing.inc.card')
    @empty
      {{-- Start empty list message --}}
      <div class="no-listings">
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
      </div>
      {{-- End empty list message --}}
    @endforelse

  </div>
  {{-- END LISTINGS --}}


  {{ $pagination_links }}

</div>
{{-- End Listings Wrapper --}}


{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
  {{-- Breadcrumbs for all listings --}}
  @if(is_null($system))
    {!! Breadcrumbs::render('listings') !!}
  {{-- Breadcrumbs for platform listings --}}
  @else
    {!! Breadcrumbs::render('platform_listings', $system) !!}
  @endif
@endsection
{{-- End Breadcrumbs --}}

{{-- Loading bar for AJAX Loading --}}
<div class="load-progress"></div>
<div class="load-progress-animation"></div>

@section('after-scripts')
<script type="text/javascript">
$(document).ready(function(){
  {{-- AJAX Pagination --}}
  $(".pagination a").click(function(e) {
      e.preventDefault();
      window.filters.page = $(this).text()
      if (window.filters.page === "«") {
          window.filters.page = null;
      }
      if (window.filters.page === "»") {
          lastPage = $('#last-page').text()
          window.filters.page = lastPage
      }
      {{-- Add spinner icon to the pagination link --}}
      $(this).html('<i class="fa fa-spinner fa-spin fa-fw" style="margin-right: -3px; margin-left: -5px;"></i>');
      {{-- Get URL from link --}}
      window.filterRedirect()
  });

    {{-- Order by change URL --}}
    $('#order_by').change(function () {
        window.filters.o = $("#order_by").val()
        window.filters.page = 1
        window.filterRedirect()
    });

    {{-- Change order direction --}}
    $('#order-direction-btn').click(function (e) {
        e.preventDefault();
        var order = $("#order_by").val()
        if ($('#order-direction').hasClass('fa-sort-amount-up')) {
            var direction = '-'
        } else {
            var direction = ''
        }
        window.filters.page = 1
        window.filters.o = direction + order.replace('-', '')
        window.filterRedirect()
    });
});
</script>
@endsection



@stop
