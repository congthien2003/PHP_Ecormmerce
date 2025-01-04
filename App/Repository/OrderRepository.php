<?php

class OrderRepository {
    private $db;
    private $table_name = "orders"; 
    public function __construct($db) {
        $this->db = $db;
    }

    public function createOrder(int $userId, string $status, float $totalAmount, int $payment): int
    {
        $query = "INSERT INTO orders (user_id, order_date, status, total_amount, payment) VALUES (:user_id, :order_date, :status, :total_amount, :payment)";
        $stmt = $this->db->prepare($query);
        $date = date("Y-m-d H:i:s");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':order_date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':total_amount', $totalAmount, PDO::PARAM_STR);
        $stmt->bindParam(':payment', $payment, PDO::PARAM_STR);

        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function getAllOrders(): array
    {
        $query = "SELECT * FROM orders";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById(int $orderId): ?array
    {
        $query = "SELECT * FROM orders WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);

        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        return $order ?: null;
    }

    public function getOrderByIdUser(int $userId): ?array
    {
        $query = "SELECT * FROM orders WHERE user_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus(int $orderId, string $newStatus): bool
    {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':status', $newStatus, PDO::PARAM_STR);
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);

        return $stmt->execute();
    }

     public function deleteOrder(int $orderId): bool
    {
        $query = "DELETE FROM orders WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>