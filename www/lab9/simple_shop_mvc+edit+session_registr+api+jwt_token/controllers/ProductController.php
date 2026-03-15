<?php
class ProductController extends Controller {
    private $productModel;
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
        $this->productModel = new Product($this->db);
    }
    
    public function index() {
        $this->auth->requireAdmin();
        
        $message = $this->getMessage();
        
        // Создание товара
        if ($_POST && isset($_POST['create'])) {
            $this->productModel->name = $_POST['name'];
            $this->productModel->price = $_POST['price'];
            $this->productModel->description = $_POST['description'];
            
            if ($this->productModel->create()) {
                $this->setMessage("Товар успешно создан!", "success");
                $this->redirect('products');
            } else {
                $message = ["text" => "Ошибка при создании товара!", "type" => "danger"];
            }
        }
        
        // Редактирование товара
        if ($_POST && isset($_POST['update'])) {
            $this->productModel->id = $_POST['id'];
            $this->productModel->name = $_POST['name'];
            $this->productModel->price = $_POST['price'];
            $this->productModel->description = $_POST['description'];
            
            if ($this->productModel->update()) {
                $this->setMessage("Товар успешно обновлен!", "success");
                $this->redirect('products');
            } else {
                $message = ["text" => "Ошибка при обновлении товара!", "type" => "danger"];
            }
        }
        
        // Удаление товара
        if (isset($_GET['delete_id'])) {
            $this->productModel->id = $_GET['delete_id'];
            if ($this->productModel->delete()) {
                $this->setMessage("Товар успешно удален!", "success");
                $this->redirect('products');
            } else {
                $message = ["text" => "Ошибка при удалении товара!", "type" => "danger"];
            }
        }
        
        $products = $this->productModel->read();
        
        $this->view('products/index', [
            'products' => $products,
            'message' => $message
        ]);
    }
    
    public function edit() {
        $this->auth->requireAdmin();
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->redirect('products');
        }
        
        $this->view('products/edit', [
            'product' => $product
        ]);
    }
}
?>