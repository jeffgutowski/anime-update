<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Developer;
use Searchy;

class DeveloperController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        return ['data' =>
            Developer::select(\DB::raw("developers.id, developers.name, MATCH (developers.name) AGAINST ('$searchTerm') AS `rank`"))
                ->where(function($query) use ($searchTerm) {
                    $query->whereRaw("MATCH (developers.name) AGAINST ('$searchTerm')")
                        ->orWhere('name', 'LIKE', "%$searchTerm%");
                })
                ->orderBy('rank', 'desc')
                ->groupBy('developers.id')->limit(10)->get()
        ];
    }

    public function searchActive(Request $request)
    {
        $searchTerm = $request->input('q');
        return ['data' =>
            Developer::select(\DB::raw("developers.id, developers.name, MATCH (developers.name) AGAINST ('$searchTerm') AS `rank`"))
                ->where(function($query) use ($searchTerm) {
                    $query->whereRaw("MATCH (developers.name) AGAINST ('$searchTerm')")
                        ->orWhere('developers.name', 'LIKE', "%$searchTerm%");
                })
                ->join('developer_game', 'developer_game.developer_id', '=', 'developers.id')
                ->join('games', 'games.id', '=', 'developer_game.game_id')
                ->whereNull('games.deleted_at')
                ->orderBy('rank', 'desc')
                ->groupBy('developers.id')->limit(10)->get()
        ];
    }
    
    public function show($id)
    {
        return Developer::find($id);
    }
}
