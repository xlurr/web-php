<?php include 'views/header.php'; ?>


<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">🛒 Создать заказ</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Покупатель</label>
                        <select name="customer_id" class="form-control" required>
                            <option value="">Выберите покупателя</option>
                            <?php while ($customer = $customers->fetch_assoc()): ?>
                                <option value="<?php echo $customer['id']; ?>">
                                    <?php echo htmlspecialchars($customer['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Товар</label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Выберите товар</option>
                            <?php while ($product = $products->fetch_assoc()): ?>
                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?> (<?php echo number_format($product['price'], 2); ?> руб.)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Количество</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                    </div>
                    <button type="submit" name="create" class="btn btn-primary w-100">Создать заказ</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">📋 Список заказов</h5>
            </div>
            <div class="card-body">
                <?php if ($orders->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Покупатель</th>
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
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                    <td><span class="badge bg-primary"><?php echo $order['quantity']; ?> шт.</span></td>
                                    <td><span class="badge bg-success"><?php echo number_format($order['total'], 2); ?> руб.</span></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=orders&action=edit&id=<?php echo $order['id']; ?>" 
                                            class="btn btn-warning">✏️</a>
                                            <a href="?page=orders&delete_id=<?php echo $order['id']; ?>" 
                                            class="btn btn-danger"
                                            onclick="return confirm('Удалить этот заказ?')">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <p>Заказы не найдены</p>
                        <p class="small">Создайте первый заказ используя форму слева</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>