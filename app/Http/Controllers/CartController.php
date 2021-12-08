<?php
namespace App\Http\Controllers;

use App\Libraries\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Listing;

class CartController extends Controller
{
    public function getCart()
    {
        return view('frontend.cart.show');
    }

    public function removeItem($id)
    {
        Cart::remove($id);

        if (!Cart::count()) {
            \Alert::success('<i class="fa fa-smile-o m-r-5"></i> ' . trans('cart.empty_cart'))->flash();
            return redirect('/');
        }
        \Alert::success('<i class="fa fa-smile-o m-r-5"></i> ' . trans('cart.removed_successfully'))->flash();
        return redirect()->back();
    }

    public function clearCart()
    {
        Cart::destroy();
        \Alert::success('<i class="fa fa-smile-o m-r-5"></i> ' . trans('cart.empty_cart'))->flash();
        return redirect('/');
    }

    public function addToCart(Request $request)
    {
        $listingId = $request->get('listing');
        $alreadyInCart = false;
        $cart = Cart::content();
        foreach ($cart as $item) {
            if ($item->id == $listingId) {
                $alreadyInCart = true;
            }
        }
        if ($alreadyInCart) {
            return redirect()->back();
        }
        $listing = Listing::where('id', $listingId)->with('game')->first();
        Cart::add($listing->id, $listing->product->name, 1, $listing->price)->associate(Listing::class);
        \Alert::success('<i class="fa fa-smile-o m-r-5"></i> ' . trans('cart.added_to_cart'))->flash();
        return redirect()->back();

    }
}