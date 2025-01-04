<?php
require_once 'App/Repository/OrderRepository.php'; 
require_once('App/config/database.php'); 
class OrderApiController {
    private $orderRepository; 
    private $db; 
    public function __construct() 
    { 
        $this->db = (new Database())->getConnection(); 
        $this->orderRepository = new OrderRepository($this->db);
    }

    // GET: /orders
    public function getAllOrders() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
        $orders = $this->orderRepository->getAllOrders();
        return $orders;
    }

    // GET: /orders/{id}
    public function getOrderById($id) {
        $order = $this->orderRepository->getOrderById($id);
        return $order;
    }

    // GET: /orders/history/{idUser}
    public function getOrderByIdUser($idUser) {
        $order = $this->orderRepository->getOrderByIdUser($idUser);
        return $order;
    }

    // POST: /orders
    public function createOrder() {
        $order = json_decode(file_get_contents('php://input'), true);
        $created = $this->orderRepository->createOrder($order['user_id'], $order['status'], $order['total_amount'], $order['payment']);
        if ($created) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Order created successfully',
                    'data' => $created
                ], 201);
            } else {
                Response::error('Failed to create product', 500);
            }
    }

    // PUT: /orders/{id}
    public function updateOrder($id) {
        $order = json_decode(file_get_contents('php://input'), true);
        $updated = $this->orderRepository->updateOrderStatus($id, $order['status']);
        if ($updated) {
            Response::json([
                'status' => 'success',
                'message' => 'Order updated successfully'
            ]);
        } else {
            Response::error('Failed to update order', 500);
        }
    }
}