<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\FriendRequest;
use App\Notifications\FriendAccepted;

class FriendController extends Controller
{

    public function index()
    {
        $friends = Friend::where('user_id', auth()->id())
            ->select('friends.*', 'fr.name')
            ->with('user', 'friend.location')
            ->join('users as fr', 'fr.id', '=', 'friends.friend_id')
            ->where('friends.status', '=', 'friend')
            ->where('fr.status', '=', true)
            ->orderByRaw('UPPER(fr.name) ASC')
            ->paginate('24');

        return view('frontend.friends.index', ['friends' => $friends, 'friend_count' => $this->friendCount(), 'pending_count' => $this->pendingCount()]);
    }

    public function search()
    {
        $friends = [];
        if (request()->has('q')) {
            $search = request()->get('q');

            $friends = User::select("users.*")
                ->leftJoin('friends', function($join) {
                    $join->on('friends.friend_id', '=', 'users.id')
                        ->where('friends.user_id', '=', auth()->id());
                })
                ->where('name', 'LIKE', "%$search%")
                ->whereNull('friends.status')
                ->where('users.status', '=', true)
                ->where('users.id', '!=', auth()->id())
                ->groupBy('users.id')
                ->orderByRaw('UPPER(name) ASC')
                ->paginate('24');
        }

        return view('frontend.friends.index', ['friends' => $friends, 'friend_count' => $this->friendCount(), 'pending_count' => $this->pendingCount()]);
    }

    public function pending()
    {
        $friends = Friend::where('user_id', auth()->id())
            ->select('friends.*', 'fr.name')
            ->with('user', 'friend.location')
            ->join('users as fr', 'fr.id', '=', 'friends.friend_id')
            ->where(function($query) {
                $query->where('friends.status', '=', 'pending')
                    ->orWhere('friends.status', '=', 'requested');
            })
            ->where('fr.status', '=', true)
            ->orderBy('friends.status', 'ASC')
            ->orderByRaw('UPPER(fr.name) ASC')
            ->paginate('24');

        return view('frontend.friends.index', ['friends' => $friends, 'friend_count' => $this->friendCount(), 'pending_count' => $this->pendingCount()]);
    }

    public function delete(Request $request, $friend_id)
    {
        return Friend::where(function($query) use ($friend_id) {
                $query->where('user_id', auth()->id())
                ->where('friend_id', $friend_id);
            })->orWhere(function($query) use ($friend_id) {
                $query->where('user_id', $friend_id)
                    ->where('friend_id', auth()->id());
            })->delete();
    }

    public function add(Request $request, $friend_id)
    {
        $friend = Friend::firstOrNew(
            ['friend_id' => auth()->id(), 'user_id' => $friend_id]
        );
        if (is_null($friend->status)) {
            $friend->status = "pending";
            $friend->save();
        }
        $self = Friend::firstOrNew(
            ['user_id' => auth()->id(), 'friend_id' => $friend_id]
        );
        if(is_null($self->status)) {
            $self->status = "requested";
            $self->save();
            $self->friend->notify(new FriendRequest($friend->friend));
        }
        return ['self' => $self, 'friend' => $friend];
    }

    public function accept(Request $request, $friend_id)
    {
        $update = Friend::where(function ($query_self) use ($friend_id) {
            $query_self->where('user_id', auth()->id())
                ->where('friend_id', $friend_id)
                ->where('status', 'pending');
        })->orWhere(function ($query_friend) use ($friend_id) {
            $query_friend->where('user_id', $friend_id)
                ->where('friend_id', auth()->id())
                ->where('status', 'requested');
        })->update(['status' => 'friend']);
        if ($update) {
            $user = User::where('id', $friend_id)->first();
            $user->notify(new FriendAccepted(auth()->user()));
        }
        return $update;
    }

    public function reject(Request $request, $friend_id)
    {
        return Friend::where(function ($query_self) use ($friend_id) {
            $query_self->where('user_id', auth()->id())
                ->where('friend_id', $friend_id)
                ->where('status', 'pending');
        })->orWhere(function ($query_friend) use ($friend_id) {
            $query_friend->where('user_id', $friend_id)
                ->where('friend_id', auth()->id())
                ->where('status', 'requested');
        })->delete();
    }

    private function friendCount()
    {
        return Friend::where('user_id', auth()->id())
            ->select('friends.*', 'fr.name')
            ->with('user', 'friend.location')
            ->join('users as fr', 'fr.id', '=', 'friends.friend_id')
            ->where('friends.status', '=', 'friend')
            ->where('fr.status', '=', true)
            ->count();
    }

    private function pendingCount()
    {
        return Friend::where('user_id', auth()->id())
            ->select('friends.*', 'fr.name')
            ->with('user', 'friend.location')
            ->join('users as fr', 'fr.id', '=', 'friends.friend_id')
            ->where(function($query) {
                $query->where('friends.status', '=', 'pending')
                    ->orWhere('friends.status', '=', 'requested');
            })
            ->where('fr.status', '=', true)
            ->count();
    }
}
