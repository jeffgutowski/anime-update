<div class="text-center">
    {{ $custom_lists->fragment("lists")->links() }}
    <div class="custom-lists">
        @if(count($custom_lists) > 0)
        @foreach($custom_lists as $list)
            <a href="/custom-lists/{{$list->id}}" class="panel list-panel list-grow list-container">
                <h4 class="list-title">
                    {{$list->title}}
                </h4>
                <div class="list-thumbnail-container" style=" ">
                    <img class="list-thumbnail" src="{{ isset($list->youtube_id) ? "http://img.youtube.com/vi/".$list->youtube_id."/mqdefault.jpg" : $list->thumbnail}}">
                </div>
                <div class="list-description">
                    {{ \Illuminate\Support\Str::limit($list->description, 100, $end='...') }}
                </div>
            </a>
        @endforeach
        @else
            <div class="no-listings">
                <div class="empty-list add-button">
                    {{-- Icon --}}
                    <div class="icon">
                        <i class="far fa-frown" aria-hidden="true"></i>
                    </div>
                    {{-- Text --}}
                    <div class="text">
                        There are no lists available.
                    </div>
                </div>
            </div>
        @endif
    </div>
    {{ $custom_lists->fragment("lists")->links() }}
</div>
