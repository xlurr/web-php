<?php include 'header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">👋 Добро пожаловать, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h4>
                </div>
                <div class="card-body">
                    <p class="lead">Вы успешно авторизованы в системе.</p>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <h5>🔐 Функции администратора:</h5>
                        <ul class="list-group mb-3">
                            <li class="list-group-item">
                                <a href="index.php?page=products">📦 Управление товарами</a>
                            </li>
                            <li class="list-group-item">
                                <a href="index.php?page=customers">👥 Управление покупателями</a>
                            </li>
                            <li class="list-group-item">
                                <a href="index.php?page=orders">📋 Все заказы</a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <h5>👤 Ваши функции:</h5>
                        <ul class="list-group mb-3">
                            <li class="list-group-item">
                                <a href="index.php?page=products">🛍️ Просмотр товаров</a>
                            </li>
                            <li class="list-group-item">
                                <a href="index.php?page=orders&action=my">📦 Мои заказы</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
