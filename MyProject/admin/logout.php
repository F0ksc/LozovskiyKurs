<?php
// Файл: MyProject/admin/logout.php
session_start();

// Видаляємо всі змінні сесії, що стосуються адміна
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

// Перенаправляємо на сторінку входу
$_SESSION['login_error'] = "Ви успішно вийшли з системи."; // Опціональне повідомлення
header('Location: login.php');
exit;
?>