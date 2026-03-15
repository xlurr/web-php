<?php
require_once 'lib/libDocs.php';

// Загрузка нового документа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    uploadDocument($_FILES['file'], $_POST['comment'] ?? '');
}

// Загружаем все документы
$documents = loadDocuments();

// --- ПАГИНАЦИЯ ---
$perPage = 6; // количество документов на страницу
$total = count($documents);
$totalPages = ceil($total / $perPage);
$page = isset($_GET['pagкакe']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;
$docsToShow = array_slice($documents, $offset, $perPage);
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

<body class="bg-light p-4">
<header>
    <div class="header-area">
        <?php include("object/header.php"); ?>
        <?php include("object/menu.php"); ?>
    </div>
</header>

<div class="container">
    <h2 class="mb-4 text-center">Документы</h2>

    <!-- Форма загрузки -->
    <form class="mb-5" action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Выберите документ (.pdf, .doc, .xls)</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Комментарий</label>
            <input type="text" name="comment" class="form-control" placeholder="Введите краткое описание" required>
        </div>
        <button class="btn btn-primary">Загрузить</button>
    </form>

    <!-- Вывод карточек документов -->
    <div class="row">
        <?php if (empty($docsToShow)): ?>
            <p class="text-center text-muted">Документы отсутствуют.</p>
        <?php else: ?>
        <ul>
            <?php foreach ($docsToShow as $doc): ?>
                <li><?= htmlspecialchars($doc['comment']) ?> <a href="<?= $doc['url'] ?>">(Формат <?= $doc['format'] ?>, <?= round($doc['size'] / 1024, 2) ?> КБ)</a></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

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