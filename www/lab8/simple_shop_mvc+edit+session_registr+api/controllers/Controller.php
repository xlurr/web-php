<?php
class Controller {
    protected $db;
    protected $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    protected function view($view, $data = []) {
        // Всегда передаем auth в представления
        $data['auth'] = $this->auth;
        extract($data);
        
        // Начинаем буферизацию вывода
        ob_start();
        include "views/{$view}.php";
        $content = ob_get_clean();
        
        // Выводим результат
        echo $content;
    }
    
    protected function redirect($page) {
        header("Location: index.php?page={$page}");
        exit;
    }
    
    protected function setMessage($text, $type = 'info') {
        $_SESSION['message'] = $text;
        $_SESSION['message_type'] = $type;
    }
    
    protected function getMessage() {
        if (isset($_SESSION['message'])) {
            $message = [
                'text' => $_SESSION['message'],
                'type' => $_SESSION['message_type'] ?? 'info'
            ];
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            return $message;
        }
        return null;
    }
}
?>