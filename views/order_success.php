<?php
session_start(); // Запускаем сессию для доступа к данным пользователя

// Проверка авторизации пользователя
if (!isset($_SESSION['id'])) {
    // Если пользователь не авторизован, перенаправляем на страницу авторизации
    echo '<script>window.location.href="index.php?page=auth";</script>';
    exit;
}

// Проверка наличия идентификатора заказа в параметрах запроса
if (!isset($_GET['order_id'])) {
    // Если параметр order_id отсутствует, перенаправляем на страницу каталога
    echo '<script>window.location.href="index.php?page=catalog";</script>';
    exit;
}

$order_id = intval($_GET['order_id']); // Приводим order_id к целому числу

// Получаем информацию о заказе, проверяя, что заказ принадлежит текущему пользователю
$stmt = $db->dbs->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['id']]);
$order = $stmt->fetch();

if (!$order) {
    // Если заказ не найден или не принадлежит пользователю, перенаправляем на страницу каталога
    echo '<script>window.location.href="index.php?page=catalog";</script>';
    exit;
}
?>

<!-- Стили для страницы успешного оформления заказа -->
<style>
    .success-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 30px;
        background-color: #f8f8f8;
        border-radius: 10px;
        text-align: center;
    }
    
    .success-icon {
        color: #2ecc71;
        font-size: 80px;
        margin-bottom: 20px;
    }
    
    .success-title {
        font-size: 28px;
        margin-bottom: 15px;
        color: #333;
    }
    
    .order-number {
        font-size: 20px;
        margin-bottom: 30px;
        color: #666;
    }
    
    .continue-btn {
        display: inline-block;
        background-color: #A6B1B4;
        color: black;
        text-decoration: none;
        padding: 15px 30px;
        border-radius: 5px;
        font-size: 18px;
        margin-top: 20px;
    }
</style>

<section>
<!-- Разметка страницы успешного оформления заказа -->
<div class="success-container">
    <div class="success-icon">✓</div>
    <h2 class="success-title">Заказ успешно оформлен!</h2>
    <!-- Вывод номера заказа -->
    <p class="order-number">Номер вашего заказа: <?php echo $order_id; ?></p>
    <p>Спасибо за ваш заказ. Мы свяжемся с вами в ближайшее время для подтверждения.</p>
    <!-- Ссылка для продолжения покупок -->
    <a href="index.php?page=catalog" class="continue-btn">Продолжить покупки</a>
</div>
</section>