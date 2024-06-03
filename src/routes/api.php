    <?php

    use App\Controllers\AuthController;
    use App\Controllers\PantsController;
    use App\Middleware\AuthMiddleware;
    use App\Controllers\BrandsController;
    use App\Controllers\SizesController;

    $authMiddleware = new AuthMiddleware();
    $authController = new AuthController();
    $pantsController = new PantsController();
    $brandController = new BrandsController();
    $sizeController = new SizesController();

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

    $notFound = true;

    if ($uri === '/pants') {
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            handleRequest(function ($req) use ($pantsController, $data) {
                $pantsController->create($data);
            }, true);
        } elseif ($method === 'GET') {
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
            $notFound = false;
        }
    }

    if ($uri === '/brands') {
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $image = $_FILES['image'];
            handleRequest(function ($req) use ($brandController, $data, $image) {
                $brandController->create($data, $image);
            }, true);
        } elseif ($method === 'GET') {
            handleRequest(function ($req) use ($brandController) {
                $brandController->getAll();
            }, true);
        } elseif (preg_match('/\/brands\/(\d+)/', $uri, $matches) && $method === 'GET') {
            $id = (int)$matches[1];
            handleRequest(function ($req) use ($brandController, $id) {
                $brandController->read($id);
            }, true);
        } elseif (preg_match('/\/brands\/(\d+)/', $uri, $matches) && $method === 'PUT') {
            $id = (int)$matches[1];
            $data = json_decode(file_get_contents('php://input'), true);
            $image = $_FILES['image'];
            handleRequest(function ($req) use ($brandController, $id, $data, $image) {
                $brandController->update($id, $data, $image);
            }, true);
        } elseif (preg_match('/\/brands\/(\d+)/', $uri, $matches) && $method === 'DELETE') {
            $id = (int)$matches[1];
            handleRequest(function ($req) use ($brandController, $id) {
                $brandController->delete($id);
            }, true);
        } else {
            $notFound = false;
        }
    }

    if ($uri === '/sizes') {
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            handleRequest(function ($req) use ($sizeController, $data) {
                $sizeController->create($data);
            }, true);
        } elseif ($method === 'GET') {
            handleRequest(function ($req) use ($sizeController) {
                $sizeController->getAll();
            }, true);
        } elseif (preg_match('/\/sizes\/(\d+)/', $uri, $matches) && $method === 'GET') {
            $id = (int)$matches[1];
            handleRequest(function ($req) use ($sizeController, $id) {
                $sizeController->read($id);
            }, true);
        } elseif (preg_match('/\/sizes\/(\d+)/', $uri, $matches) && $method === 'PUT') {
            $id = (int)$matches[1];
            $data = json_decode(file_get_contents('php://input'), true);
            handleRequest(function ($req) use ($sizeController, $id, $data) {
                $sizeController->update($id, $data);
            }, true);
        } elseif (preg_match('/\/sizes\/(\d+)/', $uri, $matches) && $method === 'DELETE') {
            $id = (int)$matches[1];
            handleRequest(function ($req) use ($sizeController, $id) {
                $sizeController->delete($id);
            }, true);
        } else {
            $notFound = false;
        }
    }

    if ($notFound) {
        http_response_code(404);
        echo json_encode(["message" => "Not Found"]);
    }
