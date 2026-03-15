<?php
class OrderController extends Controller {
    private $orderModel;
    
    public function __construct($database) {
        parent::__construct($database);
        $this->orderModel = new Order($this->db);
    }
    
    public function index() {
        $message = '';
        
        // Создание заказа
        if ($_POST && isset($_POST['create'])) {
            $this->orderModel->customer_id = $_POST['customer_id'];
            $this->orderModel->product_id = $_POST['product_id'];
            $this->orderModel->quantity = $_POST['quantity'];
            
            if ($this->orderModel->create()) {
                $message = "Заказ успешно создан!";
            } else {
                $message = "Ошибка при создании заказа!";
            }
        }
        //Изменения
        if ($_POST && isset($_POST['update'])) {
            $this->orderModel->id = $_POST['id'];
            $this->orderModel->customer_id = $_POST['customer_id'];
            $this->orderModel->product_id = $_POST['product_id'];
            $this->orderModel->quantity = $_POST['quantity'];
            $this->orderModel->total = $_POST['total'];
            if ($this->orderModel->update()) {
                $message = "Заказ успешно обновлён!";
                $this->redirect('orders');
            } else {
                $message = "Ошибка при обновлении заказа!";
            }
        }

        // Удаление заказа
        if (isset($_GET['delete_id'])) {
            $this->orderModel->id = $_GET['delete_id'];
            if ($this->orderModel->delete()) {
                $message = "Заказ успешно удален!";
            } else {
                $message = "Ошибка при удалении заказа!";
            }
        }
        
        $orders = $this->orderModel->read();
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
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $order = $this->orderModel->getById($id);
            if (!$order) {
                $this->redirect('orders');
            }
        // Для выпадающих списков покупателей и товаров
        $customers = $this->orderModel->getCustomers();
        $products = $this->orderModel->getProducts();
        $this->view('orders/edit', [
            'order' => $order,
            'customers' => $customers,
            'products' => $products
        ]);
    }
}
?>