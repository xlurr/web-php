<?php include __DIR__ . '/../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- ФОРМА СОЗДАНИЯ ЗАКАЗА -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">🛒 Создать заказ</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=orders&action=create">
                        <div class="mb-3">
                            <label class="form-label">Товар *</label>
                            <select name="product_id" class="form-control" required>
                                <option value="">-- Выберите товар --</option>
                                <?php if ($products && is_object($products) && $products->num_rows > 0): ?>
                                    <?php 
                                    // Сохраняем текущую позицию и возвращаемся в начало
                                    $products->data_seek(0);
                                    while ($product = $products->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $product['id']; ?>">
                                            <?php echo htmlspecialchars($product['name']) . ' (' . number_format($product['price'], 2) . ' руб.)'; ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="">Товаров нет</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Количество *</label>
                            <input type="number" name="quantity" class="form-control" required min="1" value="1">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" name="create_order" class="btn btn-primary">
                                ✅ Создать заказ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- ТАБЛИЦА ЗАКАЗОВ -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">📦 Ваши заказы</h5>
                </div>
                <div class="card-body">
                    <?php if ($orders && is_object($orders) && $orders->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Товар</th>
                                        <th>Кол-во</th>
                                        <th>Сумма</th>
                                        <th>Дата</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['product_name'] ?? '—'); ?></td>
                                            <td><?php echo $order['quantity']; ?> шт.</td>
                                            <td><?php echo number_format($order['total'], 2); ?> руб.</td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?></td>
                                            <td>
                                                <a href="index.php?page=orders&action=edit&id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-warning" title="Редактировать">
                                                    ✏️
                                                </a>
                                                <a href="index.php?page=orders&action=delete&id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-danger" title="Удалить"
                                                   onclick="return confirm('Вы уверены?')">
                                                    🗑️
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            📭 У вас еще нет заказов. Создайте первый заказ, выбрав товар в форме слева!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>