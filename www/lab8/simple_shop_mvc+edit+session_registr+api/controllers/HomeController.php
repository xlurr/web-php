<?php
class HomeController extends Controller {
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
    }
    
    public function index() {
        // Если пользователь авторизован, перенаправляем на заказы
        if ($this->auth->isLoggedIn()) {
            $this->redirect('orders');
        }
        
        // Показываем приветственную страницу
        $this->showWelcomePage();
    }
    
    private function showWelcomePage() {
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Магазин - Добро пожаловать</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { padding: 20px; background-color: #f8f9fa; }
                .welcome-container { max-width: 600px; margin: 100px auto; text-align: center; }
                .btn-group { margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="welcome-container">
                <h1>🛍️ Добро пожаловать в магазин</h1>
                <p class="lead">Для работы с системой требуется авторизация</p>
                
                <div class="btn-group">
                    <a href="index.php?page=login" class="btn btn-primary btn-lg">🔐 Войти</a>
                    <a href="index.php?page=register" class="btn btn-success btn-lg">📝 Зарегистрироваться</a>
                </div>
            
                <div class="mt-5">
                    <h5>Тестовые пользователи:</h5>
                    <div class="card mt-3">
                        <div class="card-body">
                            <p class="mb-1"><strong>Администратор:</strong> admin / password</p>
                            <p class="mb-0"><strong>Пользователь:</strong> user1 / password</p>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
?>