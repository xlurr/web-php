<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Проверяем наличие ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Проверяем существование студента
$student = get_student_by_id($id);
if (!$student) {
    header('Location: index.php?message=error');
    exit;
}

// Удаляем студента
if (delete_student($id)) {
    header('Location: index.php?message=deleted');
} else {
    header('Location: index.php?message=error');
}
exit;

mysqli_close($connection);
?>