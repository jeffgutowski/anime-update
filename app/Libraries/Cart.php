<?php
namespace App\Libraries;

use Gloudemans\Shoppingcart\Facades\Cart as VendorCart;

class Cart extends VendorCart
{
    public static function sellerContent($sellerId = null)
    {
        $cart = self::content();
        $sellers = (object) [];
        foreach ($cart as $item) {
            if (!isset($sellers->{$item->model->user_id})) {
                $sellers->{$item->model->user_id} = (object) ['items' => []];
            }
            $sellers->{$item->model->user_id}->items[] = $item;
            if (!isset($sellers->{$item->model->user_id}->subtotal)) {
                $sellers->{$item->model->user_id}->subtotal = $item->model->price;
            } else {
                $sellers->{$item->model->user_id}->subtotal += $item->model->price;
            }
        }
        foreach ($sellers as $seller) {
            $seller->items = (object) $seller->items;
        }
        if (!is_null($sellerId)) {
            return $sellers->$sellerId;
        }
        return $sellers;
    }
}
