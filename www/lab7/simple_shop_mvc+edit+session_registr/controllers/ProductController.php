<?php
class ProductController extends Controller {
    private $productModel;

    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
        $this->productModel = new Product($this->db);
    }
    
    public function index() {
        $message = null;
        
        // ТОЛЬКО АДМИНИСТРАТОР может создавать/редактировать/удалять
        if ($this->auth->isLoggedIn() && $this->auth->isAdmin()) {
            // Создание товара
            if ($_POST && isset($_POST['create'])) {
                $this->productModel->name = $_POST['name'] ?? '';
                $this->productModel->price = $_POST['price'] ?? 0;
                $this->productModel->description = $_POST['description'] ?? '';
                
                if ($this->productModel->create()) {
                    $this->setMessage('Товар успешно создан!', 'success');
                    $this->redirect('products');
                } else {
                    $message = [
                        'text' => 'Ошибка при создании товара',
                        'type' => 'danger'
                    ];
                }
            }
            
            // Обновление товара
            if ($_POST && isset($_POST['update'])) {
                $this->productModel->id = $_POST['id'] ?? 0;
                $this->productModel->name = $_POST['name'] ?? '';
                $this->productModel->price = $_POST['price'] ?? 0;
                $this->productModel->description = $_POST['description'] ?? '';
                
                if ($this->productModel->update()) {
                    $this->setMessage('Товар успешно обновлен!', 'success');
                    $this->redirect('products');
                } else {
                    $message = [
                        'text' => 'Ошибка при обновлении товара',
                        'type' => 'danger'
                    ];
                }
            }
            
            if (isset($_GET['delete']) && !empty($_GET['delete'])) {
                $this->productModel->id = intval($_GET['delete']);
                
                if ($this->productModel->delete()) {
                    $this->setMessage('Товар удален!', 'success');
                    $this->redirect('products');
                } else {
                    $message = [
                        'text' => 'Ошибка при удалении товара',
                        'type' => 'danger'
                    ];
                }
            }
        }
        
        $products = $this->productModel->read();
        
        // Покажи для админа полный список с кнопками
        if ($this->auth->isLoggedIn() && $this->auth->isAdmin()) {
            $this->view('products/index', [
                'products' => $products,
                'message' => $message
            ]);
        } else {
            // Для гостей и обычных пользователей - таблица без кнопок
            $this->view('products/guest-list', [
                'products' => $products
            ]);
        }
    }
    
    
    public function edit() {
        if (!$this->auth->isLoggedIn() || !$this->auth->isAdmin()) {
            $this->redirect('access-denied');
        }
        
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