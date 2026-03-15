<?php
class HomeController extends Controller {
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
    }
    
    public function index() {
        // Показываем товары всем: гостю, user и admin
        $productModel = new Product($this->db);
        $products = $productModel->read();
        
        $this->view('home/index', [
            'products' => $products
        ]);
    }
}
?>