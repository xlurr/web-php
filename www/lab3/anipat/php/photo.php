<?php
require_once 'lib/libPhoto.php';

$message = '';
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

// --- ПАГИНАЦИЯ ---
$perPage = 4;
$total = count($photos);
$totalPages = ceil($total / $perPage);
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;
$photoToShow = array_slice($photos, $offset, $perPage);

?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Animal</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.png">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/themify-icons.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/gijgo.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/slicknav.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
<header>
    <div class="header-area">
        <?php include("object/header.php"); ?>
        <?php include("object/menu.php"); ?>
    </div>
</header>

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

    <!-- Сообщение -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Фотоальбом -->
    <?php if (empty($photos)): ?>
        <p>Пока нет фотографий</p>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($photoToShow as $photo): ?>
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

    <!-- Пагинация -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">«</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">»</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include("object/footer.php"); ?>

<!-- Bootstrap JS (если нужен) -->
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>