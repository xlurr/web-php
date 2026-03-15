<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
function downloadPage($url) {
    //cURL —  библиотека, позволяющая выполнять HTTP-запросы из PHP
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $content = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Ошибка cURL: ' . curl_error($ch));
    }

    curl_close($ch);

    return $content;
}
$url = 'https://portal.novsu.ru/search/groups/i.2500/?page=search&grpname=3093';
$html = downloadPage($url);

$data = [];

$dom = new DomDocument();
@ $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .$html);
//Получаем коллекцию всех ul, чтобы в дальнейшем рассматривать только очную форму, отбросив заочную
$links = $dom->getElementsByTagName('ul');
foreach($links as $link) {
    // Для каждого ul получаем его дочерние узлы, из них берем 6 по счету
    @$node = $link->childNodes->item(6);
    if ($node !== null) {
        $text = trim($node->nodeValue);
        // Проверяем текст на совпадение
        if (stripos($text, 'Форма обучения: очная') !== false) {
            // Получаем следующий за ul узел - это #text, а следующий за ним - таблица
            $t = $link->nextSibling;
            while ($t && $t->nodeName !== 'table') {
                $t = $t->nextSibling;
            }
            if ($t) {
                foreach ($t->getElementsByTagName('tr') as $tr) {
                    $row = [];
                    foreach ($tr->getElementsByTagName('th') as $th) {
                        $row[] = $th->nodeValue;
                    }
                    foreach ($tr->getElementsByTagName('td') as $td) {
                        $row[] = trim($td->nodeValue);
                    }
                    if (!empty($row)) {
                        $data[] = $row;
                    }
                }
                for($i = 1; $i < count($data); $i++) {
                    $tmp_email = uniqid() . '@temp.com';
                    add_student($data[$i][1], $tmp_email, '', 1, 3, 3093);
                }
            }
            break; // нашли и выходим
        }
    }
}
header('Location: index.php?message=parser');