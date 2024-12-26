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
    public function registerUser($username, $password, $email, $phone, $address) 
    { 
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT); 
        $query = "INSERT INTO " . $this->table_name . " (Username, Password) VALUES (:username, :password, :email, :phone, :address)"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':username', $username); 
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address); 
        return $stmt->execute(); 
    } 

    // Đăng nhập người dùng
    public function loginUser($username, $password) 
    { 
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':username', $username); 
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
                  FROM permissionuser AS p 
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