<div style="text-align: center">
    <a href="/custom-lists/new" style="margin-right: 10px"><button class="btn btn-success"><i class="fa fa-plus"></i> New List</button></a>
    <a href="/custom-lists/arrange"><button class="btn btn-primary"><i class="fa fa-list-ol"></i> Rearrange Lists</button></a>
</div>
<div class="custom-lists">
        @foreach($custom_lists as $list)
            <a href="/custom-lists/{{$list->id}}" class="panel list-panel list-grow list-container">
                <h4 class="list-title">
                    {{$list->title}}
                </h4>
                <div class="list-thumbnail-container">
                    <img class="list-thumbnail" src="{{ isset($list->youtube_id) ? "http://img.youtube.com/vi/".$list->youtube_id."/mqdefault.jpg" : $list->thumbnail}}">
                </div>
                <div class="list-description">
                    {{ \Illuminate\Support\Str::limit($list->description, 100, $end='...') }}
                </div>
            </a>
        @endforeach
</div>
{{$custom_lists->links()}}
