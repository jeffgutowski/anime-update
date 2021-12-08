
{{-- Start Games wrapper --}}
<div id="games-wrapper">
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
                    {{ trans('games.overview.no_games') }}
                </div>
            </div>
            {{-- End empty list message --}}
        @endforelse


    </div>
    {{-- END GAME LIST --}}
    {{ $pagination_links }}
</div>
{{-- End Games Wrapper --}}
