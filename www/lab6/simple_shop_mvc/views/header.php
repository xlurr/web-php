<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Простой магазин</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background-color: #f8f9fa; }
        .navbar { margin-bottom: 30px; }
        .card { margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">🛍️ Простой магазин</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php?page=products">📦 Товары</a>
                <a class="nav-link" href="index.php?page=customers">👥 Покупатели</a>
                <a class="nav-link" href="index.php?page=orders">🛒 Заказы</a>
            </div>
        </div>
    </nav>
    <div class="container">