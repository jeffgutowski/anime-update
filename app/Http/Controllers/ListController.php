<?php

namespace App\Http\Controllers;

use App\Models\AccessoriesHardwareType as ProductType;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\Wishlist;
use App\Models\Trophy;
use App\Models\UserTrophy;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use SEO;
use Request;

class ListController extends Controller
{
    public $model;
    public $list_name;
    public $product;

    public function add($slug)
    {
        // Check if user is logged in
        if (!(Auth::check())) {
            return redirect()->route('frontend.auth.login');
        }

        // Get game id from slug string
        $product_id = ltrim(strrchr($slug,'-'),'-');
        $product = Product::find($product_id);
        $this->product = $product;

        // Check if game exists
        if (is_null($product)) {
            return abort('404');
        }

        if ($this->list_name == 'collection') {
            $list = $this->model::firstOrCreate(['game_id' => $product->id, 'user_id' => Auth::id(), 'region' => session()->get('region.code')]);
        } elseif ($this->list_name == 'completed list') {
            $list = $this->model::firstOrCreate(['game_id' => $product->id, 'user_id' => Auth::id()]);
            $list->region = session()->get('region.code');
            $list->save();
        }

        // show a success message
        \Alert::success('<i class="fas fa-heart m-r-5"></i>' . $product->name . " added to your ".$this->list_name)->flash();

        return redirect()->back();
    }

    public function delete($slug)
    {
        // Check if user is logged in
        if (!(Auth::check())) {
            return redirect()->route('frontend.auth.login');
        }

        // Get game id from slug string
        $product_id = ltrim(strrchr($slug,'-'),'-');
        $product = Product::find($product_id);
        $this->product = $product;

        // Check if game exists
        if (is_null($product)) {
            return abort('404');
        }

        // Check if game is already in the list
        $list = $this->model::where('game_id', $product->id)->where('user_id', Auth::id());
        $list->get();

        // Check if wishlist entry exists, otherwise abort with a 404 error
        if (isset($list)) {
            $list->delete();
        } else {
            return abort('404');
        }

        // show a success message
        \Alert::error('<i class="far fa-heart"></i> ' . $product->name . ' removed from your '.$this->list_name)->flash();

        return redirect()->back();
    }
}
