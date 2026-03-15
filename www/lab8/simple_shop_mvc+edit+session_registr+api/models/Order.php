<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $customer_id;
    public $product_id;
    public $quantity;
    public $total;
    public $order_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Получить заказы с учетом прав доступа
    public function read($auth) {
        if ($auth->isAdmin()) {
            // Админ видит все заказы
            $query = "SELECT o.*, c.name as customer_name, p.name as product_name, p.price as product_price
                      FROM " . $this->table_name . " o
                      LEFT JOIN customers c ON o.customer_id = c.id
                      LEFT JOIN products p ON o.product_id = p.id
                      ORDER BY o.order_date DESC";
        } else {
            // Пользователь видит только свои заказы
            $customer_id = $auth->getCustomerId();
            $query = "SELECT o.*, c.name as customer_name, p.name as product_name, p.price as product_price
                      FROM " . $this->table_name . " o
                      LEFT JOIN customers c ON o.customer_id = c.id
                      LEFT JOIN products p ON o.product_id = p.id
                      WHERE o.customer_id = ?
                      ORDER BY o.order_date DESC";
        }
        
        if ($auth->isAdmin()) {
            $result = $this->conn->query($query);
        } else {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        
        return $result;
    }

    // Создание заказа с проверкой прав
    public function create($auth) {
        // Для пользователей автоматически подставляем их customer_id
        if ($auth->isUser()) {
            $this->customer_id = $auth->getCustomerId();
        }

        // Получаем цену товара
        $product_query = "SELECT price FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($product_query);
        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if (!$product) {
            return false;
        }

        // Рассчитываем общую сумму
        $this->total = $product['price'] * $this->quantity;

        $query = "INSERT INTO " . $this->table_name . " 
                  SET customer_id=?, product_id=?, quantity=?, total=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiid", $this->customer_id, $this->product_id, $this->quantity, $this->total);
        
        return $stmt->execute();
    }

    // Обновление заказа с проверкой прав
    public function update($auth, $order_id) {
        // Проверяем права на редактирование
        if (!$this->canEdit($auth, $order_id)) {
            return false;
        }

        // Получаем цену товара для пересчета
        $product_query = "SELECT price FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($product_query);
        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if (!$product) {
            return false;
        }

        $this->total = $product['price'] * $this->quantity;

        $query = "UPDATE " . $this->table_name . " 
                  SET customer_id=?, product_id=?, quantity=?, total=?
                  WHERE id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiidi", $this->customer_id, $this->product_id, $this->quantity, $this->total, $order_id);
        
        return $stmt->execute();
    }

    // Удаление заказа с проверкой прав
    public function delete($auth, $order_id) {
        // Проверяем права на удаление
        if (!$this->canEdit($auth, $order_id)) {
            return false;
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        return $stmt->execute();
    }

    // Проверка прав на редактирование/удаление заказа
    private function canEdit($auth, $order_id) {
        if ($auth->isAdmin()) {
            return true;
        }
        
        // Пользователь может редактировать только свои заказы
        $customer_id = $auth->getCustomerId();
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE id = ? AND customer_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $order_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }

    // Получить заказ по ID с проверкой прав
    public function getById($auth, $id) {
        if ($auth->isAdmin()) {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        } else {
            $customer_id = $auth->getCustomerId();
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE id = ? AND customer_id = ?";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($auth->isAdmin()) {
            $stmt->bind_param("i", $id);
        } else {
            $stmt->bind_param("ii", $id, $customer_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getCustomers() {
        $query = "SELECT id, name FROM customers ORDER BY name";
        $result = $this->conn->query($query);
        return $result;
    }

    public function getProducts() {
        $query = "SELECT id, name, price FROM products ORDER BY name";
        $result = $this->conn->query($query);
        return $result;
    }
}
?>