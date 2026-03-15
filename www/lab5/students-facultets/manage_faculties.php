<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$faculties = get_all_faculties();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_faculty'])) {
    $name = clean_input($_POST['name'] ?? '');
    
    if (empty($name)) {
        $message = ['Название факультета не может быть пустым', 'danger'];
    } else {
        if (add_faculty($name)) {
            $message = ['Факультет успешно добавлен!', 'success'];
            // Обновляем список факультетов
            $faculties = get_all_faculties();
        } else {
            $message = ['Ошибка при добавлении факультета', 'danger'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление факультетами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1 class="h4 mb-0">
                                <i class="fas fa-building me-2"></i>Управление факультетами
                            </h1>
                            <a href="index.php" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Назад к студентам
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message[1]; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message[0]; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">Добавить факультет</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Название факультета</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="name" 
                                                       name="name" 
                                                       required
                                                       placeholder="Введите название факультета">
                                            </div>
                                            <button type="submit" name="add_faculty" class="btn btn-success">
                                                <i class="fas fa-plus me-1"></i>Добавить факультет
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">Список факультетов</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (mysqli_num_rows($faculties) > 0): ?>
                                            <div class="list-group">
                                                <?php while($faculty = mysqli_fetch_assoc($faculties)): ?>
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span><?php echo clean_input($faculty['name']); ?></span>
                                                    <small class="text-muted">
                                                        ID: <?php echo $faculty['id']; ?>
                                                    </small>
                                                </div>
                                                <?php endwhile; ?>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted text-center">Нет добавленных факультетов</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($connection); ?>