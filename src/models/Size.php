<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Size
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->connect();
    }

    public function create($sizeLabel)
    {
        $sql = "INSERT INTO sizes (size_label) VALUES (:sizeLabel)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['sizeLabel' => $sizeLabel]);
        return $this->pdo->lastInsertId();
    }

    public function read($id)
    {
        $sql = "SELECT * FROM sizes WHERE size_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $sizeLabel)
    {
        $sql = "UPDATE sizes SET size_label = :sizeLabel WHERE size_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'sizeLabel' => $sizeLabel]);
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM sizes WHERE size_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM sizes";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
