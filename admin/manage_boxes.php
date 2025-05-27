<?php
// Файл: MyProject/admin/manage_boxes.php
require_once 'includes/admin_header.php'; // Включає auth_check.php та db_connect.php

// Логіка для отримання списку боксів
$boxes_sql = "SELECT 
                b.id, 
                b.name AS box_name, 
                b.price, 
                b.is_active,
                c.name AS category_name, 
                s.name AS size_name 
              FROM boxes b
              JOIN categories c ON b.category_id = c.id
              JOIN sizes s ON b.size_id = s.id
              ORDER BY b.id DESC";
$boxes_result = $conn->query($boxes_sql);

?>
<h2>Управління Подарунковими Боксами</h2>

<p><a href="add_box.php" class="button" style="display: inline-block; margin-bottom: 20px; background-color: #27ae60; color:white;">+ Додати Новий Бокс</a></p>

<?php
if (isset($_SESSION['box_action_message'])) {
    echo '<p class="message success">' . htmlspecialchars($_SESSION['box_action_message']) . '</p>';
    unset($_SESSION['box_action_message']);
}
if (isset($_SESSION['box_action_error'])) {
    echo '<p class="message error">' . htmlspecialchars($_SESSION['box_action_error']) . '</p>';
    unset($_SESSION['box_action_error']);
}
?>

<?php if ($boxes_result && $boxes_result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Назва Боксу</th>
                <th>Категорія</th>
                <th>Розмір</th>
                <th>Ціна</th>
                <th>Активний</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php while($box = $boxes_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $box['id']; ?></td>
                <td><?php echo htmlspecialchars($box['box_name']); ?></td>
                <td><?php echo htmlspecialchars($box['category_name']); ?></td>
                <td><?php echo htmlspecialchars($box['size_name']); ?></td>
                <td><?php echo number_format($box['price'], 2, ',', ' '); ?> грн</td>
                <td><?php echo $box['is_active'] ? 'Так' : 'Ні'; ?></td>
                <td class="action-links">
                    <a href="edit_box.php?id=<?php echo $box['id']; ?>" class="button-secondary" style="padding: 5px 10px; font-size: 0.9em;">Редагувати</a>
                    <a href="box_actions.php?action=delete&id=<?php echo $box['id']; ?>" 
                       class="button-danger" style="padding: 5px 10px; font-size: 0.9em; background-color: #c0392b; color:white;"
                       onclick="return confirm('Ви впевнені, що хочете видалити цей бокс? Цю дію неможливо буде скасувати.');">Видалити</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Наразі немає жодного боксу. <a href="add_box.php">Додати перший бокс?</a></p>
<?php endif; ?>

<?php
$conn->close();
require_once 'includes/admin_footer.php';
?>