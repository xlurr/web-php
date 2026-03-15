<?php
/**
 * views/orders/my.php - Страница "Мои заказы" для пользователей
 */
?>

<?php include __DIR__ . '/../header.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0">
                    <i class="bi bi-box-seam"></i> Мои заказы
                </h2>
                <a href="?page=orders" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Все заказы
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert <?php echo $message['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message['text']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Ваши заказы</h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($orders->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Клиент</th>
                                        <th>Товар</th>
                                        <th>Количество</th>
                                        <th>Сумма</th>
                                        <th>Дата</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo $order['id']; ?></strong>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($order['customername'] ?? 'N/A'); ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['productname'] ?? 'N/A'); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6"><?php echo $order['quantity']; ?> шт.</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success fs-6">
                                                    <?php echo number_format($order['total'], 2, '.', ' '); ?> ₽
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="?page=orders&action=edit&id=<?php echo $order['id']; ?>" 
                                                       class="btn btn-outline-warning" 
                                                       title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="?page=orders&delete=<?php echo $order['id']; ?>" 
                                                       class="btn btn-outline-danger" 
                                                       onclick="return confirm('Удалить заказ #<?php echo $order['id']; ?>?')"
                                                       title="Удалить">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">У вас пока нет заказов</h4>
                            <p class="text-muted">Создайте первый заказ в <a href="?page=orders">списке заказов</a></p>
                            <a href="?page=orders" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Создать заказ
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
