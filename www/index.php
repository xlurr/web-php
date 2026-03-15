<?php
echo "<h1>PHP работает через Docker hui test (как WAMP) 🚀</h1>";

$servername = "db";
$username = "user";
$password = "userpass";
$database = "mydb";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
echo "<p>Успешное подключение к MySQL!</p>";

?>