<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Проверяем наличие ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: IndexBody.php');
    exit;
}

$id = (int)$_GET['id'];
$student = get_student_by_id($id);

// Если студент не найден
if (!$student) {
    header('Location: index.php?message=error');
    exit;
}

$errors = [];
$form_data = [
    'name' => $student['name'],
    'email' => $student['email'],
    'phone' => $student['phone'],
    'faculty_id' => $student['faculty_id'],
    'course' => $student['course'],
    'stud_group' => $student['stud_group'],
];

$faculties = get_all_faculties();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем и очищаем данные
    $form_data['name'] = clean_input($_POST['name'] ?? '');
    $form_data['email'] = clean_input($_POST['email'] ?? '');
    $form_data['phone'] = clean_input($_POST['phone'] ?? '');
    $form_data['faculty_id'] = clean_input($_POST['faculty_id'] ?? '');
    $form_data['course'] = clean_input($_POST['course'] ?? '');
    $form_data['stud_group'] = clean_input($_POST['stud_group'] ?? '');
    
    // Валидация
    if (empty($form_data['name'])) {
        $errors[] = 'ФИО обязательно для заполнения';
    }
    
    if (empty($form_data['email'])) {
        $errors[] = 'Email обязателен для заполнения';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный формат email';
    } elseif (email_exists($form_data['email'], $id)) {
        $errors[] = 'Другой студент с таким email уже существует';
    }
    
    if (empty($form_data['course']) || $form_data['course'] < 1 || $form_data['course'] > 6) {
        $errors[] = 'Курс должен быть от 1 до 6';
    }

    // Если ошибок нет, обновляем данные
    if (empty($errors)) {
        // Если факультет не выбран, устанавливаем NULL
        $faculty_id = empty($form_data['faculty_id']) ? NULL : $form_data['faculty_id'];
        if (update_student(
            $id,
            $form_data['name'],
            $form_data['email'],
            $form_data['phone'],
            $faculty_id,
            $form_data['course'],
            $form_data['stud_group'])) {
            header('Location: index.php?message=updated');
            exit;
        } else {
            $errors[] = 'Ошибка при обновлении данных';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать студента</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1 class="h4 mb-0">
                                <i class="fas fa-edit me-2"></i>Редактировать данные студента
                            </h1>
                            <a href="index.php" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Назад
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5 class="alert-heading">Ошибки валидации:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">ФИО *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo $form_data['name']; ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        Пожалуйста, введите ФИО студента.
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo $form_data['email']; ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        Пожалуйста, введите корректный email.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Телефон</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?php echo $form_data['phone']; ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="faculty_id" class="form-label">Факультет</label>
                                    <select class="form-select" id="faculty_id" name="faculty_id">
                                        <option value="">Выберите факультет</option>
                                        <?php 
                                        // Сбрасываем указатель результата
                                        mysqli_data_seek($faculties, 0);
                                        while($faculty = mysqli_fetch_assoc($faculties)): 
                                        ?>
                                            <option value="<?php echo $faculty['id']; ?>" 
                                                <?php echo $form_data['faculty_id'] == $faculty['id'] ? 'selected' : ''; ?>>
                                                <?php echo clean_input($faculty['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="course" class="form-label">Курс *</label>
                                <select class="form-select" id="course" name="course" required>
                                    <option value="">Выберите курс</option>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                        <option value="<?php echo $i; ?>"
                                            <?php echo $form_data['course'] == $i ? 'selected' : ''; ?>>
                                            <?php echo $i; ?> курс
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Пожалуйста, выберите курс.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="stud_group" class="form-label">Номер группы *</label>
                                <select class="form-select" id="stud_group" name="stud_group" required>
                                    <option value="">Выберите группу</option>
                                    <option value="3091" <?php echo $form_data['stud_group'] == '3091' ? 'selected' : ''; ?>>Группа 3091</option>
                                    <option value="3092" <?php echo $form_data['stud_group'] == '3092' ? 'selected' : ''; ?>>Группа 3092</option>
                                    <option value="3093" <?php echo $form_data['stud_group'] == '3093' ? 'selected' : ''; ?>>Группа 3093</option>
                                </select>
                                <div class="invalid-feedback">
                                    Пожалуйста, выберите номер группы.
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-1"></i>Отмена
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>Обновить данные
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Валидация формы Bootstrap
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>

<?php mysqli_close($connection);
?>