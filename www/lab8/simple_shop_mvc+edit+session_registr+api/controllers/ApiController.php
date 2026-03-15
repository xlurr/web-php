<?php
class ApiController extends Controller {
    private $productModel;
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
        $this->productModel = new Product($this->db);
    }
    
    /**
     * API для получения цены товара по названию (точное совпадение)
     */
    public function getPrice() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_GET['name']) || empty(trim($_GET['name']))) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Параметр "name" обязателен'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $productName = trim($_GET['name']);
        
        try {
            // Ищем товар по точному или частичному названию
            $exactMatch = $this->findProductByName($productName, true);
            
            if ($exactMatch) {
                echo json_encode([
                    'success' => true,
                    'match_type' => 'exact',
                    'product' => [
                        'id' => (int)$exactMatch['id'],
                        'name' => $exactMatch['name'],
                        'price' => (float)$exactMatch['price'],
                        'description' => $exactMatch['description']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Товар "' . htmlspecialchars($productName) . '" не найден'
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Внутренняя ошибка сервера'
            ], JSON_UNESCAPED_UNICODE);
            error_log("API Error: " . $e->getMessage());
        }
    }
    
    /**
     * API для поиска товаров по части названия
     */
    public function searchProducts() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Параметр "query" обязателен для поиска'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $searchQuery = trim($_GET['query']);
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        try {
            $products = $this->searchProductsByName($searchQuery, $limit);
            
            if (count($products) > 0) {
                echo json_encode([
                    'success' => true,
                    'search_query' => $searchQuery,
                    'results_count' => count($products),
                    'products' => $products
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'search_query' => $searchQuery,
                    'error' => 'По запросу "' . htmlspecialchars($searchQuery) . '" товары не найдены',
                    'suggestions' => $this->getSearchSuggestions($searchQuery)
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Внутренняя ошибка сервера'
            ], JSON_UNESCAPED_UNICODE);
            error_log("API Error: " . $e->getMessage());
        }
    }
    
    /**
     * Расширенный API поиска с различными опциями
     */
    public function advancedSearch() {
        header('Content-Type: application/json; charset=utf-8');
        
        $searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
        $minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
        $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
        
        if (empty($searchQuery)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Параметр "q" (query) обязателен для поиска'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        try {
            $result = $this->advancedProductSearch($searchQuery, $minPrice, $maxPrice, $limit, $page, $sortBy, $sortOrder);
            
            echo json_encode([
                'success' => true,
                'search_query' => $searchQuery,
                'filters' => [
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice
                ],
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total_results' => $result['total_count'],
                    'total_pages' => $result['total_pages']
                ],
                'results_count' => count($result['products']),
                'products' => $result['products']
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Внутренняя ошибка сервера'
            ], JSON_UNESCAPED_UNICODE);
            error_log("API Error: " . $e->getMessage());
        }
    }
    
    /**
     * Поиск товаров по части названия
     */
    private function searchProductsByName($query, $limit = 10) {
        $searchQuery = "%" . $query . "%";
        
        $sql = "SELECT id, name, price, description, created_at 
                FROM products 
                WHERE name LIKE ? 
                OR description LIKE ?
                ORDER BY 
                    CASE 
                        WHEN name LIKE ? THEN 1  -- Точное совпадение в начале
                        WHEN name LIKE ? THEN 2  -- Совпадение в начале слова
                        ELSE 3                  -- Простое совпадение
                    END,
                    name ASC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $this->db->error);
        }
        
        $exactStart = $query . "%";
        $stmt->bind_param("ssssi", $searchQuery, $searchQuery, $exactStart, $exactStart, $limit);
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
     * Расширенный поиск товаров
     */
    
    private function advancedProductSearch($query, $minPrice = null, $maxPrice = null, $limit = 20, $page = 1, $sortBy = 'name', $sortOrder = 'asc') {
        $searchQuery = "%" . $query . "%";
        
        // Строим базовый запрос
        $sql = "SELECT id, name, price, description, created_at 
                FROM products 
                WHERE (name LIKE ? OR description LIKE ?)";
        
        $params = [$searchQuery, $searchQuery];
        
        // Добавляем условия по цене
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
        
        // Получаем все товары (без пагинации для простоты)
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $this->db->error);
        }
        
        // Динамическое связывание параметров
        $types = str_repeat('s', 2) . str_repeat('d', ($minPrice ? 1 : 0) + ($maxPrice ? 1 : 0));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $allProducts = [];
        while ($product = $result->fetch_assoc()) {
            $allProducts[] = [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'description' => $product['description'],
                'created_at' => $product['created_at'],
                'match_score' => $this->calculateMatchScore($product['name'], $query)
            ];
        }
        
        // Применяем пагинацию в PHP
        $totalCount = count($allProducts);
        $offset = max(0, ($page - 1)) * $limit;
        $paginatedProducts = array_slice($allProducts, $offset, $limit);
        
        return [
            'products' => $paginatedProducts,
            'total_count' => $totalCount,
            'total_pages' => $limit > 0 ? ceil($totalCount / $limit) : 1
        ];
    }
    /**
     * Поиск товара по точному названию
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
                      ORDER BY 
                          CASE 
                              WHEN LOWER(name) = LOWER(?) THEN 1
                              ELSE 2
                          END
                      LIMIT 1";
            $searchName = "%" . $name . "%";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ss", $searchName, $name);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Расчет релевантности совпадения
     */
    private function calculateMatchScore($productName, $searchQuery) {
        $productNameLower = mb_strtolower($productName);
        $searchQueryLower = mb_strtolower($searchQuery);
        
        // Точное совпадение
        if ($productNameLower === $searchQueryLower) {
            return 100;
        }
        
        // Совпадение в начале названия
        if (mb_strpos($productNameLower, $searchQueryLower) === 0) {
            return 80;
        }
        
        // Простое совпадение
        if (mb_strpos($productNameLower, $searchQueryLower) !== false) {
            return 60;
        }
        
        // Совпадение отдельных слов
        $searchWords = preg_split('/\s+/', $searchQueryLower);
        $productWords = preg_split('/\s+/', $productNameLower);
        
        $matchedWords = 0;
        foreach ($searchWords as $word) {
            foreach ($productWords as $pWord) {
                if (mb_strpos($pWord, $word) !== false) {
                    $matchedWords++;
                    break;
                }
            }
        }
        
        if ($matchedWords > 0) {
            return ($matchedWords / count($searchWords)) * 40;
        }
        
        return 0;
    }
    
    /**
     * Получение подсказок для поиска
     */
    private function getSearchSuggestions($query) {
        $suggestions = [];
        $popularProducts = $this->getPopularProducts(5);
        
        foreach ($popularProducts as $product) {
            if (stripos($product['name'], $query) !== false) {
                $suggestions[] = $product['name'];
            }
        }
        
        return array_slice($suggestions, 0, 3);
    }
    
    /**
     * Получение популярных товаров
     */
    private function getPopularProducts($limit = 5) {
        $query = "SELECT name FROM products ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
      
    /**
     * API для получения всех товаров
     */
    public function getAllProducts() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $products = $this->productModel->read();
            $productsList = [];
            
            while ($product = $products->fetch_assoc()) {
                $productsList[] = [
                    'id' => (int)$product['id'],
                    'name' => $product['name'],
                    'price' => (float)$product['price'],
                    'description' => $product['description'],
                    'created_at' => $product['created_at']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'products' => $productsList,
                'count' => count($productsList)
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Внутренняя ошибка сервера'
            ], JSON_UNESCAPED_UNICODE);
            error_log("API Error: " . $e->getMessage());
        }
    }
    
    /**
     * API для получения товара по ID
     */
    public function getProductById() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Параметр "id" обязателен и должен быть числом'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $productId = (int)$_GET['id'];
        
        try {
            $product = $this->productModel->getById($productId);
            
            if ($product) {
                echo json_encode([
                    'success' => true,
                    'product' => [
                        'id' => (int)$product['id'],
                        'name' => $product['name'],
                        'price' => (float)$product['price'],
                        'description' => $product['description'],
                        'created_at' => $product['created_at']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Товар с ID ' . $productId . ' не найден'
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Внутренняя ошибка сервера'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

        /**
     * API: Получить заказы клиента по ID
     * GET: ?page=api&action=orders&customer_id=1
     */
    public function getCustomerOrders() {
        header('Content-Type: application/json; charset=utf-8');
        //говорит браузеру, что ответ будет в формате JSON в UTF‑8
        $customerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : null;
        //забирает customer_id из строки запроса и приводит к целому числ
        if (!$customerId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Параметр "customer_id" обязателен'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        try {
            $query = "SELECT id, name, email, phone, address FROM customers WHERE id = ?";
            $stmt = $this->db->prepare($query);
            //создаётся подготовленный запрос
            $stmt->bind_param("i", $customerId);
            //подставляет ID клиента
            $stmt->execute();
            // выполняет запрос
            $customerResult = $stmt->get_result();
            
            if ($customerResult->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Клиент с ID ' . $customerId . ' не найден'], JSON_UNESCAPED_UNICODE);
                return;
            }
            
            $customer = $customerResult->fetch_assoc(); //забирает одну строку
            
            $ordersQuery = "SELECT o.id, o.product_id, o.quantity, o.total, o.order_date, p.name as product_name, p.price
                            FROM orders o JOIN products p ON o.product_id = p.id
                            WHERE o.customer_id = ? ORDER BY o.order_date DESC";
            
            $ordersStmt = $this->db->prepare($ordersQuery);
            $ordersStmt->bind_param("i", $customerId);
            $ordersStmt->execute();
            $ordersResult = $ordersStmt->get_result();//результат выборки
            
            $orders = [];
            $totalSpent = 0;
            
            while ($order = $ordersResult->fetch_assoc()) {
                $orders[] = [
                    'id' => (int)$order['id'],
                    'product_id' => (int)$order['product_id'],
                    'product_name' => $order['product_name'],
                    'price' => (float)$order['price'],
                    'quantity' => (int)$order['quantity'],
                    'total' => (float)$order['total'],
                    'order_date' => $order['order_date']
                ];
                $totalSpent += $order['total'];
            }
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'customer' => [
                    'id' => (int)$customer['id'],
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'phone' => $customer['phone'],
                    'address' => $customer['address']
                ],
                'statistics' => [
                    'orders_count' => count($orders),
                    'total_spent' => round($totalSpent, 2),
                    'average_order' => count($orders) > 0 ? round($totalSpent / count($orders), 2) : 0
                ],
                'orders' => $orders
            ], JSON_UNESCAPED_UNICODE); //кодирует всё в JSON и отправляет клиенту
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Внутренняя ошибка сервера'], JSON_UNESCAPED_UNICODE);
            error_log("API Error: " . $e->getMessage());
        }
    }

    public function searchCustomerOrders() {
        header('Content-Type: application/json; charset=utf-8');
        
        $customerName = isset($_GET['name']) ? trim($_GET['name']) : null;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        
        if (!$customerName || strlen($customerName) < 2) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Параметр "name" обязателен (минимум 2 символа)'], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        try {
            $searchTerm = "%{$customerName}%";
            //Собирается шаблон для LIKE
            $query = "SELECT id, name, email, phone, address FROM customers WHERE name LIKE ? ORDER BY name ASC LIMIT ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $searchTerm, $limit);
            //подставляется имя
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['success' => false, 'search_query' => $customerName, 'error' => 'Клиенты не найдены'], JSON_UNESCAPED_UNICODE);
                return;
            }
            
            $customersWithOrders = [];
            
            while ($customer = $result->fetch_assoc()) {
                $ordersQuery = "SELECT o.id, o.product_id, o.quantity, o.total, o.order_date, p.name as product_name, p.price
                                FROM orders o JOIN products p ON o.product_id = p.id
                                WHERE o.customer_id = ? ORDER BY o.order_date DESC";
                
                $ordersStmt = $this->db->prepare($ordersQuery);
                $customerId = $customer['id'];
                $ordersStmt->bind_param("i", $customerId);
                $ordersStmt->execute();
                $ordersResult = $ordersStmt->get_result();
                
                $orders = [];
                $totalSpent = 0;
                
                while ($order = $ordersResult->fetch_assoc()) {
                    $orders[] = [
                        'id' => (int)$order['id'],
                        'product_id' => (int)$order['product_id'],
                        'product_name' => $order['product_name'],
                        'price' => (float)$order['price'],
                        'quantity' => (int)$order['quantity'],
                        'total' => (float)$order['total'],
                        'order_date' => $order['order_date']
                    ];
                    $totalSpent += $order['total'];
                }
                
                $customersWithOrders[] = [
                    'customer' => [
                        'id' => (int)$customer['id'],
                        'name' => $customer['name'],
                        'email' => $customer['email'],
                        'phone' => $customer['phone'],
                        'address' => $customer['address']
                    ],
                    'statistics' => [
                        'orders_count' => count($orders),
                        'total_spent' => round($totalSpent, 2),
                        'average_order' => count($orders) > 0 ? round($totalSpent / count($orders), 2) : 0
                    ],
                    'orders' => $orders
                ];
            }
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'search_query' => $customerName,
                'customers_found' => count($customersWithOrders),
                'results' => $customersWithOrders
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Внутренняя ошибка сервера'], JSON_UNESCAPED_UNICODE);
            error_log("API Error: " . $e->getMessage());
        }
    }
}
?>