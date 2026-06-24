<?php
session_start(); // Запускаем сессию для доступа к данным пользователя и корзины

// Проверка авторизации пользователя
if (!isset($_SESSION['id'])) {
    // Если пользователь не авторизован, перенаправляем на страницу авторизации
    echo '<script>window.location.href="index.php?page=auth";</script>';
    exit;
}

// Инициализация корзины, если она ещё не создана в сессии
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка запроса на обновление количества товара через скрытый iframe
if (isset($_GET['iframe']) && $_GET['iframe'] == 1 && isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['quantity'])) {
    $product_id = intval($_GET['id']);     // Приводим id товара к целому числу
    $quantity = intval($_GET['quantity']);   // Приводим новое количество к целому числу
    
    // Если новое количество больше 0 и товар уже есть в корзине, обновляем его количество
    if ($quantity > 0 && isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    // Если количество меньше или равно 0, удаляем товар из корзины
    } elseif ($quantity <= 0 && isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    
    // Пересчитываем общую сумму корзины
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Пересчитываем сумму для конкретного товара
    $item_total = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['price'] * $_SESSION['cart'][$product_id]['quantity'] : 0;
    // Получаем текущее количество товара (если он существует)
    $item_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
    
    // Выводим JavaScript для обновления данных на родительской странице
    echo '<script>
        window.parent.updateCart(' . json_encode($total) . ', ' . json_encode($item_total) . ', ' . json_encode($item_quantity) . ', ' . json_encode($product_id) . ');
    </script>';
    exit;
}

// Обработка запроса на добавление товара в корзину
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // Приводим id товара к целому числу
    
    // Проверяем наличие товара в базе данных
    $stmt = $db->dbs->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Если товар уже есть в корзине, увеличиваем его количество
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            // Если товара нет, добавляем его в корзину с количеством 1
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $product['nam_products'],
                'price' => $product['price'],
                'image' => $product['image_product'],
                'quantity' => 1,
                'categories_id' => $product['categoryid']
            ];
        }
    }
    
    // Перенаправляем на страницу корзины после добавления товара
    echo '<script>window.location.href="index.php?page=basket";</script>';
    exit;
}

// Обработка запроса на удаление товара из корзины
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // Приводим id товара к целому числу
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); // Удаляем товар из корзины
    }
    // Перенаправляем на страницу корзины после удаления товара
    echo '<script>window.location.href="index.php?page=basket";</script>';
    exit;
}

// Обработка оформления заказа (Checkout)
if (isset($_POST['checkout'])) {
    if (count($_SESSION['cart']) > 0) { // Проверяем, что корзина не пуста
        // Проверка наличия достаточного количества товаров
        $errors = [];
        $product_ids = array_keys($_SESSION['cart']);
        if (!empty($product_ids)) {
            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
            $stmt = $db->dbs->prepare("SELECT id, stock FROM products WHERE id IN ($placeholders)");
            $stmt->execute($product_ids);
            $products_stock = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // id => stock
        } else {
            $products_stock = [];
        }

        foreach ($_SESSION['cart'] as $product_id => $item) {
            $required = $item['quantity'];
            $available = $products_stock[$product_id] ?? 0;
            if ($required > $available) {
                $errors[] = "Товар '{$item['name']}': доступно $available шт., запрошено $required.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['checkout_errors'] = $errors;
            echo '<script>window.location.href="index.php?page=basket";</script>';
            exit;
        }
        $user_id = $_SESSION['id']; // Получаем id пользователя из сессии
        $total_price = 0;
        // Считаем общую сумму заказа
        foreach ($_SESSION['cart'] as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }
        $adres = trim($_POST['adres']);
        $delivery_method = trim($_POST['delivery_method']);
        // Добавляем новый заказ в таблицу orders
        $stmt = $db->dbs->prepare("INSERT INTO orders (user_id, total_price, status, created_at, adres, delivery_method) VALUES (?, ?, 'Ожидание', NOW(), ?, ?)");
        $stmt->execute([$user_id, $total_price, $adres, $delivery_method]);
        $order_id = $db->dbs->lastInsertId(); // Получаем id нового заказа
        
        // Добавляем каждый товар заказа в таблицу order_items
        $stmt = $db->dbs->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, categories_id) VALUES (?, ?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([
                $order_id, 
                $item['id'], 
                $item['quantity'], 
                $item['price'],
                $item['categories_id']
            ]);
            // Здесь реализуется логика уменьшения stock
            $stmtUpdate = $db->dbs->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmtUpdate->execute([$item['quantity'], $item['id']]);
        }
        $_SESSION['cart'] = []; // Очищаем корзину после оформления заказа

        // Перенаправляем пользователя на страницу успешного оформления заказа
        echo '<script>window.location.href="index.php?page=order_success&order_id=' . $order_id . '";</script>';
        exit;
    }
}

