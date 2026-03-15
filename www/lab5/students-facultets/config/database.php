<?php
// Настройки подключения к базе данных
define('DB_SERVER', 'db');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'rootpass');
define('DB_NAME', 'db');
define('DB_CHARSET', 'utf8');

// Создаем подключение
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Устанавливаем кодировку
mysqli_set_charset($connection, DB_CHARSET);
?>