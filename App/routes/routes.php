<?php


require_once 'App/Controllers/ProductApiController.php';
require_once 'App/Controllers/CategoryApiController.php';
require_once 'App/Controllers/Auth.php';

// Product routes
$router->get('/products', [ProductApiController::class, 'getAll']);
$router->get('/products/{id}', [ProductApiController::class, 'getById']);
$router->get('/products/category/{category}', [ProductApiController::class, 'getByCategory']);
$router->post('/products', [ProductApiController::class, 'create']);
$router->put('/products/{id}', [ProductApiController::class, 'update']);
$router->delete('/products/{id}', [ProductController::class, 'delete']);
$router->post('/products/login', [ProductApiController::class, 'loginTest']);

// Auth routes
$router->post('/auth/login', [AuthController::class, 'login']);
$router->post('/auth/register', [AuthController::class, 'register']);
$router->post('/auth/logout', [AuthController::class, 'logout']);

// Category routes
$router->get('/categories', [CategoryApiController::class, 'getAll']);
$router->get('/categories/{id}', [CategoryApiController::class, 'getById']);
$router->post('/categories', [CategoryApiController::class, 'create']);
$router->put('/categories/{id}', [CategoryApiController::class, 'update']);
$router->delete('/categories/{id}', [CategoryApiController::class, 'delete']);

