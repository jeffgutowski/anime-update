<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Franchise;
use Searchy;

class FranchiseController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        return ['data' => Searchy::franchises('name')->query($searchTerm)->getQuery()->limit(10)->get()];
    }

    public function show($id)
    {
        return Franchise::find($id);
    }
}
