<?php
class Order {
    private $conn;
    private $tablename = 'orders';
    
    public $id;
    public $customer_id;
    public $product_id;
    public $quantity;
    public $total;
    public $order_date;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Получить заказы (для админа - все, для user - свои)
    public function read($auth) {
        if ($auth->isAdmin()) {
            $query = "SELECT o.*, 
                            c.name as customer_name, 
                            p.name as product_name, 
                            p.price as product_price
                     FROM " . $this->tablename . " o
                     LEFT JOIN customers c ON o.customer_id = c.id
                     LEFT JOIN products p ON o.product_id = p.id
                     ORDER BY o.order_date DESC";
            
            $result = $this->conn->query($query);
        } else {
            $customer_id = $auth->getUserId();
            $query = "SELECT o.*, 
                            c.name as customer_name, 
                            p.name as product_name, 
                            p.price as product_price
                     FROM " . $this->tablename . " o
                     LEFT JOIN customers c ON o.customer_id = c.id
                     LEFT JOIN products p ON o.product_id = p.id
                     WHERE o.customer_id = (
                        SELECT customer_id FROM users WHERE id = ?
                     )
                     ORDER BY o.order_date DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        
        return $result;
    }
    
    // Создание заказа (для админа и user)
    public function create($auth) {
        // Получаем цену товара
        $product_query = "SELECT price FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($product_query);
        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if (!$product) {
            return false;
        }
        
        // Вычисляем общую стоимость
        $this->total = $product['price'] * $this->quantity;
        
        // Вставляем заказ
        $query = "INSERT INTO " . $this->tablename . " 
                  SET customer_id = ?, 
                      product_id = ?, 
                      quantity = ?, 
                      total = ?,
                      order_date = NOW()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iidi", 
            $this->customer_id, 
            $this->product_id, 
            $this->quantity, 
            $this->total
        );
        
        return $stmt->execute();
    }
    
    // Обновление заказа
    public function update($auth, $order_id) {
        // Проверка доступа
        if (!$this->canEdit($auth, $order_id)) {
            return false;
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
        
        // Вычисляем общую стоимость
        $this->total = $product['price'] * $this->quantity;
        
        // Обновляем заказ
        $query = "UPDATE " . $this->tablename . " 
                  SET customer_id = ?, 
                      product_id = ?, 
                      quantity = ?, 
                      total = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iidii", 
            $this->customer_id, 
            $this->product_id, 
            $this->quantity, 
            $this->total, 
            $order_id
        );
        
        return $stmt->execute();
    }
    
    // Удаление заказа
    public function delete($auth, $order_id) {
        // Проверка доступа
        if (!$this->canEdit($auth, $order_id)) {
            return false;
        }
        
        $query = "DELETE FROM " . $this->tablename . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        
        return $stmt->execute();
    }
    
    // Проверка прав редактирования
    private function canEdit($auth, $order_id) {
        // Админ может редактировать любой заказ
        if ($auth->isAdmin()) {
            return true;
        }
        
        // User может редактировать только свои заказы
        $customer_id = $auth->getUserId();
        $query = "SELECT id FROM " . $this->tablename . " 
                  WHERE id = ? AND customer_id = (
                    SELECT customer_id FROM users WHERE id = ?
                  )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $order_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    // Получить заказ по ID
    public function getById($auth, $id) {
        if ($auth->isAdmin()) {
            $query = "SELECT o.*, 
                            c.name as customer_name, 
                            p.name as product_name, 
                            p.price as product_price
                     FROM " . $this->tablename . " o
                     LEFT JOIN customers c ON o.customer_id = c.id
                     LEFT JOIN products p ON o.product_id = p.id
                     WHERE o.id = ?";
        } else {
            $customer_id = $auth->getUserId();
            $query = "SELECT o.*, 
                            c.name as customer_name, 
                            p.name as product_name, 
                            p.price as product_price
                     FROM " . $this->tablename . " o
                     LEFT JOIN customers c ON o.customer_id = c.id
                     LEFT JOIN products p ON o.product_id = p.id
                     WHERE o.id = ? AND o.customer_id = (
                        SELECT customer_id FROM users WHERE id = ?
                     )";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($auth->isAdmin()) {
            $stmt->bind_param("i", $id);
        } else {
            $customer_id = $auth->getUserId();
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