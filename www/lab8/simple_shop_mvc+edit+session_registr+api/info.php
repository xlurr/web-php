<?php
session_start();

class Counter {
    public function get(): int {
        $_SESSION['count'] = ($_SESSION['count'] ?? 0) + 1;
        return $_SESSION['count'];
    }
}

$count = (new Counter())->get();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Счётчик</title>
</head>
<body>
    <p>Вы заходили на сайт <?= $count ?> раза.</p>
</body>
</html>





<?php
class Calculator {
    public function add($a, $b) { return $a + $b; }
    public function sub($a, $b) { return $a - $b; }
    public function mul($a, $b) { return $a * $b; }
    public function div($a, $b) { return $b ? $a / $b : 'Ошибка'; }
}

$calc = new Calculator();
?>
<h3>1. Калькулятор</h3>
<p>5+3=<?= $calc->add(5,3) ?></p>
<p>10-4=<?= $calc->sub(10,4) ?></p>
<p>6*7=<?= $calc->mul(6,7) ?></p>
<p>15/3=<?= $calc->div(15,3) ?></p>[web:1]




<?php
class User {
    private string $password;
    
    public function __construct(string $password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function check(string $password): bool {
        return password_verify($password, $this->password);
    }
}

$user = new User('secret123');
?>
<h3>3. Пользователь</h3>
<p>Пароль верный: <?= $user->check('secret123') ? 'Да' : 'Нет' ?></p>[web:3]





<?php
class Cart {
    private array $items = [];
    
    public function add($name, $price) {
        $this->items[] = ['name' => $name, 'price' => $price];
    }
    
    public function total(): float {
        return array_sum(array_column($this->items, 'price'));
    }
    
    public function count(): int {
        return count($this->items);
    }
}

$cart = new Cart();
$cart->add('Яблоки', 100);
$cart->add('Хлеб', 50);
?>
<h3>2. Корзина</h3>
<p>Товаров: <?= $cart->count() ?>, Сумма: <?= $cart->total() ?> руб.</p>[web:2]





<?php
class FileSaver {
    public function save(string $filename, string $content): bool {
        return file_put_contents($filename, $content) !== false;
    }
}

$saver = new FileSaver();
$msg = '';

if ($_POST) {
    $saved = $saver->save($_POST['file'], $_POST['text']);
    $msg = $saved ? 'Сохранено!' : 'Ошибка!';
}
?>

<form method="POST">
    <input name="file" placeholder="file.txt">
    <textarea name="text"></textarea>
    <button>Сохранить</button>
</form>
<?php if ($msg): ?><p><?= $msg ?></p><?php endif; ?>
