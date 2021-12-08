<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SimilarityController extends Controller
{
    public function calculate()
    {
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }
        $self = auth()->user();
        $friend = User::find(request()->get('friend_id'));

        if (!$friend) {
            abort(400, 'Bad request');
        }
        // Total of games self owns
        $selfOwnTotal = \App\Models\HaveList::select('game_id')->where('user_id', $self->id)->groupBy('game_id')->get()->count();

        // Games count of how many games friends owns from self collection
        $friendOwnsCount = \App\Models\HaveList::select('games.id', 'games.name')
            ->join('games', 'game_have_lists.game_id', '=', 'games.id')
            ->join('game_have_lists as hl', function($join) use ($friend) {
                $join->on('hl.game_id', '=', 'game_have_lists.game_id')
                    ->where('hl.user_id', $friend->id);
            })
            ->where('game_have_lists.user_id', $self->id)
            ->groupBy('game_have_lists.game_id')->orderBy('games.name')
            ->get()
            ->count();

        // Total of friends games
        $friendOwnTotal = \App\Models\HaveList::select('game_id')->where('user_id', $friend->id)->groupBy('game_id')->get()->count();

        // Games count of how many games self owns from friends collection
        $selfOwnsCount = \App\Models\HaveList::select('games.id', 'games.name')
            ->join('games', 'game_have_lists.game_id', '=', 'games.id')
            ->join('game_have_lists as hl', function($join) use ($self) {
                $join->on('hl.game_id', '=', 'game_have_lists.game_id')
                    ->where('hl.user_id', $self->id);
            })
            ->where('game_have_lists.user_id', $friend->id)
            ->groupBy('game_have_lists.game_id')->orderBy('games.name')
            ->get()
            ->count();

        // Total of games self has played
        $selfPlayedTotal = \App\Models\CompletedList::select('game_id')->where('user_id', $self->id)->groupBy('game_id')->get()->count();

        // Games count of how many games friend completed from self completed games
        $friendPlayedCount = \App\Models\CompletedList::select('games.id', 'games.name')
            ->join('games', 'game_completed_lists.game_id', '=', 'games.id')
            ->join('game_completed_lists as cl', function($join) use ($friend) {
                $join->on('cl.game_id', '=', 'game_completed_lists.game_id')
                    ->where('cl.user_id', $friend->id);
            })
            ->where('game_completed_lists.user_id', $self->id)
            ->groupBy('game_completed_lists.game_id')->orderBy('games.name')
            ->get()
            ->count();


        // Total of friends games
        $friendPlayedTotal = \App\Models\CompletedList::select('game_id')->where('user_id', $friend->id)->groupBy('game_id')->get()->count();

        // Games count of how many games self completed from friends completed
        $selfPlayedCount = \App\Models\CompletedList::select('games.id', 'games.name')
            ->join('games', 'game_completed_lists.game_id', '=', 'games.id')
            ->join('game_completed_lists as cl', function($join) use ($self) {
                $join->on('cl.game_id', '=', 'game_completed_lists.game_id')
                    ->where('cl.user_id', $self->id);
            })
            ->where('game_completed_lists.user_id', $friend->id)
            ->groupBy('game_completed_lists.game_id')->orderBy('games.name')
            ->get()
            ->count();

        return [
            'percent_friend_owns_from_self' => ($friendOwnsCount > 0 && $selfOwnTotal > 0) ? round($friendOwnsCount / $selfOwnTotal * 100) : 0,
            'percent_self_owns_of_friend' => ($selfOwnsCount > 0 && $friendOwnTotal > 0) ? round($selfOwnsCount / $friendOwnTotal * 100) : 0,
            'percent_friend_played_from_self' => ($friendPlayedCount > 0 && $selfPlayedTotal > 0) ? round($friendPlayedCount / $selfPlayedTotal * 100) : 0,
            'percent_self_played_of_friend' => ($selfPlayedCount > 0 && $friendPlayedTotal > 0) ? round($selfPlayedCount / $friendPlayedTotal * 100) : 0,
        ];
    }
}
