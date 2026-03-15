<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pagination Example</title>
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .pagination a {
            padding: 5px 10px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            margin-right: 5px;
        }
        
        .pagination a.active {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

<h1>Строки с пагинацией:</h1>

<?php
// Функция пагинации (та же самая, что была представлена ранее)
function paginateArray($data, $page = 1, $perPage = 10)
{
    $totalPages = ceil(count($data) / $perPage);
    if ($page > $totalPages || $page <= 0) {
        return [];
    }
    $offset = ($page - 1) * $perPage;
    $pagedData = array_slice($data, $offset, $perPage);
    return [
        'current_page' => $page,
        'pages_total' => $totalPages,
        'items_per_page' => $perPage,
        'data' => $pagedData
    ];
}

// Массив строк
$data = ["Москва", "Санкт-Петербург", "Новосибирск", "Екатеринбург", "Нижний Новгород",
         "Казань", "Челябинск", "Омск", "Самара", "Ростов-на-Дону", "Уфа", "Красноярск",
         "Пермь", "Воронеж", "Волгоград"];

// Текущие параметры страницы (можно получать из GET-запросов)
$currentPage = isset($_GET['object']) ? intval($_GET['object']) : 1;
$perPage = 5; // Количество записей на странице

// Получаем список городов для текущей страницы
$result = paginateArray($data, $currentPage, $perPage);

// Вывод списка городов
if (!empty($result['data'])) {
    echo "<ul>";
    foreach ($result['data'] as $city) {
        echo "<li>$city</li>";
    }
    echo "</ul>";
} else {
    echo '<p>Нет данных.</p>';
}

// Создание навигационной панели
echo '<div class="pagination">';
for ($i = 1; $i <= $result['pages_total']; $i++) {
    $class = ($i === $currentPage) ? 'active' : '';
    echo '<a href="?object=' . $i . '" class="' . $class . '">' . $i . '</a>';
}
echo '</div>';
?>

</body>
</html>
