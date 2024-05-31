<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Pant
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->connect();
    }

    public function create($data)
    {
        $sql = "INSERT INTO pants (name, description, price, category_id) VALUES (:name, :description, :price, :category_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
        ]);
        return $this->pdo->lastInsertId();
    }

    public function read($id)
    {
        $sql = "SELECT p.*, c.name as category_name FROM pants p JOIN categories c ON p.category_id = c.id WHERE p.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE pants SET name = :name, description = :description, price = :price, category_id = :category_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
        ]);
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM pants WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

    public function getAll()
    {
        $sql = "SELECT p.*, c.name as category_name FROM pants p JOIN categories c ON p.category_id = c.id";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}
