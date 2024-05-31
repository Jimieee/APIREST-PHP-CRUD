<?php

use App\Controllers\AuthController;
use App\Controllers\PantsController;
use App\Middleware\AuthMiddleware;

$authMiddleware = new AuthMiddleware();
$authController = new AuthController();
$pantsController = new PantsController();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

function handleRequest($controllerMethod, $requireAuth = false)
{
    global $authMiddleware;

    $request = [];
    if ($requireAuth) {
        $result = $authMiddleware->handle($request, function ($req) use ($controllerMethod) {
            return $controllerMethod($req);
        });
    } else {
        $result = $controllerMethod($request);
    }
    return $result;
}

if ($uri === '/register' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $authController->register($data);
} elseif ($uri === '/login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $authController->login($data);
    return;
}

if ($uri === '/pants' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    handleRequest(function ($req) use ($pantsController, $data) {
        $pantsController->create($data);
    }, true);
} elseif ($uri === '/pants' && $method === 'GET') {
    handleRequest(function ($req) use ($pantsController) {
        $pantsController->getAll();
    }, true);
} elseif (preg_match('/\/pants\/(\d+)/', $uri, $matches) && $method === 'GET') {
    $id = (int)$matches[1];
    handleRequest(function ($req) use ($pantsController, $id) {
        $pantsController->read($id);
    }, true);
} elseif (preg_match('/\/pants\/(\d+)/', $uri, $matches) && $method === 'PUT') {
    $id = (int)$matches[1];
    $data = json_decode(file_get_contents('php://input'), true);
    handleRequest(function ($req) use ($pantsController, $id, $data) {
        $pantsController->update($id, $data);
    }, true);
} elseif (preg_match('/\/pants\/(\d+)/', $uri, $matches) && $method === 'DELETE') {
    $id = (int)$matches[1];
    handleRequest(function ($req) use ($pantsController, $id) {
        $pantsController->delete($id);
    }, true);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Not Found"]);
}
