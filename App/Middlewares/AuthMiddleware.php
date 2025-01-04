<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private static $secretKey = "12345678"; // Use the same key as in AuthController

    public static function handleAuth() {
        try {
            // Get headers
            $headers = apache_request_headers();
            //echo $headers['Authorization'];
            // Check if Authorization header exists
            if (!isset($headers['Authorization'])) {
                self::sendUnauthorizedResponse('No token provided');
            }

            // Get the token from the Authorization header
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : $headers['authorization'];
            $token = str_replace('Bearer ', '', $authHeader);
            // Verify and decode the token
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
            return true;
        } catch (\Exception $e) {
            self::sendUnauthorizedResponse('Invalid token: ' . $e->getMessage());
        }
    }

    public static function isAdmin() {
        try {
            self::handleAuth();
            
            // Check if user has admin role
            if ($_REQUEST['user']->role !== 'admin') {
                self::sendUnauthorizedResponse('Admin access required');
            }

            return true;
        } catch (\Exception $e) {
            self::sendUnauthorizedResponse('Admin access required');
        }
    }

    private static function sendUnauthorizedResponse($message) {
        header('HTTP/1.0 401 Unauthorized');
        Response::json([
                'status' => 'error',
                'message' => "Unauthorized"
                ]
            , 401);
        exit();
    }
} 