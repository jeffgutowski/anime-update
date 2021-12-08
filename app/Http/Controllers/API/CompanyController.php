<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Searchy;

class CompanyController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        return ['data' => Searchy::accessories_hardware_companies('name')->query($searchTerm)->getQuery()->limit(10)->get()];
    }

    public function show($id)
    {
        return Company::find($id);
    }
}
