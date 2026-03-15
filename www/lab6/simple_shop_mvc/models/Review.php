<?php
class Review {
    private $conn;
    private $table_name = "reviews";

    public $id;
    public $product_id;
    public $review_text;
    public $rating;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (product_id, review_text, rating) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isi", $this->product_id, $this->review_text, $this->rating);
        return $stmt->execute();
    }

    public function getByProductId($product_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
