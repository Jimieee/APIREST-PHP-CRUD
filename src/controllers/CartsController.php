<?php

namespace App\Controllers;

use App\Models\Cart;

class CartsController
{
    public function create($user_id)
    {
        $cart = new Cart();
        $cart_id = $cart->create($user_id);
        http_response_code(201);
        echo json_encode(["message" => "Cart created", "cart_id" => $cart_id]);
    }

    public function addToCart($cart_id, $pant_id, $quantity)
    {
        $cart = new Cart();
        $cart->addToCart($cart_id, $pant_id, $quantity);
        http_response_code(200);
        echo json_encode(["message" => "Item added to cart"]);
    }

    public function removeFromCart($cart_item_id)
    {
        $cart = new Cart();
        $cart->removeFromCart($cart_item_id);
        http_response_code(200);
        echo json_encode(["message" => "Item removed from cart"]);
    }

    public function completePurchase($cart_id, $user_id)
    {
        $cart = new Cart();
        $cart->completePurchase($cart_id, $user_id);
        http_response_code(200);
        echo json_encode(["message" => "Purchase completed"]);
    }

    public function getCartItems($cart_id)
    {
        $cart = new Cart();
        $results = $cart->getCartItems($cart_id);
        http_response_code(200);
        echo json_encode($results);
    }
}
