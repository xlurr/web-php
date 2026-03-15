<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $role;
    public $customer_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Регистрация нового пользователя
    public function register($username, $email, $password, $customer_id = null) {
        // Проверяем, существует ли пользователь
        if ($this->userExists($username, $email)) {
            return "user_exists";
        }

        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "invalid_email";
        }

        // Валидация пароля (минимум 6 символов)
        if (strlen($password) < 6) {
            return "weak_password";
        }

        // Хешируем пароль
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Если customer_id не указан, создаем нового покупателя
        if ($customer_id === null) {
            $customer_id = $this->createCustomerForUser($username, $email);
            if (!$customer_id) {
                return "customer_creation_failed";
            }
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  SET username=?, email=?, password_hash=?, customer_id=?, role='user'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $username, $email, $password_hash, $customer_id);

        if ($stmt->execute()) {
            return true;
        } else {
            // Если не удалось создать пользователя, удаляем созданного покупателя
            $this->deleteCustomer($customer_id);
            return "database_error";
        }
    }

    // Создание покупателя для нового пользователя
    private function createCustomerForUser($username, $email) {
        $customer = new Customer($this->conn);
        
        // Генерируем имя для покупателя на основе имени пользователя
        $customer_name = $this->generateCustomerName($username);
        
        // Создаем покупателя
        $customer_data = [
            'name' => $customer_name,
            'email' => $email,
            'phone' => '',
            'address' => ''
        ];
        
        $result = $customer->create($customer_data);
        
        if ($result && isset($result['id'])) {
            return $result['id'];
        }
        
        return false;
    }

    // Генерация имени для покупателя
    private function generateCustomerName($username) {
        // Преобразуем username в читаемое имя
        $name = ucfirst(strtolower($username));
        return "Покупатель " . $name;
    }

    // Удаление покупателя (в случае ошибки)
    private function deleteCustomer($customer_id) {
        $query = "DELETE FROM customers WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
    }

    // Авторизация пользователя
    public function login($username, $password) {
        $query = "SELECT id, username, email, password_hash, role, customer_id 
                  FROM " . $this->table_name . " 
                  WHERE username = ? OR email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                return $user;
            }
        }

        return false;
    }

    // Проверка существования пользователя
    private function userExists($username, $email) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE username = ? OR email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    // Получить пользователя по ID
    public function getById($id) {
        $query = "SELECT u.*, c.name as customer_name, c.email as customer_email, 
                         c.phone as customer_phone, c.address as customer_address 
                  FROM " . $this->table_name . " u
                  LEFT JOIN customers c ON u.customer_id = c.id
                  WHERE u.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Получить всех пользователей (для админа)
    public function getAll() {
        $query = "SELECT u.*, c.name as customer_name, c.email as customer_email
                  FROM " . $this->table_name . " u
                  LEFT JOIN customers c ON u.customer_id = c.id
                  ORDER BY u.created_at DESC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Обновить роль пользователя
    public function updateRole($user_id, $role) {
        $query = "UPDATE " . $this->table_name . " SET role = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $role, $user_id);
        return $stmt->execute();
    }

    // Удалить пользователя
    public function delete($user_id) {
        // Сначала получаем customer_id
        $user = $this->getById($user_id);
        $customer_id = $user['customer_id'];
        
        // Удаляем пользователя
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        
        // Если пользователь удален и у него был customer_id, удаляем покупателя
        if ($result && $customer_id) {
            $this->deleteCustomer($customer_id);
        }
        
        return $result;
    }

    // Получить всех покупателей для привязки к пользователю
    public function getAvailableCustomers() {
        $query = "SELECT id, name, email FROM customers 
                  WHERE id NOT IN (SELECT customer_id FROM users WHERE customer_id IS NOT NULL)
                  ORDER BY name";
        $result = $this->conn->query($query);
        return $result;
    }
}
?>