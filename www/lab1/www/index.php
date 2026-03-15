<?php
// Простой список всех PHP файлов для быстрого доступа
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лаба 1 - PHP скрипты</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h1 class="mb-4">Лаба 1 - Веб-программирование</h1>
            
            <div class="list-group">
                <a href="form.php" class="list-group-item list-group-item-action">
                    <h5>📝 Форма регистрации (form.php)</h5>
                    <p class="text-muted">Заполни форму и сохрани данные в JSON</p>
                </a>
                
                <a href="data-form.php" class="list-group-item list-group-item-action">
                    <h5>📊 Просмотр и фильтрация (data-form.php)</h5>
                    <p class="text-muted">Просмотри сохранённые записи, отфильтруй по имени/email</p>
                </a>
                
                <a href="data-student.php" class="list-group-item list-group-item-action">
                    <h5>👥 Парсинг студентов (data-student.php)</h5>
                    <p class="text-muted">Скачивает и парсит список группы с портала NOVSU</p>
                </a>
                
                <a href="ex1.php" class="list-group-item list-group-item-action">
                    <h5>🔍 Парсинг (вариант 2) (ex1.php)</h5>
                    <p class="text-muted">Альтернативный способ парсинга данных с портала</p>
                </a>

                <a href="filter.php" class="list-group-item list-group-item-action">
                    <h5>📊 фильтр</h5>
                </a>
            </div>

            <div class="alert alert-info mt-4">
                <strong>💡 Как работает:</strong>
                <ul>
                    <li>Сначала заполни форму на de>form.php</code> и сохрани данные</li>
                    <li>Потом иди на de>data-form.php</code> и смотри сохранённые записи</li>
                    <li>Остальные файлы парсят данные с внешних сайтов</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
