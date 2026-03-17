#!/bin/bash

echo "🚀 Начинаем применение исправлений..."

# 1. Обновление docker-compose.yml
cat << 'EOF' > docker-compose.yml
version: '3.9'

services:
  apache-php:
    build: .
    container_name: apache-php
    volumes:
      - ./www:/var/www/html
    ports:
      - "8080:80"
    depends_on:
      - db

  db:
    image: mysql:8.0
    container_name: mysql
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: db
      MYSQL_USER: phpuser
      MYSQL_PASSWORD: phppass
    volumes:
      - db_data:/var/lib/mysql

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: phpmyadmin
    ports:
      - "8081:80"
    environment:
      PMA_HOST: db
      PMA_USER: root
      PMA_PASSWORD: root
    depends_on:
      - db

volumes:
  db_data:
EOF
echo "✅ docker-compose.yml обновлен"

# 2. Создание Dockerfile
cat << 'EOF' > Dockerfile
FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql && a2enmod rewrite
EOF
echo "✅ Dockerfile создан"

# 3. Создание www/index.php
cat << 'EOF' > www/index.php
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторные работы по Web-программированию</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 2rem; padding-bottom: 2rem; }
        .lab-card { transition: transform 0.2s; margin-bottom: 1.5rem; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .lab-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .card-title { color: #0d6efd; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-5">Лабораторные работы</h1>
        <div class="row">
            <!-- Lab 1 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 1</h5>
                        <h6 class="card-subtitle mb-3 text-muted">Основы PHP и работа с формами</h6>
                        <p class="card-text flex-grow-1">Изучение базового синтаксиса PHP, обработка данных из HTML-форм, чтение и запись данных в текстовые файлы, а также простой парсинг веб-страниц (cURL + DOMDocument).</p>
                        <a href="lab1/www/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 1</a>
                    </div>
                </div>
            </div>
            <!-- Lab 2 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 2</h5>
                        <h6 class="card-subtitle mb-3 text-muted">Шаблонизация и маршрутизация</h6>
                        <p class="card-text flex-grow-1">Разделение логики и представления. Использование include/require для сборки страниц из компонентов (header, footer, menu). Реализация простой пагинации.</p>
                        <a href="lab2/anipat/www/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 2</a>
                    </div>
                </div>
            </div>
            <!-- Lab 3 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 3</h5>
                        <h6 class="card-subtitle mb-3 text-muted">Работа с файлами и загрузка</h6>
                        <p class="card-text flex-grow-1">Организация загрузки файлов на сервер. Валидация типов и размеров файлов (изображения, документы). Создание простого фотоальбома и файлового архива.</p>
                        <a href="lab3/anipat/php/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 3</a>
                    </div>
                </div>
            </div>
            <!-- Lab 4 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 4</h5>
                        <h6 class="card-subtitle mb-3 text-muted">ООП в PHP</h6>
                        <p class="card-text flex-grow-1">Применение принципов ООП в PHP. Создание классов, наследование, интерфейсы. Использование автозагрузчика классов (autoloader) для структурирования проекта.</p>
                        <a href="lab4/anipat-rewrite/php/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 4</a>
                    </div>
                </div>
            </div>
            <!-- Lab 5 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 5</h5>
                        <h6 class="card-subtitle mb-3 text-muted">Работа с базами данных (MySQL)</h6>
                        <p class="card-text flex-grow-1">Подключение к MySQL через mysqli. Реализация CRUD-операций для сущностей "Студенты" и "Факультеты". Использование подготовленных запросов.</p>
                        <a href="lab5/students-facultets/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 5</a>
                    </div>
                </div>
            </div>
            <!-- Lab 6 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 6</h5>
                        <h6 class="card-subtitle mb-3 text-muted">Паттерн MVC</h6>
                        <p class="card-text flex-grow-1">Построение архитектуры приложения по шаблону MVC на примере простого интернет-магазина. Разделение ответственности между моделями, контроллерами и представлениями.</p>
                        <a href="lab6/simple_shop_mvc/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 6</a>
                    </div>
                </div>
            </div>
            <!-- Lab 7 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 7</h5>
                        <h6 class="card-subtitle mb-3 text-muted">Сессии, куки и авторизация</h6>
                        <p class="card-text flex-grow-1">Реализация системы регистрации и авторизации пользователей. Разграничение прав доступа (администратор/пользователь) с использованием механизма сессий PHP.</p>
                        <a href="lab7/simple_shop_mvc+edit+session_registr/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 7</a>
                    </div>
                </div>
            </div>
            <!-- Lab 8 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 8</h5>
                        <h6 class="card-subtitle mb-3 text-muted">Создание REST API</h6>
                        <p class="card-text flex-grow-1">Разработка собственного RESTful API для интернет-магазина. Обработка GET и POST запросов, возврат данных в формате JSON. Реализация клиентской части.</p>
                        <a href="lab8/simple_shop_mvc+edit+session_registr+api/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 8</a>
                    </div>
                </div>
            </div>
            <!-- Lab 9 -->
            <div class="col-md-4">
                <div class="card lab-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Лабораторная работа 9</h5>
                        <h6 class="card-subtitle mb-3 text-muted">Безопасность API (JWT токены)</h6>
                        <p class="card-text flex-grow-1">Защита REST API с помощью JSON Web Tokens (JWT). Генерация, передача и валидация токенов для аутентификации запросов к защищенным эндпоинтам.</p>
                        <a href="lab9/simple_shop_mvc+edit+session_registr+api+jwt_token/index.php" class="btn btn-primary mt-auto">Перейти к ЛР 9</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
EOF
echo "✅ www/index.php обновлен"

# 4. Обновление подключений к БД
DB_FILES=(
    "www/lab5/students-facultets/config/database.php"
    "www/lab6/simple_shop_mvc/config/database.php"
    "www/lab7/simple_shop_mvc+edit+session_registr/config/database.php"
    "www/lab8/simple_shop_mvc+edit+session_registr+api/config/database.php"
    "www/lab8/simple_shop_mvc+edit+session_registr+api/client/config.php"
    "www/lab9/simple_shop_mvc+edit+session_registr+api+jwt_token/config/database.php"
)

for file in "${DB_FILES[@]}"; do
    if [ -f "$file" ]; then
        sed -i "s/define('DB_HOST', '[^']*');/define('DB_HOST', 'db');/g" "$file"
        sed -i "s/define('DB_NAME', '[^']*');/define('DB_NAME', 'db');/g" "$file"
        sed -i "s/define('DB_USER', '[^']*');/define('DB_USER', 'root');/g" "$file"
        sed -i "s/define('DB_USERNAME', '[^']*');/define('DB_USERNAME', 'root');/g" "$file"
        sed -i "s/define('DB_PASS', '[^']*');/define('DB_PASS', 'rootpass');/g" "$file"
        sed -i "s/define('DB_PASSWORD', '[^']*');/define('DB_PASSWORD', 'rootpass');/g" "$file"
        echo "✅ Обновлен $file"
    fi
done

# Обновление модели клиента в 8 лабе
CLIENT_MODEL="www/lab8/simple_shop_mvc+edit+session_registr+api/client/models/Client.php"
if [ -f "$CLIENT_MODEL" ]; then
    sed -i "s/new mysqli('localhost', 'root', '', 'myshop')/new mysqli('db', 'root', 'rootpass', 'db')/g" "$CLIENT_MODEL"
    echo "✅ Обновлен $CLIENT_MODEL"
fi

# 5. Обновление API URL
SEARCH_8="www/lab8/simple_shop_mvc+edit+session_registr+api/search_product.php"
if [ -f "$SEARCH_8" ]; then
    sed -i "s|http://127.0.0.1/simple_shop_mvc+edit+session_registr+api/index.php|index.php|g" "$SEARCH_8"
    echo "✅ Обновлен $SEARCH_8"
fi

SEARCH_9="www/lab9/simple_shop_mvc+edit+session_registr+api+jwt_token/search_product.php"
if [ -f "$SEARCH_9" ]; then
    sed -i "s|http://127.0.0.1/simple_shop_mvc+edit+session_registr+api/index.php|index.php|g" "$SEARCH_9"
    echo "✅ Обновлен $SEARCH_9"
fi

API_CLIENT_9="www/lab9/simple_shop_mvc+edit+session_registr+api+jwt_token/api_client.php"
if [ -f "$API_CLIENT_9" ]; then
    sed -i "s|http://127.0.0.1/simple_shop_mvc+edit+session_registr+api+jwt_token|http://localhost:8080/lab9/simple_shop_mvc+edit+session_registr+api+jwt_token|g" "$API_CLIENT_9"
    echo "✅ Обновлен $API_CLIENT_9"
fi

echo "🎉 Все изменения успешно применены!"
