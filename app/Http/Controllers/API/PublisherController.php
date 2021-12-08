<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Searchy;

class PublisherController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        return ['data' =>
            Publisher::select(\DB::raw("publishers.id, publishers.name, MATCH (publishers.name) AGAINST ('$searchTerm') AS `rank`"))
                ->where(function($query) use ($searchTerm) {
                    $query->whereRaw("MATCH (publishers.name) AGAINST ('$searchTerm')")
                        ->orWhere('name', 'LIKE', "%$searchTerm%");
                })
                ->orderBy('rank', 'desc')
                ->groupBy('publishers.id')->limit(10)->get()
        ];
    }

    public function searchActive(Request $request)
    {
        $searchTerm = $request->input('q');
        return ['data' =>
            Publisher::select(\DB::raw("publishers.id, publishers.name, MATCH (publishers.name) AGAINST ('$searchTerm') AS `rank`"))
                ->where(function($query) use ($searchTerm) {
                    $query->whereRaw("MATCH (publishers.name) AGAINST ('$searchTerm')")
                        ->orWhere('publishers.name', 'LIKE', "%$searchTerm%");
                })
                ->join('game_publisher', 'game_publisher.publisher_id', '=', 'publishers.id')
                ->join('games', 'games.id', '=', 'game_publisher.game_id')
                ->whereNull('games.deleted_at')
                ->where('region', '=', session('region.abbr'))
                ->orderBy('rank', 'desc')
                ->groupBy('publishers.id')->limit(10)->get()
        ];
    }

    public function show($id)
    {
        return Publisher::find($id);
    }
}
