<?php

class OrderDetailRepository {
    private $db;
    private $table_name = "orderdetails"; 
    public function __construct($db) {
        $this->db = $db;
    }

    public function createOrderDetail(int $orderId, int $productId, int $quantity, float $price): int
    {
        $query = "INSERT INTO orderdetails (IdOrder, IdProduct, quantity, price, CreatedAt) VALUES (:order_id, :product_id, :quantity, :price, :created_at)";
        $stmt = $this->db->prepare($query);
        $date = date("Y-m-d H:i:s");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $date, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function getAllOrderDetails(int $idOrder): array
    {
        $query = "
        SELECT 
            orderdetails.*, 
            product.Name AS ProductName  
        FROM 
            orderdetails
        JOIN 
            product 
        ON 
            orderdetails.IdProduct = product.ID
        WHERE 
            orderdetails.IdOrder = :idorder
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idorder', $idOrder, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDetailById(int $orderDetailId): ?array
    {
        $query = "SELECT * FROM orderdetails WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $orderDetailId, PDO::PARAM_INT);

        $stmt->execute();
        $orderDetail = $stmt->fetch(PDO::FETCH_ASSOC);
        return $orderDetail ?: null;
    }

    public function updateOrderDetail(int $orderDetailId, int $quantity, float $price): bool
    {
        $query = "UPDATE orderdetails SET Quantity = :quantity WHERE Id = :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':id', $orderDetailId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteOrderDetail(int $orderDetailId)
    {
        $query = "DELETE FROM orderdetails WHERE Id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $orderDetailId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}