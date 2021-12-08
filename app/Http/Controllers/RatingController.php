<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Product;
use DB;

class RatingController extends Controller
{
    public function save()
    {
        if (!auth()->user()) {
            return response()->json(["unauthorized"], 401);
        }
        if (request()->rating < 0.5 || request()->rating > 5) {
            return response()->json('Invalid Rating', 422);
        }
        $user_id = auth()->user()->id;
        $product = Product::find(request()->game_id);
        Rating::updateOrCreate([
                'game_id' => request()->game_id,
                'user_id' => $user_id
            ],
            ['rating' => request()->rating]
        );
        $average_rating = Rating::selectRaw("AVG(rating) as average_rating")->join('games', 'games.id', '=', 'game_rating.game_id')->where('games.rating_game_id', $product->rating_game_id)->groupBy('games.rating_game_id')->first();
        Product::where('rating_game_id', $product->rating_game_id)->update(['average_rating' => $average_rating->average_rating]);

        return response()->json($average_rating, 200);
    }

    public function delete(Request $request, $game_id)
    {
        if (!auth()->user()) {
            return response()->json(["unauthorized"], 401);
        }
        // update rating to be null
        Rating::where('game_id', $game_id)->where('user_id', auth()->user()->id)->update(['rating' => null]);

        // Delete if rating, difficulty and duration are null
        Rating::where('game_id', $game_id)->where('user_id', auth()->user()->id)->whereNull('rating')->whereNull('difficulty')->whereNull('duration')->delete();

        // recalculate product rating average
        $product = Product::find($game_id);
        $average_rating = Rating::selectRaw("AVG(rating) as average_rating")->join('games', 'games.id', '=', 'game_rating.game_id')->where('games.rating_game_id', $product->rating_game_id)->groupBy('games.rating_game_id')->first();
        $rating = isset($average_rating->average_rating) ? $average_rating->average_rating : null;
        Product::where('rating_game_id', $product->rating_game_id)->update(['average_rating' => $rating]);

        return response()->json($average_rating, 200);
    }

    public function saveDifficulty()
    {
        if (!auth()->user()) {
            return response()->json(["unauthorized"], 401);
        }
        if (request()->difficulty < 0.5 || request()->difficulty > 5) {
            return response()->json('Invalid Difficulty', 422);
        }
        $user_id = auth()->user()->id;
        $product = Product::find(request()->game_id);
        Rating::updateOrCreate([
            'game_id' => request()->game_id,
            'user_id' => $user_id
        ],
            ['difficulty' => request()->difficulty]
        );
        $average_difficulty = Rating::selectRaw("AVG(difficulty) as average_difficulty")->join('games', 'games.id', '=', 'game_rating.game_id')->where('games.rating_game_id', $product->rating_game_id)->groupBy('games.rating_game_id')->first();
        Product::where('rating_game_id', $product->rating_game_id)->update(['average_difficulty' => $average_difficulty->average_difficulty]);

        return response()->json($average_difficulty, 200);
    }

    public function deleteDifficulty(Request $request, $game_id)
    {
        if (!auth()->user()) {
            return response()->json(["unauthorized"], 401);
        }
        // update rating to be null
        Rating::where('game_id', $game_id)->where('user_id', auth()->user()->id)->update(['difficulty' => null]);

        // Delete if rating, difficulty and duration are null
        Rating::where('game_id', $game_id)->where('user_id', auth()->user()->id)->whereNull('rating')->whereNull('difficulty')->whereNull('duration')->delete();

        // recalculate product rating average
        $product = Product::find($game_id);
        $average_difficulty = Rating::selectRaw("AVG(difficulty) as average_difficulty")->join('games', 'games.id', '=', 'game_rating.game_id')->where('games.rating_game_id', $product->rating_game_id)->groupBy('games.rating_game_id')->first();
        $rating = isset($average_difficulty->average_difficulty) ? $average_difficulty->average_difficulty : null;
        Product::where('rating_game_id', $product->rating_game_id)->update(['average_difficulty' => $rating]);

        return response()->json($average_difficulty, 200);
    }

    public function saveDuration()
    {
        if (!auth()->user()) {
            return response()->json(["unauthorized"], 401);
        }
        if (request()->duration < 0 || request()->duration > 999 * 60 + 59) {
            return response()->json('Invalid Duration', 422);
        }
        $user_id = auth()->user()->id;
        $product = Product::find(request()->game_id);
        Rating::updateOrCreate([
            'game_id' => request()->game_id,
            'user_id' => $user_id
        ],
            ['duration' => request()->duration]
        );
        $average_duration = Rating::selectRaw("AVG(duration) as average_duration")->join('games', 'games.id', '=', 'game_rating.game_id')->where('games.rating_game_id', $product->rating_game_id)->groupBy('games.rating_game_id')->first();
        Product::where('rating_game_id', $product->rating_game_id)->update(['average_duration' => $average_duration->average_duration]);

        return response()->json($average_duration, 200);
    }

    public function deleteDuration(Request $request, $game_id)
    {
        if (!auth()->user()) {
            return response()->json(["unauthorized"], 401);
        }
        // update rating to be null
        Rating::where('game_id', $game_id)->where('user_id', auth()->user()->id)->update(['duration' => null]);

        // Delete if rating, difficulty and duration are null
        Rating::where('game_id', $game_id)->where('user_id', auth()->user()->id)->whereNull('rating')->whereNull('difficulty')->whereNull('duration')->delete();

        // recalculate product rating average
        $product = Product::find($game_id);
        $average_duration = Rating::selectRaw("AVG(duration) as average_duration")->join('games', 'games.id', '=', 'game_rating.game_id')->where('games.rating_game_id', $product->rating_game_id)->groupBy('games.rating_game_id')->first();
        $rating = isset($average_duration->average_duration) ? $average_duration->average_duration : null;
        Product::where('rating_game_id', $product->rating_game_id)->update(['average_duration' => $rating]);

        return response()->json($average_duration, 200);
    }
}
