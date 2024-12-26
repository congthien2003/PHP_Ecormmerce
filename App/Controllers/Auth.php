<?php
require_once 'App/Repository/AuthRepository.php';
require_once 'App/config/database.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController {
    private $authRepository;
    private $db;
    private $secretKey = "12345678"; // Change this to a secure key

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->authRepository = new AuthRepository($this->db);
    }

    public function register() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $email = $data['email'];
            $phone = $data['phone'] ?? "";
            $address = $data['address'] ?? "";
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $role = isset($data['role']) ? $data['role'] : 'user';

            $userId = $this->authRepository->registerUser($email, $password, $phone, $address, $role);

            if ($userId) {
                Response::json([
                    'status' => 'success',
                    'message' => 'User registered successfully',
                    'data' => [
                        'user_id' => $userId,
                        'username' => $email,
                        'role' => $role
                    ]
                ], 201);
            } else {
                Response::error('Registration failed', 400);
            }
        } catch (\PDOException $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $email = $data['email'];
            $password = $data['password'];

            $user = $this->authRepository->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $token = $this->generateJWT($user);
                Response::json([
                    'status' => 'success',
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ]);
                return;
            }

            Response::error('Invalid credentials', 401);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    private function generateJWT($user) {
        $payload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + (60 * 60) // Token expires in 1 hour
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            Response::json([
                'status' => 'success',
                'data' => $decoded
            ]);
        } catch (\Exception $e) {
            Response::error('Invalid token', 401);
        }
    }
}
