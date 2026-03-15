<?php
class Controller {
    protected $db;
    protected $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    protected function view($view, $data = []) {
        // Получаем абсолютный путь к views папке
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        // Проверяем, что файл существует
        if (!file_exists($viewPath)) {
            die("View файл не найден: " . $view);
        }
        
        // Делаем переменные доступными в view
        extract($data);
        
        // Подключаем view файл
        include $viewPath;
    }
    
    protected function redirect($page) {
        header('Location: index.php?page=' . $page);
        exit;
    }
    
    protected function setMessage($text, $type = 'info') {
        $_SESSION['message'] = [
            'text' => $text,
            'type' => $type
        ];
    }
    
    protected function getMessage() {
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
            unset($_SESSION['messagetype']);
            return $message;
        }
        return null;
    }
}
?>