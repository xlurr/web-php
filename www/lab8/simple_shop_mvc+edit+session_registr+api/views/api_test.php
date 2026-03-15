<?php include __DIR__ . '/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">API Тестирование - Заказы клиентов</h1>
        </div>
    </div>

    <!-- Левая колонка: Поиск по ID и Поиск по имени -->
    <div class="row">
        <!-- По ID клиента -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Заказы по ID клиента</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Получить всю информацию о клиенте и его заказы по ID</p>
                    
                    <form class="mb-3">
                        <div class="mb-3">
                            <label class="form-label">ID клиента:</label>
                            <input type="number" id="customer-id" class="form-control" value="1" min="1" placeholder="Введите ID клиента">
                        </div>
                        <button type="button" class="btn btn-primary w-100" onclick="getCustomerOrders()">
                            Получить заказы
                        </button>
                    </form>
                    
                    <div id="result-customer-id" class="alert alert-light" style="display:none; max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6;">
                        <h6 class="mb-2">Результат:</h6>
                        <pre id="json-customer-id" style="font-size: 11px; margin: 0;"></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Поиск по имени -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Поиск по имени клиента</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Поиск клиента по имени и вывод его заказов</p>
                    
                    <form class="mb-3">
                        <div class="mb-3">
                            <label class="form-label">Имя клиента:</label>
                            <input type="text" id="customer-name" class="form-control" placeholder="Введите имя" value="Иван">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Лимит результатов:</label>
                            <input type="number" id="customer-limit" class="form-control" value="10" min="1" max="50">
                        </div>
                        <button type="button" class="btn btn-success w-100" onclick="searchCustomerOrders()">
                            Поиск
                        </button>
                    </form>
                    
                    <div id="result-customer-name" class="alert alert-light" style="display:none; max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6;">
                        <h6 class="mb-2">Результат:</h6>
                        <pre id="json-customer-name" style="font-size: 11px; margin: 0;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Правая колонка: Документация и структура ответа -->
    <div class="row mt-5">
        <div class="col-lg-8">
            <!-- Полная документация -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Полная документация API</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Заказы по ID</h6>
                            <p><strong>Метод:</strong> GET</p>
                            <p><strong>Endpoint:</strong></p>
                            <code style="word-break: break-all;">?page=api&action=customer_orders&customer_id={id}</code>
                            
                            <p class="mt-3"><strong>Параметры:</strong></p>
                            <ul style="font-size: 12px;">
                                <li><code>customer_id</code> (число) - ID клиента</li>
                            </ul>
                            
                            <p class="mt-3"><strong>Ответ 200 OK:</strong></p>
                            <pre style="font-size: 11px; background: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto;">{
  "success": true,
  "customer": {...},
  "statistics": {
    "orders_count": 5,
    "total_spent": 15000.00,
    "average_order": 3000.00
  },
  "orders": [...]
}</pre>

                            <p class="mt-3"><strong>Коды ошибок:</strong></p>
                            <ul style="font-size: 12px;">
                                <li><strong>400</strong> - Параметр не передан</li>
                                <li><strong>404</strong> - Клиент не найден</li>
                                <li><strong>500</strong> - Ошибка сервера</li>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-success">Поиск по имени</h6>
                            <p><strong>Метод:</strong> GET</p>
                            <p><strong>Endpoint:</strong></p>
                            <code style="word-break: break-all;">?page=api&action=search_customer_orders&name={имя}&limit={лимит}</code>
                            
                            <p class="mt-3"><strong>Параметры:</strong></p>
                            <ul style="font-size: 12px;">
                                <li><code>name</code> (строка) - Имя клиента (мин. 2 символа)</li>
                                <li><code>limit</code> (число) - Максимум результатов (по умолчанию 10)</li>
                            </ul>
                            
                            <p class="mt-3"><strong>Ответ 200 OK:</strong></p>
                            <pre style="font-size: 11px; background: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto;">{
  "success": true,
  "search_query": "Иван",
  "customers_found": 2,
  "results": [
    {
      "customer": {...},
      "statistics": {...},
      "orders": [...]
    }
  ]
}</pre>

                            <p class="mt-3"><strong>Коды ошибок:</strong></p>
                            <ul style="font-size: 12px;">
                                <li><strong>400</strong> - Параметр не передан или менее 2 символов</li>
                                <li><strong>404</strong> - Клиенты не найдены</li>
                                <li><strong>500</strong> - Ошибка сервера</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая боковая панель: Структура ответа -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Структура данных в ответе</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <p><strong>Объект Customer:</strong></p>
                            <pre style="font-size: 11px; background: #f8f9fa; padding: 10px; border-radius: 4px;">{
  "id": 1,
  "name": "Иван",
  "email": "ivan@example.com",
  "phone": "+7...",
  "address": "ул..."
}</pre>
                        </div>
                        <div class="col-12 mb-3">
                            <p><strong>Объект Order:</strong></p>
                            <pre style="font-size: 11px; background: #f8f9fa; padding: 10px; border-radius: 4px;">{
  "id": 1,
  "product_id": 1,
  "product_name": "Товар",
  "price": 1000.00,
  "quantity": 2,
  "total": 2000.00,
  "order_date": "2024-01-15..."
}</pre>
                        </div>
                        <div class="col-12">
                            <p><strong>Объект Statistics:</strong></p>
                            <pre style="font-size: 11px; background: #f8f9fa; padding: 10px; border-radius: 4px;">{
  "orders_count": 5,
  "total_spent": 15000.00,
  "average_order": 3000.00
}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formatJson(obj) { //принимает JSON из API
    return JSON.stringify(obj, null, 2); //преобразует объект в читаемый JSON с отступами
}

function getCustomerOrders() {
    const customerId = document.getElementById('customer-id').value;
    
    if (!customerId) {
        alert('Введите ID клиента');
        return;
    }
    
    const url = `?page=api&action=customer_orders&customer_id=${customerId}`;
    //Берёт значение из <input id="customer-id">
    
    fetch(url) //отправляет GET-запрос в API
        .then(response => response.json()) //парсит HTTP-ответ в JavaScript-объект
        .then(data => {//получает готовый объект из API
            document.getElementById('json-customer-id').textContent = formatJson(data);
            document.getElementById('result-customer-id').style.display = 'block';
            document.getElementById('result-customer-id').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => {
            document.getElementById('json-customer-id').textContent = 'Ошибка: ' + error.message;
            document.getElementById('result-customer-id').style.display = 'block';
        });
}

function searchCustomerOrders() {
    const name = document.getElementById('customer-name').value;
    const limit = document.getElementById('customer-limit').value;
    
    if (!name || name.length < 2) {
        alert('Введите имя (минимум 2 символа)');
        return;
    }
    
    const url = `?page=api&action=search_customer_orders&name=${encodeURIComponent(name)}&limit=${limit}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            document.getElementById('json-customer-name').textContent = formatJson(data);
            document.getElementById('result-customer-name').style.display = 'block';
            document.getElementById('result-customer-name').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => {
            document.getElementById('json-customer-name').textContent = 'Ошибка: ' + error.message;
            document.getElementById('result-customer-name').style.display = 'block';
        });
}

// Загружаем результаты при открытии страницы
window.addEventListener('load', function() {
    getCustomerOrders();
    setTimeout(() => searchCustomerOrders(), 500);
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
