<?php include 'views/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">✏️ Редактировать заказ</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?page=orders">
                    <input type="hidden" name="update" value="1">
                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Покупатель</label>
                        <select name="customer_id" class="form-control" required>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>"
                                    <?php if ($customer['id'] == $order['customer_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($customer['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Товар</label>
                        <select name="product_id" class="form-control" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"
                                    <?php if ($product['id'] == $order['product_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Количество</label>
                        <input type="number" name="quantity" class="form-control"
                               value="<?php echo $order['quantity']; ?>" required min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Сумма (руб.)</label>
                        <input type="number" step="0.01" name="total" class="form-control"
                               value="<?php echo $order['total']; ?>" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">💾 Сохранить изменения</button>
                        <a href="?page=orders" class="btn btn-secondary">❌ Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'views/footer.php'; ?>
