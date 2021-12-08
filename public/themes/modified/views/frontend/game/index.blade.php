@extends(Theme::getLayout())
{{-- Subheader --}}
@section('subheader')
  <div style="position: relative">
    <div class="page-top-background">
      <div class="background-overlay listings-overview"></div>
    </div>
  </div>

@stop

{{-- Content --}}
@section('content')
  @include('frontend.layouts.inc.regions')
  {{-- Load Google AdSense --}}
  @if(config('settings.google_adsense'))
    @include('frontend.ads.google')
  @endif
  @include('frontend.game.filterControls')
  @include('frontend.game.filters')
  @include('frontend.game.content')
  {{-- Start Breadcrumbs --}}
  @section('breadcrumbs')
  {!! Breadcrumbs::render('games') !!}
  @endsection
  {{-- End Breadcrumbs --}}

  {{-- Loading bar for AJAX Loading --}}
  <div class="load-progress"></div>
  <div class="load-progress-animation"></div>



@stop
