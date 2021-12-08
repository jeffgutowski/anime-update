@extends(Theme::getLayout())

@section('subheader')

<div style="position: relative">


  <div class="page-top-background" style="position: absolute; z-index:0 !important; top: 0; width: 100%;">
    <div class="background-overlay listings-overview"></div>
  </div>

</div>

@stop

@section('content')

<div class="content-title m-b-20"><i class="fa fa-search" aria-hidden="true"></i> {{ trans('games.overview.search_result', ['value' => $value]) }}</div>

{{-- <hr style="border-top: 1px solid rgba(255,255,255,0.2)"> --}}


{{-- START GAME LIST --}}
    <div class="row">
      @forelse ($games as $game)
        @include('frontend.game.inc.card')
      @empty
        {{-- Start empty list message --}}
        <div class="empty-list">
          {{-- Icon --}}
          <div class="icon">
            <i class="far fa-frown" aria-hidden="true"></i>
          </div>
          {{-- Text --}}
          <div class="text">
          {{ trans('games.overview.no_search_result', ['value' => $value]) }}
          </div>
        </div>
        {{-- End empty list message --}}
      @endforelse

    </div>
    {{-- END GAME LIST --}}

  {{ $games->links() }}

@stop

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('search', $value) !!}
@endsection
{{-- End Breadcrumbs --}}
