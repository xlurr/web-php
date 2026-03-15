<?php
class Client {
    private $conn;
    private $table_name = 'customers';
    public function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'myshop');
        $this->conn->set_charset('utf8');
    }
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $result = $this->conn->query($query);
        return $result;
    }
}
?>
