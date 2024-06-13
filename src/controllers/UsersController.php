<?php

namespace App\Controllers;

use App\Models\User;

class UsersController
{
    public function create($data)
    {
        $user = new User();
        $user->create($data['username'], $data['email'], $data['password'], $data['role_id']);
        http_response_code(201);
        echo json_encode(["message" => "User created"]);
    }

    public function read($id)
    {
        $userId = $id['id'];

        $user = new User();
        $result = $user->read((int)$userId);
        if ($result) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
        }
    }

    public function update($id, $data)
    {
        $user = new User();
        $rowCount = $user->update($id, $data);
        if ($rowCount) {
            http_response_code(200);
            echo json_encode(["message" => "User updated"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found or no change in data"]);
        }
    }

    public function delete($id)
    {
        $user = new User();
        $rowCount = $user->delete($id);
        if ($rowCount) {
            http_response_code(200);
            echo json_encode(["message" => "User deleted"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
        }
    }

    public function getAll()
    {
        $user = new User();
        $result = $user->getAllUsers();
        http_response_code(200);
        echo json_encode($result);
    }
}