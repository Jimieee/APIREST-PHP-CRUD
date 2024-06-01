<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Brand
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->connect();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM brands";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    public function create($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("El nombre de la marca no puede estar vacío");
        }

        if ($this->exists($name)) {
            throw new \RuntimeException("La marca '$name' ya existe");
        }

        $sql = "INSERT INTO brands (brand_name) VALUES (:name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name]);

        return $this->pdo->lastInsertId();
    }

    private function exists($name)
    {
        $sql = "SELECT COUNT(*) FROM brands WHERE brand_name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name]);
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public function update($id, $name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("El nombre de la marca no puede estar vacío");
        }

        if ($this->exists($name)) {
            throw new \RuntimeException("La marca '$name' ya existe");
        }

        $sql = "UPDATE brands SET brand_name = :name WHERE brand_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'name' => $name]);

        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM brands WHERE brand_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount();
    }

    public function read($id)
    {
        $sql = "SELECT * FROM brands WHERE brand_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}
