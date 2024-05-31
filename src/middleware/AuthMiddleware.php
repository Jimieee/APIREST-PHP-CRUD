<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(["message" => "Authorization header not found"]);
            return;
        }

        list($jwt) = sscanf($authHeader, 'Bearer %s');

        if (!$jwt) {
            http_response_code(401);
            echo json_encode(["message" => "Bearer token not found"]);
            return;
        }

        try {
            $secretKey = $_ENV['JWT_SECRET'];
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
            $request['user'] = (array) $decoded;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid token"]);
            return;
        }

        return $next($request);
    }
}
