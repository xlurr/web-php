<?php
class OrderController extends Controller {
    private $orderModel;
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
        $this->orderModel = new Order($this->db);
    }
    
    public function index() {
        $this->auth->requireAuth();
        
        $message = $this->getMessage();
        
        // Создание заказа
        if ($_POST && isset($_POST['create'])) {
            $this->orderModel->customer_id = $this->auth->isAdmin() ? $_POST['customer_id'] : $this->auth->getCustomerId();
            $this->orderModel->product_id = $_POST['product_id'];
            $this->orderModel->quantity = $_POST['quantity'];
            
            if ($this->orderModel->create($this->auth)) {
                $this->setMessage("Заказ успешно создан!", "success");
                $this->redirect('orders');
            } else {
                $message = ["text" => "Ошибка при создании заказа!", "type" => "danger"];
            }
        }
        
        // Редактирование заказа
        if ($_POST && isset($_POST['update'])) {
            $order_id = $_POST['id'];
            $this->orderModel->customer_id = $_POST['customer_id'];
            $this->orderModel->product_id = $_POST['product_id'];
            $this->orderModel->quantity = $_POST['quantity'];
            
            if ($this->orderModel->update($this->auth, $order_id)) {
                $this->setMessage("Заказ успешно обновлен!", "success");
                $this->redirect('orders');
            } else {
                $message = ["text" => "Ошибка при обновлении заказа!", "type" => "danger"];
            }
        }
        
        // Удаление заказа
        if (isset($_GET['delete_id'])) {
            $order_id = $_GET['delete_id'];
            if ($this->orderModel->delete($this->auth, $order_id)) {
                $this->setMessage("Заказ успешно удален!", "success");
                $this->redirect('orders');
            } else {
                $message = ["text" => "Ошибка при удалении заказа!", "type" => "danger"];
            }
        }
        
        $orders = $this->orderModel->read($this->auth);
        $customers = $this->orderModel->getCustomers();
        $products = $this->orderModel->getProducts();
        
        $this->view('orders/index', [
            'orders' => $orders,
            'customers' => $customers,
            'products' => $products,
            'message' => $message
        ]);
    }
    
    public function edit() {
        $this->auth->requireAuth();
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $order = $this->orderModel->getById($this->auth, $id);
        
        if (!$order) {
            $this->setMessage("Заказ не найден или у вас нет прав для его редактирования!", "danger");
            $this->redirect('orders');
        }
        
        $customers = $this->orderModel->getCustomers();
        $products = $this->orderModel->getProducts();
        
        $this->view('orders/edit', [
            'order' => $order,
            'customers' => $customers,
            'products' => $products
        ]);
    }
    
    public function my() {
        $this->auth->requireAuth();
        
        if ($this->auth->isAdmin()) {
            $this->redirect('orders');
        }
        
        // Для пользователя показываем только его заказы
        $orders = $this->orderModel->read($this->auth);
        
        $this->view('orders/my', [
            'orders' => $orders,
            'message' => $this->getMessage()
        ]);
    }
}
?>