<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Пагинация животных</title>
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

        ul {
            list-style: none;
        }
    </style>
</head>
<body>

<h1>Животные на сайте:</h1>

<?php
// Пример массива животных (можно брать из БД)
$animals = [
    "Кошка", "Собака", "Хомяк", "Кролик", "Попугай",
    "Черепаха", "Морская свинка", "Игуана", "Лиса", "Ослик",
    "Козёл", "Лошадь", "Енот", "Сурикат", "Курица"
];

// Функция пагинации
function paginateArray($data, $page = 1, $perPage = 5) {
    $totalPages = ceil(count($data) / $perPage);
    if ($page > $totalPages || $page <= 0) return [];
    $offset = ($page - 1) * $perPage;
    $pagedData = array_slice($data, $offset, $perPage);
    return [
        'current_page' => $page,
        'pages_total' => $totalPages,
        'items_per_page' => $perPage,
        'data' => $pagedData
    ];
}

// Получаем текущую страницу из GET-параметра
$currentPage = isset($_GET['object']) ? intval($_GET['object']) : 1;
$perPage = 5;

// Получаем животных для текущей страницы
$result = paginateArray($animals, $currentPage, $perPage);

// Вывод животных
if (!empty($result['data'])) {
    echo "<ul>";
    foreach ($result['data'] as $animal) {
        echo "<li>$animal</li>";
    }
    echo "</ul>";
} else {
    echo '<p>Нет данных.</p>';
}

// Навигация
echo '<div class="pagination">';
for ($i = 1; $i <= $result['pages_total']; $i++) {
    $class = ($i === $currentPage) ? 'active' : '';
    echo '<a href="?object=' . $i . '" class="' . $class . '">' . $i . '</a>';
}
echo '</div>';
?>

</body>
</html>