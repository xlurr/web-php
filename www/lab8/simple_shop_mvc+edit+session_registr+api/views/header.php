<?php
// Безопасная проверка авторизации через сессии
@session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$username = $_SESSION['username'] ?? null;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин - Система управления</title>
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
            <a class="navbar-brand" href="index.php">🛍️ Магазин</a>
            
            <?php if ($isLoggedIn): ?>
            <div class="navbar-nav me-auto">
                <?php if ($isAdmin): ?>
                    <a class="nav-link" href="index.php?page=products">📦 Товары</a>
                    <a class="nav-link" href="index.php?page=customers">👥 Покупатели</a>
                    <a class="nav-link" href="index.php?page=orders">🛒 Все заказы</a>
                    <a class="nav-link" href="index.php?page=api_test">API Тест</a>
                <?php else: ?>
                    <a class="nav-link" href="index.php?page=orders&action=my">🛒 Мои заказы</a>
                <?php endif; ?>
            </div>
            
            <div class="navbar-nav">
                <span class="navbar-text me-3">
                    <?php echo htmlspecialchars($username); ?>
                    <?php if ($isAdmin): ?>
                        <span class="badge bg-warning">Админ</span>
                    <?php else: ?>
                       
                    <?php endif; ?>
                </span>
                <a class="nav-link" href="index.php?page=logout">🚪 Войти</a>
            </div>
            <?php else: ?>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php?page=login">🔐 Войти</a>
                <a class="nav-link" href="index.php?page=register">📝 Регистрация</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Блок уведомлений -->
    <?php 
    $message = isset($message) ? $message : (isset($_SESSION['message']) ? [
        'text' => $_SESSION['message'],
        'type' => $_SESSION['message_type'] ?? 'info'
    ] : null);
    
    if ($message): 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    ?>
        <div class="container">
            <div class="alert alert-<?php echo $message['type'] == 'success' ? 'success' : ($message['type'] == 'danger' ? 'danger' : 'info'); ?> alert-dismissible fade show">
                <?php echo $message['text']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">