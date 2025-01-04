<?php 
require_once 'App/Repository/OrderDetailRepository.php'; 
require_once 'App/Repository/OrderRepository.php'; 
require_once('App/config/database.php'); 
class OrderDetailApiController {
    private $orderDetailRepository;
    private $orderRepository;
    private $db; 
    public function __construct() {
        $this->db = (new Database())->getConnection(); 
        $this->orderDetailRepository = new OrderDetailRepository($this->db);
        $this->orderRepository = new OrderRepository($this->db);
    }

    // GET: /orderdetails
    public function getAll($idOrder) {
        $orderDetails = $this->orderDetailRepository->getAllOrderDetails($idOrder);
        Response::json([
            'status' => 'success',
            'IdOrder' => $idOrder,
            'data' => $orderDetails
        ]);
    }

    // POST: /orderdetails
    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if order exists
            $order = $this->orderRepository->getOrderById($data['IdOrder']);
            if (!$order) {
                Response::error('Order not found', 404);
                return;
            }

            $success = $this->orderDetailRepository->createOrderDetail($data['IdOrder'], $data['IdProduct'], $data['Quantity'], floatval($data['Price']));
            
            if ($success) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Order detail created successfully'
                ]);
            } else {
                Response::error('Failed to create order detail', 500);
            }

        } catch (\Exception $e) {
            Response::error('Failed to create order detail: ' . $e->getMessage(), 500);
        }
    }

    // PUT: /orderdetails/{id}
    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if order detail exists
            $orderDetail = $this->orderDetailRepository->getOrderDetailById($id);
            if (!$orderDetail) {
                Response::error('Order detail not found', 404);
                return;
            }

            $success = $this->orderDetailRepository->updateOrderDetail($id, $data['quantity'], floatval($data['price']));
            
            if ($success) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Order detail updated successfully'
                ]);
            } else {
                Response::error('Failed to update order detail', 400);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            Response::error('Failed to update order detail', 500);
        }
    }

    // DELETE: /orderdetails/{id}
    public function delete($id) {
        try {
            // Check if order detail exists
            $orderDetail = $this->orderDetailRepository->getOrderDetailById($id);
            if (!$orderDetail) {
                Response::error('Order detail not found', 404);
                return;
            }
            
            $success = $this->orderDetailRepository->deleteOrderDetail($id);
            
            if ($success) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Order detail deleted successfully'
                ]);
            } else {
                Response::error('Failed to delete order detail', 400);
            }
        } catch (\Exception $e) {
            Response::error($e, 500);
        }
    }
}