<?php
// Проверяем, что сессия уже запущена (в index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Получаем данные из сессии
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;

// Отладка
error_log("Header - isLoggedIn: " . ($isLoggedIn ? 'true' : 'false') . ", username: " . $username . ", role: " . $role);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        .badge-admin { background-color: #ff9800; }
        .badge-user { background-color: #4caf50; }
        .nav-user-info { color: #fff; font-size: 14px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-store"></i> Магазин
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if ($isLoggedIn): ?>
                <!-- NAVBAR ДЛЯ АДМИНА -->
                <?php if ($role === 'admin'): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=products">
                                <i class="fas fa-box"></i> Товары
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=customers">
                                <i class="fas fa-users"></i> Покупатели
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=orders">
                                <i class="fas fa-list"></i> Все заказы
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center nav-user-info gap-2">
                        <span class="me-2">
                            <i class="fas fa-user-shield"></i> 
                            <strong><?php echo htmlspecialchars($username ?? 'Unknown'); ?></strong>
                        </span>
                        <span class="badge badge-admin">Админ</span>
                        <a href="index.php?page=logout" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Выйти
                        </a>
                    </div>
                <?php else: ?>
                    <!-- NAVBAR ДЛЯ ПОЛЬЗОВАТЕЛЯ -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=orders&action=my">
                                <i class="fas fa-shopping-cart"></i> Мои заказы
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center nav-user-info gap-2">
                        <span class="me-2">
                            <i class="fas fa-user"></i> 
                            <strong><?php echo htmlspecialchars($username ?? 'Unknown'); ?></strong>
                        </span>
                        <span class="badge badge-user">Пользователь</span>
                        <a href="index.php?page=logout" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Выйти
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- NAVBAR ДЛЯ НЕАВТОРИЗОВАННОГО ПОЛЬЗОВАТЕЛЯ -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=login">
                            <i class="fas fa-sign-in-alt"></i> Вход
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=register">
                            <i class="fas fa-user-plus"></i> Регистрация
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container" style="margin-top: 20px;">