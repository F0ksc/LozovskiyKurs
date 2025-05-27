<?php
// Файл: MyProject/cart_actions.php
session_start();
require_once 'includes/db_connect.php'; // Для отримання деталей товару при додаванні

// Перевірка, чи кошик ініціалізовано
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обробка POST-запитів (додавання, оновлення всього кошика)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $box_id = isset($_POST['box_id']) ? intval($_POST['box_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if ($quantity <= 0) $quantity = 1;

        if ($box_id > 0) {
            $sql = "SELECT name, price, image_url FROM boxes WHERE id = ? AND is_active = TRUE";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $box_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($product_details = $result->fetch_assoc()) {
                    if (isset($_SESSION['cart'][$box_id])) {
                        $_SESSION['cart'][$box_id]['quantity'] += $quantity;
                    } else {
                        $_SESSION['cart'][$box_id] = [
                            'id' => $box_id,
                            'name' => $product_details['name'],
                            'price' => floatval($product_details['price']),
                            'quantity' => $quantity,
                            'image_url' => $product_details['image_url']
                        ];
                    }
                    $_SESSION['cart_message'] = "Товар \"" . htmlspecialchars($product_details['name']) . "\" додано/оновлено в кошику!";
                } else {
                    $_SESSION['cart_message_error'] = "Помилка: Товар не знайдено.";
                }
                $stmt->close();
            } else {
                $_SESSION['cart_message_error'] = "Помилка підготовки запиту: " . $conn->error;
            }
        }
    } elseif ($action === 'update_all') {
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $box_id_to_update => $new_quantity) {
                $box_id_to_update = intval($box_id_to_update);
                $new_quantity = intval($new_quantity);

                if (isset($_SESSION['cart'][$box_id_to_update])) {
                    if ($new_quantity > 0) {
                        $_SESSION['cart'][$box_id_to_update]['quantity'] = $new_quantity;
                    } else {
                        // Якщо нова кількість 0 або менше, видаляємо товар
                        unset($_SESSION['cart'][$box_id_to_update]);
                    }
                }
            }
            $_SESSION['cart_message'] = "Кошик оновлено!";
        }
    }
    // Після POST-дій перенаправляємо на сторінку кошика
    header("Location: cart.php");
    exit();

// Обробка GET-запитів (видалення)
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'remove') {
        $box_id_to_remove = isset($_GET['box_id']) ? intval($_GET['box_id']) : 0;
        if ($box_id_to_remove > 0 && isset($_SESSION['cart'][$box_id_to_remove])) {
            $removed_item_name = $_SESSION['cart'][$box_id_to_remove]['name'];
            unset($_SESSION['cart'][$box_id_to_remove]);
            $_SESSION['cart_message'] = "Товар \"" . htmlspecialchars($removed_item_name) . "\" видалено з кошика.";
        } else {
            $_SESSION['cart_message_error'] = "Помилка: Не вдалося видалити товар.";
        }
        // Після GET-дій перенаправляємо на сторінку кошика
        header("Location: cart.php");
        exit();
    }
    // Якщо невідома GET дія
    header("Location: cart.php");
    exit();

} else {
    // Якщо хтось зайшов на cart_actions.php напряму без POST/GET або потрібних параметрів
    header("Location: index.php");
    exit();
}
?>