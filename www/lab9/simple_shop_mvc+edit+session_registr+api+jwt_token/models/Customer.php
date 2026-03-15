<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $result = $this->conn->query($query);
        return $result;
    }
    // Создание нового покупателя
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=?, email=?, phone=?, address=?, created_at=NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Ошибка подготовки запроса: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("ssss", 
            $data['name'], 
            $data['email'], 
            $data['phone'], 
            $data['address']
        );

        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return [
                'id' => $this->id,
                'name' => $data['name'],
                'email' => $data['email']
            ];
        } else {
            error_log("Ошибка выполнения запроса: " . $stmt->error);
            return false;
        }
    }

    // Получить покупателя по ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Обновить покупателя
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=?, email=?, phone=?, address=?
                  WHERE id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", 
            $data['name'], 
            $data['email'], 
            $data['phone'], 
            $data['address'],
            $id
        );
        
        return $stmt->execute();
    }

    // Удалить покупателя
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Получить всех покупателей
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Проверить существование покупателя по email
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
?>