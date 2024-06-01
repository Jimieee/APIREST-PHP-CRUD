<?php

namespace App\Controllers;

use App\Models\Brand;

class BrandsController
{
    public function create($data)
    {
        $brand = new Brand();
        $id = $brand->create($data['brand_name']);
        http_response_code(201);
        echo json_encode(["message" => "Brand created", "id" => $id]);
    }

    public function read($id)
    {
        $brand = new Brand();
        $result = $brand->read($id);
        if ($result) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Brand not found"]);
        }
    }

    public function update($id, $data)
    {
        $brand = new Brand();
        $success = $brand->update($id, $data['brand_name']);
        if ($success) {
            http_response_code(200);
            echo json_encode(["message" => "Brand updated"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Brand not found or no change in data"]);
        }
    }

    public function delete($id)
    {
        $brand = new Brand();
        $success = $brand->delete($id);
        if ($success) {
            http_response_code(200);
            echo json_encode(["message" => "Brand deleted"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Brand not found"]);
        }
    }

    public function getAll()
    {
        $brand = new Brand();
        $results = $brand->getAll();
        http_response_code(200);
        echo json_encode($results);
    }
}
