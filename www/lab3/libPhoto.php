<?php
const MAX_WIDTH = 1000;
const MAX_HEIGHT = 1000;
const IMAGE_DIR = 'img';

if (!file_exists(IMAGE_DIR)) {
    mkdir(IMAGE_DIR, 0777, true);
}

function uploadPhoto(array $file, string $comment)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Ошибка загрузки файла!";
        return false;
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        echo "Файл не является изображением!";
        return false;
    }

    [$width, $height, $type] = $imageInfo;

    if ($width > MAX_WIDTH || $height > MAX_HEIGHT) {
        echo "Изображение превышает допустимые размеры!";
        return false;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid("photo_", true) . "." . strtolower($ext);
    $destination = IMAGE_DIR . "/" . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo "Не удалось сохранить файл!";
        return false;
    }

    file_put_contents($destination . ".txt", $comment);

    // Формируем объект изображения
    $photo = [
        'name' => $filename,
        'url' => $destination,
        'width' => $width,
        'height' => $height,
        'comment' => $comment,
        'time' => time(),
    ];

    return $photo;
}

function loadPhotos(string $dir = 'img'): array
{
    echo "<script>console.log('Message: 1);</script>";
    if (!file_exists($dir)) {
        echo "<script>console.log('Message: 2);</script>";
        return [];
    }
    echo "<script>console.log('Message: 3);</script>";

    $photos = [];
    $files = scandir($dir);

    foreach ($files as $file) {
        if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $path = $dir . "/" . $file;
            $info = getimagesize($path);
            $commentFile = $path . ".txt";
            $comment = file_exists($commentFile) ? file_get_contents($commentFile) : '';

            $photos[] = [
                'name' => $file,
                'url' => $path,
                'width' => $info[0],
                'height' => $info[1],
                'comment' => $comment,
                'time' => filemtime($path),
            ];
        }
    }

    // Сортируем по дате — новые сверху
    usort($photos, fn($a, $b) => $b['time'] <=> $a['time']);

    return $photos;
}
?>