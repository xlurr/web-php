<?php include 'views/header.php'; ?>

<?php if ($message): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <?php echo $message['text'] ?? $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">👤 Добавить покупателя</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?page=customers&action=create">
                    <div class="mb-3">
                        <label class="form-label">Имя покупателя</label>
                        <input type="text" name="name" class="form-control" placeholder="Введите имя" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="text" name="phone" class="form-control" placeholder="+7 XXX XXX XX XX">
                    </div>
                    <button type="submit" name="create" class="btn btn-primary w-100">Добавить покупателя</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">📋 Список покупателей</h5>
            </div>
            <div class="card-body">
                <?php if ($customers && is_object($customers) && $customers->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Имя</th>
                                    <th>Email</th>
                                    <th>Телефон</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $customers->data_seek(0);
                                while ($customer = $customers->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $customer['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($customer['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                    <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?page=customers&action=edit&id=<?php echo $customer['id']; ?>" 
                                        class="btn btn-warning" title="Редактировать">✏️</a>
                                        <a href="?page=customers&delete=<?php echo $customer['id']; ?>" 
                                        class="btn btn-danger" title="Удалить"
                                        onclick="return confirm('Удалить этого покупателя?')">🗑️</a>
                                    </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <p>Покупатели не найдены</p>
                        <p class="small">Добавьте первого покупателя используя форму слева</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>