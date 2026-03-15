<?php
class Controller {
    protected $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require_once "views/{$view}.php";
    }
    
    protected function redirect($page) {
        header("Location: index.php?page={$page}");
        exit;
    }
}
?>