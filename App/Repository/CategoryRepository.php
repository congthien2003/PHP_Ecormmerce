<?php
class CategoryRepository {
    private $db;
    private $table_name = "categories";

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllCategories() {
        try {
            $query = "SELECT * FROM " . $this->table_name . "";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            throw new Exception("Error fetching categories: " . $e->getMessage());
        }
    }

    public function getCategoryById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            throw new Exception("Error fetching category: " . $e->getMessage());
        }
    }

    public function createCategory($name, $description = '') {
        try {
            $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
            $stmt = $this->db->prepare($query);
            
            // Sanitize inputs
            $name = htmlspecialchars(strip_tags($name));
            $description = htmlspecialchars(strip_tags($description));

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);

            if ($stmt->execute()) {
                $newCategoryId = $this->db->lastInsertId();
                return $this->getCategoryById($newCategoryId);
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception("Error creating category: " . $e->getMessage());
        }
    }

    public function updateCategory($id, $name, $description = '') {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET name = :name, description = :description 
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            
            // Sanitize inputs
            $name = htmlspecialchars(strip_tags($name));
            $description = htmlspecialchars(strip_tags($description));
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);

            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Error updating category: " . $e->getMessage());
        }
    }

    public function deleteCategory($id) {
        try {
            // First check if category has any products
            $checkQuery = "SELECT COUNT(*) as count FROM products WHERE categoryId = :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':id', $id);
            $checkStmt->execute();
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                throw new Exception("Cannot delete category with existing products");
            }

            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Error deleting category: " . $e->getMessage());
        }
    }
} 