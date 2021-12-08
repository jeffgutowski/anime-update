@extends(Theme::getLayout())

@section('content-full-width')

{{-- Load Google AdSense --}}
@if(config('settings.google_adsense'))
  @include('frontend.ads.google')
@endif
<div class="page">
  <div class="page-content container-fluid landing-center">
      @include('frontend.layouts.inc.regions')
    @foreach($platforms as $platform)
        <div class="platform-landing" style="background: {{$platform->color}}; color: {{$platform->text_color}}; text-align: {{$platform->cover_position}}; vertical-align: middle;">
            <div class="platform-cover-container">
                @if($platform->cover_image)
                <img class="platform-landing-cover" src="{{$platform->cover_image}}">
                @else
                    <div class="landing-platform-name">{{$platform->name}}</div>
                @endif
            </div>
            @if($platform->image)
            <div class="landing-container">
                <img src="{{$platform->image}}" class="landing-image">
                <div class="landing-overlay">
                    <div class="landing-button-wrapper">
                        <a href="/listings?p={{$platform->id}}"><button class="platform-landing-button">Listings</button></a>
                        <a href="/games?p={{$platform->id}}"><button class="platform-landing-button">Games</button></a>
                        <a href="/hardware?p={{$platform->id}}"><button style="border-radius: 0px 0px 5px 5px" class="platform-landing-button">Hardware</button></a>
                    </div>
                </div>
            </div>
            @else
                <div>
                    <a href="/listings?p={{$platform->id}}"><button class="platform-landing-button">Listings</button></a>
                    <a href="/games?p={{$platform->id}}"><button class="platform-landing-button">Games</button></a>
                    <a href="/hardware?p={{$platform->id}}"><button style="border-radius: 0px 0px 5px 5px" class="platform-landing-button">Hardware</button></a>
                </div>
            @endif
        </div>
    @endforeach
  </div>
</div>
@stop
