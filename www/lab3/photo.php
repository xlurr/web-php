<?php
require_once 'libPhoto.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $comment = $_POST['comment'] ?? '';
    $photo = uploadPhoto($_FILES['file'], $comment);

    if ($photo === null) {
        $message = "Не удалось загрузить фото";
    } else {
        $message = "Фото успешно загружено";
    }
}

$photos = loadPhotos();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Фотоальбом</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Фотоальбом</h2>

    <!-- Форма загрузки -->
    <form action="photo.php" method="post" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <input type="file" name="file" class="form-control" required>
        </div>
        <div class="mb-3">
            <input type="text" name="comment" class="form-control" placeholder="Комментарий" required>
        </div>
        <button type="submit" class="btn btn-primary">Загрузить</button>
    </form>

    <!-- Сообщение о загрузке -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Фотоальбом -->
    <?php if (empty($photos)): ?>
        <p>Пока нет фотографий</p>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($photos as $photo): ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="<?= $photo['url'] ?>" class="card-img-top" alt="Фото">
                        <div class="card-body">
                            <p class="card-text"><b><?= htmlspecialchars($photo['comment']) ?></b></p>
                            <p class="card-text">
                                Размер: <?= $photo['width'] ?>×<?= $photo['height'] ?> px<br>
                                Добавлено: <?= date("d.m.Y H:i:s", $photo['time']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS (опционально, если нужны интерактивные компоненты) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>