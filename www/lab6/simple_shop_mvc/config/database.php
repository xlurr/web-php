<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'shop_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Создаем подключение
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Проверяем подключение
if (!$connection) {
    die("Ошибка подключения: " . mysqli_connect_error());
}

// Устанавливаем кодировку
mysqli_set_charset($connection, "utf8");

?>