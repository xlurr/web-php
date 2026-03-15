<?php
session_start();

// Загрузка конфигурации и моделей
require_once 'config/database.php';
require_once 'models/Database.php';
require_once 'models/Product.php';
require_once 'models/Customer.php';
require_once 'models/Order.php';
require_once 'models/User.php';
require_once 'models/Auth.php';

// Загрузка контроллеров
require_once 'controllers/Controller.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/ProductController.php';
require_once 'controllers/CustomerController.php';
require_once 'controllers/OrderController.php';
require_once 'controllers/AuthController.php';

// Инициализация БД и Auth
$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Определение страницы и действия
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Роутинг
switch ($page) {
    case 'home':
        $controller = new HomeController($db, $auth);
        $controller->index();
        break;

    case 'login':
        $controller = new AuthController($db, $auth);
        $controller->login();
        break;

    case 'register':
        $controller = new AuthController($db, $auth);
        $controller->register();
        break;

    case 'logout':
        $controller = new AuthController($db, $auth);
        $controller->logout();
        break;

    case 'access-denied':
        $controller = new AuthController($db, $auth);
        $controller->accessdenied();
        break;

    case 'products':
        // Проверка авторизации
        if (!$auth->isLoggedIn()) {
            $_SESSION['message'] = [
                'text' => 'Требуется авторизация!',
                'type' => 'warning'
            ];
            header('Location: index.php?page=login');
            exit;
        }
        // Проверка прав администратора
        if (!$auth->isAdmin()) {
            header('Location: index.php?page=access-denied');
            exit;
        }
        
        $controller = new ProductController($db, $auth);
        if ($action === 'edit' && method_exists($controller, 'edit')) {
            $controller->edit();
        } elseif ($action === 'show' && method_exists($controller, 'show')) {
            $controller->show();
        } elseif ($action === 'add-review' && method_exists($controller, 'addReview')) {
            $controller->addReview();
        } else {
            $controller->index();
        }
        break;

    case 'customers':
        // Проверка авторизации и прав администратора
        if (!$auth->isLoggedIn()) {
            $_SESSION['message'] = [
                'text' => 'Требуется авторизация!',
                'type' => 'warning'
            ];
            header('Location: index.php?page=login');
            exit;
        }
        if (!$auth->isAdmin()) {
            header('Location: index.php?page=access-denied');
            exit;
        }

        $controller = new CustomerController($db, $auth);
        if ($action === 'edit' && method_exists($controller, 'edit')) {
            $controller->edit();
        } else {
            $controller->index();
        }
        break;

    case 'orders':
    // Проверка авторизации
    if (!$auth->isLoggedIn()) {
        $_SESSION['message'] = [
            'text' => 'Требуется авторизация!',
            'type' => 'warning'
        ];
        header('Location: index.php?page=login');
        exit;
    }

    $controller = new OrderController($db, $auth);

    if ($action === 'create' && method_exists($controller, 'create')) {
        $controller->create();
    } elseif ($action === 'edit' && method_exists($controller, 'edit')) {
        if (!$auth->isAdmin()) {
            header('Location: index.php?page=access-denied');
            exit;
        }
        $controller->edit();
    } elseif ($action === 'delete' && method_exists($controller, 'delete')) {
        $controller->delete();
    } elseif ($action === 'my' && method_exists($controller, 'my')) {
        if ($auth->isAdmin()) {
            header('Location: index.php?page=access-denied');
            exit;
        }
        $controller->my();
    } else {
        if (!$auth->isAdmin()) {
            header('Location: index.php?page=access-denied');
            exit;
        }
        $controller->index();
    }
    break;


    default:
        $controller = new HomeController($db, $auth);
        $controller->index();
        break;
}
?>