@extends(Theme::getLayout())

@section('content')
    @if($list->user_id == auth()->id())
        <div style="width: 100%; text-align: center;">
            <a href="/user/{{auth()->user()->name}}?frag=customlists#customlists"><button class="btn btn-primary" style="margin-right: 10px">My Lists</button></a>
            <a href="/custom-lists/edit/{{$list->id}}" class="btn btn-success">Edit</a>
        </div>
    @endif
    <div class="custom-list">
        <h2 class="show-list-title">{{$list->title}}</h2>
    </div>
    <div style="text-align: center; margin-bottom: 10px;">
        <a href="/user/{{$list->user->name}}"><img class="avatar profile-avatar m-r-10"  style="max-width: 40px; max-height: 40px" src="{{$list->user->avatar}}"><span style="vertical-align: 50%; color: white">{{$list->user->name}}</span></a>
    </div>
    @if(isset($list->youtube_id))
    <div class="video-container">
        <iframe src="https://www.youtube.com/embed/{{$list->youtube_id}}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    @elseif(isset($list->thumbnail))
        <img class="show-list-thumbnail" src="{{$list->thumbnail}}"></img>
    @endif
    <br/>
    <div>
        {{$list->description}}
    </div>
    <br/>
        <div class="list-desktop">
            <table style="width: 100%">
                <tbody>
                @foreach($items as $item)
                <tr class="list-row" style="">
                    @if($list->show_order_number)
                    <td>
                        <h1 style="text-align: center; padding: 5px">{{$item->order_number}}</h1>
                    </td>
                    @endif
                    <td style="width: 220px;">
                        <div style="padding: 10px;">
                            <a target="_blank" href="/{{$item->product->type == 'game' ? 'games' : 'hardware' }}/{{str_slug($item->product->name) . '-' . $item->product->platform->acronym . '-' . $item->product->id}}">
                                @if($list->custom_item_thumbnails)
                                    <img style="max-width:200px; padding: 10px" src="{{ $item->thumbnail }}">
                                @else
                                    <div class="platform-background" style="background: {{ $item->product->platform->color }}; text-align: {{ $item->product->platform->cover_position  }}">
                                        <img class="platform-cover" src="{{ $item->product->platform->cover_image }}">
                                    </div>
                                    <img style="max-width:200px;" src="{{$item->product->{'cover_'.session('region.abbr')} }}">
                                @endif
                            </a>
                        </div>
                    </td>
                    <td style="padding: 10px;">
                        <div>
                            <h3>{{$item->product->name}}</h3>
                            <div style="color: white">{{$item->description}}</div>
                        </div>
                     </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="list-mobile">
            @foreach($items as $item)
                <div class="mobile-item">
                    @if($list->show_order_number)
                    <div>
                        <h1 style="padding-left: 10px">{{$item->order_number}}</h1>
                    </div>
                    @endif
                        <div style="padding: 10px;">
                            <a target="_blank" href="/{{$item->product->type == 'game' ? 'games' : 'hardware' }}/{{str_slug($item->product->name) . '-' . $item->product->platform->acronym . '-' . $item->product->id}}">
                                @if($list->custom_item_thumbnails)
                                    <img style="max-width:200px; padding: 10px" src="{{ $item->thumbnail }}">
                                @else
                                    <div class="platform-background" style="background: {{ $item->product->platform->color }}; text-align: {{ $item->product->platform->cover_position  }}; margin: auto;">
                                        <img class="platform-cover" src="{{ $item->product->platform->cover_image }}">
                                    </div>
                                    <img style="max-width:200px;" src="{{$item->product->{'cover_'.session('region.abbr')} }}">
                                @endif
                            </a>
                        </div>

                    <h3>{{$item->product->name}}</h3>
                    <div style="color: white; text-align: left">{{$item->description}}</div>

                </div>
            @endforeach
        </div
        {{ $items->links() }}
    </div>
@stop
