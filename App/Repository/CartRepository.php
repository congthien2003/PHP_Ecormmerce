<?php 

class CartRepository
{ 
    private $conn; 
    private $table_name = "cart"; 

    public function __construct($db) 
    { 
        $this->conn = $db; 
    } 
    
    // Lấy tất cả các sản phẩm trong giỏ hàng của một người dùng
    public function getCartByUserId($userId) 
    { 
        $query = "SELECT 
                    c.IdUser,
                    c.IdProduct,
                    c.Quantity,
                    c.UpdatedAt,
                    p.Name AS productName,
                    p.Price AS productPrice,
                    p.ImageURL AS productImage
                  FROM 
                    cart AS c
                  JOIN 
                    product AS p ON c.IdProduct = p.ID
                  WHERE 
                    c.IdUser = :userId";

        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':userId', $userId); 
        $stmt->execute(); 
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        return $result; 
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addProductToCart($userId, $productId, $quantity) 
    { 
        // Kiểm tra nếu sản phẩm đã có trong giỏ hàng thì cập nhật số lượng
        $query = "SELECT * FROM " . $this->table_name . " WHERE IdUser = :userId AND IdProduct = :productId";
        $stmt = $this->conn->prepare($query); 
        $stmt->bindParam(':userId', $userId); 
        $stmt->bindParam(':productId', $productId); 
        $stmt->execute();
        $existingProduct = $stmt->fetch(PDO::FETCH_OBJ);

        if ($existingProduct) {
            // Cập nhật số lượng sản phẩm nếu đã tồn tại trong giỏ
            $query = "UPDATE " . $this->table_name . " SET Quantity = Quantity + :quantity, UpdatedAt = :updatedAt WHERE IdUser = :userId AND IdProduct = :productId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':updatedAt', date('Y-m-d H:i:s'));
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':productId', $productId);
            if ($stmt->execute()) {
                return true;
            }
        } else {
            // Thêm sản phẩm mới vào giỏ hàng
            $query = "INSERT INTO " . $this->table_name . " (IdUser, IdProduct, Quantity, UpdatedAt) VALUES (:userId, :productId, :quantity, :updatedAt)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':updatedAt', date('Y-m-d H:i:s'));
            if ($stmt->execute()) {
                return true;
            }
        }

        return false;
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateProductQuantityInCart($userId, $productId, $quantity) 
    { 
        $query = "UPDATE " . $this->table_name . " SET Quantity = :quantity, UpdatedAt = :updatedAt WHERE IdUser = :userId AND IdProduct = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':updatedAt', date('Y-m-d H:i:s'));
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':productId', $productId);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeProductFromCart($userId, $productId) 
    { 
        $query = "DELETE FROM " . $this->table_name . " WHERE IdUser = :userId AND IdProduct = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':productId', $productId);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa giỏ hàng của người dùng
    public function clearCart($userId) 
    { 
        $query = "DELETE FROM " . $this->table_name . " WHERE IdUser = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

?>
