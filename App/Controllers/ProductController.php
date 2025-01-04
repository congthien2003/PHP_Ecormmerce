<?php 
 
require_once 'app/models/ProductModel.php'; 
require_once 'app/Repository/ProductRepository.php'; 
require_once('app/config/database.php'); 
class ProductController 
{   
    private $productModel; 
    private $db; 
    private $products = [
            
    ]; 
    private $productRepository;
    public function __construct() 
    { 
        // Giả sử chúng ta lưu trữ sản phẩm trong session để giữ lại khi làm mới trang 
        session_start(); 
        if (isset($_SESSION['products'])) { 
            $this->products = $_SESSION['products']; 
        } 

        $this->db = (new Database())->getConnection(); 
    } 
 
    public function index() 
    { 
        $this->list(); 
    } 
 
    public function list() 
    { 
        // Hiển thị danh sách sản phẩm 
        $products = (new ProductRepository($this->db))->getProducts(); 
        include 'App/views/product/list.php'; 
    } 
 
    public function add()
{
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];

        // Validate name
        if (empty($name)) {
            $errors[] = 'Tên sản phẩm là bắt buộc.';
        } elseif (strlen($name) < 10 || strlen($name) > 100) {
            $errors[] = 'Tên sản phẩm phải có từ 10 đến 100 ký tự.';
        }

        // Validate price
        if (!is_numeric($price) || $price <= 0) {
            $errors[] = 'Giá phải là một số dương lớn hơn 0.';
        }

        // Validate and upload image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image']['tmp_name'];
            $imageName = $_FILES['image']['name'];
            $bucket = 'images'; // Supabase storage bucket
            $destinationPath = "products/" . uniqid() . "_" . basename($imageName);

            // Use the UploadService
            try {
                require_once 'App/Services/UploadService.php'; // Include the UploadService class

                $supabaseUrl = 'https://your-supabase-url.supabase.co'; // Replace with your Supabase URL
                $supabaseKey = 'your-supabase-key'; // Replace with your Supabase API key

                $uploadService = new UploadService($supabaseUrl, $supabaseKey);
                $response = $uploadService->uploadImage($bucket, $imageTmpPath, $destinationPath);

                if (isset($response['Key'])) {
                    // Supabase returns the object key; construct the URL
                    $imageURL = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$response['Key']}";
                } else {
                    $errors[] = 'Failed to upload image.';
                }
            } catch (\Exception $e) {
                $errors[] = "Image upload error: " . $e->getMessage();
            }
        } else {
            $errors[] = 'Image is required or failed to upload.';
        }

        // If no errors, proceed to add the product
        if (empty($errors)) {
            $newproduct = (new ProductRepository($this->db))->addProduct($name, $description, $price, $imageURL, $category_id);

            if ($newproduct) {
                $this->products[] = $newproduct;
                $_SESSION['products'] = $this->products;
                header('Location: /php/S4_PHP/Product/list');
                exit();
            } else {
                header('Location: /php/S4_PHP/Product/add');
                exit();
            }
        }
    }

    include 'App/views/product/add.php';
}
    
    public function addFavorites() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json'); // Định dạng trả về là JSON

            $id = $_POST['idFood'] ?? null;
            $status = $_POST['isFavorite'] ?? null;

            if (!$id || !in_array($status, ['true', 'false'], true)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ.'
                ]);
                return;
            }

            $productRepo = new ProductRepository($this->db);
            $product = $productRepo->getProductById($id);

            if (!$product) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại.'
                ]);
                return;
            }

            $result = $status === 'true'
                ? $productRepo->addProductFavorite($id)
                : $productRepo->removeFavorite($id);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => $status === 'true' ? 'Thêm yêu thích thành công.' : 'Xóa yêu thích thành công.',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi khi cập nhật yêu thích.'
                ]);
            }
        } else {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode([
                'success' => false,
                'message' => 'Chỉ chấp nhận yêu cầu POST.'
            ]);
        }
    }
 
    public function edit($id) 
    { 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
            foreach ($this->products as $key => $product) { 
                if ($product->getID() == $id) { 
                    $this->products[$key]->setName($_POST['name']); 
                    $this->products[$key]->setDescription($_POST['description']); 
                    $this->products[$key]->setPrice($_POST['price']); 
                    break; 
                } 
            } 
 
            $_SESSION['products'] = $this->products; 
 
            header('Location: /php/S4_PHP/Product/list'); 
            exit(); 
        } 
 
        foreach ($this->products as $product) { 
            if ($product->getID() == $id) { 
                include 'App/views/product/edit.php'; 
                return; 
            } 
        } 
 
        die('Product not found'); 
    } 
 
    public function delete($id) 
    { 
        foreach ($this->products as $key => $product) { 
            if ($product->getID() == $id) { 
                unset($this->products[$key]); 
                break; 
            } 
        } 

        $this->products = array_values($this->products); 
        $_SESSION['products'] = $this->products; 
 
        header('Location: /php/S4_PHP/Product/list'); 
        exit(); 
    } 

} 
 
?> 