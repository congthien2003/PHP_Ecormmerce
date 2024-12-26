<?php
require_once __DIR__ . '/vendor/autoload.php';

// use App\Middlewares\CorsMiddleware;
// CorsMiddleware::handle();

require_once __DIR__ . '/App/api.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Debug information
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$basePath = '/php/S4_PHP/api';

// Log original and processed request
error_log("Original Request URI: " . $request);
$request = str_replace($basePath, '', $request);
error_log("Processed Request Path: " . $request);

try {
    // Initialize Router
    $router = new Router();
    require_once __DIR__ . '/App/routes/routes.php'; 
    $router->resolve(); // Process request
} catch (Exception $e) {
    // Handle uncaught exceptions
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error',
        'debug' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
