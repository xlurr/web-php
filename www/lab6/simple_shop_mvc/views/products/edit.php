<?php include 'views/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">✏️ Редактировать товар</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?page=products">
                    <input type="hidden" name="update" value="1">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Название товара</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Цена (руб.)</label>
                        <input type="number" step="0.01" name="price" class="form-control" 
                               value="<?php echo $product['price']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">💾 Сохранить изменения</button>
                        <a href="?page=products" class="btn btn-secondary">❌ Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'views/footer.php'; ?>
