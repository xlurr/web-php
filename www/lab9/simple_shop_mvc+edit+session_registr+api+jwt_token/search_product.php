<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск товаров - Магазин</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .product-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .match-score {
            font-size: 0.8rem;
        }
        .price-tag {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .search-form {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
        }
        .loader {
            display: none;
        }
        .fade-in {
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Заголовок -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="display-4 text-primary">
                    <i class="bi bi-search"></i> Поиск товаров
                </h1>
                <p class="lead text-muted">Найдите нужный товар по названию или описанию</p>
            </div>
        </div>

        <!-- Форма поиска -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <div class="card search-form text-white">
                    <div class="card-body p-4">
                        <form id="searchForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="searchQuery" class="form-label">
                                        <i class="bi bi-search"></i> Поисковый запрос
                                    </label>
                                    <input type="text" class="form-control form-control-lg" 
                                           id="searchQuery" placeholder="Например: телевизор, телефон..." 
                                           required>
                                    <div class="form-text text-white-50">
                                        Введите часть названия или описания товара
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="minPrice" class="form-label">
                                        <i class="bi bi-currency-dollar"></i> Мин. цена
                                    </label>
                                    <input type="number" class="form-control" 
                                           id="minPrice" placeholder="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label for="maxPrice" class="form-label">
                                        <i class="bi bi-currency-dollar"></i> Макс. цена
                                    </label>
                                    <input type="number" class="form-control" 
                                           id="maxPrice" placeholder="100000" min="0" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label for="sortBy" class="form-label">
                                        <i class="bi bi-sort-down"></i> Сортировка
                                    </label>
                                    <select class="form-select" id="sortBy">
                                        <option value="name">По названию</option>
                                        <option value="price">По цене</option>
                                        <option value="created_at">По дате</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="sortOrder" class="form-label">
                                        <i class="bi bi-arrow-up-down"></i> Порядок
                                    </label>
                                    <select class="form-select" id="sortOrder">
                                        <option value="asc">По возрастанию</option>
                                        <option value="desc">По убыванию</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="resultsLimit" class="form-label">
                                        <i class="bi bi-list-ol"></i> Результатов на странице
                                    </label>
                                    <select class="form-select" id="resultsLimit">
                                        <option value="10">10</option>
                                        <option value="20" selected>20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-warning btn-lg w-100">
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
        <div class="row">
            <div class="col-12">
                <!-- Статистика -->
                <div id="searchStats" class="alert alert-info d-none">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <strong id="resultsCount">0</strong> товаров найдено
                        </div>
                        <div class="col-md-6 text-md-end">
                            Запрос: "<span id="currentQuery"></span>"
                        </div>
                    </div>
                </div>

                <!-- Лоадер -->
                <div id="loader" class="loader text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-3 text-muted">Ищем товары...</p>
                </div>

                <!-- Сообщение об ошибке -->
                <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span id="errorMessage"></span>
                </div>

                <!-- Результаты -->
                <div id="resultsContainer" class="row g-4">
                    <!-- Товары будут загружены здесь -->
                </div>

                <!-- Пагинация -->
                <nav id="pagination" class="mt-5 d-none" aria-label="Навигация по страницам">
                    <ul class="pagination justify-content-center">
                        <!-- Кнопки пагинации будут сгенерированы здесь -->
                    </ul>
                </nav>

                <!-- Сообщение "нет результатов" -->
                <div id="noResults" class="text-center py-5 d-none">
                    <div class="text-muted">
                        <i class="bi bi-inbox" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Товары не найдены</h4>
                        <p>Попробуйте изменить параметры поиска</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        class ProductSearch {
            constructor() {
                this.currentPage = 1;
                this.totalPages = 1;
                this.apiBaseUrl = 'http://127.0.0.1/simple_shop_mvc+edit+session_registr+api/index.php';
                this.init();
            }

            init() {
                document.getElementById('searchForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.searchProducts();
                });

                // Загружаем популярные товары при загрузке страницы
                this.loadPopularProducts();
            }

            async searchProducts(page = 1) {
                this.currentPage = page;
                
                // Показываем лоадер
                this.showLoader();
                this.hideResults();
                this.hideError();

                // Получаем параметры поиска
                const params = this.getSearchParams();

                try {
                    const apiUrl = `http://127.0.0.1/simple_shop_mvc+edit+session_registr+api/index.php?page=api&action=advanced_search&${params}`;
                   console.log('API Request:', apiUrl);
                
                    const response = await fetch(apiUrl);
                 if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Ошибка сервера');
                    }

                    if (data.success) {
                        this.displayResults(data);
                    } else {
                        throw new Error(data.error || 'Неизвестная ошибка');
                    }

                } catch (error) {
                    this.showError(error.message);
                } finally {
                    this.hideLoader();
                }
            }

            getSearchParams() {
                const query = document.getElementById('searchQuery').value;
                const minPrice = document.getElementById('minPrice').value;
                const maxPrice = document.getElementById('maxPrice').value;
                const sortBy = document.getElementById('sortBy').value;
                const sortOrder = document.getElementById('sortOrder').value;
                const limit = document.getElementById('resultsLimit').value;

                let params = `q=${encodeURIComponent(query)}`;
                
                if (minPrice) params += `&min_price=${minPrice}`;
                if (maxPrice) params += `&max_price=${maxPrice}`;
                if (sortBy) params += `&sort_by=${sortBy}`;
                if (sortOrder) params += `&sort_order=${sortOrder}`;
                if (limit) params += `&limit=${limit}`;
                if (this.currentPage > 1) params += `&page=${this.currentPage}`;

                return params;
            }

            displayResults(data) {
                const resultsContainer = document.getElementById('resultsContainer');
                const searchStats = document.getElementById('searchStats');
                const resultsCount = document.getElementById('resultsCount');
                const currentQuery = document.getElementById('currentQuery');
                const pagination = document.getElementById('pagination');
                const noResults = document.getElementById('noResults');

                // Обновляем статистику
                resultsCount.textContent = data.total_count || data.results_count || 0;
                currentQuery.textContent = data.search_query;
                searchStats.classList.remove('d-none');

                // Очищаем контейнер
                resultsContainer.innerHTML = '';

                if (data.products && data.products.length > 0) {
                    // Отображаем товары
                    data.products.forEach(product => {
                        const productCard = this.createProductCard(product);
                        resultsContainer.appendChild(productCard);
                    });

                    // Показываем пагинацию если есть
                    if (data.pagination && data.pagination.total_pages > 1) {
                        this.displayPagination(data.pagination);
                        pagination.classList.remove('d-none');
                    } else {
                        pagination.classList.add('d-none');
                    }

                    noResults.classList.add('d-none');
                    resultsContainer.classList.remove('d-none');

                } else {
                    // Нет результатов
                    resultsContainer.classList.add('d-none');
                    pagination.classList.add('d-none');
                    noResults.classList.remove('d-none');
                }
            }

            createProductCard(product) {
                const col = document.createElement('div');
                col.className = 'col-md-6 col-lg-4 col-xl-3 fade-in';

                const card = document.createElement('div');
                card.className = 'card product-card h-100';

                // Форматируем цену
                const formattedPrice = new Intl.NumberFormat('ru-RU', {
                    style: 'currency',
                    currency: 'RUB',
                    minimumFractionDigits: 0
                }).format(product.price);

                // Создаем рейтинг релевантности
                const matchScore = product.match_score ? 
                    `<div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-success match-score">
                            <i class="bi bi-star-fill"></i> ${product.match_score}%
                        </span>
                    </div>` : '';

                card.innerHTML = `
                    <div class="card-body d-flex flex-column">
                        ${matchScore}
                        <h5 class="card-title text-primary">${this.escapeHtml(product.name)}</h5>
                        <div class="price-tag text-success mb-2">${formattedPrice}</div>
                        ${product.description ? 
                            `<p class="card-text flex-grow-1 text-muted">${this.escapeHtml(product.description)}</p>` : 
                            '<p class="card-text flex-grow-1 text-muted"><em>Описание отсутствует</em></p>'
                        }
                        <div class="mt-auto">
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> 
                                ${product.created_at ? new Date(product.created_at).toLocaleDateString('ru-RU') : 'Не указано'}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="productSearch.viewProduct(${product.id})">
                                <i class="bi bi-eye"></i> Подробнее
                            </button>
                        </div>
                    </div>
                `;

                col.appendChild(card);
                return col;
            }

            displayPagination(pagination) {
                const paginationContainer = document.querySelector('#pagination .pagination');
                paginationContainer.innerHTML = '';

                const totalPages = pagination.total_pages;
                const currentPage = pagination.page;

                // Кнопка "Назад"
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `
                    <a class="page-link" href="#" onclick="productSearch.searchProducts(${currentPage - 1})">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                `;
                paginationContainer.appendChild(prevLi);

                // Номера страниц
                for (let i = 1; i <= totalPages; i++) {
                    const pageLi = document.createElement('li');
                    pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    pageLi.innerHTML = `
                        <a class="page-link" href="#" onclick="productSearch.searchProducts(${i})">${i}</a>
                    `;
                    paginationContainer.appendChild(pageLi);
                }

                // Кнопка "Вперед"
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `
                    <a class="page-link" href="#" onclick="productSearch.searchProducts(${currentPage + 1})">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                `;
                paginationContainer.appendChild(nextLi);
            }

            async loadPopularProducts() {
                try {
                    const response = await fetch('http://127.0.0.1/simple_shop_mvc+edit+session_registr+api/index.php?page=api&action=products&limit=8');
                    const data = await response.json();

                    if (data.success && data.products.length > 0) {
                        this.displayPopularProducts(data.products);
                    }
                } catch (error) {
                    console.error('Ошибка загрузки популярных товаров:', error);
                }
            }

            displayPopularProducts(products) {
                const resultsContainer = document.getElementById('resultsContainer');
                resultsContainer.innerHTML = '';

                const titleRow = document.createElement('div');
                titleRow.className = 'col-12';
                titleRow.innerHTML = `
                    <h4 class="text-center text-muted mb-4">
                        <i class="bi bi-star"></i> Популярные товары
                    </h4>
                `;
                resultsContainer.appendChild(titleRow);

                products.forEach(product => {
                    const productCard = this.createProductCard(product);
                    resultsContainer.appendChild(productCard);
                });
            }

            async viewProduct(productId) {
                try {
                    const response = await fetch(`http://127.0.0.1/simple_shop_mvc+edit+session_registr+api/index.php?page=api&action=products&id=${productId}`);
                    const data = await response.json();

                    if (data.success) {
                        this.showProductModal(data.product);
                    } else {
                        alert('Товар не найден');
                    }
                } catch (error) {
                    alert('Ошибка загрузки товара');
                }
            }

            showProductModal(product) {
                const formattedPrice = new Intl.NumberFormat('ru-RU', {
                    style: 'currency',
                    currency: 'RUB',
                    minimumFractionDigits: 0
                }).format(product.price);

                const modalHtml = `
                    <div class="modal fade" id="productModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">${this.escapeHtml(product.name)}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h4 class="text-success">${formattedPrice}</h4>
                                            ${product.description ? 
                                                `<p class="lead">${this.escapeHtml(product.description)}</p>` : 
                                                '<p class="text-muted"><em>Описание отсутствует</em></p>'
                                            }
                                            <div class="mt-4">
                                                <h6>Информация о товаре:</h6>
                                                <ul class="list-unstyled">
                                                    <li><strong>ID:</strong> ${product.id}</li>
                                                    ${product.created_at ? 
                                                        `<li><strong>Добавлен:</strong> ${new Date(product.created_at).toLocaleDateString('ru-RU')}</li>` : 
                                                        ''
                                                    }
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Удаляем старый модал если есть
                const oldModal = document.getElementById('productModal');
                if (oldModal) {
                    oldModal.remove();
                }

                // Добавляем новый модал
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // Показываем модал
                const modal = new bootstrap.Modal(document.getElementById('productModal'));
                modal.show();
            }

            showLoader() {
                document.getElementById('loader').style.display = 'block';
            }

            hideLoader() {
                document.getElementById('loader').style.display = 'none';
            }

            showError(message) {
                const errorAlert = document.getElementById('errorAlert');
                const errorMessage = document.getElementById('errorMessage');
                
                errorMessage.textContent = message;
                errorAlert.classList.remove('d-none');
            }

            hideError() {
                document.getElementById('errorAlert').classList.add('d-none');
            }

            hideResults() {
                document.getElementById('resultsContainer').classList.add('d-none');
                document.getElementById('pagination').classList.add('d-none');
                document.getElementById('noResults').classList.add('d-none');
                document.getElementById('searchStats').classList.add('d-none');
            }

            escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        }

        // Инициализация при загрузке страницы
        let productSearch;
        document.addEventListener('DOMContentLoaded', function() {
            productSearch = new ProductSearch();
        });
    </script>
</body>
</html>