<?php
// Файл: MyProject/order_success.php
require_once 'includes/header.php'; // тут вже є session_start()

$order_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : null;
unset($_SESSION['last_order_id']); // Видаляємо ID з сесії після використання

?>

<main>
    <div class="order-success-container">
        <?php if ($order_id): ?>
            <h2>Дякуємо за ваше замовлення!</h2>
            <p>Ваше замовлення № <strong><?php echo htmlspecialchars($order_id); ?></strong> успішно оформлено.</p>
            <p>Наш менеджер зв'яжеться з вами найближчим часом для уточнення деталей оплати та доставки.</p>
        <?php else: ?>
            <h2>Замовлення оформлено!</h2>
            <p>Дякуємо, що обрали нас! Наш менеджер зв'яжеться з вами найближчим часом.</p>
            <p>(Не вдалося отримати номер вашого замовлення з сесії. Будь ласка, зв'яжіться з нами, якщо у вас є питання.)</p>
        <?php endif; ?>
        
        <p style="margin-top: 30px;">
            <a href="catalog.php" class="button">Продовжити покупки</a>
        </p>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>