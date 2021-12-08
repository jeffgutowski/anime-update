<div>
@if(!$game->image_cover)
    {{-- Show game name, when no cover exist --}}
    <div style="background-color: {{$game->platform->color}}; color: {{$game->platform->text_color}}; text-align: {{$game->platform->cover_position}}; font-size: 16px; font-weight: bold; padding: 5px; border-radius: 0px 0px 0px 0px;">
        {{$game->platform->name}}
    </div>
    <div class="no-cover-name">{{$game->name}}</div>
@else
    {{-- Normal game cover --}}
    @if($game->cover_generator)
    <div style="background-color: {{$game->platform->color}}; color: {{$game->platform->text_color}}; text-align: {{$game->platform->cover_position}}; font-size: 16px; font-weight: bold; padding: 2px; border-radius: 0px 0px 0px 0px;">
        @if(isset($game->platform->cover_image))
            <img style="max-height:18px; padding-left:3px; max-width: 100%" src="{{ $game->platform->cover_image }}" alt="{{$game->platform->name}}">
        @else
            <span>{{$game->platform->name}}</span>
        @endif
    </div>
    @endif
    @if(request()->url() != $game->url_slug)
        <a href="{{ $game->url_slug }}">
    @endif
    <img class="game-cover" src="{{$game->image_cover}}" style="position:relative; max-width:233px; height:auto; border-radius: 0px 0px 0px 0px;">
    @if(request()->path != $game->url_slug)
        </a>
    @endif
@endif
</div>
