<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$errors = [];
$form_data = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'faculty_id' => '',
    'subjects_id' => [],
    'stud_group' => '',
    'course' => '',
];

$faculties = get_all_faculties();
$subjects = get_all_subjects();

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем и очищаем данные
    $form_data['name'] = clean_input($_POST['name'] ?? '');
    $form_data['email'] = clean_input($_POST['email'] ?? '');
    $form_data['phone'] = clean_input($_POST['phone'] ?? '');
    $form_data['faculty_id'] = clean_input($_POST['faculty_id'] ?? '');
    $form_data['stud_group'] = clean_input($_POST['stud_group'] ?? '');
    $form_data['course'] = clean_input($_POST['course'] ?? '');
    $form_data['subjects_id'] = $_POST['subjects_id'] ?? [];
    $form_data['subjects_id'] = array_map('intval', $form_data['subjects_id']);

    // Если нажали кнопку "Добавить предмет"
    if (isset($_POST['add_subject']) && !empty($_POST['subject_to_add'])) {
        $new_subject = intval($_POST['subject_to_add']);
        if ($new_subject && !in_array($new_subject, $form_data['subjects_id'])) {
            $form_data['subjects_id'][] = $new_subject;
        }
    }

    // Если нажали "Сохранить студента"
    if (isset($_POST['save_student'])) {
        // Валидация
        if (empty($form_data['name'])) $errors[] = 'ФИО обязательно для заполнения';
        if (empty($form_data['email'])) $errors[] = 'Email обязателен для заполнения';
        elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный формат email';
        elseif (email_exists($form_data['email'])) $errors[] = 'Студент с таким email уже существует';
        if (empty($form_data['course']) || $form_data['course'] < 1 || $form_data['course'] > 6) $errors[] = 'Курс должен быть от 1 до 6';

        // Добавление студента
        if (empty($errors)) {
            $faculty_id = empty($form_data['faculty_id']) ? NULL : $form_data['faculty_id'];
            $student_id = add_student(
                $form_data['name'],
                $form_data['email'],
                $form_data['phone'],
                $faculty_id,
                $form_data['course'],
                $form_data['stud_group']
            );
            if ($student_id > 0) {
                $status = true;
                foreach ($form_data['subjects_id'] as $subject_id) {
                    if (!add_student_subject($student_id, $subject_id)) $status = false;
                }
                if ($status) {
                    header('Location: index.php?message=added');
                    exit;
                } else {
                    $errors[] = 'Ошибка при добавлении предметов к студенту';
                }
            } else {
                $errors[] = 'Ошибка при добавлении студента';
            }
        }
    }

    // Если нажали "Удалить предмет"
    if (isset($_POST['remove_subject'])) {
        $remove_id = intval($_POST['remove_subject']);
        if (($key = array_search($remove_id, $form_data['subjects_id'])) !== false) {
            unset($form_data['subjects_id'][$key]);
            $form_data['subjects_id'] = array_values($form_data['subjects_id']);
        }
    }
}
?>

    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Добавить студента</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
<body class="bg-light">
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h1 class="h4 mb-0">Добавить нового студента</h1>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>ФИО *</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $form_data['name']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" value="<?php echo $form_data['email']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Телефон</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo $form_data['phone']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Факультет</label>
                            <select class="form-select" name="faculty_id">
                                <option value="">Выберите факультет</option>
                                <?php mysqli_data_seek($faculties,0); ?>
                                <?php while($faculty = mysqli_fetch_assoc($faculties)): ?>
                                    <option value="<?php echo $faculty['id']; ?>" <?php echo $form_data['faculty_id']==$faculty['id']?'selected':''; ?>>
                                        <?php echo clean_input($faculty['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Курс *</label>
                            <select class="form-select" name="course">
                                <option value="">Выберите курс</option>
                                <?php for($i=1;$i<=6;$i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $form_data['course']==$i?'selected':''; ?>><?php echo $i; ?> курс</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Группа *</label>
                            <select class="form-select" name="stud_group">
                                <option value="">Выберите группу</option>
                                <?php for($g=3091;$g<=3093;$g++): ?>
                                    <option value="<?php echo $g; ?>" <?php echo $form_data['stud_group']==$g?'selected':''; ?>>Группа <?php echo $g; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Добавление предмета -->
                        <div class="mb-3">
                            <label>Добавить предмет:</label>
                            <div class="d-flex">
                                <select class="form-select me-2" name="subject_to_add">
                                    <option value="">Выберите предмет</option>
                                    <?php mysqli_data_seek($subjects,0); ?>
                                    <?php while($subject = mysqli_fetch_assoc($subjects)): ?>
                                        <option value="<?php echo $subject['id']; ?>">
                                            <?php echo clean_input($subject['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <button type="submit" name="add_subject" class="btn btn-primary">Добавить</button>
                            </div>
                        </div>

                        <!-- Список выбранных предметов -->
                        <div class="mb-3">
                            <label>Выбранные предметы:</label>
                            <ul class="list-group">
                                <?php
                                mysqli_data_seek($subjects,0);
                                $all_subjects=[];
                                while($subject=mysqli_fetch_assoc($subjects)){
                                    $all_subjects[$subject['id']] = $subject['name'];
                                }
                                foreach($form_data['subjects_id'] as $subject_id):
                                    if(isset($all_subjects[$subject_id])):
                                        ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?php echo clean_input($all_subjects[$subject_id]); ?>
                                            <button type="submit" name="remove_subject" value="<?php echo $subject_id; ?>" class="btn btn-sm btn-danger">Удалить</button>
                                        </li>
                                    <?php endif; endforeach; ?>
                            </ul>
                        </div>

                        <!-- Скрытые поля для передачи выбранных предметов при сохранении -->
                        <?php foreach($form_data['subjects_id'] as $subject_id): ?>
                            <input type="hidden" name="subjects_id[]" value="<?php echo $subject_id; ?>">
                        <?php endforeach; ?>

                        <button type="submit" name="save_student" class="btn btn-success">Сохранить студента</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php mysqli_close($connection); ?>