<?php
// указываем где хранятся данные
$dataFile = "data.txt";
$alert = null;
$alertType = "success";

// проверяем была ли отправка формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // собираем данные из формы и чистим от лишних пробелов
    $name = trim($_POST["name"]);
    $age = trim($_POST["age"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $comment = trim($_POST["comment"]);
    
    // для чекбоксов проверяем были ли они отмечены
    $intensive_courses = isset($_POST["intensive-courses"]) ? "Да" : "Нет";
    $books = isset($_POST["books"]) ? "Да" : "Нет";
    $video = isset($_POST["video"]) ? "Да" : "Нет";
    $preference = isset($_POST["preference"]) ? $_POST["preference"] : "Не указано";
    
    // добавляем дату и время записи
    $created_at = date("Y-m-d H:i:s");

    // склеиваем все поля в одну строку через разделитель
    $line = implode("|", [
        $name,
        $age,
        $email,
        $phone,
        $comment,
        $intensive_courses,
        $books,
        $video,
        $preference,
        $created_at
    ]);

    // записываем строку в конец файла
    if(file_put_contents($dataFile, $line . PHP_EOL, FILE_APPEND)) {
        $alert = "✅ Данные сохранены!";
        $alertType = "success";
    } else {
        $alert = "❌ Ошибка при сохранении данных!";
        $alertType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма регистрации</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">

    <?php if ($alert): ?>
        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show shadow" role="alert">
            <?= $alert ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-lg">
        <div class="card-body">
            <h2 class="card-title mb-4">Форма регистрации</h2>
            <form method="post">
                <fieldset class="mb-3">
                    <legend>Персональные данные</legend>
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя*</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Иван Иванов" required>
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Возраст</label>
                        <input type="number" class="form-control" name="age" id="age" placeholder="27" min="0" max="125">
                    </div>
                </fieldset>

                <fieldset class="mb-3">
                    <legend>Контакты</legend>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail*</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="ivanov@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон*</label>
                        <input type="tel" class="form-control" name="phone" id="phone" placeholder="+7 000 000-00-00" maxlength="21" required>
                    </div>
                </fieldset>

                <div class="mb-3">
                    <label for="comment" class="form-label">Увлечения</label>
                    <textarea class="form-control" name="comment" id="comment" placeholder="Расскажите обо всём, что для вас важно"></textarea>
                </div>

                <fieldset class="mb-3">
                    <legend>Учёба</legend>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="intensive-courses" id="courses" checked>
                        <label class="form-check-label" for="courses">Прохожу курсы</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="books" id="books">
                        <label class="form-check-label" for="books">Читаю книги</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="video" id="video">
                        <label class="form-check-label" for="video">Смотрю видео</label>
                    </div>
                </fieldset>

                <fieldset class="mb-3">
                    <legend>Предпочтения</legend>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="preference" id="front" value="frontend" checked>
                        <label class="form-check-label" for="front">Фронтенд-разработка</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="preference" id="back" value="backend">
                        <label class="form-check-label" for="back">Бэкенд-разработка</label>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-success">Отправить</button>
                <p class="mt-2 text-muted">* — Обязательные поля</p>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
