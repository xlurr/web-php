<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>TODO MVC</title>
    <style>body{font-family:Arial; max-width:500px; margin:50px auto;}</style>
</head>
<body>
    <h1>TODO Список (MVC)</h1>
    
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="task" placeholder="Новая задача" required>
        <button type="submit">Добавить</button>
    </form>
    
    <ul>
        <?php foreach($tasks as $i => $task): ?>
            <li>
                <?= htmlspecialchars($task) ?>
                <a href="?action=delete&id=<?= $i ?>">✕</a>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <p>Задач: <?= count($tasks) ?></p>
</body>
</html>
