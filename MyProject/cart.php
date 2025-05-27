<?php
// Файл: MyProject/cart.php
require_once 'includes/header.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
?>

<main>
    <h2>Ваш Кошик</h2>

    <?php
    if (isset($_SESSION['cart_message'])) {
        echo '<p class="message success">' . htmlspecialchars($_SESSION['cart_message']) . '</p>';
        unset($_SESSION['cart_message']);
    }
    if (isset($_SESSION['cart_message_error'])) {
        echo '<p class="message error">' . htmlspecialchars($_SESSION['cart_message_error']) . '</p>';
        unset($_SESSION['cart_message_error']);
    }
    ?>

    <?php if (!empty($cart_items)): ?>
        <form action="cart_actions.php" method="post"> 
            <input type="hidden" name="action" value="update_all"> 
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Зображення</th>
                        <th>Назва товару</th>
                        <th>Ціна за од.</th>
                        <th style="width: 120px;">Кількість</th> 
                        <th>Сума</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item_id => $item): ?>
                        <?php
                        $item_subtotal = $item['price'] * $item['quantity'];
                        $total_price += $item_subtotal;
                        ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['image_url']) && file_exists('assets/' . $item['image_url'])): ?>
                                    <img src="/MyProject/assets/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                                <?php else: ?>
                                    <img src="/MyProject/assets/images/placeholder.png" alt="Зображення відсутнє" class="cart-item-image">
                                <?php endif; ?>
                            </td>
                            <td><a href="box_detail.php?id=<?php echo $item_id; ?>"><?php echo htmlspecialchars($item['name']); ?></a></td>
                            <td><?php echo number_format($item['price'], 2, ',', ' '); ?> грн</td>
                            <td>

                                <input type="number" name="quantities[<?php echo $item_id; ?>]" value="<?php echo $item['quantity']; ?>" min="0" max="99" class="cart-item-quantity-input">
                              
                            </td>
                            <td><?php echo number_format($item_subtotal, 2, ',', ' '); ?> грн</td>
                            <td>
                              
                                <a href="cart_actions.php?action=remove&box_id=<?php echo $item_id; ?>" class="remove-item-link" onclick="return confirm('Ви впевнені, що хочете видалити цей товар з кошика?');">Видалити</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;">
                            <button type="submit" class="button-secondary update-cart-button">Оновити кошик</button>
                        </td>
                        <td style="text-align:right;"><strong>Загальна сума:</strong></td>
                        <td colspan="2"><strong><?php echo number_format($total_price, 2, ',', ' '); ?> грн</strong></td>
                    </tr>
                </tfoot>
            </table>
        </form> 

        <div class="cart-actions-footer">
            <a href="catalog.php" class="button-secondary">Продовжити покупки</a>
            <a href="checkout.php" class="button checkout-button">Оформити замовлення</a>
        </div>

    <?php else: ?>
        <p>Ваш кошик порожній.</p>
        <p><a href="catalog.php" class="button">Перейти до каталогу</a></p>
    <?php endif; ?>
</main>

<?php
require_once 'includes/footer.php';
?>