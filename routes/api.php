<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/games/{id}', function ($id) {

    $game = \App\Models\Game::find($id);

    if (!$game) {
        return abort('404');
    }

    $data = array();

    $data['id'] = $game->id;
    $data['name'] = $game->name;
    $data['pic'] = $game->image_square_tiny;
    $data['console_name'] = $game->platform->name;
    $data['console_color'] = $game->platform->color;
    $data['listings'] = $game->listings_count;
    $data['cheapest_listing'] = $game->cheapest_listing;
    $data['url'] = $game->url_slug;

    return response()->json($data);

});

Route::get('/digitals/{acronym}', function ($acronym) {

    $platform = \App\Models\Platform::where('acronym', $acronym)->first();

    if(!$platform) {
        return abort('404');
    }

    return response()->json($platform->digitals);

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/product/search', 'API\ProductController@searchProduct');
Route::get('/product/{id}', 'API\ProductController@showProduct');
Route::get('/game/search', 'API\ProductController@searchGame');
Route::get('/game/{id}', 'API\ProductController@showGame');
Route::get('/accessories/search', 'API\ProductController@searchAccessoriesHardware');
Route::get('/accessories/{id}', 'API\ProductController@showAccessoriesHardware');
Route::get('/franchises/search', 'API\FranchiseController@search');
Route::get('/franchises/{id}', 'API\FranchiseController@show');
Route::get('/developers/search', 'API\DeveloperController@search');
Route::get('/developers/active', 'API\DeveloperController@searchActive');
Route::get('/developers/{id}', 'API\DeveloperController@show');
Route::get('/publishers/search', 'API\PublisherController@search');
Route::get('/publishers/active', 'API\PublisherController@searchActive');
Route::get('/publishers/{id}', 'API\PublisherController@show');
Route::get('/companies/search', 'API\CompanyController@search');
Route::get('/companies/active', 'API\CompanyController@searchActive');
Route::get('/companies/{id}', 'API\CompanyController@show');
