<?php
require_once 'App/Models/ProductModel.php'; 
require_once 'App/Repository/ProductRepository.php'; 
require_once('App/config/database.php'); 
require_once('App/Middlewares/AuthMiddleware.php');
class ProductApiController {

    private $productRepository; 
    private $db; 
    private $products = [
            
    ]; 
    public function __construct() 
    { 
        // Giả sử chúng ta lưu trữ sản phẩm trong session để giữ lại khi làm mới trang 
        session_start(); 
        if (isset($_SESSION['products'])) { 
            $this->products = $_SESSION['products']; 
        } 

        $this->db = (new Database())->getConnection(); 
        $this->productRepository = new ProductRepository($this->db);
    }
    // GET: /products
    public function getAll() {
        AuthMiddleware::handleAuth();
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
            $category = isset($_GET['category']) ? $_GET['category'] : null;
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

            $result = $this->productRepository->getProducts($page, $limit, $category, $sort);
            Response::json([
                'status' => 'success',
                'data' => [
                    'products' => $result['data'],
                    'pagination' => $result['pagination']
                ]
            ]);
        } catch (\Exception $e) {
            Response::error('Failed to fetch products', 500);
        }
    }

    // GET: /products/{id}
    public function getById($id) {
        try {
            $product = $this->productRepository->getProductById($id);
            
            if (!$product) {
                Response::error('Product not found', 404);
                return;
            }

            Response::json([
                'status' => 'success',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            Response::error('Failed to fetch product', 500);
        }
    }

    // GET: /products/category/{category}
    public function getByCategory($category) {
        try {
            $products = $this->productRepository->getProductsByCategory($category);
            Response::json([
                'status' => 'success',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Response::error('Failed to fetch products by category', 500);
        }
    }

    // POST: /products
    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            $requiredFields = ['name', 'price', 'description', 'imageUrl', 'categoryId'];
            $errors = [];

            // Check for missing fields
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $errors[$field] = ucfirst($field) . " is required";
                }
            }

            // Validate data types and values
            if (isset($data['price'])) {
                if (!is_numeric($data['price']) || $data['price'] <= 0) {
                    $errors['price'] = "Price must be a positive number";
                }
            }

            if (isset($data['categoryId'])) {
                if (!is_numeric($data['categoryId'])) {
                    $errors['categoryId'] = "Category ID must be a number";
                }
            }

            if (isset($data['imageUrl'])) {
                if (!filter_var($data['imageUrl'], FILTER_VALIDATE_URL)) {
                    $errors['imageUrl'] = "Invalid image URL format";
                }
            }

            // If there are validation errors, return them
            if (!empty($errors)) {
                Response::json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ], 400);
                return;
            }

            // Create the product
            $product = $this->productRepository->addProduct($data['name'], $data['description'], floatval($data['price']), $data['imageUrl'], $data['categoryId']);
            
            if ($product) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Product created successfully',
                    'data' => $product
                ], 201);
            } else {
                Response::error('Failed to create product', 500);
            }

        } catch (\Exception $e) {
            Response::error('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    // PUT: /products/{id}
    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if product exists
            $product = $this->productRepository->getProductById($id);
            if (!$product) {
                Response::error('Product not found', 404);
                return;
            }

            $success = $this->productRepository->updateProduct($id, $data['name'], $data['description'], floatval($data['price']), $data['imageUrl'], $data['categoryId']);
            
            if ($success) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Product updated successfully'
                ]);
            } else {
                Response::error('Failed to update product', 400);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            Response::error('Failed to update product', 500);
        }
    }

    // DELETE: /products/{id}
    public function delete($id) {
        try {
            // Check if product exists
            $product = $this->productRepository->getProductById($id);
            if (!$product) {
                Response::error('Product not found', 404);
                return;
            }

            $success = $this->productRepository->deleteProduct($id);
            
            if ($success) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Product deleted successfully'
                ]);
            } else {
                Response::error('Failed to delete product', 400);
            }
        } catch (\Exception $e) {
            Response::error('Failed to delete product', 500);
        }
    }

    public function loginTest() {
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data['email'] === 'admin@gmail.com' && $data['password'] === '123456') {
            Response::json([
                'status' => 'success',
                'message' => 'Login successfully'
            ]);
        } else {
            Response::error('Login failed', 401);
        }
    }
}
