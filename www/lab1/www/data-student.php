<?php
function downloadPage($url) {
    //cURL библа позволяет выполнять хттп запросы
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

$url = 'https://portal.novsu.ru/search/groups/i.2500/?page=search&grpname=3091';
$html = downloadPage($url);

$data = [];

$dom = new DomDocument();
@ $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .$html);
//получаем коллекцию всех ul
$links = $dom->getElementsByTagName('ul');
foreach($links as $link) {
    // для каждого ul получаем его дочерние
    @$node = $link->childNodes->item(6);
    if ($node !== null) {
        $text = trim($node->nodeValue);
        // проверяем текст на совпадение
        if (stripos($text, 'Форма обучения: очная') !== false) {
            // получаем следующий за ul узел это #text след за ним таблица
            $t = $link->nextSibling;
            while ($t && $t->nodeName !== 'table') {
                $t = $t->nextSibling;
            }
            if ($t) {
                foreach($t->getElementsByTagName('tr') as $tr) { //все
                    $row = [];
                    foreach ($tr->getElementsByTagName('th') as $th) { //заголов
                        $row[] = $th->nodeValue;
                    }
                    foreach ($tr->getElementsByTagName('td') as $td) { //обычн
                        $row[] = trim($td->nodeValue);
                    }
                    if (!empty($row)) {
                        $data[] = $row;
                    }
                }
                echo $dom->saveHTML($t); //обратно в хтмл
                for($i = 1; $i < count($data); $i++) { //второй столбец каждой строки
                    print_r($data[$i][1]); //второй элемент
                }
            }
            break; // нашли
        }
    }
}
?>