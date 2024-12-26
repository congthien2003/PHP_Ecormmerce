<?php
class AuthRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function registerUser($email, $password, $phone, $address, $role) {
        try {
            $this->db->beginTransaction();

            // Insert user
            $stmt = $this->db->prepare("INSERT INTO users (email, password, phone, address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $password, $phone, $address]);
            
            // Get the last inserted user ID
            $userId = $this->db->lastInsertId();

            // Insert permission
            $roleId = ($role === 'admin') ? 1 : 2; // 1 for admin, 2 for user
            $this->insertUserPermission($userId, $roleId);

            $this->db->commit();
            return $userId;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function insertUserPermission($userId, $roleId) {
        $stmt = $this->db->prepare("INSERT INTO permissions (IdUser, IdRole) VALUES (?, ?)");
        return $stmt->execute([$userId, $roleId]);
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("
            SELECT u.*, p.role_id 
            FROM users u 
            LEFT JOIN permissions p ON u.id = p.user_id 
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
} 