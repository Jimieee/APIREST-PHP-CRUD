<?php

namespace App\Controllers;

use App\Models\Cart;

class CartsController
{
    public function create($data)
    {
        $cart = new Cart();
        $cart_id = $cart->create($data['user_id']);
        http_response_code(201);
        echo json_encode(["message" => "Cart created", "cart_id" => $cart_id]);
    }
    public function getCart($user_id)
    {
        $user_id = $user_id['user_id'];
        $cart = new Cart();
        $resultado = $cart->getCart($user_id);
        http_response_code(201);
        echo json_encode(["message" => "Cart selected", "cart_id" => $resultado]);
    }

    public function addToCart($data)
    {
        $cart = new Cart();
        $cart->addToCart($data['cart_id'], $data['pant_id'], $data['quantity']);
        http_response_code(200);
        echo json_encode(["message" => "Item added to cart"]);
    } 

    public function updateFromCart($data)
    {
        $cart = new Cart();
        $cart->updateFromCart($data['cart_item_id'], $data['quantity']);
        http_response_code(200);
        echo json_encode(["message" => "Item Updated from cart"]);
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
