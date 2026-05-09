<?php

declare(strict_types=1);

use App\Controllers\ApiController;
use App\Controllers\AuthController;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Repositories\TollRepository;

require dirname(__DIR__) . '/src/Support/bootstrap.php';

set_exception_handler(static function (Throwable $exception): void {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [
    'http://127.0.0.1:5173',
    'http://localhost:5173',
];

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: {$origin}");
    header('Vary: Origin');
}

header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, OPTIONS');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$request = new Request();
$repository = new TollRepository(database());
$auth = new Auth($repository);
$authController = new AuthController($repository, $auth);
$apiController = new ApiController($repository, $auth);
$router = new Router();

$router->get('/api/health', fn() => Response::success(['status' => 'ok']));

$router->post('/api/auth/login', fn() => $authController->login($request));
$router->get('/api/auth/me', fn() => $authController->me($request));
$router->post('/api/auth/logout', fn() => $authController->logout($request));

$router->get('/api/dashboard/admin', fn() => $apiController->adminDashboard($request));
$router->get('/api/dashboard/operator', fn() => $apiController->operatorDashboard($request));
$router->get('/api/tariffs', fn() => $apiController->tariffs($request));
$router->put('/api/tariffs', fn() => $apiController->updateTariffs($request));
$router->get('/api/operators', fn() => $apiController->operators($request));
$router->get('/api/subscribers', fn() => $apiController->subscribers($request));
$router->post('/api/subscribers', fn() => $apiController->createSubscriber($request));
$router->patch('/api/subscribers/{id}/renew', fn(array $params) => $apiController->renewSubscriber($request, (int) $params['id']));
$router->get('/api/history', fn() => $apiController->history($request));
$router->get('/api/reports', fn() => $apiController->reports($request));
$router->get('/api/settings', fn() => $apiController->settings($request));
$router->put('/api/settings', fn() => $apiController->updateSettings($request));
$router->get('/api/incidents', fn() => $apiController->incidents($request));
$router->post('/api/incidents', fn() => $apiController->createIncident($request));
$router->post('/api/passages', fn() => $apiController->createPassage($request));

$router->dispatch($request);
