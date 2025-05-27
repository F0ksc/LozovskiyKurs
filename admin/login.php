<?php
session_start();
// Якщо адмін вже залогінений, перенаправляємо на головну адмінки
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error_message = '';
if (isset($_SESSION['login_error'])) {
    $error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Видаляємо помилку після показу
}

// Якщо використовуємо окремі стилі для адмінки:
// $admin_css_path = 'assets/style_admin.css';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід в Адмін-панель</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; }
        .login-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 100%; max-width: 400px; }
        .login-container h2 { text-align: center; margin-bottom: 25px; color: #333; }
        .login-container .form-group { margin-bottom: 20px; }
        .login-container label { display: block; margin-bottom: 5px; font-weight: bold; }
        .login-container input[type="text"],
        .login-container input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .login-container .button { width: 100%; background-color: #e67e22; border-color: #e67e22; }
        .login-container .button:hover { background-color: #d35400; }
        .login-container .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Вхід в Адмін-панель "Buzz"</h2>
        <?php if ($error_message): ?>
            <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Ім'я користувача:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login_admin" class="button">Увійти</button>
        </form>
    </div>
</body>
</html>

<?php
// Обробка логіну в цьому ж файлі
if (isset($_POST['login_admin'])) {
    require_once '../includes/db_connect.php'; // Підключення до БД

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Будь ласка, введіть ім'я користувача та пароль.";
        header('Location: login.php');
        exit;
    }

    $sql = "SELECT id, username, password_hash FROM admins WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password_hash'])) {
                // Пароль вірний
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                unset($_SESSION['login_error']); // Очищаємо помилку, якщо була
                header('Location: index.php'); // Перенаправлення на головну адмінки
                exit;
            } else {
                // Невірний пароль
                $_SESSION['login_error'] = "Невірне ім'я користувача або пароль.";
            }
        } else {
            // Користувача не знайдено
            $_SESSION['login_error'] = "Невірне ім'я користувача або пароль.";
        }
        $stmt->close();
    } else {
        $_SESSION['login_error'] = "Помилка підготовки запиту: " . $conn->error;
    }
    $conn->close();
    header('Location: login.php');
    exit;
}
?>