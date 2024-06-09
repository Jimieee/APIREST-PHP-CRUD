<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use Intervention\Image\ImageManagerStatic as Image;

class Pant
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->connect();
    }

    public function create($data)
    {

        $sql = "INSERT INTO pants (name, price, brand_id, size_id, stock_quantity, is_limited) VALUES (:name, :price, :brand_id, :size_id, :stock_quantity, :is_limited)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'price' => $data['price'],
            'brand_id' => $data['brand_id'],
            'size_id' => $data['size_id'],
            'stock_quantity' => $data['stock_quantity'],
            'is_limited' => $data['is_limited']
        ]);
        $pantId = $this->pdo->lastInsertId();

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images']['tmp_name'] as $key => $tmp_name) {
                if ($data['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $uploadDirectory = __DIR__ . '/../uploads/';

                    $fileName = basename($data['images']['name'][$key]);
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                    $uniqueFileName = uniqid('pant_') . '_' . $key . '.' . $fileExtension;

                    $filePath = $uploadDirectory . $uniqueFileName;

                    if (move_uploaded_file($tmp_name, $filePath)) {
                        $sql = "INSERT INTO pants_images (pant_id, image_path) VALUES (:pantId, :imagePath)";
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute(['pantId' => $pantId, 'imagePath' => $filePath]);
                    }
                }
            }
        }

        return $pantId;
    }

    public function read($id)
    {
        $sql = "SELECT p.*, b.brand_name, s.size_label, pi.image_path AS image_path
                FROM pants p 
                JOIN brands b ON p.brand_id = b.brand_id
                JOIN sizes s ON p.size_id = s.size_id
                LEFT JOIN pants_images pi ON p.pant_id = pi.pant_id
                WHERE p.pant_id = :pant_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pant_id' => $id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pants = [];
        foreach ($result as $row) {
            $pants[$row['pant_id']] = [
                'pant_id' => $row['pant_id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'brand_name' => $row['brand_name'],
                'size_label' => $row['size_label'],
                'images' => $row['image_path'] ? [$row['image_path']] : []
            ];
        }

        return !empty($pants) ? reset($pants) : null;
    }

    public function update($id, $data)
    {
        $sql = "UPDATE pants SET name = :name, price = :price, brand_id = :brand_id, size_id = :size_id, stock_quantity = :stock_quantity, is_limited = :is_limited, updated_at = CURRENT_TIMESTAMP WHERE pant_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'price' => $data['price'],
            'brand_id' => $data['brand_id'],
            'size_id' => $data['size_id'],
            'stock_quantity' => $data['stock_quantity'],
            'is_limited' => $data['is_limited']
        ]);

        if (isset($data['image']) && $data['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDirectory = __DIR__ . '/../uploads/';

            $deleteSql = "DELETE FROM pants_images WHERE pant_id = :id";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->execute(['id' => $id]);

            $fileName = basename($data['image']['name']);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid('pant_') . '.' . $fileExtension;
            $filePath = $uploadDirectory . $uniqueFileName;

            if (move_uploaded_file($data['image']['tmp_name'], $filePath)) {
                $insertImageSql = "INSERT INTO pants_images (pant_id, image_path) VALUES (:pantId, :imagePath)";
                $insertImageStmt = $this->pdo->prepare($insertImageSql);
                $insertImageStmt->execute(['pantId' => $id, 'imagePath' => $filePath]);
            }
        }

        return $stmt->rowCount();
    }


    public function delete($id)
    {
        $sql = "DELETE FROM pants WHERE pant_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

    public function getAll()
    {
        $sql = "SELECT p.*, b.brand_name, s.size_label, pi.image_path AS image_path
            FROM pants p 
            JOIN brands b ON p.brand_id = b.brand_id
            JOIN sizes s ON p.size_id = s.size_id
            LEFT JOIN pants_images pi ON p.pant_id = pi.pant_id";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $pants = [];
        $index = 1; 
        foreach ($results as $result) {
            $pants[$index] = [
                'pant_id' => $result['pant_id'],
                'name' => $result['name'],
                'price' => $result['price'],
                'brand_name' => $result['brand_name'],
                'size_label' => $result['size_label'],
                'images' => $result['image_path'] ? [$result['image_path']] : []
            ];
            $index++;
        }
    
        return array_values($pants);
    }
    
    //get top selling products
    public function getTopSellingProducts($limit = 10)
    {
        $sql = "WITH product_sales AS (
                    SELECT 
                        p.name AS product_name, 
                        SUM(pi.quantity) AS total_quantity_sold
                    FROM 
                        purchase_items pi
                    JOIN 
                        pants p ON pi.pant_id = p.pant_id
                    GROUP BY 
                        p.name
                )
                SELECT * FROM product_sales
                ORDER BY total_quantity_sold DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }

    //get top selling brands
    public function getTopSellingBrands($limit = 10)
    {
        $sql = "WITH brand_sales AS (
                    SELECT 
                        b.brand_name, 
                        SUM(pi.quantity) AS total_quantity_sold
                    FROM 
                        purchase_items pi
                    JOIN 
                        pants p ON pi.pant_id = p.pant_id
                    JOIN 
                        brands b ON p.brand_id = b.brand_id
                    GROUP BY 
                        b.brand_name
                )
                SELECT * FROM brand_sales
                ORDER BY total_quantity_sold DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }

    //get total revenue by product
    public function getTotalRevenueByProduct($limit = 10)
    {
        $sql = "WITH product_revenue AS (
                    SELECT 
                        p.name AS product_name, 
                        SUM(pi.quantity * pi.price_at_purchase) AS total_revenue
                    FROM 
                        purchase_items pi
                    JOIN 
                        pants p ON pi.pant_id = p.pant_id
                    GROUP BY 
                        p.name
                )
                SELECT * FROM product_revenue
                ORDER BY total_revenue DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }

    //get total revenue by brand
    public function getTotalRevenueByBrand($limit = 10)
    {
        $sql = "WITH brand_revenue AS (
                    SELECT 
                        b.brand_name, 
                        SUM(pi.quantity * pi.price_at_purchase) AS total_revenue
                    FROM 
                        purchase_items pi
                    JOIN 
                        pants p ON pi.pant_id = p.pant_id
                    JOIN 
                        brands b ON p.brand_id = b.brand_id
                    GROUP BY 
                        b.brand_name
                )
                SELECT * FROM brand_revenue
                ORDER BY total_revenue DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }
}
