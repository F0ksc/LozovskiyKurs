<?php
require_once 'auth_check.php'; // Перевірка авторизації
require_once '../includes/db_connect.php'; // Підключення до БД (може знадобитись)

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмін-панель "Buzz"</title>
    <link rel="stylesheet" href="../assets/css/style.css">  
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .admin-body { display: flex; flex-grow: 1; }
        .admin-sidebar { width: 250px; background-color: #2c3e50; color: #ecf0f1; padding: 20px; min-height: 100%; }
        .admin-sidebar h3 { margin-top: 0; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        .admin-sidebar ul { list-style: none; padding: 0; margin: 0; }
        .admin-sidebar ul li a { display: block; color: #ecf0f1; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-bottom: 5px; }
        .admin-sidebar ul li a:hover, .admin-sidebar ul li a.active { background-color: #e67e22; color: #2c3e50; }
        .admin-main-content { flex-grow: 1; padding: 25px; background-color: #f8f9fa; }
        .admin-header-bar { background-color: #34495e; color: #fff; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .admin-header-bar a { color: #fff; }
        .admin-footer-bar { background-color: #34495e; color: #bdc3c7; text-align: center; padding: 10px 0; font-size: 0.9em; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #e9ecef; }
        .action-links a { margin-right: 10px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom:15px; border-radius:4px;}
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom:15px; border-radius:4px;}

    </style>
</head>
<body class="admin-body-wrapper">
    <div class="admin-header-bar">
        <span>Адмін-панель "Buzz" - Ласкаво просимо, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
        <a href="logout.php">Вийти</a>
    </div>
    <div class="admin-body">
        <aside class="admin-sidebar">
            <h3>Меню</h3>
            <ul>
                <li><a href="index.php" class="<?php echo ($current_page === 'index.php' || $current_page === 'manage_orders.php') ? 'active' : ''; ?>">Замовлення</a></li>
                <li><a href="manage_boxes.php" class="<?php echo ($current_page === 'manage_boxes.php') ? 'active' : ''; ?>">Подарункові Бокси</a></li>

            </ul>
        </aside>
        <main class="admin-main-content">
