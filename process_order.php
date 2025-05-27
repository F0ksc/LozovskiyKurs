<?php
// Файл: MyProject/process_order.php
session_start();
require_once 'includes/db_connect.php'; // Підключення до БД

// Перевірка, чи запит методом POST і чи кошик не порожній
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    // Якщо не POST або кошик порожній, перенаправляємо на головну або кошик
    header('Location: cart.php');
    exit();
}

// 1. Отримання та валідація даних з форми
$customer_name = trim($_POST['customer_name'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? ''); // Необов'язкове поле
$delivery_address = trim($_POST['delivery_address'] ?? '');
$notes = trim($_POST['notes'] ?? ''); // Необов'язкове поле
$total_amount = floatval($_POST['total_amount'] ?? 0); // Отримуємо загальну суму з форми

$errors = [];
$_SESSION['form_data'] = $_POST; // Зберігаємо дані форми для повторного заповнення у разі помилки

// Валідація обов'язкових полів
if (empty($customer_name)) {
    $errors[] = "Ім'я є обов'язковим для заповнення.";
}
if (empty($customer_phone)) {
    $errors[] = "Номер телефону є обов'язковим для заповнення.";
} elseif (!preg_match('/^\+?[0-9\s\-\(\)]{7,20}$/', $customer_phone)) { // Проста перевірка формату телефону
    $errors[] = "Некоректний формат номеру телефону.";
}
if (empty($delivery_address)) {
    $errors[] = "Адреса доставки є обов'язковою для заповнення.";
}

// Валідація email, якщо він вказаний
if (!empty($customer_email) && !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Некоректний формат Email.";
}

// Перевірка, чи загальна сума збігається з розрахованою сумою кошика (захист від підміни)
$calculated_cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $calculated_cart_total += $item['price'] * $item['quantity'];
}
if (abs($total_amount - $calculated_cart_total) > 0.01) { // Порівняння float з допуском
    $errors[] = "Загальна сума замовлення не збігається з вмістом кошика. Будь ласка, спробуйте ще раз.";
    // Можна також очистити кошик або зробити додаткову логіку безпеки
}


// 2. Якщо є помилки валідації, повертаємо на сторінку оформлення
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header('Location: checkout.php');
    exit();
}

// 3. Збереження замовлення в базу даних (використовуємо транзакції)
$conn->begin_transaction();

try {
    // Вставка даних в таблицю 'orders'
    $sql_order = "INSERT INTO orders (customer_name, customer_phone, customer_email, delivery_address, total_amount, status, notes) 
                  VALUES (?, ?, ?, ?, ?, 'new', ?)";
    $stmt_order = $conn->prepare($sql_order);
    if ($stmt_order === false) {
        throw new Exception("Помилка підготовки запиту для orders: " . $conn->error);
    }
    $stmt_order->bind_param("ssssds", $customer_name, $customer_phone, $customer_email, $delivery_address, $total_amount, $notes);
    
    if (!$stmt_order->execute()) {
        throw new Exception("Помилка виконання запиту для orders: " . $stmt_order->error);
    }
    
    $order_id = $conn->insert_id; // Отримуємо ID щойно створеного замовлення
    $stmt_order->close();

    // Вставка даних в таблицю 'order_items' для кожного товару в кошику
    $sql_order_item = "INSERT INTO order_items (order_id, box_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_order_item);
    if ($stmt_item === false) {
        throw new Exception("Помилка підготовки запиту для order_items: " . $conn->error);
    }

    foreach ($_SESSION['cart'] as $box_id => $item) {
        $stmt_item->bind_param("iiid", $order_id, $box_id, $item['quantity'], $item['price']);
        if (!$stmt_item->execute()) {
            throw new Exception("Помилка виконання запиту для order_items (box_id: $box_id): " . $stmt_item->error);
        }
    }
    $stmt_item->close();

    // Якщо все успішно, підтверджуємо транзакцію
    $conn->commit();

    // 4. Очищення кошика та даних форми з сесії
    unset($_SESSION['cart']);
    unset($_SESSION['form_data']); // Також очищуємо дані форми, бо вони більше не потрібні
    unset($_SESSION['form_errors']); // І помилки

    // 5. Перенаправлення на сторінку успішного замовлення
    $_SESSION['last_order_id'] = $order_id; // Зберігаємо ID замовлення для сторінки успіху
    header('Location: order_success.php');
    exit();

} catch (Exception $e) {
    // Якщо сталася помилка, відкочуємо транзакцію
    $conn->rollback();
    
    // Зберігаємо повідомлення про помилку для відображення
    $_SESSION['form_errors'] = ["Сталася помилка під час обробки замовлення: " . $e->getMessage() . " Будь ласка, спробуйте ще раз або зв'яжіться з нами."];
    // Повертаємо на сторінку оформлення
    header('Location: checkout.php');
    exit();
}

?>