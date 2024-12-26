<?php
require_once 'app/Repository/UserRepository.php'; 
require_once('app/config/database.php'); 
// UserController.php
class UserController 
{
    private $userRepository;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection(); 
        $this->userRepository = new UserRepository($this->db);
    }

    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        include "App/views/auth/register.php";
    }

    // Xử lý đăng ký
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            if ($this->userRepository->registerUser($username, $password, "", 
                "", "")) {
                header("Location: /php/s4_php/user/showLoginForm"); // Điều hướng tới trang đăng nhập
                exit;
            } else {
                $error = "Đăng ký thất bại. Vui lòng thử lại.";
                include "views/auth/register.php";
            }
        }
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        include "App/views/auth/login.php";
    }

    // Xử lý đăng nhập
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userRepository->loginUser($username, $password);

            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['Id'];
                $_SESSION['username'] = $user['Username'];

                $roles = $this->userRepository->getUserRole($user['Id']);
                $_SESSION['roles'] = $roles;


                // $data = $user['Id'] + $user['Username'] + $roles;
                // $_COOKIE['token'] = password_hash($data, 'TokenSecret');

                header("Location: /php/S4_PHP/Product/list"); // Điều hướng tới trang chính
                exit;
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng.";
                include "App/views/auth/login.php";
            }
        }
    }

    // Đăng xuất
    public function logout()
    {
        session_start();
        session_destroy();
        header("Location: /php/s4_php/user/showLoginForm");
        exit;
    }
}
?>
