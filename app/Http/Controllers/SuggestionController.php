<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Genre;
use App\Models\Product;
use App\Models\Platform;
use App\Models\Suggestion;
use App\Models\AccessoriesHardwareType as ProductType;
use App\Models\AccessoriesHardwareCompanies as Company;

class SuggestionController extends Controller
{
    public function __construct()
    {
        $this->platforms = Platform::where(function($query){
            $query->where('ntsc_u', 1)
                ->orWhere('ntsc_j', 1)
                ->orWhere('pal', 1);
        })->orderBy('name')->get();
        $this->genres = Genre::orderBy('name')->get();
    }

    public function new()
    {
        return view('frontend.suggestions.form', [
            'platforms' => $this->platforms,
            'types' => ProductType::all(),
            'genres' => $this->genres,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $product = Product::where('id', $id)->with('genres', 'publishers')->first();
        $this->formatProduct($product);

        return view('frontend.suggestions.form', [
            'platforms' => $this->platforms,
            'types' => ProductType::all(),
            'genres' => $this->genres,
            'after' => $product,
        ]);
    }

    public function submit()
    {
        $genres = Genre::select(['id', 'name'])->orderBy('name')->get();
        $request = request()->all();
        $request['user_id'] = auth()->user()->id;
        // Genre Edits
        $request['genres'] = [];
        foreach ($request as $key => $value) {
            if (strpos($key, "genre-") !== false) {
                $genreId = (int) str_replace('genre-', '', $key);
                foreach ($genres as $genre) {
                    if ($genreId == $genre->id) {
                        $genreName = $genre->name;
                    }
                }
                $request['genres'][] = $genreName;
            }
        }
        $request['genres'] = json_encode($request['genres']);

        // Component Edits
        foreach (config('components.all') as $component) {
            if (isset($request[$component]) && $request[$component] == 'on') {
                $request[$component] = true;
            }
        }
        // cover
        foreach (['us', 'jp', 'eu'] as $region) {
            if (isset($request['cover_'.$region])) {
                $request['cover_'.$region] = $this->convertImage($region, $request['cover_'.$region]);
            }
        }
        Suggestion::create(array_filter($request));

        \Alert::success('<i class="fa fa-save m-r-5"></i> Suggestion Submitted')->flash();
        return redirect()->to("/suggestion");

    }

    public function review(Request $request, $id)
    {
        $suggestion = Suggestion::find($id);
        $product = null;
        if ($suggestion->product_id) {
            $product = Product::where('id', $suggestion->product_id)->with('genres', 'publishers')->first();
            $product = $this->formatProduct($product);
        }
        $suggestion = $this->formatSuggestion($suggestion);
        return view('frontend.suggestions.form', [
            'platforms' => $this->platforms,
            'types' => ProductType::all(),
            'genres' => $this->genres,
            'before' => $product,
            'after' => $suggestion,
        ]);
    }

    public function approve(Request $request)
    {
        // format fields to save correctly
        $fields = $request->all();
        // create manufacturer/company if it doesn't exist
        if (isset($fields['company'])) {
            $company = Company::firstOrCreate([
                'name' => $fields['company']
            ]);
        }
        // format components for the product
        $platform = Platform::find($fields['platform_id']);
        if (isset($platform)) {
            foreach (config('components.all') as $component) {
                // component not possible on platform, set to null
                if ($platform->$component == false || $fields['type'] != 'game') {
                    $fields[$component] = null;
                } else if (isset($fields[$component]) && $fields[$component] == 'on') {
                    $fields[$component] = true;
                } else {
                    $fields[$component] = false;
                }
            }
        }
        $developers = array_keys(json_decode($fields['developers'], 1));
        $fields['developers'] = $developers;

        // format genres to sync
        $genres = [];
        foreach ($fields as $key => $value) {
            if (strpos($key, "genre-") !== false) {
                $genres[] = (int) str_replace('genre-', '', $key);
            }
        }
        $fields['genres'] = $genres;

        $product = Product::updateOrCreate(
            ['id' => $request->get('product_id')],
            $fields
        );

        // associate developers
        $product->developers()->sync($fields['developers']);

        // associate genres
        $product->genres()->sync($fields['genres']);

        // associate publishers
        $publishers = [];
        foreach (json_decode($fields['publishers_us']) as $id => $publisher) {
            $publishers[] = ['game_id' => $product->id, 'publisher_id' => $id, 'region' => 'us'];
        }
        foreach (json_decode($fields['publishers_jp']) as $id =>  $publisher) {
            $publishers[] = ['game_id' => $product->id, 'publisher_id' => $id, 'region' => 'jp'];
        }
        foreach (json_decode($fields['publishers_eu']) as $id =>  $publisher) {
            $publishers[] = ['game_id' => $product->id, 'publisher_id' => $id, 'region' => 'publishers_eu'];
        }
        $product->publishers()->sync($publishers);

        // update suggestion to be reviewed
        $suggestion = Suggestion::find($request->get('suggestion_id'));
        $suggestion->review_comments = $request->get('review_comments');
        $suggestion->reviewed_at = date('Y-m-d H:i:s');
        $suggestion->reviewer_id = auth()->user()->id;
        $suggestion->approved = true;
        $suggestion->save();
        \Alert::success('<i class="fa fa-save m-r-5"></i> Suggestion Approved')->flash();
        return redirect("/admin/suggestion");
    }

    public function disapprove(Request $request, $id)
    {
        $suggestion = Suggestion::find($id);
        $suggestion->review_comments = $request->get('review_comments');
        $suggestion->reviewed_at = date('Y-m-d H:i:s');
        $suggestion->reviewer_id = auth()->user()->id;
        $suggestion->approved = false;
        $suggestion->save();
        \Alert::error('<i class="fa fa-save m-r-5"></i> Suggestion Disapproved')->flash();
        return redirect("/admin/suggestion");
    }

    private function formatProduct($product)
    {
        $product->platform_name = $product->platform->name;
        $components = [];
        foreach(config('components.all') as $component) {
            if (!is_null($product->$component) && $product->$component != false) {
                $components[] = trans('games.components.'.$component);
            }
        }
        $product->components = $components;
        $genres = [];
        foreach ($product->genres as $genre) {
            $genres[] = $genre->name;
        }
        $product->genres = $genres;
        $developers = [];
        foreach ($product->developers as $developer) {
            $developers[$developer->id] = $developer->name;
        }
        $product->developers = $developers;

        $publishers_us = [];
        $publishers_eu = [];
        $publishers_jp = [];
        foreach ($product->publishers as $publisher) {
            if ($publisher->pivot->region == 'us') {
                $publishers_us[$publisher->id] = $publisher->name;
            } else if ($publisher->pivot->region == 'eu') {
                $publishers_eu[$publisher->id] = $publisher->name;
            } else if ($publisher->pivot->region == 'jp') {
                $publishers_jp[$publisher->id] = $publisher->name;
            }
        }
        $product->publishers_us = (object) $publishers_us;
        $product->publishers_eu = (object) $publishers_eu;
        $product->publishers_jp = (object) $publishers_jp;
        $product->comments = null;
        return $product;
    }

    private function formatSuggestion($suggestion)
    {
        $suggestion->true_name = $suggestion->name;
        $components = [];
        foreach(config('components.all') as $component) {
            if (!is_null($suggestion->$component) && $suggestion->$component != false) {
                $components[] = trans('games.components.'.$component);
            }
        }
        $suggestion->components = $components;
        foreach (['publishers_eu', 'publishers_us', 'publishers_jp', 'developers', 'genres'] as $field) {
            $suggestion->$field = json_decode($suggestion->$field);
        }
        return $suggestion;
    }

    public function convertImage($region, $value)
    {
        if (!in_array($region, regions())) {
            throw new \Exception('Invalid Region');
        }
        $disk = "s3";
        $destination_path = env('S3_DESTINATION')."suggestions";
        // if a base64 was sent, store it in the db
        if (is_file($value))
        {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);
            // 1. Generate a filename.
            $filename = (string) Str::uuid().'.jpg';
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());
            // 3. Save the path to the database
            return env('S3_BUCKET_URL').$destination_path.'/'.$filename;
        }
    }
}
