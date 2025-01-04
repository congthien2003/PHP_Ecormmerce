<?php

require_once 'App/Common/Response.php';

class Router {
    private $routes = [];
    private $currentRoute = null;
    private $basePath = '/php/s4_php/api';
    
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute($method, $path, $handler) {
        // Convert URL parameters to regex pattern
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<\1>[^/]+)', $path);
        $pattern = "#^{$pattern}$#";
        
        $this->routes[$method][$pattern] = [
            'handler' => $handler,
            'path' => $path
        ];
    }

    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path and ensure we have a clean path
        $path = str_replace($this->basePath, '', $uri);
        
        // Debug logging
        error_log("Original URI: " . $uri);
        error_log("Base Path: " . $this->basePath);
        error_log("Clean Path: " . $path);
        error_log("Available Routes: " . print_r($this->routes[$method] ?? [], true));

        // Check if method exists in routes
        if (!isset($this->routes[$method])) {
            Response::json([
                'status' => 'error',
                'message' => 'Method not allowed'
            ], 405);
            return;
        }

        // Find matching route
        foreach ($this->routes[$method] as $pattern => $route) {
            error_log("Checking pattern: " . $pattern . " against path: " . $path);
            if (preg_match($pattern, $path, $matches)) {
                error_log("Route matched!");
                // Remove numeric keys from matches
                $params = array_filter($matches, function($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                return $this->handleRoute($route['handler'], $params);
            }
        }

        // No route found
        Response::json([
            'status' => 'error',
            'message' => 'Route not found',
            'debug' => [
                'path' => $path,
                'method' => $method,
                'uri' => $uri,
                'basePath' => $this->basePath,
                'available_routes' => array_values(array_map(function($route) {
                    return $route['path'];
                }, $this->routes[$method] ?? [])),
                'patterns' => array_keys($this->routes[$method] ?? [])
            ]
        ], 404);
    }

    private function handleRoute($handler, $params) {
        try {
            if (is_array($handler)) {
                [$controllerClass, $method] = $handler;
                
                if (!class_exists($controllerClass)) {
                    throw new \Exception("Controller class {$controllerClass} not found");
                }

                $controller = new $controllerClass();
                
                if (!method_exists($controller, $method)) {
                    throw new \Exception("Method {$method} not found in controller {$controllerClass}");
                }
                
                

                $response = call_user_func_array([$controller, $method], $params);
                
                if ($response !== null) {
                    Response::json([
                        'status' => 'success',
                        'data' => $response
                    ]);
                }
            } else {
                throw new \Exception('Invalid route handler');
            }
        } catch (\Exception $e) {
            Response::json([
                'status' => 'error',
                'message' => 'Internal Server Error',
                'debug' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}