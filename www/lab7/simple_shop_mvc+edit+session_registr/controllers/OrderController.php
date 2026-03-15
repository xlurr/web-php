<?php
class OrderController extends Controller {
    private $orderModel;
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
        $this->orderModel = new Order($database);
    }
    
    // Просмотр всех заказов (для админа)
    public function index() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
        }
        
        if (!$this->auth->isAdmin()) {
            $this->redirect('access-denied');
        }
        
        $orders = $this->orderModel->read($this->auth);
        $customerModel = new Customer($this->db);
        $customers = $customerModel->read();
        $productModel = new Product($this->db);
        $products = $productModel->read();
        
        $this->view('orders/index', [
            'orders' => $orders,
            'customers' => $customers,
            'products' => $products,
            'message' => null
        ]);
    }
    
    // Просмотр заказов пользователя (для обычных пользователей)
    public function my() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
        }
        
        // Для админа это запрещено
        if ($this->auth->isAdmin()) {
            $this->redirect('access-denied');
        }
        
        $userId = $this->auth->getUserId();
        $userModel = new User($this->db);
        $userData = $userModel->getById($userId);
        $customerId = $userData['customer_id'] ?? null;
        
        // Получаем заказы пользователя
        $orders = $this->orderModel->read($this->auth);
        
        $productModel = new Product($this->db);
        $products = $productModel->read();
        
        $this->view('orders/my', [
            'orders' => $orders,
            'products' => $products,
            'customerId' => $customerId,
            'message' => null
        ]);
    }
    
    // Создание нового заказа (для АДМИНА и USER)
    public function create() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
        }
        
        if ($_POST && isset($_POST['create_order'])) {
            $userId = $this->auth->getUserId();
            $userModel = new User($this->db);
            $userData = $userModel->getById($userId);
            $customerId = $userData['customer_id'];
            
            // Установим данные в orderModel
            $this->orderModel->customer_id = $customerId;
            $this->orderModel->product_id = $_POST['product_id'];
            $this->orderModel->quantity = $_POST['quantity'];
            
            if ($this->orderModel->create($this->auth)) {
                $this->setMessage('Заказ успешно создан!', 'success');
            } else {
                $this->setMessage('Ошибка при создании заказа', 'danger');
            }
        }
        
        // Перенаправляем в зависимости от роли
        if ($this->auth->isAdmin()) {
            $this->redirect('orders');
        } else {
            $this->redirect('orders&action=my');
        }
    }
    
    // Редактирование заказа
    public function edit() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
        }
        
        if (!$this->auth->isAdmin()) {
            $this->redirect('access-denied');
        }
        
        if ($_POST && isset($_POST['update_order'])) {
            $orderId = $_POST['order_id'];
            
            $this->orderModel->id = $orderId;
            $this->orderModel->customer_id = $_POST['customer_id'];
            $this->orderModel->product_id = $_POST['product_id'];
            $this->orderModel->quantity = $_POST['quantity'];
            
            if ($this->orderModel->update($this->auth, $orderId)) {
                $this->setMessage('Заказ успешно обновлен!', 'success');
                $this->redirect('orders');
            } else {
                $this->setMessage('Ошибка при обновлении заказа', 'danger');
            }
        }
        
        $orderId = $_GET['id'] ?? null;
        if (!$orderId) {
            $this->redirect('orders');
        }
        
        $order = $this->orderModel->getById($this->auth, $orderId);
        $customerModel = new Customer($this->db);
        $customers = $customerModel->read();
        $productModel = new Product($this->db);
        $products = $productModel->read();
        
        $this->view('orders/edit', [
            'order' => $order,
            'customers' => $customers,
            'products' => $products
        ]);
    }
    
    public function delete() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
        }
        
        $orderId = $_GET['id'] ?? null;
        if (!$orderId) {
            if ($this->auth->isAdmin()) {
                $this->redirect('orders');
            } else {
                $this->redirect('orders&action=my');
            }
        }
        
        if ($this->orderModel->delete($this->auth, $orderId)) {
            $this->setMessage('Заказ удален!', 'success');
        } else {
            $this->setMessage('Ошибка при удалении заказа', 'danger');
        }
        
        if ($this->auth->isAdmin()) {
            $this->redirect('orders');
        } else {
            $this->redirect('orders&action=my');
        }
    }
}
?>