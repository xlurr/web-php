<?php

class ProductController extends Controller {
    private $productModel;
    private $reviewModel;
    public function __construct($database) {
        parent::__construct($database);
        $this->productModel = new Product($this->db);
        $this->reviewModel = new Review($this->db);
    }
    public function index() {
        $message = '';
        // Создание товара
        if ($_POST && isset($_POST['create'])) {
            $this->productModel->name = $_POST['name'];
            $this->productModel->price = $_POST['price'];
            $this->productModel->description = $_POST['description'];
            if ($this->productModel->create()) {
                $message = "Товар успешно создан!";
            } else {
                $message = "Ошибка при создании товара!";
            }
        }
        // Редактирование товара
        if ($_POST && isset($_POST['update'])) {
            $this->productModel->id = $_POST['id'];
            $this->productModel->name = $_POST['name'];
            $this->productModel->price = $_POST['price'];
            $this->productModel->description = $_POST['description'];
            if ($this->productModel->update()) {
                $message = "Товар успешно обновлен!";
                $this->redirect('products');
            } else {
                $message = "Ошибка при обновлении товара!";
            }
        }
        // Удаление товара
        if (isset($_GET['delete_id'])) {
            $this->productModel->id = $_GET['delete_id'];
            if ($this->productModel->delete()) {
                $message = "Товар успешно удален!";
            } else {
                $message = "Ошибка при удалении товара!";
            }
        }
        $products = $this->productModel->read();
        $this->view('products/index', [
            'products' => $products,
            'message' => $message
        ]);
    }

     // Страница с детальной информацией о товаре, где выводятся отзывы
    public function show() {
        // id товара из параметров
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Получаем информацию о товаре
        $product = $this->productModel->getById($id);
        if (!$product) {
            $this->redirect('products');
            return;
        }

        // Получаем отзывы для товара
        $reviews = $this->reviewModel->getByProductId($id);

        // Выводим представление с данным товаром и отзывами
        $this->view('products/show', [
            'product' => $product,
            'reviews' => $reviews,
            'message' => ''
        ]);
    }

    // Метод для обработки POST запроса добавления отзыва
    public function add_review() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reviewModel->product_id = $_POST['product_id'];
            $this->reviewModel->review_text = $_POST['review_text'];
            $this->reviewModel->rating = $_POST['rating'];

            if ($this->reviewModel->create()) {
                $message = "Отзыв успешно добавлен!";
            } else {
                $message = "Ошибка при добавлении отзыва.";
            }

            // После добавления отзыва, показываем страницу товара
            $this->show();
        }
    }
    public function edit() {
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
