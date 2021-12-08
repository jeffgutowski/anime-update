@php
$friend = App\Models\User::where('id', $notification->data['friend_id'] )->first();
@endphp
{{-- Start Notification --}}
<a class="notification hvr-grow-shadow2 {{ $notification->read_at ? 'grayscale' : '' }}" href="/friends/pending" data-notif-id="{{$notification->id}}">
  <div class="icons">
    <span class="avatar no-flex-shrink">
      <img src="{{$friend->avatar_square_tiny}}">
    </span>
  </div>
  <div class="info">
    {{-- Notification text --}}
    <h1>
      {{ $friend->name }} has requested to be friends with you.
    </h1>
    {{-- Notificaion icon and date --}}
    <p><i class="fa fa-comment"></i> {{$notification->created_at->diffForHumans()}}</p>
  </div>
</a>
{{-- End notification --}}
