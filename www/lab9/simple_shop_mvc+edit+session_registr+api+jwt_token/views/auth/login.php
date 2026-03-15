<?php //include 'views/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="text-center mb-0">🔐 Вход в систему</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Имя пользователя или Email</label>
                        <input type="text" name="username" class="form-control" required 
                               value="<?php echo $_POST['username'] ?? ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="login" class="btn btn-primary btn-lg">
                            Войти
                        </button>
                    </div>
                </form>

                <div class="mt-4">
                    <h6>Тестовые пользователи:</h6>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-1"><strong>Администратор:</strong></p>
                            <p class="mb-1">Логин: <code>admin</code></p>
                            <p class="mb-3">Пароль: <code>password</code></p>
                            
                            <p class="mb-1"><strong>Пользователь (Иван):</strong></p>
                            <p class="mb-1">Логин: <code>user1</code></p>
                            <p class="mb-0">Пароль: <code>password</code></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>