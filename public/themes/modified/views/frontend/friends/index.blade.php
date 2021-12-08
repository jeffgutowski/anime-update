@extends(Theme::getLayout())

@section('subheader')
    <div class="subheader tabs">

        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
        <div class="background-color"></div>

        <div class="content">
            <span class="title"><i class="fas fa-user-friends"></i> Friends</span>
        </div>
        <div class="tabs">
            <a class="tab {{request()->path() == "friends" ? "active" : null}}" href="{{url('friends')}}">
                Your Friends {{$friend_count > 0 ? "($friend_count)" : ""}}
            </a>
            <a class="tab {{request()->path() == "friends/pending" ? "active" : null}}" href="{{url('friends/pending')}}">
                Pending Requests {{$pending_count > 0 ? "($pending_count)" : ""}}
            </a>
            <a class="tab {{request()->path() == "friends/add" ? "active" : null}}" href="{{url('friends/add')}}">
                Add Friend
            </a>
        </div>

    </div>

@stop


@section('content')

@if(is_null(request()->segment(2)))
    {{-- Pagination Link before wishlist entries --}}
    {{ $friends->links() }}

    @forelse($friends as $friend)
        <div class="friends">
            <div class="flex-center-space m-b-20">
                <div class="flex-center">
                    <a href="/user/{{$friend->friend->name}}" target="_blank">
                        {{-- User Avatar --}}
                        <span class="hidden-xs-down avatar profile-avatar m-r-20 {{ $friend->friend->isOnline() ? 'avatar-online' : 'avatar-offline' }}">
                            <img src="{{$friend->friend->avatar_square}}" alt="{{$friend->friend->name}}'s Avatar"><i></i>
                        </span>
                    </a>
                    <div>
                        <a href="/user/{{$friend->friend->name}}" target="_blank">
                            {{-- User Name & Location --}}
                            <span class="profile-name dont-break-out">
                                {{$friend->friend->name}}
                            </span>
                        </a>
                        <span class="profile-location">
                            @if($friend->friend->location)
                                <img src="{{ asset('img/flags/' .   $friend->friend->location->country_abbreviation . '.svg') }}" height="16"/> {{$friend->friend->location->country_abbreviation}}, {{$friend->friend->location->place}} <span class="postal-code">{{$friend->friend->location->postal_code}}</span>
                            @endif
                        </span>
                        <span>
                            <button class="btn btn-danger btn-friend remove-friend" onclick="removeFriend(this, {{$friend->friend_id}}, '{{$friend->friend->name}}')">Remove</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    @empty
        {{-- Start empty list message --}}
        <div class="empty-list">
            {{-- Icon --}}
            <div class="icon">
                <i class="far fa-frown" aria-hidden="true"></i>
            </div>
            {{-- Text --}}
            <div class="text">
                No Friends
            </div>
        </div>
        {{-- End empty list message --}}
    @endforelse

    {{-- Pagination Link after wishlist entries --}}
    {{ $friends->links() }}
@elseif(request()->segment(2) == 'add')

    <div class="text-center">
        <label class="friend-search-label">Search Friends: </label>
        <input id="search-friends" class="dark-input" type="text">
        <button id="search-btn" class="btn btn-primary friend-search-btn" onclick="search()">Search</button>
    </div>

    @if(request()->has('q'))
        {{ $friends->links() }}

        @forelse($friends as $friend)
            <div class="friends">
                <div class="flex-center-space m-b-20">
                    <div class="flex-center">
                        {{-- User Avatar --}}
                        <a href="/user/{{$friend->name}}" target="_blank">
                            <span class="hidden-xs-down avatar profile-avatar m-r-20 {{ $friend->isOnline() ? 'avatar-online' : 'avatar-offline' }}">
                                <img src="{{$friend->avatar_square}}" alt="{{$friend->name}}'s Avatar"><i></i>
                            </span>
                        </a>
                        <div>
                            <a href="/user/{{$friend->name}}" target="_blank">
                                {{-- User Name & Location --}}
                                <span class="profile-name dont-break-out">
                                    {{$friend->name}}
                                </span>
                            </a>
                            <span class="profile-location">
                                @if($friend->location)
                                    <img src="{{ asset('img/flags/' .   $friend->location->country_abbreviation . '.svg') }}" height="16"/> {{$friend->location->country_abbreviation}}, {{$friend->location->place}} <span class="postal-code">{{$friend->location->postal_code}}</span>
                                @endif
                            </span>
                            <span>
                                <button class="btn btn-success btn-friend" onclick="friendAction(this, {{$friend->id}}, '/friends/', 'Requested')">Request</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            {{-- Start empty list message --}}
            <div class="empty-list">
                {{-- Icon --}}
                <div class="icon">
                    <i class="far fa-frown" aria-hidden="true"></i>
                </div>
                {{-- Text --}}
                <div class="text">
                    None Found
                </div>
            </div>
            {{-- End empty list message --}}
        @endforelse

        {{-- Pagination Link after wishlist entries --}}
        {{ $friends->links() }}
    @endif
