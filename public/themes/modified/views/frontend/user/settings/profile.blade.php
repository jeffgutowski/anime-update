@extends(Theme::getLayout())

{{-- Add Game Subheader --}}
@include('frontend.user.settings.subheader')

@section('content')
  <section class="panel">

    {{-- Panel heading (Profile) --}}
    <div class="panel-heading">
      <h3 class="panel-title">{{ trans('users.dash.settings.profile') }}</h3>
    </div>

    {!! Form::open(array('url'=>'dash/settings','id'=>'form-settings','files' => true)) !!}
    <div class="panel-body">
      <div class="input-wrapper">
        {{-- Username label --}}
        <label>{{ trans('users.dash.settings.username') }}</label>
        {{-- Username input --}}
        <div class="input-group">
          <span class="input-group-addon fixed-width">
            <i class="fa fa-user" aria-hidden="true"></i>
          </span>
          <input type="text" class="form-control rounded inline input" name="name" id="name" autocomplete="off" value="{{$user->name}}" placeholder="{{ trans('users.dash.settings.username') }}" readonly/>
        </div>
        {{-- Profile link --}}
        <span style="opacity:0.5;"><i class="fa fa-link" aria-hidden="true"></i> {{ trans('users.dash.settings.profile_link') }} {{ $user->url }}</span>
      </div>
      <div class="input-wrapper">
        {{-- eMail Address label --}}
        <label>{{ trans('users.dash.settings.email') }}</label>
        {{-- Error messages for eMail Address --}}
        @if($errors->has('email'))
        <div class="bg-danger input-error">
          @foreach($errors->get('email') as $message)
          {{$message}}
          @endforeach
        </div>
        @endif
        {{-- eMail Address input --}}
        <div class="input-group {{$errors->has('email') ? 'has-error' : '' }}">
          <span class="input-group-addon fixed-width">
            <i class="fa fa-envelope" aria-hidden="true"></i>
          </span>
          <input type="text" class="form-control rounded inline input" data-validation="number,required" name="email" id="email" autocomplete="off" value="{{$user->email}}" placeholder="{{ trans('users.dash.settings.email') }}"/>
        </div>
      </div>
      <div class="input-wrapper">
        {{-- Change profile image label --}}
        <label>{{ trans('users.dash.settings.change_avatar') }}</label>
        {{-- Error messages for image --}}
        @if($errors->has('avatar'))
        <div class="bg-danger input-error">
          @foreach($errors->get('avatar') as $message)
          {{$message}}
          @endforeach
        </div>
        @endif
        <div class="flex-center">
          <div class="m-r-10">
            <span class="avatar">
              <img src="{{$user->avatar_square_tiny}}" alt="">
            </span>
          </div>
          <div>
            {{-- File browser --}}
            <div class="input-group">
              <label class="input-group-btn">
                <span class="btn {{ $errors->has('avatar') ? 'bg-danger' : 'bg-success' }}">
                  <i class="fa fa-file-image" aria-hidden="true"></i> {{ trans('users.dash.settings.browse') }}&hellip; <input type="file" name="avatar" style="display: none;" multiple>
                </span>
              </label>
              <input type="text" class="form-control input" readonly>
            </div>
          </div>
        </div>
      </div>
      <div class="input-wrapper">
        {{-- Change or set location label --}}
        <label>{{ $location ? trans('users.dash.settings.location_change') : trans('users.dash.settings.location_set')}}</label>
        <div class="flex-center">
          {{-- Show current location --}}
          @if($location)
          <div class="current-location m-r-10">
            <img src="{{ asset('img/flags/' .   $location->country_abbreviation . '.svg') }}" height="14"/> {{$location->country_abbreviation}}, {{$location->place}} <span class="postal-code">{{$location->postal_code}}</span>
          </div>
          {{-- Show if no location is saved --}}
          @else
          <div class="current-location m-r-10">
            <i class="fa fa-times text-danger"></i> {{ trans('users.dash.settings.location_no') }}
          </div>
          @endif
          {{-- Button to open modal for location change --}}
          <a data-toggle="modal" data-target="#modal_user_location" href="javascript:void(0)" role="button" class="btn btn-success">
            <i class="fa fa-map-marker"></i>
            {{ $location ? trans('users.dash.settings.location_change') : trans('users.dash.settings.location_set')}}
          </a>
        </div>
      </div>
      <div>
        @if($location)
          <label>{{'Region Code'}}</label>
          <div class="current-location" style="width:246px">
            {{$location->region_code}}
          </div>
        @endif
      </div>
      <br/>
      <div class="input-wrapper">
        <label>About Me</label>
        <div class="flex-center">
          {!! Form::textarea('about_me', (isset($user->about_me) ? $user->about_me : null), array('class'=>'form-control input', 'placeholder'=> '', 'id' => 'about-me' )) !!}
        </div>
        <br/>
        <div>
          <label>Favorite Game</label>
          <div>
            <input style="width: 100%;" class="text-input" name="favorite_game" type='text' id='favorite-game' list='game-list' placeholder="Search Games" value="{{$user->favorite_game}}">
            <datalist id="game-list"></datalist>
          </div>
        </div>
        <br/>
        <div>
          <label>Favorite Genre</label>
          <div>
            <select class="dark-input" name="favorite_genre_id">
              <option value="">-- Select Genre --</option>
              @foreach($genres as $genre)
                <option value="{{$genre->id}}" {{ $user->favorite_genre_id == $genre->id ? "selected" : null }}>{{$genre->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <br/>
        <div>
          <label>Favorite Platform</label>
          <div>
            <select class="dark-input" name="favorite_platform_id">
              <option value="">-- Select Platform --</option>
              @foreach($platforms as $platform)
                <option value="{{$platform->id}}" {{ $user->favorite_platform_id == $platform->id ? "selected" : null }}>{{$platform->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <br/>
        <div>
          <label>Favorite Developer</label>
          <div>
            <input class="text-input" name="favorite_developer" type='text' id='favorite-dev' list='dev-list' placeholder="Search Developers"
                value="{{$user->favorite_developer}}">
            <datalist id="dev-list"></datalist>
          </div>
        </div>
        <br/>
        <div>
          <label>Favorite Publisher</label>
          <div>
            <input class="text-input" name="favorite_publisher" type='text' id='favorite-pub' list='pub-list' placeholder="Search Publishers"
                value="{{$user->favorite_publisher}}">
            <datalist id="pub-list"></datalist>
          </div>
        </div>
      </div>
    </div>

    <div class="panel-footer">
      <div>
      </div>
      {{-- Save button --}}
      <div>
        <a href="javascript:void(0)" class="button" id="save-submit">
          <i class="fa fa-save" aria-hidden="true"></i> {{ trans('general.save') }}
        </a>
      </div>
    </div>
    {!! Form::close() !!}


  </section>

@stop


@section('after-scripts')

@include('frontend.user.location.' . config('settings.location_api') )

<link href="{{ asset('vendor/backpack/summernote/summernote_frontend.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('vendor/backpack/summernote/summernote.js')}}"></script>

<script type="text/javascript">

$(function() {

  // We can attach the `fileselect` event to all file inputs on the page
  $(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  // We can watch for our custom `fileselect` event like this
  $(document).ready( function() {
      $(':file').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
  });

});

$(document).ready(function(){


  $("#favorite-game").on("keypress", debounce(function(){
    var typedValue = $(this).val()
    $("#game-list").empty();
    $.ajax({
      url: "/api/product/search",
      data: {q: typedValue, g: true},
      success: function (response) {
        $.each(response.data, function(i, item){
          $("#game-list").append($("<option>").attr('data-value', item.id).text(item.name));
        })
      }
    });
  }, 500));

  $("#favorite-dev").on("keypress", debounce(function(){
    var typedValue = $(this).val()
    $("#dev-list").empty();
    $.ajax({
      url: "/api/developers/active",
      data: {q: typedValue},
      success: function (response) {
        $.each(response.data, function(i, item){
          $("#dev-list").append($("<option>").attr('data-value', item.id).text(item.name));
        })
      }
    });
  }, 500));

  $("#favorite-pub").on("keypress", debounce(function(){
    var typedValue = $(this).val()
    $("#pub-list").empty();
    $.ajax({
      url: "/api/publishers/active",
      data: {q: typedValue},
      success: function (response) {
        $.each(response.data, function(i, item){
          $("#pub-list").append($("<option>").attr('data-value', item.id).text(item.name));
        })
      }
    });
  }, 500));


  {{-- password submit --}}
  $("#save-submit").click( function(){
    $('#save-submit').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#save-submit').addClass('loading');
    $('#form-settings').submit();
  });

  $('#about-me').summernote({
    toolbar: [
      // [groupName, [list of button]]
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['font', ['strikethrough']],
      ['para', ['ul', 'ol']],
    ],
    disableDragAndDrop: true,
    focus: true,
    color: 'white',
    width: "100%"
  }).on("summernote.paste",function(e,ne) {

    var bufferText = ((ne.originalEvent || ne).clipboardData || window.clipboardData).getData('Text');
    ne.preventDefault();
    document.execCommand('insertText', false, bufferText);

  });

})
</script>
@stop
