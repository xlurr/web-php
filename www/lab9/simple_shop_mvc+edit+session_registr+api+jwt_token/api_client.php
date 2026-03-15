<?php
session_start();
//Если в $_SESSION['api_token'] есть токен, вызывается validateToken()

// Настройки API
$api_base_url = 'http://127.0.0.1/simple_shop_mvc+edit+session_registr+api+jwt_token';
$login_url = $api_base_url . '/index.php?page=api&action=login';
$search_url = $api_base_url . '/index.php?page=api&action=advanced_search';
$validate_url = $api_base_url . '/index.php?page=api&action=validate_token';

// Функция для выполнения API запросов с полной отладкой
function makeApiRequest($url, $method = 'GET', $data = null, $token = null) {
    $options = [
        'http' => [
            'method' => $method,
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ];
    //Собирает HTTP‑контекст (stream_context_create)
    
    // Добавляем заголовок авторизации если есть токен
    $headers = [];
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    
    // Добавляем заголовки для JSON
    if ($method === 'POST' && $data) {
        $headers[] = "Content-Type: application/json";
        $options['http']['content'] = json_encode($data);
    }
    
    if (!empty($headers)) {
        $options['http']['header'] = implode("\r\n", $headers) . "\r\n";
    }
    
    $context = stream_context_create($options);
    
    // Детальная отладка запроса
    error_log("API Request: $url");
    error_log("Method: $method");
    error_log("Token: " . ($token ? 'present' : 'absent'));
    error_log("Headers: " . print_r($headers, true));
    
    $response = @file_get_contents($url, false, $context);
    //получаем строку с содержимым ответа(JSON)
    //Символ @ перед функцией подавляет PHP‑warnings
    
    if ($response === false) {
        $error = error_get_last();
        $debug_info = [
            'url' => $url,
            'method' => $method,
            'error' => $error['message'] ?? 'Unknown error',
            'token_present' => !empty($token)
        ];
        error_log("API Connection Failed: " . print_r($debug_info, true));
        return ['success' => false, 'error' => 'Connection failed: ' . ($error['message'] ?? 'Unknown error'), 'debug' => $debug_info];
    }
    
    $decoded_response = json_decode($response, true);
    
    // Логируем ответ
    error_log("API Response: " . print_r($decoded_response, true));
    
    return $decoded_response;
}

// Функция для авторизации
function apiLogin($username, $password) {
    global $login_url;
    return makeApiRequest($login_url, 'POST', [
        'username' => $username,
        'password' => $password
    ]);
} //шлёт POST на action=login и возвращает JSON с token

// Функция для валидации токена
function validateToken($token) {
    global $validate_url;
    return makeApiRequest($validate_url, 'GET', null, $token);
}

// Функция для поиска товаров с JWT
function searchProductsWithAuth($token, $query, $minPrice = null, $maxPrice = null, $limit = 20, $page = 1, $sortBy = 'name', $sortOrder = 'asc') {
    global $search_url;
    
    $url = $search_url . "&q=" . urlencode($query);
    
    if ($minPrice !== null) $url .= "&min_price=" . floatval($minPrice);
    if ($maxPrice !== null) $url .= "&max_price=" . floatval($maxPrice);
    if ($limit) $url .= "&limit=" . intval($limit);
    if ($page > 1) $url .= "&page=" . intval($page);
    if ($sortBy) $url .= "&sort_by=" . urlencode($sortBy);
    if ($sortOrder) $url .= "&sort_order=" . urlencode($sortOrder);
    
    error_log("Search URL: " . $url);
    
    return makeApiRequest($url, 'GET', null, $token);
}

// Обработка авторизации
$token = null;
$user = null;
$login_error = '';

// Проверяем существующий токен
if (isset($_SESSION['api_token'])) {
    $validation = validateToken($_SESSION['api_token']);
    if ($validation['success']) {
        $token = $_SESSION['api_token'];
        $user = $_SESSION['api_user'];
    } else {
        unset($_SESSION['api_token']);
        unset($_SESSION['api_user']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $login_result = apiLogin($username, $password);
    
    if ($login_result && isset($login_result['success']) && $login_result['success'] && 
        isset($login_result['token']) && isset($login_result['user']) && is_array($login_result['user'])) {
        $token = $login_result['token'];
        $user = $login_result['user'];
        $_SESSION['api_token'] = $token;
        $_SESSION['api_user'] = $user;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = $login_result['error'] ?? 'Ошибка авторизации';
    }
}

// Обработка выхода
if (isset($_GET['logout'])) {
    unset($_SESSION['api_token']);
    unset($_SESSION['api_user']);
    $token = null;
    $user = null;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Обработка поискового запроса
$search_results = null;
$search_query = '';
$search_params = [];
$search_url_debug = '';

if ($token) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])) {
        $search_query = trim($_POST['search_query']);
        $min_price = isset($_POST['min_price']) ? floatval($_POST['min_price']) : null;
        $max_price = isset($_POST['max_price']) ? floatval($_POST['max_price']) : null;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 20;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'name';
        $sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'asc';
        
        if (!empty($search_query)) {
            // Сохраняем отладочную информацию URL
            $search_url_debug = $search_url . "&q=" . urlencode($search_query);
            if ($min_price !== null) $search_url_debug .= "&min_price=" . $min_price;
            if ($max_price !== null) $search_url_debug .= "&max_price=" . $max_price;
            
            $search_results = searchProductsWithAuth($token, $search_query, $min_price, $max_price, $limit, $page, $sort_by, $sort_order);
            $search_params = compact('search_query', 'min_price', 'max_price', 'limit', 'page', 'sort_by', 'sort_order');
            
            $_SESSION['last_search'] = $search_results;
            $_SESSION['last_search_params'] = $search_params;
            $_SESSION['last_search_url'] = $search_url_debug;
            
            header('Location: ' . $_SERVER['PHP_SELF'] . '?search=1');
            exit;
        }
    }
    
    if (isset($_GET['search']) || isset($_SESSION['last_search'])) {
        if (isset($_SESSION['last_search'])) {
            $search_results = $_SESSION['last_search'];
            $search_params = $_SESSION['last_search_params'];
            $search_query = $search_params['search_query'] ?? '';
            $search_url_debug = $_SESSION['last_search_url'] ?? '';
            
            unset($_SESSION['last_search']);
            unset($_SESSION['last_search_params']);
            unset($_SESSION['last_search_url']);
        }
    }
}

