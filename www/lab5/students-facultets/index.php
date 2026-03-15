<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$students = get_all_students();
$message = '';

if (isset($_GET['message'])) {
    $message_types = [
            'added' => ['Студент успешно добавлен!', 'success'],
            'updated' => ['Данные студента обновлены!', 'success'],
            'deleted' => ['Студент удален!', 'success'],
            'parser' => ['Студенты получены с поратала!', 'success'],
            'error' => ['Произошла ошибка!', 'danger']
    ];
    
    if (isset($message_types[$_GET['message']])) {
        $message = $message_types[$_GET['message']];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление студентами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1 class="h4 mb-0">
                                <i class="fas fa-user-graduate me-2"></i>Система управления студентами
                            </h1>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-users me-1"></i>
                                <?php echo mysqli_num_rows($students); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message[1]; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message[0]; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <a href="add_student.php" class="btn btn-success me-2">
                                    <i class="fas fa-plus-circle me-1"></i>Добавить студента
                                </a>
                                <a href="manage_faculties.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-building me-1"></i>Управление факультетами
                                </a>
                                <a href="upload_from_portal.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-building me-1"></i>Получить с портала
                                </a>
                            </div>
                            <div class="text-muted">
                                Всего студентов: <strong><?php echo mysqli_num_rows($students); ?></strong>
                            </div>
                        </div>

                        <?php if (mysqli_num_rows($students) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>ФИО</th>
                                            <th>Email</th>
                                            <th>Телефон</th>
                                            <th>Факультет</th>
                                            <th>Предметы</th>
                                            <th>Курс</th>
                                            <th>Группа</th>
                                            <th>Дата добавления</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($students)): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $row['id']; ?></td>
                                            <td><?php echo clean_input($row['name']); ?></td>
                                            <td>
                                                <a href="mailto:<?php echo clean_input($row['email']); ?>" class="text-decoration-none">
                                                    <?php echo clean_input($row['email']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['phone'])): ?>
                                                    <a href="tel:<?php echo clean_input($row['phone']); ?>" class="text-decoration-none">
                                                        <?php echo clean_input($row['phone']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['faculty_name'])): ?>
                                                    <span class="badge bg-secondary"><?php echo clean_input($row['faculty_name']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $subjects_str = $row['subjects_name'] ?? '';
                                                if (!empty($subjects_str)):
                                                    foreach (explode(',', $subjects_str) as $subject): ?>
                                                        <span class="badge bg-info"><?php echo htmlspecialchars($subject); ?></span>
                                                    <?php endforeach;
                                                else: ?>
                                                    <span class="text-muted">Нет предметов</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $row['course']; ?> курс</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $row['stud_group']; ?></span>
                                            </td>
                                            <td class="text-muted small">
                                                <?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="edit_student.php?id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-outline-primary" 
                                                       title="Редактировать">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="delete_student.php?id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-outline-danger" 
                                                       title="Удалить"
                                                       onclick="return confirm('Вы уверены, что хотите удалить студента <?php echo addslashes($row['name']); ?>?')">
                                                        <i class="fas fa-trash"></i>
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
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Нет данных о студентах</h4>
                                <p class="text-muted">Добавьте первого студента, нажав на кнопку выше</p>
                                <a href="add_student.php" class="btn btn-success mt-2">
                                    <i class="fas fa-plus-circle me-1"></i>Добавить первого студента
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($connection); ?>