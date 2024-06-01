<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Cart
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->connect();
    }

    public function create($user_id)
    {
        $sql = "INSERT INTO shopping_carts (user_id) VALUES (:user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $this->pdo->lastInsertId();
    }

    public function addToCart($cart_id, $pant_id, $quantity)
    {
        $sql = "INSERT INTO cart_items (cart_id, pant_id, quantity) VALUES (:cart_id, :pant_id, :quantity)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'cart_id' => $cart_id,
            'pant_id' => $pant_id,
            'quantity' => $quantity
        ]);
    }

    public function removeFromCart($cart_item_id)
    {
        $sql = "DELETE FROM cart_items WHERE cart_item_id = :cart_item_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cart_item_id' => $cart_item_id]);
    }

    public function completePurchase($cart_id, $user_id)
    {
        $sql = "SELECT complete_purchase(:cart_id, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cart_id' => $cart_id, 'user_id' => $user_id]);
    }

    public function getCartItems($cart_id)
    {
        $sql = "SELECT ci.*, p.name AS pant_name, p.price AS pant_price
                FROM cart_items ci
                JOIN pants p ON ci.pant_id = p.pant_id
                WHERE ci.cart_id = :cart_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cart_id' => $cart_id]);
        return $stmt->fetchAll();
    }
}
