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

    public function read() {
        $query = "SELECT o.*, c.name as customer_name, p.name as product_name, p.price as product_price
          FROM " . $this->table_name . " o
          LEFT JOIN customers c ON o.customer_id = c.id
          LEFT JOIN products p ON o.product_id = p.id
          ORDER BY o.order_date DESC";
        $result = $this->conn->query($query);
        return $result;

    }

    public function create() {
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

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }

    public function update() {
    $query = "UPDATE " . $this->table_name . " SET customer_id=?, product_id=?, quantity=?, total=? WHERE id=?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("iiidi", $this->customer_id, $this->product_id, $this->quantity, $this->total, $this->id);
    return $stmt->execute();

    }
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
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