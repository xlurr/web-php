<?php
const MAX_WIDTH = 1000;
const MAX_HEIGHT = 1000;
const IMAGE_DIR = '../img/photoAlbum';

$dataImageFile = "../img/dataImg/dataImage.txt";

if (!file_exists(IMAGE_DIR)) {
    mkdir(IMAGE_DIR, 0777, true);
}

function uploadPhoto(array $file, string $comment)
{
    global $dataImageFile;
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
    $filename = uniqid("photo_", true) . "." . $ext; // точка перед расширением
    $destination = IMAGE_DIR . "/" . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo "Не удалось сохранить файл!";
        return false;
    }

    // Формируем объект изображения
    $photo = [
        'name' => $filename,
        'url' => $destination,
        'width' => $width,
        'height' => $height,
        'comment' => $comment,
        'time' => time(),
    ];

    if (!file_exists($dataImageFile)) {
        file_put_contents($dataImageFile, "");
    }

    $content = trim(file_get_contents($dataImageFile));
    $records = $content ? explode(';', $content) : [];

    $newRecord = implode(',', [
        $photo['name'],
        $photo['url'],
        $photo['width'],
        $photo['height'],
        $photo['comment'],
        $photo['time']
    ]);

    $records[] = $newRecord;

    file_put_contents($dataImageFile, implode(';', $records));

    return $photo;
}

function loadPhotos(string $dir = IMAGE_DIR): array
{
    global $dataImageFile;
    $photos = [];
    if (!file_exists($dir)) return [];

    // получаем все файлы в директории
    $filesInDir = [];
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') continue;
        $filesInDir[] = $file;
    }

    // Загружаем данные из txt, если есть
    if (file_exists($dataImageFile)) {
        $content = trim(file_get_contents($dataImageFile));
        $records = $content ? explode(';', $content) : [];

        foreach ($records as $record) {
            $parts = explode(',', $record);
            if (count($parts) === 6) {
                $savedFileName = basename($parts[1]);
                if (in_array($savedFileName, $filesInDir)) {
                    $photos[] = [
                        'name' => $parts[0],
                        'url' => $parts[1],
                        'width' => $parts[2],
                        'height' => $parts[3],
                        'comment' => $parts[4],
                        'time' => (int)$parts[5],
                    ];
                }
            }
        }
    }

    // Сортировка по дате (новые сверху)
    usort($photos, fn($a, $b) => $b['time'] <=> $a['time']);

    return $photos;
}
?>