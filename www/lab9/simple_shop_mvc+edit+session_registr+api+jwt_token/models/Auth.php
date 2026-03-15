<?php
require_once 'jwt/JWT.php';

class Auth {
    private $db;
    private $userModel;

    public function __construct($database) {
        $this->db = $database;
        $this->userModel = new User($this->db);
        
        // Не стартуем сессию для API
        if (session_status() == PHP_SESSION_NONE && !$this->isApiRequest()) {
            session_start();
        }
    }
    
    private function isApiRequest() {
        return isset($_SERVER['HTTP_AUTHORIZATION']) || 
               (isset($_GET['page']) && $_GET['page'] === 'api');
    }

    // Вход пользователя (для веб-интерфейса)
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
    
    // Генерация JWT токена
    public function generateToken($user_id, $username, $role) {
        $payload = [
            'user_id' => $user_id,
            'username' => $username,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24 часа
        ];
        
        return JWT::encode($payload);
    }
    
    // Валидация JWT токена
    public function validateToken($token) {
        try {
            $payload = JWT::decode($token);
            return $payload;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // API аутентификация
    public function apiLogin($username, $password) {
        $user = $this->userModel->login($username, $password);
        
        if ($user) {
            return [
                'success' => true,
                'token' => $this->generateToken($user['id'], $user['username'], $user['role']),
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'customer_id' => $user['customer_id']
                ]
            ];
        }
        
        return ['success' => false, 'error' => 'Invalid credentials'];
    }
    
    // Получение пользователя из токена
    public function getUserFromToken() {
        $token = $this->getBearerToken();
        if (!$token) {
            return false;
        }
        
        return $this->validateToken($token);
    }
    
    // Извлечение токена из заголовка
    private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        
        // Также проверяем GET параметр (для удобства тестирования)
        return isset($_GET['token']) ? $_GET['token'] : null;
    }
    
    private function getAuthorizationHeader() {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }
        
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                return $headers['Authorization'];
            }
        }
        
        return null;
    }

    // Выход пользователя
    public function logout() {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
            $_SESSION = array();
        }
    }

    // Проверка авторизации
    public function isLoggedIn() {
        if ($this->isApiRequest()) {
            return (bool)$this->getUserFromToken();
        }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Проверка роли администратора
    public function isAdmin() {
        if ($this->isApiRequest()) {
            $user = $this->getUserFromToken();
            return $user && $user['role'] === 'admin';
        }
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }

    // Проверка роли пользователя
    public function isUser() {
        if ($this->isApiRequest()) {
            $user = $this->getUserFromToken();
            return $user && $user['role'] === 'user';
        }
        return $this->isLoggedIn() && $_SESSION['role'] === 'user';
    }

    // Получить ID текущего пользователя
    public function getUserId() {
        if ($this->isApiRequest()) {
            $user = $this->getUserFromToken();
            return $user ? $user['user_id'] : null;
        }
        return $_SESSION['user_id'] ?? null;
    }

    // Получить customer_id текущего пользователя
    public function getCustomerId() {
        if ($this->isApiRequest()) {
            $user = $this->getUserFromToken();
            return $user ? $user['customer_id'] : null;
        }
        return $_SESSION['customer_id'] ?? null;
    }

    // Получить роль текущего пользователя
    public function getRole() {
        if ($this->isApiRequest()) {
            $user = $this->getUserFromToken();
            return $user ? $user['role'] : null;
        }
        return $_SESSION['role'] ?? null;
    }

    // Получить имя пользователя
    public function getUsername() {
        if ($this->isApiRequest()) {
            $user = $this->getUserFromToken();
            return $user ? $user['username'] : null;
        }
        return $_SESSION['username'] ?? null;
    }

    // Редирект если не авторизован
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            if ($this->isApiRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Authentication required']);
                exit;
            } else {
                header('Location: index.php?page=login');
                exit;
            }
        }
    }

    // Редирект если не админ
    public function requireAdmin() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            if ($this->isApiRequest()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            } else {
                header('Location: index.php?page=access_denied');
                exit;
            }
        }
    }
}
?>