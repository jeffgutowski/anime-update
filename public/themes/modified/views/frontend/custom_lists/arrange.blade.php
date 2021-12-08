@extends(Theme::getLayout())
@section('subheader')
    {{-- Start Subheader--}}
    <div class="subheader">

        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
        <div class="background-color"></div>

        <div class="content">
            <span class="title"><i class="fa fa-list-ol"></i> Rearrange Lists</span>
        </div>

    </div>
    {{-- End Subheader --}}
@stop
@section('content')
    <div class="cl-navigation">
        <a href="/user/{{auth()->user()->name}}?frag=customlists#customlists" class="btn btn-primary">
            <i class="fa fa-arrow-left" aria-hidden="true"></i><span class="hidden-xs-down"> Back to Lists </span>
        </a>
    </div>
    <div class="cl-body">
        <ul class="custom-lists cl-ul">
            @foreach($custom_lists as $list)
                <li class="cl-li">

                    <span class="panel list-panel list-grow">
                        <span class="saving cl-save"></span>
                        <span class="cl-moveable-left">
                            <i class="fa fa-chevron-left"></i>
                        </span>
                        <span class="cl-moveable-right">
                            <i class="fa fa-chevron-right"></i>
                        </span>
                        <div>
                            <i class="fa fa-chevron-up"></i>
                        </div>
                        <span class="cl-input">
                            <label class="input-header cl-li-label">Order Number </label><input type="number" class="dark-input cl-order-number" value="{{$list->order_number}}" min="1">
                        </span>
                        <input type="hidden" class="cl-id" value="{{$list->id}}">

                        <h4 class="list-title">
                            {{$list->title}}
                        </h4>
                        <div class="list-thumbnail-container">
                            <img class="list-thumbnail" src="{{ isset($list->youtube_id) ? "http://img.youtube.com/vi/".$list->youtube_id."/mqdefault.jpg" : $list->thumbnail}}">
                        </div>
                        <div class="list-description">
                            {{ \Illuminate\Support\Str::limit($list->description, 100, $end='...') }}
                        </div>
                        <div>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@stop

@section('after-scripts')
    <script src="{{ asset('js/custom-lists.js') }}?v={{ hash_file('md5', base_path().'/public/js/custom-lists.js') }}"></script>
@stop