<?php


require_once 'App/Controllers/ProductApiController.php';
require_once 'App/Controllers/CategoryApiController.php';
require_once 'App/Controllers/OrderApiController.php';
require_once 'App/Controllers/OrderDetailApiController.php';
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

// Order routes
$router->get('/orders', [OrderApiController::class, 'getAllOrders']);
$router->get('/orders/{id}', [OrderApiController::class, 'getOrderById']);
$router->get('/orders/history/{idUser}', [OrderApiController::class, 'getOrderByIdUser']);
$router->post('/orders', [OrderApiController::class, 'createOrder']);
$router->put('/orders/{id}', [OrderApiController::class, 'updateOrder']);

// Order Details
$router->get('/orderdetails/{idOrder}', [OrderDetailApiController::class, 'getAll']);
$router->post('/orderdetails', [OrderDetailApiController::class, 'create']);
$router->delete('/orderdetails/{id}', [OrderDetailApiController::class, 'delete']);