<?php

namespace App\Controllers;

use App\Models\Pant;

class PantsController
{
    public function create($data)
    {
        $pant = new Pant();
        $id = $pant->create($data);
        http_response_code(201);
        echo json_encode(["message" => "Pant created", "id" => $id]);
    }

    public function read($id)
    {
        $pant = new Pant();
        $result = $pant->read($id);
        if ($result) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Pant not found"]);
        }
    }

    public function update($id, $data)
    {
        $pant = new Pant();
        $rowCount = $pant->update($id, $data);
        if ($rowCount) {
            http_response_code(200);
            echo json_encode(["message" => "Pant updated"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Pant not found or no change in data"]);
        }
    }

    public function delete($id)
    {
        $pant = new Pant();
        $rowCount = $pant->delete($id);
        if ($rowCount) {
            http_response_code(200);
            echo json_encode(["message" => "Pant deleted"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Pant not found"]);
        }
    }

    public function getAll()
    {
        $pant = new Pant();
        $results = $pant->getAll();
        http_response_code(200);
        echo json_encode($results);
    }

    public function getTopSellingProducts()
    {
        $pant = new Pant();
        $results = $pant->getTopSellingProducts();
        http_response_code(200);
        echo json_encode($results);
    }

    public function getTopSellingBrands()
    {
        $pant = new Pant();
        $results = $pant->getTopSellingBrands();
        http_response_code(200);
        echo json_encode($results);
    }

    public function getTotalRevenueByProduct()
    {
        $pant = new Pant();
        $results = $pant->getTotalRevenueByProduct();
        http_response_code(200);
        echo json_encode($results);
    }

    public function getTotalRevenueByBrand()
    {
        $pant = new Pant();
        $results = $pant->getTotalRevenueByBrand();
        http_response_code(200);
        echo json_encode($results);
    }
}
