<?php

namespace App\Controllers;

use App\Models\Size;

class SizesController
{
    public function create($data)
    {
        $size = new Size();
        $id = $size->create($data['size_label']);
        http_response_code(201);
        echo json_encode(["message" => "Size created", "id" => $id]);
    }

    public function read($id)
    {
        $size = new Size();
        $result = $size->read($id);
        if ($result) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Size not found"]);
        }
    }

    public function update($id, $data)
    {
        $size = new Size();
        $rowCount = $size->update($id, $data['size_label']);
        if ($rowCount) {
            http_response_code(200);
            echo json_encode(["message" => "Size updated"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Size not found or no change in data"]);
        }
    }

    public function delete($id)
    {
        $size = new Size();
        $rowCount = $size->delete($id);
        if ($rowCount) {
            http_response_code(200);
            echo json_encode(["message" => "Size deleted"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Size not found"]);
        }
    }

    public function getAll()
    {
        $size = new Size();
        $results = $size->getAll();
        http_response_code(200);
        echo json_encode($results);
    }
}
