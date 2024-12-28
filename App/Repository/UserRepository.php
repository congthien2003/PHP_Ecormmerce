<?php

class UserRepository 
{ 
    private $conn; 
    private $table_name = "users"; 

    public function __construct($db) 
    { 
        $this->conn = $db; 
    } 

    // Đăng ký người dùng mới
    public function registerUser($email, $password, $phone, $address) 
    { 
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT); 
        $query = "INSERT INTO " . $this->table_name . " (email, Password) VALUES (:email, :password, :phone, :address)"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':email', $email); 
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address); 
        return $stmt->execute(); 
    } 

    // Đăng nhập người dùng
    public function loginUser($email, $password) 
    { 
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':email', $email); 
        $stmt->execute(); 
        $user = $stmt->fetch(PDO::FETCH_ASSOC); 
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        if($user != null && password_verify($password, $user['password'])) { 
            return $user; 
        };
        return null; 
    } 

    // Lấy vai trò của người dùng
    public function getUserRole($userId) 
    { 
        $query = "SELECT r.Name AS roleName 
                  FROM permissions AS p 
                  JOIN roles AS r ON p.IdRole = r.Id 
                  WHERE p.IdUser = :userId"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':userId', $userId); 
        $stmt->execute(); 
        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $roles; 
    } 

    public function hashPassword($password) {
        return hash('sha256', $password . 'your_secret_salt'); // Kết hợp với "salt" để tăng tính bảo mật
    }

    // Hàm so sánh mật khẩu
    public function comparePassword($inputPassword, $hashedPassword) {
        return $this->hashPassword($inputPassword) === $hashedPassword;
    }
} 

?>