/**
 * Функция для получения всех заказов через API с JWT авторизацией
 */
function getOrdersWithAuth($token, $limit = 50, $page = 1) {
    global $api_base_url;
    $orders_url = $api_base_url . '/index.php?page=api&action=orders';
    
    if ($limit) $orders_url .= "&limit=" . intval($limit);
    if ($page > 1) $orders_url .= "&page=" . intval($page);
    
    error_log("Fetching orders from: " . $orders_url);
    
    return makeApiRequest($orders_url, 'GET', null, $token);
}

/**
 * Функция для получения заказа по ID через API с JWT авторизацией
 */
function getOrderWithAuth($token, $order_id) {
    global $api_base_url;
    $order_url = $api_base_url . '/index.php?page=api&action=order&id=' . intval($order_id);
    
    error_log("Fetching order from: " . $order_url);
    
    return makeApiRequest($order_url, 'GET', null, $token);
}

/**
 * Функция для создания заказа через API с JWT авторизацией
 * 
 * @param string $token JWT токен
 * @param int $product_id ID товара
 * @param int $quantity Количество
 * @param int|null $customer_id ID покупателя (только для админа)
 * @return array Результат создания заказа
 */
function createOrderWithAuth($token, $product_id, $quantity, $customer_id = null) {
    global $api_base_url;
    $create_order_url = $api_base_url . '/index.php?page=api&action=create_order';
    
    $data = [
        'product_id' => intval($product_id),
        'quantity' => intval($quantity)
    ];
    
    if ($customer_id !== null) {
        $data['customer_id'] = intval($customer_id);
    }
    
    error_log("Creating order with data: " . print_r($data, true));
    
    return makeApiRequest($create_order_url, 'POST', $data, $token);
}

/**
 * Функция для обновления заказа через API с JWT авторизацией
 * 
 * @param string $token JWT токен
 * @param int $order_id ID заказа
 * @param int|null $product_id ID товара (если нужно обновить)
 * @param int|null $quantity Количество (если нужно обновить)
 * @param int|null $customer_id ID покупателя (только для админа)
 * @return array Результат обновления
 */
