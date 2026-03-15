<?php
class AuthController extends Controller {
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
    }
    
    public function login() {
        // Если пользователь уже авторизован, перенаправляем на заказы
        if ($this->auth->isLoggedIn()) {
            $this->redirect('home');
        }
        
        $error = '';
        
        // Обработка формы входа
        if ($_POST && isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            if ($this->auth->login($username, $password)) {
                $this->redirect('orders');
            } else {
                $error = "Неверное имя пользователя или пароль!";
            }
        }
        
        $this->showLoginPage($error);
    }

    
        // Страница регистрации
    
      public function register() {
        // Если пользователь уже авторизован, перенаправляем на заказы
        if ($this->auth->isLoggedIn()) {
            $this->redirect('orders');
        }

        $error = '';
        $success = '';
        $userModel = new User($this->db);
        $customers = $userModel->getAvailableCustomers();

        // Обработка формы регистрации
        if ($_POST && isset($_POST['register'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $customer_id = !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null;

            // Проверка подтверждения пароля
            if ($password !== $password_confirm) {
                $error = "Пароли не совпадают!";
            } else {
                // Регистрация пользователя
                $result = $userModel->register($username, $email, $password, $customer_id);

                if ($result === true) {
                    $success = "Регистрация успешна! Теперь вы можете войти в систему.";
                    // Очищаем форму
                    $_POST = [];
                } else {
                    switch ($result) {
                        case 'user_exists':
                            $error = "Пользователь с таким именем или email уже существует!";
                            break;
                        case 'invalid_email':
                            $error = "Неверный формат email!";
                            break;
                        case 'weak_password':
                            $error = "Пароль должен содержать минимум 6 символов!";
                            break;
                        case 'customer_creation_failed':
                            $error = "Ошибка при создании профиля покупателя. Попробуйте еще раз.";
                            break;
                        default:
                            $error = "Ошибка при регистрации. Попробуйте еще раз.";
                    }
                }
            }
        }

        $this->showRegisterPage($error, $success, $customers);
    }
    
    public function logout() {
        $this->auth->logout();
        $this->redirect('home');
    }
    
    public function accessdenied() {
        $this->view('auth/access_denied');
    }
    
    private function showLoginPage($error = '') {
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Вход в систему - Магазин</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { 
                    padding: 20px; 
                    background-color: #f8f9fa;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                .auth-container { 
                    max-width: 400px; 
                    width: 100%;
                }
            </style>
        </head>
        <body>
            <div class="auth-container">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">🔐 Вход в систему</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Имя пользователя или Email</label>
                                <input type="text" name="username" class="form-control" required 
                                       value="<?php echo $_POST['username'] ?? ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Пароль</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="login" class="btn btn-primary btn-lg">
                                    Войти
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <p>Нет аккаунта? <a href="index.php?page=register">Зарегистрироваться</a></p>
                        </div>

                        <div class="mt-4">
                            <h6>Тестовые пользователи:</h6>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-1"><strong>Администратор:</strong></p>
                                    <p class="mb-1">Логин: <code>admin</code></p>
                                    <p class="mb-3">Пароль: <code>password</code></p>
                                    
                                    <p class="mb-1"><strong>Пользователь (Иван):</strong></p>
                                    <p class="mb-1">Логин: <code>user1</code></p>
                                    <p class="mb-0">Пароль: <code>password</code></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    private function showRegisterPage($error = '', $success = '', $customers = null) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Регистрация - Магазин</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { 
                padding: 20px; 
                background-color: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
            .auth-container { 
                max-width: 500px; 
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="card">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">📝 Регистрация</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Имя пользователя *</label>
                            <input type="text" name="username" class="form-control" required 
                                   value="<?php echo $_POST['username'] ?? ''; ?>"
                                   minlength="3" maxlength="50">
                            <div class="form-text">От 3 до 50 символов</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required 
                                   value="<?php echo $_POST['email'] ?? ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Привязать к существующему покупателю (необязательно)</label>
                            <select name="customer_id" class="form-control">
                                <option value="">-- Создать нового покупателя --</option>
                                <?php while ($customer = $customers->fetch_assoc()): ?>
                                    <option value="<?php echo $customer['id']; ?>" 
                                        <?php echo (isset($_POST['customer_id']) && $_POST['customer_id'] == $customer['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($customer['name']); ?> (<?php echo htmlspecialchars($customer['email']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text">
                                Если вы уже являетесь покупателем, выберите себя из списка. 
                                Иначе будет создан новый профиль покупателя.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Пароль *</label>
                            <input type="password" name="password" class="form-control" required
                                   minlength="6">
                            <div class="form-text">Минимум 6 символов</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Подтверждение пароля *</label>
                            <input type="password" name="password_confirm" class="form-control" required
                                   minlength="6">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" name="register" class="btn btn-success btn-lg">
                                Зарегистрироваться
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p>Уже есть аккаунт? <a href="index.php?page=login">Войти</a></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
}
?>