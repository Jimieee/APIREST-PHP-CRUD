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
        return $this->pdo->lastInsertId();
    }

    public function read($id)
    {
        $sql = "SELECT p.*, b.brand_name, s.size_label, pi.image_path AS image_path
                FROM pants p 
                JOIN brands b ON p.brand_id = b.brand_id
                JOIN sizes s ON p.size_id = s.size_id
                LEFT JOIN pants_images pi ON p.pant_id = pi.pant_id
                WHERE p.pant_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pants = [];
        foreach ($result as $row) {
            $pants[$row['pant_id']] = [
                'pant_id' => $row['pant_id'],
                'name' => $row['name'],
                'description' => $row['description'],
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
        $results = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

        $pants = [];
        foreach ($results as $result) {
            $pants[$result[0]['pant_id']] = [
                'pant_id' => $result[0]['pant_id'],
                'name' => $result[0]['name'],
                'description' => $result[0]['description'],
                'price' => $result[0]['price'],
                'brand_name' => $result[0]['brand_name'],
                'size_label' => $result[0]['size_label'],
                'images' => array_column($result, 'image_path')
            ];
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
