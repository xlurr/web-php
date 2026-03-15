<?php
class ApiController extends Controller {
    private $productModel;
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
        $this->productModel = new Product($this->db);
    }
    
    /**
     * API аутентификации - получение JWT токена
     */
    public function login() {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        //Читает «сырое» тело HTTP‑запроса (php://input), ожидая там JSON, и декодирует его в ассоциативный массив $input
        if (!isset($input['username']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Username and password are required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $username = trim($input['username']);
        $password = $input['password'];
        
        try {
            $result = $this->auth->apiLogin($username, $password);
            //метод проверит пользователя, сгенерирует JWT
            
            if ($result['success']) {
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
                //возвращает весь массив $result как JSON
            } else {
                http_response_code(401);
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Internal server error'], JSON_UNESCAPED_UNICODE);
        }
        echo $_SESSION['api_token'];
    }
    
    
    /**
     * Валидация токена
     */
    public function validateToken() {
        header('Content-Type: application/json; charset=utf-8');
        
        $user = $this->auth->getUserFromToken();
        
        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Invalid or expired token'], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Расширенный API поиска
     */
    public function advancedSearch() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
        $minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
        $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
        
        if (empty($searchQuery)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Параметр "q" обязателен'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        try {
            $result = $this->advancedProductSearch($searchQuery, $minPrice, $maxPrice, $limit, $page, $sortBy, $sortOrder);
            
            echo json_encode([
                'success' => true,
                'user' => $this->auth->getUsername(),
                'search_query' => $searchQuery,
                'results_count' => count($result['products']),
                'total_count' => $result['total_count'],
                'products' => $result['products']
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Внутренняя ошибка сервера'], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Простой поиск товаров
     */
    public function searchProducts() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Параметр "query" обязателен'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $searchQuery = trim($_GET['query']);
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        try {
            $products = $this->searchProductsByName($searchQuery, $limit);
            
            echo json_encode([
                'success' => true,
                'user' => $this->auth->getUsername(),
                'search_query' => $searchQuery,
                'results_count' => count($products),
                'products' => $products
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Внутренняя ошибка сервера'], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Получение цены товара по названию
     */
    public function getPrice() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!isset($_GET['name']) || empty(trim($_GET['name']))) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Параметр "name" обязателен'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $productName = trim($_GET['name']);
        
        try {
            $product = $this->findProductByName($productName, true);
            
            if ($product) {
                echo json_encode([
                    'success' => true,
                    'user' => $this->auth->getUsername(),
                    'product' => [
                        'id' => (int)$product['id'],
                        'name' => $product['name'],
                        'price' => (float)$product['price'],
                        'description' => $product['description']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Товар не найден'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Внутренняя ошибка сервера'], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Получение всех товаров
     */
    public function getAllProducts() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        try {
            $products = $this->productModel->read();
            $productsList = [];
            
            while ($product = $products->fetch_assoc()) {
                $productsList[] = [
                    'id' => (int)$product['id'],
                    'name' => $product['name'],
                    'price' => (float)$product['price'],
                    'description' => $product['description']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'user' => $this->auth->getUsername(),
                'products' => $productsList,
                'count' => count($productsList)
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Внутренняя ошибка сервера'], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Получение товара по ID
     */
    public function getProductById() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Параметр "id" обязателен'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $productId = (int)$_GET['id'];
        
        try {
            $product = $this->productModel->getById($productId);
            
            if ($product) {
                echo json_encode([
                    'success' => true,
                    'user' => $this->auth->getUsername(),
                    'product' => [
                        'id' => (int)$product['id'],
                        'name' => $product['name'],
                        'price' => (float)$product['price'],
                        'description' => $product['description']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Товар не найден'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Внутренняя ошибка сервера'], JSON_UNESCAPED_UNICODE);
        }
    }
    
    // ========== ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ ==========
    
    /**
     * Расширенный поиск товаров
     */
    private function advancedProductSearch($query, $minPrice = null, $maxPrice = null, $limit = 20, $page = 1, $sortBy = 'name', $sortOrder = 'asc') {
        $searchQuery = "%" . $query . "%";
        
        $sql = "SELECT id, name, price, description, created_at 
                FROM products 
                WHERE (name LIKE ? OR description LIKE ?)";
        
        $params = [$searchQuery, $searchQuery];
        
        if ($minPrice !== null && $minPrice > 0) {
            $sql .= " AND price >= ?";
            $params[] = $minPrice;
        }
        
        if ($maxPrice !== null && $maxPrice > 0) {
            $sql .= " AND price <= ?";
            $params[] = $maxPrice;
        }
        
        // Сортировка
        $allowedSort = ['name' => 'name', 'price' => 'price', 'created_at' => 'created_at'];
        $sortField = $allowedSort[$sortBy] ?? 'name';
        $sortDir = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $sortField $sortDir";
        
        // Лимит и пагинация
        if ($limit > 0) {
            $offset = max(0, ($page - 1)) * $limit;
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $this->db->error);
        }
        
        // Динамическое связывание параметров
        $types = str_repeat('s', 2);
        if ($minPrice !== null && $minPrice > 0) $types .= 'd';
        if ($maxPrice !== null && $maxPrice > 0) $types .= 'd';
        if ($limit > 0) $types .= 'ii';
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($product = $result->fetch_assoc()) {
            $products[] = [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'description' => $product['description'],
                'match_score' => $this->calculateMatchScore($product['name'], $query)
            ];
        }
        
        // Общее количество
        $countSql = "SELECT COUNT(*) as total FROM products WHERE (name LIKE ? OR description LIKE ?)";
        $countParams = [$searchQuery, $searchQuery];
        
        if ($minPrice !== null && $minPrice > 0) {
            $countSql .= " AND price >= ?";
            $countParams[] = $minPrice;
        }
        
        if ($maxPrice !== null && $maxPrice > 0) {
            $countSql .= " AND price <= ?";
            $countParams[] = $maxPrice;
        }
        
        $countStmt = $this->db->prepare($countSql);
        $countTypes = str_repeat('s', 2);
        if ($minPrice !== null && $minPrice > 0) $countTypes .= 'd';
        if ($maxPrice !== null && $maxPrice > 0) $countTypes .= 'd';
        
        $countStmt->bind_param($countTypes, ...$countParams);
        $countStmt->execute();
        $totalCount = $countStmt->get_result()->fetch_assoc()['total'];
        
        return [
            'products' => $products,
            'total_count' => (int)$totalCount
        ];
    }
    
    /**
     * Поиск товаров по части названия
     */
    private function searchProductsByName($query, $limit = 10) {
        $searchQuery = "%" . $query . "%";
        
        $sql = "SELECT id, name, price, description 
                FROM products 
                WHERE name LIKE ? OR description LIKE ?
                ORDER BY name ASC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $this->db->error);
        }
        
        $stmt->bind_param("ssi", $searchQuery, $searchQuery, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($product = $result->fetch_assoc()) {
            $products[] = [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'description' => $product['description'],
                'match_score' => $this->calculateMatchScore($product['name'], $query)
            ];
        }
        
        return $products;
    }
    
    /**
     * Поиск товара по названию
     */
    private function findProductByName($name, $exactMatch = false) {
        if ($exactMatch) {
            $query = "SELECT id, name, price, description 
                      FROM products 
                      WHERE LOWER(name) = LOWER(?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $name);
        } else {
            $query = "SELECT id, name, price, description 
                      FROM products 
                      WHERE LOWER(name) LIKE LOWER(?) 
                      LIMIT 1";
            $searchName = "%" . $name . "%";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $searchName);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }
    
    /**
     * Расчет релевантности совпадения
     */
    private function calculateMatchScore($productName, $searchQuery) {
        $productNameLower = mb_strtolower($productName);
        $searchQueryLower = mb_strtolower($searchQuery);
        
        if ($productNameLower === $searchQueryLower) return 100;
        if (mb_strpos($productNameLower, $searchQueryLower) === 0) return 80;
        if (mb_strpos($productNameLower, $searchQueryLower) !== false) return 60;
        
        return 0;
    }
    public function getOrders() {
        header('Content-Type: application/json; charset=utf-8');
        
        // Проверяем авторизацию
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        try {
            $orderModel = new Order($this->db);
            $orders = $orderModel->read($this->auth);
            
            $ordersList = [];
            while ($order = $orders->fetch_assoc()) {
                $ordersList[] = [
                    'id' => (int)$order['id'],
                    'customer_id' => (int)$order['customer_id'],
                    'customer_name' => $order['customer_name'],
                    'product_id' => (int)$order['product_id'],
                    'product_name' => $order['product_name'],
                    'product_price' => (float)$order['product_price'],
                    'quantity' => (int)$order['quantity'],
                    'total' => (float)$order['total'],
                    'order_date' => $order['order_date']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'user' => $this->auth->getUsername(),
                'role' => $this->auth->getRole(),
                'orders' => $ordersList,
                'count' => count($ordersList)
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Internal server error'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API получение заказа по ID (с JWT авторизацией)
     */
    public function getOrderById() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Parameter "id" is required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $orderId = (int)$_GET['id'];
        
        try {
            $orderModel = new Order($this->db);
            $order = $orderModel->getById($this->auth, $orderId);
            
            if ($order) {
                echo json_encode([
                    'success' => true,
                    'user' => $this->auth->getUsername(),
                    'order' => [
                        'id' => (int)$order['id'],
                        'customer_id' => (int)$order['customer_id'],
                        'product_id' => (int)$order['product_id'],
                        'quantity' => (int)$order['quantity'],
                        'total' => (float)$order['total'],
                        'order_date' => $order['order_date']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Order not found or access denied'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Internal server error'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API создание нового заказа (с JWT авторизацией)
     * Методы: POST, PUT
     */
    public function createOrderApi() {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Проверяем обязательные параметры
        if (!isset($input['product_id']) || !isset($input['quantity'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Parameters product_id and quantity are required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        try {
            $orderModel = new Order($this->db);
            $orderModel->product_id = (int)$input['product_id'];
            $orderModel->quantity = (int)$input['quantity'];
            
            // Для пользователей customer_id должен совпадать с их профилем
            if (!$this->auth->isAdmin()) {
                $orderModel->customer_id = $this->auth->getCustomerId();
            } else {
                // Админ может создавать заказы для других покупателей
                if (isset($input['customer_id'])) {
                    $orderModel->customer_id = (int)$input['customer_id'];
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Admin must specify customer_id'], JSON_UNESCAPED_UNICODE);
                    return;
                }
            }
            
            if ($orderModel->create($this->auth)) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'order' => [
                        'product_id' => $orderModel->product_id,
                        'customer_id' => $orderModel->customer_id,
                        'quantity' => $orderModel->quantity,
                        'total' => $orderModel->total
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Failed to create order'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API обновление заказа (с JWT авторизацией)
     * Метод: PUT
     */
    public function updateOrderApi() {
        header('Content-Type: application/json; charset=utf-8');
        
        // Получаем метод запроса (PHP не поддерживает PUT напрямую в $_REQUEST)
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        if ($method !== 'PUT' && $method !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!isset($_GET['id']) && !isset($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Parameter "id" is required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $orderId = (int)($_GET['id'] ?? $_POST['id']);
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        try {
            $orderModel = new Order($this->db);
            
            // Получаем текущий заказ
            $currentOrder = $orderModel->getById($this->auth, $orderId);
            if (!$currentOrder) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Order not found'], JSON_UNESCAPED_UNICODE);
                return;
            }
            
            // Устанавливаем параметры для обновления
            $orderModel->product_id = isset($input['product_id']) ? (int)$input['product_id'] : $currentOrder['product_id'];
            $orderModel->quantity = isset($input['quantity']) ? (int)$input['quantity'] : $currentOrder['quantity'];
            $orderModel->customer_id = isset($input['customer_id']) && $this->auth->isAdmin() ? (int)$input['customer_id'] : $currentOrder['customer_id'];
            
            if ($orderModel->update($this->auth, $orderId)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Order updated successfully'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Failed to update order'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API удаление заказа (с JWT авторизацией)
     * Метод: DELETE
     */
    public function deleteOrderApi() {
        header('Content-Type: application/json; charset=utf-8');
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        if ($method !== 'DELETE' && $method !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (!isset($_GET['id']) && !isset($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Parameter "id" is required'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $orderId = (int)($_GET['id'] ?? $_POST['id']);
        
        try {
            $orderModel = new Order($this->db);
            
            if ($orderModel->delete($this->auth, $orderId)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Order deleted successfully'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Failed to delete order or access denied'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>