function updateOrderWithAuth($token, $order_id, $product_id = null, $quantity = null, $customer_id = null) {
    global $api_base_url;
    $update_order_url = $api_base_url . '/index.php?page=api&action=update_order&id=' . intval($order_id);
    
    $data = [];
    
    if ($product_id !== null) {
        $data['product_id'] = intval($product_id);
    }
    
    if ($quantity !== null) {
        $data['quantity'] = intval($quantity);
    }
    
    if ($customer_id !== null) {
        $data['customer_id'] = intval($customer_id);
    }
    
    if (empty($data)) {
        return ['success' => false, 'error' => 'No data to update'];
    }
    
    error_log("Updating order " . $order_id . " with data: " . print_r($data, true));
    
    return makeApiRequest($update_order_url, 'POST', $data, $token);
}

/**
 * Функция для удаления заказа через API с JWT авторизацией
 * 
 * @param string $token JWT токен
 * @param int $order_id ID заказа
 * @return array Результат удаления
 */
function deleteOrderWithAuth($token, $order_id) {
    global $api_base_url;
    $delete_order_url = $api_base_url . '/index.php?page=api&action=delete_order&id=' . intval($order_id);
    
    error_log("Deleting order: " . $order_id);
    
    return makeApiRequest($delete_order_url, 'POST', ['_method' => 'DELETE'], $token);
}

// ===== ПРИМЕРЫ ИСПОЛЬЗОВАНИЯ В HTML ИНТЕРФЕЙСЕ =====

/**
 * Функция для отображения таблицы с заказами (если пользователь авторизован)
 */
