<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Game;
use App\Models\AccessoriesHardware;

class ProductController extends Controller
{
    public function searchProduct(Request $request)
    {
        $searchTerm = $request->input('q');
        $limit = $request->input('limit', 25) > 100 ? 100 : $request->input('limit', 25);
        $group = $request->input('g', false);

        $games = Product::selectRaw("*, MATCH (games.name, games.name_us, games.name_jp, games.name_eu) AGAINST (?) AS `rank`")
            ->whereRaw("MATCH (games.name, games.name_us, games.name_jp, games.name_eu) AGAINST (?)")
            ->whereNotNull(session('region.code'))
            ->setBindings([$searchTerm, $searchTerm])
            ->with('platform')
            ->orderBy('rank', 'desc')
            ->limit($limit);

        if ($group) {
            $games = $games->groupBy('name');
        }

        if ($request->input('p')) {
            $games->where('platform_id', $request->input('p'));
        }

        $games = $games->get();
        foreach ($games as &$game) {
            $game->name_and_platform = $game->name." <".$game->platform->name.">";
        }
        return ['data' => $games];
    }

    public function showProduct($id)
    {
        return Product::find($id);
    }

    public function searchGame(Request $request)
    {
        $searchTerm = $request->input('q');
        $games = Game::selectRaw("*, MATCH (name) AGAINST ('$searchTerm' IN BOOLEAN MODE) AS `rank`")
            ->whereRaw("MATCH (name) AGAINST ('$searchTerm' IN BOOLEAN MODE)")
            ->whereNotNull(session('region.code'))
            ->with('platform')
            ->orderBy('rank', 'desc')
            ->limit(25)->get();
        foreach ($games as &$game) {
            $game->name_and_platform = $game->name." <".$game->platform->name.">";
        }
        return response()->json(['data' => $games]);
    }

    public function showGame($id)
    {
        return Game::find($id);
    }

    public function searchAccessoriesHardware(Request $request)
    {
        $searchTerm = $request->input('q');
        $games = AccessoriesHardware::selectRaw("*, MATCH (name) AGAINST ('$searchTerm' IN BOOLEAN MODE) AS `rank`")
            ->whereRaw("MATCH (name) AGAINST ('$searchTerm' IN BOOLEAN MODE)")
            ->whereNotNull(session('region.code'))
            ->with('platform')
            ->orderBy('rank', 'desc')
            ->limit(25)->get();
        foreach ($games as &$game) {
            $game->name_and_platform = $game->name." <".$game->platform->name.">";
        }
        return ['data' => $games];
    }

    public function showAccessoriesHardware($id)
    {
        return AccessoriesHardware::find($id);
    }
}
