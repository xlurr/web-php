<?php
// файл с данными и переменная для фильтра
$dataFile = "data.txt";
$filterField = $_POST['filter_field'] ?? 'all';
$results = [];
$alert = null;

// читаем данные из текстового файла
if (file_exists($dataFile)) {
    // file читает весь файл в массив по строкам
    $lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // разбираем каждую строку
    foreach ($lines as $line) {
        // explode разделяет строку по символу |
        $fields = explode("|", $line);
        
        // проверяем что в строке правильное количество полей
        if (count($fields) === 10) {
            $results[] = [
                'name' => $fields[0],
                'age' => $fields[1],
                'email' => $fields[2],
                'phone' => $fields[3],
                'comment' => $fields[4],
                'intensive_courses' => ($fields[5] === "Да"),
                'books' => ($fields[6] === "Да"),
                'video' => ($fields[7] === "Да"),
                'preference' => $fields[8],
                'created_at' => $fields[9]
            ];
        }
    }
} else {
    $alert = "Файл данных не найден.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Фильтр записей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">

    <h2 class="mb-4">Записи пользователей</h2>

    <!-- форма для выбора фильтра -->
    <form method="post" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="filter_field" class="form-label">Выберите что показывать</label>
                <select name="filter_field" id="filter_field" class="form-select">
                    <option value="all" <?= ($filterField=='all')?'selected':'' ?>>Все записи</option>
                    <option value="name" <?= ($filterField=='name')?'selected':'' ?>>Только Имя</option>
                    <option value="email" <?= ($filterField=='email')?'selected':'' ?>>Только Email</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Применить</button>
            </div>
        </div>
    </form>

    <?php if($alert): ?>
        <div class="alert alert-warning"><?= $alert ?></div>
    <?php endif; ?>

    <!-- выводим карточки с данными -->
    <div class="row g-4 mt-2">
        <?php foreach ($results as $entry): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <?php // показываем имя если выбрано "все" или "имя" ?>
                        <?php if($filterField == 'all' || $filterField == 'name'): ?>
                            <h5 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($entry['name']) ?></h5>
                        <?php endif; ?>

                        <?php // показываем email если выбрано "все" или "email" ?>
                        <?php if($filterField == 'all' || $filterField == 'email'): ?>
                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($entry['email']) ?></h6>
                        <?php endif; ?>

                        <?php // остальные поля только если показываем все ?>
                        <?php if($filterField == 'all'): ?>
                            <p class="card-text">
                                <strong>Возраст:</strong> <?= htmlspecialchars($entry['age']) ?><br>
                                <strong>Телефон:</strong> <?= htmlspecialchars($entry['phone']) ?><br>
                                <strong>Увлечения:</strong> <?= htmlspecialchars($entry['comment']) ?><br>
                                <strong>Предпочтения:</strong> <?= htmlspecialchars($entry['preference']) ?><br>
                                <strong>Прохождение курсов:</strong> <?= $entry['intensive_courses'] ? 'Да' : 'Нет' ?><br>
                                <strong>Читает книги:</strong> <?= $entry['books'] ? 'Да' : 'Нет' ?><br>
                                <strong>Смотрит видео:</strong> <?= $entry['video'] ? 'Да' : 'Нет' ?><br>
                            </p>
                            <div class="card-footer text-muted">
                                Добавлено: <?= htmlspecialchars($entry['created_at']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
