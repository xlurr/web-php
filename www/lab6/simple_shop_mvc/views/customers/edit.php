<?php include 'views/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">✏️ Редактировать покупателя</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?page=customers">
                    <input type="hidden" name="update" value="1">
                    <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Имя</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($customer['phone']); ?>">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">💾 Сохранить</button>
                        <a href="?page=customers" class="btn btn-secondary">❌ Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'views/footer.php'; ?>
