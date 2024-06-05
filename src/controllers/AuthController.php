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
        $usernameExists = $user->findByUsername($data['username']);

        if ($userExists) {
            http_response_code(401);
            echo json_encode(["message" => "Email en uso"]);
            return;
        }

        if ($usernameExists) {
            http_response_code(401);
            echo json_encode(["message" => "El nombre de usuario ya está en uso"]);
            return;
        }

        $user->create($data['username'], $data['email'], $data['password']);
        http_response_code(200);
        echo json_encode(["message" => "User created successfully"]);
    }

    public function login($data)
    {
        $user = new User();
        $userExists = $user->findByEmail($data['email']);

        if (!$userExists || !password_verify($data['password'], $userExists['password'])) {
            http_response_code(401);
            echo json_encode(["message" => "Credenciales incorrectas"]);
            return;
        }

        $secretKey = $_ENV['JWT_SECRET'];
        $issuedAt = time();
        //expiration time is 5 days
        $expirationTime = $issuedAt + 60 * 60 * 24 * 5;
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'UserData' => [
                'id' => $userExists['user_id'],
                'username' => $userExists['username'],
                'email' => $userExists['email'],
                'role' => $userExists['role_id']
            ],
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        http_response_code(200);
        echo json_encode([
            "message" => "Login successful",
            "token" => $jwt
        ]);
    }
}
