<?php include 'views/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="access-denied">
                    <i class="fas fa-ban fa-5x text-danger mb-4"></i>
                    <h2>Доступ запрещен</h2>
                    <p class="text-muted mb-4">
                        У вас недостаточно прав для просмотра этой страницы.
                    </p>
                    <div class="d-grid gap-2 d-md-block">
                        <a href="index.php?page=orders" class="btn btn-primary">
                            На главную
                        </a>
                        <a href="index.php?page=logout" class="btn btn-outline-secondary">
                            Сменить пользователя
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>