<?php
$name = "иван";
$mail = "ivan.@gmail.com";

$filterField = $_POST['filter_field'] ?? 'all';
$output = $filterField;

echo "Привет, {$output}!"; 

$url = 'http://localhost:8080/filter.php';
$html = getField($url);

echo "<a href='http://localhost:8080/filter.php'>"; 
isset($_GET["param"])
    echo htmlspecialchars($_GET["param"]); 

echo "</a>";
?>


<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Фильтр</title></head>
<body>

<form method = "post">
  <select type = "filter_field">
    <option value = "all"> </option>
    <option value = "mail"> </option>
    <option value = "name"> </option>
</option>
<button type = "submit"> Применить <button>
</form>





<p>Текущий фильтр: <?= htmlspecialchars($filterField) ?></p>

</body>
</html>