if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = []; // Очищаем корзину
    // Перенаправляем пользователя на страницу корзины после очистки
    echo '<script>window.location.href="index.php?page=basket";</script>';
    exit;
}
?>

<!-- Скрытый iframe для отправки форм без перезагрузки страницы -->
<iframe name="hiddenFrame" style="display:none;"></iframe>

<!-- Стили для оформления корзины -->
<style>
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    .cart-table th, .cart-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .cart-table th {
        background-color: white;
    }
    .cart-table td {
        background-color: white ;
    }
    .cart-image {
        width: 100px;
        height: auto;
    }
    .quantity-control {
        align-items: center;
    }
    .quantity-btn {
        width: 30px;
        height: 30px;
        background-color: #A6B1B4;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    .quantity-input {
        width: 40px;
        height: 30px;
        text-align: center;
        margin: 0 10px;
    }
    .remove-btn {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
    }
    .checkout-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px;
        background-color: white;
        border-radius: 10px;
    }
    .total-price {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
    }
    .checkout-btn {
        background-color: #2ecc71;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 5px;
        font-size: 18px;
        cursor: pointer;
        margin-top: 10px;
    }
    .clear_cart-btn {
        background-color: #DC3545;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 5px;
        font-size: 18px;
        cursor: pointer; 
        margin-top: 10px;
    }
    .empty-cart {
        text-align: center;
        padding: 50px;
        font-size: 18px;
        color: #666;
    }
    .continue-shopping {
        display: inline-block;
        margin-top: 20px;
        background-color: #A6B1B4;
        color: black;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 5px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    label {
        font-weight: bold;
        margin-right: 10px;
    }
    input[type="text"], select {
        padding: 8px;
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .error-message {
    color: red;
    margin-bottom: 15px;
    padding: 10px;
    background-color: #ffe6e6;
    border: 1px solid #ffcccc;
    border-radius: 5px;
}


</style>

<!-- JavaScript функции для управления корзиной -->
<script>
function updateCart(total, item_total, quantity, productId) {
    // Обновляем значение поля количества товара
    var quantityInput = document.getElementById('quantity-' + productId);
    if(quantityInput) {
        quantityInput.value = quantity;
    }
    // Обновляем стоимость для конкретного товара
    var itemTotalElem = document.getElementById('item-total-' + productId);
    if(itemTotalElem) {
        itemTotalElem.innerText = item_total + ' ₽';
    }
    // Обновляем общую сумму корзины
    var totalPriceElem = document.getElementById('total-price');
    if(totalPriceElem) {
        totalPriceElem.innerText = total;
    }
}

// Функция, вызываемая перед отправкой формы обновления количества товара
function submitUpdate(form, operator, productId) {
    var quantityInput = document.getElementById('quantity-' + productId);
    var currentQuantity = parseInt(quantityInput.value);
    // Вычисляем новое количество в зависимости от нажатой кнопки (+ или -)
    var newQuantity = operator === 'plus' ? currentQuantity + 1 : currentQuantity - 1;
    if(newQuantity < 1) return false; // Если новое количество меньше 1, обновление не производится
    // Обновляем значение скрытого поля формы с новым количеством
    form.querySelector('input[name="quantity"]').value = newQuantity;
    return true;
}
</script>

<!-- Разметка страницы корзины -->
<section>
    <h2>Корзина</h2>
</section>
<section>
<body style="background-color: #ECF0F1">
<div class="cart-container">
    <?php if (empty($_SESSION['cart'])): ?>
        <!-- Если корзина пуста, выводим сообщение и ссылку для продолжения покупок -->
        <div class="empty-cart">
            <p>Ваша корзина пуста.</p>
            <a href="index.php?page=catalog" class="continue-shopping">Перейти к покупкам</a>
        </div>
    <?php else: ?>

        <?php if (!empty($_SESSION['checkout_errors'])): ?>
    <div class="error-message">
        <?php foreach ($_SESSION['checkout_errors'] as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['checkout_errors']); ?>
<?php endif; ?>
        <!-- Если в корзине есть товары, выводим таблицу с товарами -->
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Наименование</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0; // Инициализируем переменную для общей суммы заказа
                foreach ($_SESSION['cart'] as $item): 
                    $item_total = $item['price'] * $item['quantity']; // Сумма по товару
                    $total += $item_total; // Добавляем к общей сумме
                ?>
                <tr id="item-<?php echo $item['id']; ?>">
                    <td>
                        <!-- Вывод изображения товара -->
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-image">
                    </td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?> ₽</td>
                    <td>
                        <div class="quantity-control">
                            <!-- Форма для уменьшения количества товара -->
                            <form method="get" target="hiddenFrame" style="display:inline;" onsubmit="return submitUpdate(this, 'minus', <?php echo $item['id']; ?>);">
                                <input type="hidden" name="page" value="basket">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="iframe" value="1">
                                <!-- Значение количества передается через скрытое поле -->
                                <input type="hidden" name="quantity" value="<?php echo $item['quantity']; ?>">
                                <button class="quantity-btn" type="submit">-</button>
                            </form>
                            <!-- Поле для отображения текущего количества -->
                            <input type="text" class="quantity-input" id="quantity-<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" readonly>
                            <!-- Форма для увеличения количества товара -->
                            <form method="get" target="hiddenFrame" style="display:inline;" onsubmit="return submitUpdate(this, 'plus', <?php echo $item['id']; ?>);">
                                <input type="hidden" name="page" value="basket">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="iframe" value="1">
                                <input type="hidden" name="quantity" value="<?php echo $item['quantity']; ?>">
                                <button class="quantity-btn" type="submit">+</button>
                            </form>
                        </div>
                    </td>
                    <td id="item-total-<?php echo $item['id']; ?>"><?php echo $item_total; ?> ₽</td>
                    <td>
                        <!-- Ссылка для удаления товара из корзины -->
                        <a href="index.php?page=basket&action=remove&id=<?php echo $item['id']; ?>">
                            <button class="remove-btn">Удалить</button>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    <!-- Блок с итоговой суммой заказа и формой оформления заказа -->
    <div class="checkout-container">
        <div class="total-price">Итого: <span id="total-price"><?php echo $total; ?></span> ₽</div>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <!-- Форма оформления заказа -->
         
        
    
        <form method="post">
            <div class="form-group">
                <label for="adres">Адрес доставки:</label>
                <input type="text" id="adres" name="adres" required>
            </div>
            <div class="form-group">
                <label for="delivery_method">Способ доставки:</label>
                <select id="delivery_method" name="delivery_method" required>
                    <!-- <option value="Выберите способ доставки">Выберите способ доставки</option> -->
                    <option value="Самовывоз">Самовывоз</option>
                    <option value="На дом">На дом</option>
                </select>
            </div>
            <button type="submit" name="checkout" class="checkout-btn">Оформить заказ</button>
        </form>
                    
        <!-- Отдельная форма для очистки корзины -->
        <form method="post">
            <button type="submit" name="clear_cart" class="clear_cart-btn">Очистить корзину</button>
        </form>

        <!-- Скрипт, отвечающее за то, что если мы выбираем способ доставки "Самовывоз" делало строку адреса disabled -->
        <script>
            const selectElement = document.getElementById('delivery_method');
            const inputElement = document.getElementById('adres');

            // Деактивируем поле адреса доставки сразу при загрузке страницы
            inputElement.disabled = true;

            // Добавляем обработчик события изменения значения select
            selectElement.addEventListener('change', function () {
                if (this.value === 'Самовывоз') {
                    inputElement.disabled = true; // Делаем input неактивным
                    inputElement.value = '';
                } else {
                    inputElement.disabled = false; // Включаем input
                }
            });
        </script>
    </div>
        
    <p><a href="index.php?page=catalog" class="continue-shopping">Продолжить покупки</a></p>
    <?php endif; ?>
    </section>

</div>
</body>