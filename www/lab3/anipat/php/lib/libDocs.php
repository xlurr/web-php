<?php
const DOC_DIR = '../docs/'; // каталог для хранения документов

$dataDocsFile = "../dataDocs/dataDocs.txt";
const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

if (!file_exists(DOC_DIR)) {
    mkdir(DOC_DIR, 0777, true);
}

/**
 * Загрузка документа
 */
function uploadDocument(array $file, string $comment)
{
    global $dataDocsFile;
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Ошибка загрузки файла!";
        return false;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        echo "Недопустимый формат файла!";
        return false;
    }

    $filename = uniqid("doc_", true) . "." . $ext;
    $destination = DOC_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo "Не удалось сохранить файл!";
        return false;
    }

    $doc = [
        'name' => $file['name'],
        'saved_as' => $filename,
        'url' => $destination,
        'comment' => $comment,
        'format' => $ext,
        'size' => filesize($destination),
        'time' => time(),
    ];

    if (!file_exists($dataDocsFile)) {
        file_put_contents($dataDocsFile, "");
    }

    $content = trim(file_get_contents($dataDocsFile));
    $records = $content ? explode(';', $content) : [];
    $newRecord = implode(',', [
        $doc['name'],
        $doc['saved_as'],
        $doc['url'],
        $doc['comment'],
        $doc['format'],
        $doc['size'],
        $doc['time']
    ]);

    $records[] = $newRecord;

    file_put_contents($dataDocsFile, implode(';', $records));

    return $doc;
}
/**
 * Загрузка списка документов из папки
 */
function loadDocuments(string $dir = DOC_DIR): array
{
    global $dataDocsFile;
    $docs = [];

    if (!file_exists($dir)) return [];

    // получаем все файлы в директории
    $filesInDir = [];
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') continue;
        $filesInDir[] = $file;
    }


    if (file_exists($dataDocsFile)) {
        $content = trim(file_get_contents($dataDocsFile));
        $records = $content ? explode(';', $content) : [];

        foreach ($records as $record) {
            $parts = explode(',', $record);
            if (count($parts) === 7) {
                $docs[] = [
                    'name' => $parts[0],
                    'saved_as' => $parts[1],
                    'url' => $parts[2],
                    'comment' => $parts[3],
                    'format' => $parts[4],
                    'size' => $parts[5],
                    'time' => (int)$parts[6],
                ];
            }
        }
    }

    // сортировка по дате
    usort($docs, fn($a, $b) => $b['time'] <=> $a['time']);

    return $docs;
}
?>
