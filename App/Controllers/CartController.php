<?php

require_once 'app/models/CartModel.php'; 
require_once 'app/Repository/CartRepository.php'; 
require_once 'app/Repository/ProductRepository.php'; 
require_once('app/config/database.php'); 

class CartController
{
    private $cartRepository;
    private $productRepository;
    private $db;

    public function __construct()
    {
        // Khởi tạo kết nối cơ sở dữ liệu
        $this->db = (new Database())->getConnection();
        $this->cartRepository = new CartRepository($this->db);
        $this->productRepository = new ProductRepository($this->db);
    }

    public function index()
    {
        $this->showCart();
    }

    // Hiển thị giỏ hàng
    public function showCart()
    {
        session_start();
        // Giả sử người dùng đã đăng nhập và có user_id
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $cartItems = $this->cartRepository->getCartByUserId($userId);

            $total = 0;
           
            foreach ($cartItems as $item) {
                $total += $item['productPrice'] * $item['Quantity']; // Tính tổng
            }

            include 'App/views/cart/show.php'; // Gọi view để hiển thị giỏ hàng
        } else {
             echo 'Bạn cần đăng nhập để xem giỏ hàng.';
        }
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['idProduct'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            // Kiểm tra dữ liệu hợp lệ
            if (!$productId || !is_numeric($quantity) || $quantity <= 0) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                return;
            }

            // Kiểm tra sản phẩm tồn tại
            $product = $this->productRepository->getProductById($productId);
            if (!$product) {
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
                return;
            }

            session_start();

            // Lấy user_id từ session (giả sử người dùng đã đăng nhập)
            $userId = $_SESSION['user_id'] ?? null;

            if (!$userId) {
                header('Location: /php/S4_PHP/Product/list'); 
                return;
            }


            $add = $this->cartRepository->addProductToCart($userId, $productId, 1);
            if ($add) {
                header('Location: /php/S4_PHP/Product/list'); 
            } else {
                header('Location: /php/S4_PHP/Product/list'); 
            }
        } else {
            header('Location: /php/S4_PHP/Product/list'); 

        }
    }

    // Cập nhật số lượng sản phẩm trong giỏ
    public function updateCart()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['idProduct'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            if (!$productId || !is_numeric($quantity) || $quantity <= 0) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                return;
            }

            session_start();
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId) {
                $this->cartRepository->updateProductQuantityInCart($userId, $productId, $quantity);
            } 
            header('Location: /php/S4_PHP/Cart');
        } else {
            header('Location: /php/S4_PHP/Cart');
        }
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['idProduct'] ?? null;

            if (!$productId) {
                header('Location: /php/S4_PHP/Cart');
            }

            session_start();
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId) {
                $this->cartRepository->removeProductFromCart($userId, $productId);
                
            }
            header('Location: /php/S4_PHP/Cart');
        } else {
            header('Location: /php/S4_PHP/Cart');
        }
    }

    // Xóa toàn bộ giỏ hàng
    public function clearCart()
    {
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $this->cartRepository->clearCart($userId);
            header('Location: /php/S4_PHP/Product/list'); // Quay lại trang giỏ hàng
            exit();
        } else {
            echo 'Bạn cần đăng nhập để xóa giỏ hàng.';
        }
    }
}
?>
