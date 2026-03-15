<?php
class Auth {
    private $db;
    private $userModel;
    
    public function __construct($database) {
        $this->db = $database;
        $this->userModel = new User($this->db);
    }
    
    public function login($username, $password) {
        $user = $this->userModel->login($username, $password);
        
        if ($user) {
            // Установка сессии
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['customer_id'] = $user['customer_id'];
            $_SESSION['loggedin'] = true;
            
            // Отладка
            error_log("Login successful: user_id=" . $user['id'] . ", role=" . $user['role']);
            
            return true;
        }
        
        error_log("Login failed for username: " . $username);
        return false;
    }
    
    public function logout() {
        session_destroy();
        $_SESSION = array();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }
    
    public function isAdmin() {
        return $this->isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    public function isUser() {
        return $this->isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'user';
    }
    
    // ID пользователя
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    // customer_id
    public function getCustomerId() {
        return $_SESSION['customer_id'] ?? null;
    }
    
    public function getRole() {
        return $_SESSION['role'] ?? null;
    }
    
    public function getUsername() {
        return $_SESSION['username'] ?? null;
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
    }
    
    public function requireAdmin() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            header('Location: index.php?page=access-denied');
            exit;
        }
    }
}
?>