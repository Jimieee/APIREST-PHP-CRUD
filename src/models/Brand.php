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

    public function create($name, $image)
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
    
        $brandId = $this->pdo->lastInsertId();
    
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadDirectory = __DIR__ . '/../uploads/brands/';
    
            $fileName = basename($image['name']);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid('brand_') . '.' . $fileExtension;
            $filePath = $uploadDirectory . $uniqueFileName;
    
            if (move_uploaded_file($image['tmp_name'], $filePath)) {
                $updateImagePathSql = "UPDATE brands SET image_path = :imagePath WHERE brand_id = :id";
                $updateImagePathStmt = $this->pdo->prepare($updateImagePathSql);
                $updateImagePathStmt->execute(['id' => $brandId, 'imagePath' => $filePath]);
            }
        }
    
        return $brandId;
    }    

    private function exists($name)
    {
        $sql = "SELECT COUNT(*) FROM brands WHERE brand_name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name]);
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public function update($id, $name, $image)
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
    
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadDirectory = __DIR__ . '/../uploads/brands/';
    
            $oldImage = $this->read($id)['image_path'];
            if ($oldImage) {
                unlink($oldImage);
            }
    
            $fileName = basename($image['name']);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid('brand_') . '.' . $fileExtension;
            $filePath = $uploadDirectory . $uniqueFileName;
    
            if (move_uploaded_file($image['tmp_name'], $filePath)) {
                $updateImagePathSql = "UPDATE brands SET image_path = :imagePath WHERE brand_id = :id";
                $updateImagePathStmt = $this->pdo->prepare($updateImagePathSql);
                $updateImagePathStmt->execute(['id' => $id, 'imagePath' => $filePath]);
            }
        }
    
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