@elseif(request()->segment(2) == 'pending')
    {{ $friends->links() }}

    @forelse($friends as $friend)
        <div class="friends">
            <div class="flex-center-space m-b-20">
                <div class="flex-center">
                    <a href="/user/{{$friend->name}}" target="_blank">
                        {{-- User Avatar --}}
                        <span class="hidden-xs-down avatar profile-avatar m-r-20 {{ $friend->friend->isOnline() ? 'avatar-online' : 'avatar-offline' }}">
                            <img src="{{$friend->friend->avatar_square}}" alt="{{$friend->friend->name}}'s Avatar"><i></i>
                        </span>
                    </a>
                    <div>
                        <a href="/user/{{$friend->name}}" target="_blank">
                            {{-- User Name & Location --}}
                            <span class="profile-name dont-break-out">
                                {{$friend->friend->name}}
                            </span>
                        </a>
                        <span class="profile-location">
                            @if($friend->friend->location)
                                <img src="{{ asset('img/flags/' .   $friend->friend->location->country_abbreviation . '.svg') }}" height="16"/> {{$friend->friend->location->country_abbreviation}}, {{$friend->friend->location->place}} <span class="postal-code">{{$friend->friend->location->postal_code}}</span>
                            @endif
                        </span>
                        <span>
                            @if($friend->status == 'pending')
                                <button class="btn btn-success btn-friend" onclick="friendAction(this, {{$friend->friend->id}}, '/friends/accept/', 'Accepted')">Accept</button>
                                <button class="btn btn-danger btn-friend" onclick="friendAction(this, {{$friend->friend->id}}, '/friends/reject/', 'Rejected')">Reject</button>
                            @else
                                <div class="friend-requested">Requested</div>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

    @empty
        {{-- Start empty list message --}}
        <div class="empty-list">
            {{-- Icon --}}
            <div class="icon">
                <i class="far fa-frown" aria-hidden="true"></i>
            </div>
            {{-- Text --}}
            <div class="text">
                No Pending Request
            </div>
        </div>
        {{-- End empty list message --}}
    @endforelse

    {{-- Pagination Link after wishlist entries --}}
    {{ $friends->links() }}
@endif
</div>
@section('after-scripts')

    <script>
        // remove a user as your friend
        function removeFriend(self, friend_id, name) {
            if (confirm('Are you sure you want to remove '+name+' as a friend?')) {
                $(self).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
                $(self).addClass('loading')
                setTimeout(function(){
                    $.ajax({
                        type: 'DELETE',
                        url: "/friends/"+friend_id,
                        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                        success: function (response) {
                            let parent = $(self).parent()
                            parent.html('Removed').addClass('friend-requested')
                        }
                    });
                }, 1000);
            }
        }

        // request a user to be friends
        function friendAction(self, friend_id, action, message) {
            $(self).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
            $(self).addClass('loading')
            setTimeout(function(){
                $.ajax({
                    type: 'POST',
                    url: action+friend_id,
                    headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                    success: function (response) {
                        let parent = $(self).parent()
                        parent.html(message).addClass('friend-requested')
                    }
                });
            }, 1000);
        }

        // search users for available friends
        function search() {
            let search_term = $('#search-friends').val()
            if (search_term.length > 0) {
                return window.location = '/friends/add?q=' + search_term
            }
        }

        // search on enter press
        $(document).on('keypress',function(e) {
            if(e.which == 13) {
                search();
            }
        });

        $(document).ready(function() {

        });
    </script>
@endsection

@stop
