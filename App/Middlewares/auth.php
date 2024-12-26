<?php
use Firebase\JWT\JWT;
$config = require "../config/config.php";

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /php/s4_php/user/showLoginForm");
        exit;
    }
    
}

function requireAdminRole() {
    // Kiểm tra quyền truy cập
    if (!in_array('Admin', $_SESSION['roles'])) {
        echo "Bạn không có quyền truy cập vào trang này.";
        exit;
    }
}

function requireUserRole() {
    // Kiểm tra quyền truy cập
    if (!in_array('User', $_SESSION['roles'])) {
        echo "Bạn không có quyền truy cập vào trang này.";
        exit;
    }
}

function verifyJWT() {
    global $config;
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (strpos($token, 'Bearer ') !== 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    $jwtSecret = $config['jwt_secret_key'];
    $token = str_replace('Bearer ', '', $token);
    $userData = JWT::decode($token, $jwtSecret);

    if (!$userData) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired token']);
        exit;
    }

    return $userData;
}
?>