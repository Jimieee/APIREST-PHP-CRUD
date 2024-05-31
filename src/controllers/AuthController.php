<?php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    public function register($data)
    {
        $user = new User();
        $userExists = $user->findByEmail($data['email']);

        if ($userExists) {
            http_response_code(400);
            echo json_encode(["message" => "User already exists"]);
            return;
        }

        $user->create($data['name'], $data['email'], $data['password']);
        http_response_code(201);
        echo json_encode(["message" => "User created successfully"]);
    }

    public function login($data)
    {
        $user = new User();
        $userExists = $user->findByEmail($data['email']);

        if (!$userExists || !password_verify($data['password'], $userExists['password'])) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
            return;
        }

        $secretKey = $_ENV['JWT_SECRET'];
        $issuedAt = time();
        //expiration time is 5 days
        $expirationTime = $issuedAt + 60 * 60 * 24 * 5;
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $userExists['id'], 
            'name' => $userExists['name'],
            'email' => $userExists['email']
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        http_response_code(200);
        echo json_encode([
            "message" => "Login successful",
            "token" => $jwt
        ]);
    }
}
