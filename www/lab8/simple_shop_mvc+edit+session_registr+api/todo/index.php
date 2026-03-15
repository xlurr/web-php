<?php
session_start();
require 'models/Todo.php';

$todo = new Todo();
$action = $_GET['action'] ?? '';

if ($_POST && $action === 'add') {
    $todo->add(trim($_POST['task']));
    header('Location: index.php');
    exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
    $todo->delete((int)$_GET['id']);
    header('Location: index.php');
    exit;
}

$tasks = $todo->all();
include 'views/todo.php';
?>
