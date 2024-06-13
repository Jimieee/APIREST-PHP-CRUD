<?php

use App\Controllers\AuthController;
use App\Controllers\BrandsController;
use App\Controllers\PantsController;
use App\Controllers\SizesController;
use App\Controllers\UsersController;
use App\Controllers\CartsController;
use App\Middleware\AuthMiddleware;
use FastRoute\RouteCollector;

$authMiddleware = new AuthMiddleware();
$authController = new AuthController();
$pantsController = new PantsController();
$brandController = new BrandsController();
$sizeController = new SizesController();
$userController = new UsersController();
$cartController = new CartsController();

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) use ($authController, $pantsController, $cartController, $brandController, $sizeController, $userController, $authMiddleware) {
    $r->addRoute('POST', '/register', [$authController, 'register']);
    $r->addRoute('POST', '/login', [$authController, 'login']);

    $r->addGroup('/pants', function (RouteCollector $r) use ($pantsController) {
        $r->addRoute('POST', '', [$pantsController, 'create']);
        $r->addRoute('GET', '', [$pantsController, 'getAll']);
        $r->addRoute('GET', '/{id:\d+}', [$pantsController, 'read']);
        $r->addRoute('PUT', '/{id:\d+}', [$pantsController, 'update']);
        $r->addRoute('DELETE', '/{id:\d+}', [$pantsController, 'delete']);
    });

    $r->addGroup('/brands', function (RouteCollector $r) use ($brandController) {
        $r->addRoute('POST', '', [$brandController, 'create']);
        $r->addRoute('GET', '', [$brandController, 'getAll']);
        $r->addRoute('GET', '/{id:\d+}', [$brandController, 'read']);
        $r->addRoute('PUT', '/{id:\d+}', [$brandController, 'update']);
        $r->addRoute('DELETE', '/{id:\d+}', [$brandController, 'delete']);
    });

    $r->addGroup('/sizes', function (RouteCollector $r) use ($sizeController) {
        $r->addRoute('POST', '', [$sizeController, 'create']);
        $r->addRoute('GET', '', [$sizeController, 'getAll']);
        $r->addRoute('GET', '/{id:\d+}', [$sizeController, 'read']);
        $r->addRoute('PUT', '/{id:\d+}', [$sizeController, 'update']);
        $r->addRoute('DELETE', '/{id:\d+}', [$sizeController, 'delete']);
    });

    $r->addGroup('/users', function (RouteCollector $r) use ($userController) {
        $r->addRoute('POST', '', [$userController, 'create']);
        $r->addRoute('GET', '', [$userController, 'getAll']);
        $r->addRoute('GET', '/{id:\d+}', [$userController, 'read']);
        $r->addRoute('PUT', '/{id:\d+}', [$userController, 'update']);
        $r->addRoute('DELETE', '/{id:\d+}', [$userController, 'delete']);
    });

    $r->addGroup('/cart', function (RouteCollector $r) use ($cartController) {
        $r->addRoute('GET', '/{cart_id:\d+}', [$cartController, 'getCartItems']);
        $r->addRoute('GET', '/select/{user_id:\d+}', [$cartController, 'getCart']);
        $r->addRoute('POST', '', [$cartController, 'create']);
        $r->addRoute('POST', '/add', [$cartController, 'addToCart']);
        $r->addRoute('PUT', '/update', [$cartController, 'updateFromCart']);
        $r->addRoute('DELETE', '/delete/{cart_item_id:\d+}', [$cartController, 'removeFromCart']);
    });

});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(["message" => "Not Found"]);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        echo json_encode(["message" => "Method Not Allowed. Allowed methods: " . implode(", ", $allowedMethods)]);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        [$controller, $method] = $handler;
        $data = json_decode(file_get_contents('php://input'), true);

        if (is_null($data)) {
            $data = [];
        }

        $params = array_merge($data, $vars);

        // echo "<pre>";
        // print_r($params);
        // echo "</pre>";

        call_user_func_array([$controller, $method], [$params]);
        break;
}