function displayOrdersTable($orders) {
    if (!$orders || !isset($orders['orders'])) {
        echo '<div class="alert alert-warning">Заказы не найдены</div>';
        return;
    }
    
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-hover">';
    echo '<thead class="table-dark">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Покупатель</th>';
    echo '<th>Товар</th>';
    echo '<th>Цена</th>';
    echo '<th>Количество</th>';
    echo '<th>Сумма</th>';
    echo '<th>Дата заказа</th>';
    echo '<th>Действия</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($orders['orders'] as $order) {
        echo '<tr>';
        echo '<td>' . $order['id'] . '</td>';
        echo '<td>' . htmlspecialchars($order['customer_name']) . '</td>';
        echo '<td>' . htmlspecialchars($order['product_name']) . '</td>';
        echo '<td>' . number_format($order['product_price'], 2) . ' руб.</td>';
        echo '<td>' . $order['quantity'] . '</td>';
        echo '<td><strong>' . number_format($order['total'], 2) . ' руб.</strong></td>';
        echo '<td>' . $order['order_date'] . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-primary" onclick="editOrder(' . $order['id'] . ')">✏️</button>';
        echo '<button class="btn btn-sm btn-danger" onclick="deleteOrder(' . $order['id'] . ')">🗑️</button>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

/**
 * Функция для отображения формы создания заказа
 */
function displayCreateOrderForm() {
    echo '<div class="card">';
    echo '<div class="card-header bg-success text-white">';
    echo '<h5 class="mb-0">➕ Создать новый заказ</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<form id="createOrderForm">';
    echo '<div class="row">';
    echo '<div class="col-md-6">';
    echo '<div class="mb-3">';
    echo '<label class="form-label">Товар (ID)</label>';
    echo '<input type="number" class="form-control" name="product_id" id="product_id" required>';
    echo '</div>';
    echo '</div>';
    echo '<div class="col-md-6">';
    echo '<div class="mb-3">';
    echo '<label class="form-label">Количество</label>';
    echo '<input type="number" class="form-control" name="quantity" id="quantity" min="1" value="1" required>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<button type="submit" class="btn btn-success btn-lg w-100">Создать заказ</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
}


// Проверка прямого доступа к API
$direct_test_url = $search_url . "&q=телевизор&token=" . urlencode($token ?? '');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Client с JWT авторизацией - DEBUG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .debug-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .token-display { font-family: monospace; font-size: 0.8rem; word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 5px; }
        .url-test { background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="display-4 text-primary">
                    <i class="bi bi-bug"></i> API Client - DEBUG MODE
                </h1>
                <p class="lead text-muted">Полная отладка API запросов</p>
            </div>
        </div>

        <!-- Секция отладки -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="debug-section">
                    <h5><i class="bi bi-wrench"></i> Отладочная информация</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>API Base URL:</strong> <?php echo htmlspecialchars($api_base_url); ?><br>
                            <strong>Token Status:</strong> 
                            <span class="badge <?php echo $token ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $token ? 'Установлен' : 'Отсутствует'; ?>
                            </span><br>
                            <strong>Search Query:</strong> <?php echo htmlspecialchars($search_query); ?><br>
                            <strong>Request Method:</strong> <?php echo $_SERVER['REQUEST_METHOD']; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Search API URL:</strong><br>
                            <small><?php echo htmlspecialchars($search_url_debug ?: $search_url); ?></small><br><br>
                            <strong>Direct Test URL:</strong><br>
                            <small><a href="<?php echo htmlspecialchars($direct_test_url); ?>" target="_blank"><?php echo htmlspecialchars($direct_test_url); ?></a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$token): ?>
            <!-- Форма авторизации -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-center mb-4"><i class="bi bi-key"></i> Авторизация API</h4>
                            <?php if ($login_error): ?>
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <?php echo htmlspecialchars($login_error); ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Имя пользователя</label>
                                    <input type="text" class="form-control" name="username" value="admin" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Пароль</label>
                                    <input type="password" class="form-control" name="password" value="password" required>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-box-arrow-in-right"></i> Войти в API
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Информация о пользователе -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="bi bi-person-check"></i> Авторизован как: <?php echo htmlspecialchars($user['username']); ?></h5>
                            <p class="mb-1"><strong>Роль:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
                            <p class="mb-0"><strong>User ID:</strong> <?php echo $user['id']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="?logout=1" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Выйти
                    </a>
                </div>
            </div>

            <!-- Токен -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-key"></i> JWT Токен</h6>
                        </div>
                        <div class="card-body">
                            <div class="token-display"><?php echo htmlspecialchars($token); ?></div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Форма поиска -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-search"></i> Поиск товаров</h5>
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Поисковый запрос</label>
                                        <input type="text" class="form-control form-control-lg" 
                                               name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" 
                                               placeholder="Например: телевизор, телефон..." required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Мин. цена</label>
                                        <input type="number" class="form-control" name="min_price" 
                                               value="<?php echo isset($search_params['min_price']) ? $search_params['min_price'] : ''; ?>" 
                                               placeholder="0" min="0" step="0.01">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Макс. цена</label>
                                        <input type="number" class="form-control" name="max_price" 
                                               value="<?php echo isset($search_params['max_price']) ? $search_params['max_price'] : ''; ?>" 
                                               placeholder="100000" min="0" step="0.01">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="bi bi-search"></i> Найти товары
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Результаты поиска -->
            <?php if (isset($search_results)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-list-check"></i> Результаты поиска</h6>
                            </div>
                            <div class="card-body">
                                <!-- Детальная отладка ответа -->
                                <div class="debug-section">
                                    <h6>Детали ответа API:</h6>
                                    <pre><?php echo htmlspecialchars(print_r($search_results, true)); ?></pre>
                                </div>

                                <?php if ($search_results['success'] && isset($search_results['products']) && count($search_results['products']) > 0): ?>
                                    <div class="alert alert-success">
                                        <strong><?php echo $search_results['results_count']; ?></strong> товаров найдено
                                        <span class="badge bg-success ms-2">JWT Auth</span>
                                    </div>

                                    <div class="row g-4">
                                        <?php foreach ($search_results['products'] as $product): ?>
                                            <div class="col-md-6 col-lg-4 col-xl-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <?php if (isset($product['match_score'])): ?>
                                                            <span class="badge bg-success float-end">
                                                                <?php echo $product['match_score']; ?>%
                                                            </span>
                                                        <?php endif; ?>
                                                        <h6 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                        <div class="text-success fw-bold">
                                                            <?php echo number_format($product['price'], 2, '.', ' '); ?> руб.
                                                        </div>
                                                        <p class="card-text small text-muted">
                                                            <?php echo $product['description'] ? htmlspecialchars($product['description']) : '<em>Нет описания</em>'; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                <?php elseif ($search_results['success'] && empty($search_results['products'])): ?>
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> 
                                        Товары не найдены по запросу "<?php echo htmlspecialchars($search_query); ?>"
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <i class="bi bi-x-circle"></i> 
                                        <strong>Ошибка API:</strong> <?php echo $search_results['error'] ?? 'Unknown error'; ?>
                                        <?php if (isset($search_results['debug'])): ?>
                                            <br><small>Debug: <?php echo htmlspecialchars(print_r($search_results['debug'], true)); ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($token && empty($search_query)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> Введите поисковый запрос выше
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>