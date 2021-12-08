<div>
@if(!$game->image_cover)
    {{-- Show game name, when no cover exist --}}
    <div style="background-color: {{$game->platform->color}}; height: 30px; padding: 5px; border-radius: 0px 0px 0px 0px;  font-size: 16px; font-weight:bold; color:white;">
        {{$game->platform->name}}
    </div>
    <div class="no-cover-name">{{$game->name}}</div>
@else
    {{-- Normal game cover --}}
    <div style="background-color: {{$game->platform->color}}; height: 30px; padding: 5px; font-size: 16px; font-weight:bold; color:white; border-radius: 0px 0px 0px 0px;">
        {{$game->platform->name}}
    </div>
    <img class="game-cover" src="{{$game->image_cover}}" style="position:relative; max-width:233px; height:auto; border-radius: 0px 0px 0px 0px;">
@endif
</div>
