<?php 
class ProductRepository
{ 
    private $conn; 
    private $table_name = "product"; 
 
    public function __construct($db) 
    { 
        $this->conn = $db; 
    } 
 
    public function getProducts($page = 1, $limit = 12, $category = null, $sort = 'default') 
{ 
    try {
        // Calculate offset for pagination
        $offset = (int)($page - 1) * $limit;
        $params = [];
        
        // Base query for products
        $query = "SELECT 
            p.ID,
            p.Name,
            p.Description,
            p.Price,
            p.ImageURL,
            c.ID as CategoryId,
            c.Name as CategoryName,
            CASE 
                WHEN pf.idFood IS NOT NULL THEN 1 -- Mark as favorite
                ELSE 0 -- Not favorite
            END AS isFavorite
        FROM 
            product AS p
        LEFT JOIN 
            categories AS c 
        ON 
            p.CategoryId = c.ID
        LEFT JOIN 
            productfavorite AS pf 
        ON 
            p.ID = pf.idFood
        WHERE 1=1";

        // Add category filter if specified
        if ($category !== null && $category !== 0) {
            $query .= " AND p.CategoryId = :category";
            $params[':category'] = $category;
        }

        // Add sorting
        switch ($sort) {
            case 'price_asc':
                $query .= " ORDER BY p.Price ASC";
                break;
            case 'price_desc':
                $query .= " ORDER BY p.Price DESC";
                break;
            case 'name_asc':
                $query .= " ORDER BY p.Name ASC";
                break;
            case 'name_desc':
                $query .= " ORDER BY p.Name DESC";
                break;
            default:
                $query .= " ORDER BY p.ID DESC";
        }

        // Add pagination
        $query .= " LIMIT $limit OFFSET $offset";

        // Prepare and execute the query
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM product";
        if ($category !== null && $category !== 0) {
            $countQuery .= " WHERE CategoryId = $category";
        }
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute();
        $total = $countStmt->fetch(PDO::FETCH_OBJ)->total;

        // Return result with pagination info
        return [
            'data' => $products,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
            ]
        ];
    } 
    catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}

    

 
    public function getProductById($id) 
    { 
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':id', $id); 
        $stmt->execute(); 
        $result = $stmt->fetch(PDO::FETCH_OBJ); 
        return $result; 
    } 
 
    public function addProduct($name, $description, $price, $imageURL, $category_id) 
    { 
        try {
            $errors = []; 
            if (empty($name)) { 
                $errors['name'] = 'Tên sản phẩm không được để trống'; 
            } 
            if (empty($description)) { 
                $errors['description'] = 'Mô tả không được để trống';
            } 
            if (!is_numeric($price) || $price < 0) { 
                $errors['price'] = 'Giá sản phẩm không hợp lệ'; 
            } 
            
            if (count($errors) > 0) { 
                return $errors; 
            } 

            $query = "INSERT INTO " . $this->table_name . " (name, description, price, imageURL, categoryId) 
                     VALUES (:name, :description, :price, :imageURL, :category_id)";  
            
            $stmt = $this->conn->prepare($query); 

            // Sanitize inputs
            $name = htmlspecialchars(strip_tags($name)); 
            $description = htmlspecialchars(strip_tags($description)); 
            $price = htmlspecialchars(strip_tags($price)); 
            $imageURL = htmlspecialchars(strip_tags($imageURL)); 

            // Bind parameters
            $stmt->bindParam(':name', $name); 
            $stmt->bindParam(':description', $description); 
            $stmt->bindParam(':price', $price); 
            $stmt->bindParam(':imageURL', $imageURL); 
            $stmt->bindParam(':category_id', $category_id); 

            // Execute query
            if ($stmt->execute()) { 
                $newProductId = $this->conn->lastInsertId();
                
                // Fetch the newly created product
                $query = "SELECT 
                            p.ID,
                            p.Name,
                            p.Description,
                            p.Price,
                            p.ImageURL,
                            p.categoryId,
                            c.name as category_name
                         FROM " . $this->table_name . " p
                         LEFT JOIN categories c ON p.categoryId = c.id
                         WHERE p.ID = :id";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $newProductId);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_OBJ);
            } 

            return false; 
        } catch(PDOException $e) {
            throw new Exception("Error creating product: " . $e->getMessage());
        }
    } 

    public function addProductFavorite($id) 
    { 
        $errors = [];
        $tablename = "productfavorite";

        if (count($errors) > 0) {
            return $errors;
        }

        $query = "INSERT INTO " . $tablename . " (idFood, createdAt, timeExpired) VALUES (:idFood, :createdAt, :timeExpired)";
        $stmt = $this->conn->prepare($query);

        // Lấy dữ liệu
        $idFood = htmlspecialchars(strip_tags($id));
        $createdAt = date('Y-m-d H:i:s'); // Thời gian hiện tại
        $timeExpired = date('Y-m-d H:i:s', strtotime('+30 days')); // Thời gian hết hạn sau 30 ngày

        // Gắn tham số
        $stmt->bindParam(':idFood', $idFood);
        $stmt->bindParam(':createdAt', $createdAt);
        $stmt->bindParam(':timeExpired', $timeExpired);

        // Thực thi và trả về kết quả
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function removeFavorite($id)
    {
        $tablename = "productfavorite";
        $query = "DELETE FROM " . $tablename . " WHERE idFood = :idFood";

        $stmt = $this->conn->prepare($query);
        $idFood = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':idFood', $idFood);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }   

    public function removeExpiredFavorites()
    {
        $tablename = "productfavorite";
        $query = "DELETE FROM " . $tablename . " WHERE timeExpired < :currentDate";

        $stmt = $this->conn->prepare($query);
        $currentDate = date('Y-m-d H:i:s'); // Thời gian hiện tại
        $stmt->bindParam(':currentDate', $currentDate);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


 
    public function updateProduct($id, $name, $description, $price, $imageURL, $category_id) 
    { 
        $query = "UPDATE " . $this->table_name . " SET name=:name, 
description=:description, price=:price, imageURL=:imageURL, categoryId=:category_id WHERE id=:id"; 
        $stmt = $this->conn->prepare($query); 
 
        $name = htmlspecialchars(strip_tags($name)); 
        $description = htmlspecialchars(strip_tags($description)); 
        $price = htmlspecialchars(strip_tags($price)); 
        $category_id = htmlspecialchars(strip_tags($category_id)); 
 
        $stmt->bindParam(':id', $id); 
        $stmt->bindParam(':name', $name); 
        $stmt->bindParam(':description', $description); 
        $stmt->bindParam(':price', $price); 
        $stmt->bindParam(':imageURL', $imageURL); 
        $stmt->bindParam(':category_id', $category_id); 
 
        if ($stmt->execute()) {
             return true; 
        } 
        return false; 
    } 
    
    public function deleteProduct($id) 
    { 
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':id', $id); 
        if ($stmt->execute()) { 
        return true; 
        } 
        return false; 
    } 

    public function getProductsPagi($page = 1, $limit = 12, $category = null, $sort = 'default') {
        $offset = ($page - 1) * $limit;
        $params = [];
        
        // Base query
        $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE 1=1";

        // Add category filter if specified
        if ($category && $category !== 'all') {
            $query .= " AND c.slug = ?";
            $params[] = $category;
        }

        // Add sorting
        switch ($sort) {
            case 'price_asc':
                $query .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $query .= " ORDER BY p.price DESC";
                break;
            case 'name_asc':
                $query .= " ORDER BY p.name ASC";
                break;
            case 'name_desc':
                $query .= " ORDER BY p.name DESC";
                break;
            default:
                $query .= " ORDER BY p.created_at DESC";
        }

        // Get total count for pagination
        $countQuery = str_replace("SELECT p.*, c.name as category_name", "SELECT COUNT(*) as total", $query);
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute($params);
        $totalCount = $countStmt->fetch()['total'];
        $totalPages = ceil($totalCount / $limit);

        // Add pagination
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        return [
            'products' => $products,
            'totalPages' => $totalPages
        ];
    }

    public function getProductByIdApi($id) {
        $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getProductsByCategory($category) {
        echo $category;
        if ($category == 0) {
            $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id";   
        }
        else {
            $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE c.slug = ?";
        }
        
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }
} 
?> 