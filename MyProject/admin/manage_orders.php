<?php
// Файл: MyProject/admin/manage_orders.php
require_once 'includes/auth_check.php';
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    $allowed_statuses = ['new', 'processing', 'shipped', 'delivered', 'cancelled'];

    if (in_array($new_status, $allowed_statuses) && $order_id > 0) {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $new_status, $order_id);
            if ($stmt->execute()) {
                $_SESSION['admin_message'] = "Статус замовлення #{$order_id} оновлено на '{$new_status}'.";
            } else {
                $_SESSION['admin_error'] = "Помилка оновлення статусу: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['admin_error'] = "Помилка підготовки запиту: " . $conn->error;
        }
    } else {
        $_SESSION['admin_error'] = "Невірні дані для оновлення статусу.";
    }
}
$conn->close();
header('Location: index.php'); // Повертаємося на сторінку зі списком замовлень
exit;
?>