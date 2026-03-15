<?php
class Auth {
    private $db;
    private $userModel;

    public function __construct($database) {
        $this->db = $database;
        $this->userModel = new User($this->db);
        session_start();
    }

    // Вход пользователя
    public function login($username, $password) {
        $user = $this->userModel->login($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['customer_id'] = $user['customer_id'];
            $_SESSION['logged_in'] = true;

            return true;
        }

        return false;
    }

    // Выход пользователя
    public function logout() {
        session_destroy();
        $_SESSION = array();
    }

    // Проверка авторизации
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Проверка роли администратора
    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }

    // Проверка роли пользователя
    public function isUser() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'user';
    }

    // Получить ID текущего пользователя
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    // Получить customer_id текущего пользователя
    public function getCustomerId() {
        return $_SESSION['customer_id'] ?? null;
    }

    // Получить роль текущего пользователя
    public function getRole() {
        return $_SESSION['role'] ?? null;
    }

    // Получить имя пользователя
    public function getUsername() {
        return $_SESSION['username'] ?? null;
    }

    // Редирект если не авторизован
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    // Редирект если не админ
    public function requireAdmin() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            header('Location: index.php?page=access_denied');
            exit;
        }
    }
}
?>