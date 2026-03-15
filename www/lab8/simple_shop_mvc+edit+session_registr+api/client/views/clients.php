<!DOCTYPE html>
<html>
<head>
    <title>Клиенты</title>
</head>
<body>    
    <table>
        <tr><th>ID</th><th>Имя</th><th>Email</th><th>Телефон</th></tr>
        <?php foreach($clients as $client): ?>
        <tr>
            <td><?=$client['id']?></td>
            <td><?=$client['name']?></td>
            <td><?=$client['email']?></td>
            <td><?=$client['phone']?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
