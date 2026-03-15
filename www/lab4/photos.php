<?php
require_once 'PhotoUploader.php';
$uploader = new PhotoUploader();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploader->uploadFile($_FILES['file'], $_POST['comment']);
}
if (isset($_GET['delete'])) $uploader->deleteFile($_GET['delete']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Фото</title>
</head>
<body class="container py-4">
<h2>Загрузка фото</h2>
<form method="post" enctype="multipart/form-data" class="mb-4">
    <input type="file" name="file" required>
    <input type="text" name="comment" placeholder="Комментарий">
    <button class="btn btn-success">Загрузить</button>
</form>
<div class="row">
    <?php foreach ($uploader->getAllFiles() as $file)
        if ($file['class_type'] === 'PhotoUploader') echo $uploader->generateCard($file); ?>
</div>
</body>
</html>