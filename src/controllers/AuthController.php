<?php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;

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

        $user->create($data['username'], $data['email'], $data['password']);
        http_response_code(201);
        echo json_encode(["message" => "User created successfully"]);
    }

    public function login($data)
    {
        $user = new User();
        $userExists = $user->findByEmail($data['email']);

        if (!$userExists || !password_verify($data['password'], $userExists['password_hash'])) {
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
            'sub' => $userExists['user_id'], 
            'username' => $userExists['username'],
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
