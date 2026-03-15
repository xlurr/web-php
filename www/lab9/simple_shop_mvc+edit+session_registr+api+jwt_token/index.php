<?php
// Загрузка конфигурации
require_once 'config/database.php';

// Загрузка моделей
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
require_once 'controllers/ApiController.php';


// Инициализация базы данных и аутентификации
$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Определение страницы и действия
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

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
        
    case 'register':  // Добавляем регистрацию
        $controller = new AuthController($db, $auth);
        $controller->register();
        break;
        
    case 'logout':
        $controller = new AuthController($db, $auth);
        $controller->logout();
        break;
        
     case 'access_denied':
        $controller = new AuthController($db, $auth);
        $controller->access_denied();
        break;
        
    case 'products':
        $controller = new ProductController($db, $auth);
        if ($action === 'edit' && method_exists($controller, 'edit')) {
            $controller->edit();
        } else {
          $controller->index();
        }
        break;
        
    case 'customers':
        $controller = new CustomerController($db, $auth);
        if ($action === 'edit' && method_exists($controller, 'edit')) {
            $controller->edit();
        } else {
            $controller->index();
        }
        break;
        
    case 'orders':
        $controller = new OrderController($db, $auth);
        if ($action === 'edit' && method_exists($controller, 'edit')) {
            $controller->edit();
        } elseif ($action === 'my') {
            $controller->my();
        } else {
            $controller->index();
        }
        break;
        case 'api':
        $controller = new ApiController($db, $auth);
        if ($action === 'login') {
            $controller->login();
        } elseif ($action === 'validate_token') {
            $controller->validateToken();
        } elseif ($action === 'price') {
            $controller->getPrice();
        } elseif ($action === 'search') {
            $controller->searchProducts();
        } elseif ($action === 'advanced_search') {
            $controller->advancedSearch();
        } elseif ($action === 'products' && isset($_GET['id'])) {
            $controller->getProductById();
        } elseif ($action === 'products') {
            $controller->getAllProducts();
        } elseif ($action === 'orders') {
            $controller->getOrders();
        } elseif ($action === 'order' && isset($_GET['id'])) {
            $controller->getOrderById();
        } elseif ($action === 'create_order') {
            $controller->createOrderApi();
        } elseif ($action === 'update_order') {
            $controller->updateOrderApi();
        } elseif ($action === 'delete_order') {
            $controller->deleteOrderApi();
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'API endpoint not found']);
        }
        break;
    }      
?>
