<?php

require_once 'includes/header.php'; // тут вже є session_start() та db_connect.php
?>

<main>
    <h2>Наш Каталог Подарункових Боксів</h2>

    <div class="product-grid">
        <?php
        // SQL-запит для вибірки всіх активних боксів
        // Ми також отримуємо назви категорії та розміру через JOIN
        $sql = "SELECT 
                    b.id, 
                    b.name AS box_name, 
                    b.description, 
                    b.contents, 
                    b.price, 
                    b.image_url, 
                    c.name AS category_name, 
                    s.name AS size_name 
                FROM boxes b
                JOIN categories c ON b.category_id = c.id
                JOIN sizes s ON b.size_id = s.id
                WHERE b.is_active = TRUE
                ORDER BY c.name, s.sort_order, b.name"; // Сортуємо для кращого вигляду

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            // Виводимо дані кожного боксу
            while($box = $result->fetch_assoc()) {
        ?>
                <div class="product-card">
                    <?php if (!empty($box['image_url']) && file_exists('assets/' . $box['image_url'])): // Перевіряємо чи існує файл ?>
                        <img src="/MyProject/assets/<?php echo htmlspecialchars($box['image_url']); ?>" alt="<?php echo htmlspecialchars($box['box_name']); ?>" class="product-image">
                    <?php else: ?>
                        <img src="/MyProject/assets/images/placeholder.png" alt="Зображення відсутнє" class="product-image"> <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($box['box_name']); ?></h3>
                    <p class="product-category-size">
                        <strong>Категорія:</strong> <?php echo htmlspecialchars($box['category_name']); ?><br>
                        <strong>Розмір:</strong> <?php echo htmlspecialchars($box['size_name']); ?>
                    </p>
                    <p class="product-description"><?php echo nl2br(htmlspecialchars($box['description'])); ?></p>
                    <p class="product-contents"><strong>Склад:</strong> <?php echo nl2br(htmlspecialchars($box['contents'])); ?></p>
                    <p class="product-price">Ціна: <?php echo number_format($box['price'], 2, ',', ' '); ?> грн</p>
                    
                    <a href="box_detail.php?id=<?php echo $box['id']; ?>" class="button">Детальніше</a>
                </div>
        <?php
            }
        } else {
            echo "<p>На жаль, наразі немає доступних боксів у каталозі.</p>";
        }
        // Закриваємо з'єднання, якщо воно більше не потрібне на цій сторінці
        // $conn->close(); // Зазвичай це не обов'язково, якщо скрипт завершується
        ?>
    </div>
</main>

<?php
// Підключаємо підвал сайту
require_once 'includes/footer.php';
?>