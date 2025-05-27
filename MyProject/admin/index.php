<?php
// Файл: MyProject/admin/index.php
require_once 'includes/admin_header.php'; 

// Логіка для отримання замовлень
$orders_sql = "SELECT o.id, o.customer_name, o.customer_phone, o.delivery_address, o.total_amount, o.status, o.created_at,
                      GROUP_CONCAT(CONCAT(b.name, ' (', oi.quantity, ' шт.)') SEPARATOR '<br>') AS items_ordered
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN boxes b ON oi.box_id = b.id
                GROUP BY o.id
                ORDER BY o.created_at DESC";
$orders_result = $conn->query($orders_sql);

?>
<h2>Список Замовлень</h2>

<?php
if (isset($_SESSION['admin_message'])) {
    echo '<p class="message success">' . htmlspecialchars($_SESSION['admin_message']) . '</p>';
    unset($_SESSION['admin_message']);
}
if (isset($_SESSION['admin_error'])) {
    echo '<p class="message error">' . htmlspecialchars($_SESSION['admin_error']) . '</p>';
    unset($_SESSION['admin_error']);
}
?>

<?php if ($orders_result && $orders_result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Клієнт</th>
                <th>Телефон</th>
                <th>Адреса</th>
                <th>Товари</th>
                <th>Сума</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php while($order = $orders_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo date("d.m.Y H:i", strtotime($order['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></td>
                <td><?php echo $order['items_ordered']; ?></td> 
                <td><?php echo number_format($order['total_amount'], 2, ',', ' '); ?> грн</td>
                <td>
                    <form action="manage_orders.php" method="post" style="display:inline;"> 
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="new_status" onchange="this.form.submit()">
                            <option value="new" <?php if($order['status'] == 'new') echo 'selected'; ?>>Нове</option>
                            <option value="processing" <?php if($order['status'] == 'processing') echo 'selected'; ?>>В обробці</option>
                            <option value="shipped" <?php if($order['status'] == 'shipped') echo 'selected'; ?>>Відправлено</option>
                            <option value="delivered" <?php if($order['status'] == 'delivered') echo 'selected'; ?>>Доставлено</option>
                            <option value="cancelled" <?php if($order['status'] == 'cancelled') echo 'selected'; ?>>Скасовано</option>
                        </select>

                    </form>
                </td>
                <td>
                     <a href="order_details_admin.php?id=<?php echo $order['id']; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Наразі немає активних замовлень.</p>
<?php endif; ?>

<?php
$conn->close(); // Закриваємо з'єднання з БД
require_once 'includes/admin_footer.php';
?>