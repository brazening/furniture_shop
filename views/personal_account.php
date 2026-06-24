<?php
session_start()
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <style>
        
        .container_personal_account {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .navbar {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .navbar a {
            flex: 1;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .navbar a:hover {
            background-color: #eee;
        }
        .navbar a.active {
            background-color: #007BFF;
            color: #fff;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }

        .button_save {
            background-color: #A6B1B4;
            width: 250px;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: auto; /* Кнопка всегда прижимается вниз */
        }

        .button_delete_avatar {
            /* background-color: #A6B1B4; */
            /* width: 100px; */
            border: none;
            padding: 8px 10px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            /* transition: background-color 0.3s ease; */
            margin-top: auto; 
            /* visibility: hidden; */
        }

    </style>
</head>
<body>
    <section>
        <div class="container_personal_account">
            <!-- Панель переключения вкладок -->
            <div class="navbar">
                <a class="tab-link active" data-tab="profile">Личный кабинет</a>
                <a class="tab-link" data-tab="orders">История заказов</a>
                <a class="tab-link" data-tab="notifications">Уведомления</a>
            </div>
            <!-- Содержимое вкладок -->
            <div class="tabs">
            <!-- Вкладка профиля -->
            <div id="profile" class="tab-content active">
                    <h2>Личный кабинет</h2>
                    <p>Добро пожаловать в ваш личный кабинет.</p>
                    <?php
                        // Получение данных пользователя
                        $r = $db->dbs->prepare("SELECT * FROM users WHERE id=:i");
                        $r->execute([':i' => $_SESSION['id']]);
                        $user = $r->fetch();

                        // Обработка запроса на удаление аватарки
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_avatar'])) {
                            if (!empty($user['image_profile']) && file_exists("img/avatar_users/" . $user['image_profile'])) {
                                unlink("img/avatar_users/" . $user['image_profile']);
                            }
                            $stmt = $db->dbs->prepare("UPDATE users SET image_profile = '' WHERE id = :id");
                            $stmt->execute([':id' => $_SESSION['id']]);
                            echo '<script>window.location.href="index.php?page=personal_account";</script>';
                            exit;
                        }
                    ?>
                    
                    <!-- Форма для изменения данных профиля -->
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="save_profile">
                        
                        <div class="mb-3" style="text-align:center;">
                            <label for="avatarUpload" style="display:block; font-weight:bold;">Аватар</label>
                            <div style="
                                width: 150px;
                                height: 150px;
                                margin: 10px auto;
                                border-radius: 50%;
                                overflow: hidden;
                                background-color: #e0e0e0;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                position: relative;
                            ">
                                <?php if (!empty($user['image_profile']) && file_exists("img/avatar_users/" . $user['image_profile'])): ?>
                                    <img src="img/avatar_users/<?= htmlspecialchars($user['image_profile']) ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;" 
                                         alt="Аватар">
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#aaa" viewBox="0 0 24 24">
                                        <path d="M12 2a7 7 0 0 0 0 14 7 7 0 0 0 0-14Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10ZM4 22a8 8 0 0 1 16 0H4Z"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="avatar" id="avatarUpload" accept="image/*" style="margin-top:10px;">
                            <?php if (!empty($user['image_profile']) && file_exists("img/avatar_users/" . $user['image_profile'])): ?>
                                <button type="submit" name="delete_avatar" class="button_delete_avatar">Удалить аватар</button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                <!-- Иконка может оставаться -->
                            </span>
                            <div class="form-floating">
                                <input type="text" name="fio" class="form-control" placeholder="ФИО" value="<?= htmlspecialchars($user['fio']); ?>">
                                <label>ФИО</label>
                            </div>
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                <!-- Иконка -->
                            </span>
                            <div class="form-floating">
                                <input type="text" name="login" class="form-control" placeholder="Логин" value="<?= htmlspecialchars($user['login']); ?>">
                                <label>Логин</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="button_save">Сохранить</button>
                    </form>
                    
                    <hr style="margin:20px 0;">
                    
                    <!-- Форма для изменения пароля -->
                    <form method="post" action="action.php">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                <!-- Иконка -->
                            </span>
                            <div class="form-floating">
                                <input type="password" name="old_pass" class="form-control" placeholder="Старый пароль">
                                <label>Старый пароль</label>
                            </div>
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                <!-- Иконка -->
                            </span>
                            <div class="form-floating">
                                <input type="password" name="new_pass" class="form-control" placeholder="Новый пароль">
                                <label>Новый пароль</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="button_save">Изменить пароль</button>
                    </form>
                </div>

                <div id="orders" class="tab-content">
                    <h2>История заказов</h2>
                    <?php
                    // Получаем все заказы текущего пользователя
                    $stmt = $db->dbs->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
                    $stmt->execute([':user_id' => $_SESSION['id']]);
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($orders) {
                        foreach ($orders as $order) {
                            if ($order['status'] == "Ожидание") {
                                $order['status'] = "⏳" . " Ожидание";
                            }
                            elseif ($order['status'] == "В процессе") {
                                $order['status'] = "🔄" . " В процессе";
                            }
                            elseif ($order['status'] == "Принят") {
                                $order['status'] = "✅" . " Принят";
                            }
                            elseif ($order['status'] == "Выполнен") {
                                $order['status'] = "🟢" . " Выполнен";
                            }
                            elseif ($order['status'] == "Отменён") {
                                $order['status'] = "❌" . " Отменён";
                            }

                            if ($order['delivery_method'] == "Самовывоз") {
                                $order['delivery_method'] = "🏬" . " Самовывоз";
                            }
                            elseif ($order['delivery_method'] == "На дом") {
                                $order['delivery_method'] = "🚚" . " На дом";
                            }


                            ?>
                            <div class="order" style="border:1px solid #ddd; padding:15px; margin-bottom:20px; border-radius:5px;">
                                <h3>Заказ №<?= htmlspecialchars($order['id']); ?> от <?= date("d.m.Y H:i", strtotime($order['created_at'])); ?></h3>
                                <p><strong>Статус:</strong> <?= htmlspecialchars($order['status']); ?></p>
                                <?if (!empty($order['adres'])) {
                                    ?><p><strong>Адрес:</strong> <?= htmlspecialchars($order['adres']); ?></p>
                                <?php }?>
                                <p><strong>Метод доставки:</strong> <?= htmlspecialchars($order['delivery_method']); ?></p>
                                <p><strong>Общая стоимость:</strong> <?= htmlspecialchars($order['total_price']); ?> руб.</p>

                                <?php
                                // Получаем детали заказа
                                $stmt2 = $db->dbs->prepare("SELECT oi.*, p.nam_products AS Name_Products, c.nam AS Name_Categories FROM order_items oi INNER JOIN products p ON oi.product_id=p.id INNER JOIN categories c ON p.categoryid=c.id   WHERE order_id = :order_id");
                                $stmt2->execute([':order_id' => $order['id']]);
                                $order_items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                                if ($order_items) {
                                    ?>
                                    <table style="width:100%; border-collapse:collapse; margin-top:15px;">
                                        <thead>
                                            <tr style="background-color:#f7f7f7;">
                                                <th style="border:1px solid #ddd; padding:8px;">ID</th>
                                                <th style="border:1px solid #ddd; padding:8px;">Название товара</th>
                                                <th style="border:1px solid #ddd; padding:8px;">Количество</th>
                                                <th style="border:1px solid #ddd; padding:8px;">Цена</th>
                                                <th style="border:1px solid #ddd; padding:8px;">Сумма</th>
                                                <th style="border:1px solid #ddd; padding:8px;">Категория</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($order_items as $item) {
                                                // Сумма отдельно взятых товаров
                                                $summa_tovara = $item['price'] * $item['quantity'];
                                                ?>
                                                <tr>
                                                    <td style="border:1px solid #ddd; padding:8px;"><?= htmlspecialchars($item['id']); ?></td>
                                                    <td style="border:1px solid #ddd; padding:8px;"><?= htmlspecialchars($item['Name_Products']); ?></td>
                                                    <td style="border:1px solid #ddd; padding:8px;"><?= htmlspecialchars($item['quantity']); ?></td>
                                                    <td style="border:1px solid #ddd; padding:8px;"><?= htmlspecialchars($item['price']); ?></td>
                                                    <td style="border:1px solid #ddd; padding:8px;"><?= htmlspecialchars($summa_tovara); ?></td>
                                                    <td style="border:1px solid #ddd; padding:8px;"><?= htmlspecialchars($item['Name_Categories']); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                } else {
                                    echo "<p>Детали для этого заказа отсутствуют.</p>";
                                }
                                ?>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Вы пока не делали заказов.</p>";
                    }
                    ?>
                </div>

                
                <div id="notifications" class="tab-content">
                <h2>Уведомления</h2>
    <?php
    // Получаем все выполненные и отмененные заказы пользователя
    $stmt = $db->dbs->prepare("SELECT * FROM orders 
                              WHERE user_id = :user_id AND (status = 'Выполнен' OR status = 'Отменён') 
                              ORDER BY created_at DESC");
    $stmt->execute([':user_id' => $_SESSION['id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($notifications) {
        echo '<div class="notifications-list">';
        foreach ($notifications as $order) {
            // Получаем детали заказа
            $stmt2 = $db->dbs->prepare("SELECT oi.*, p.nam_products, c.nam as category_name 
                                      FROM order_items oi
                                      JOIN products p ON oi.product_id = p.id
                                      JOIN categories c ON oi.categories_id = c.id
                                      WHERE oi.order_id = :order_id");
            $stmt2->execute([':order_id' => $order['id']]);
            $order_items = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $order_date = date("d.m.Y H:i", strtotime($order['created_at']));
            
            // Определяем стиль и иконку в зависимости от статуса
            $status_icon = '';
            $border_color = '';
            $title = '';
            
            if ($order['status'] == 'Выполнен') {
                $status_icon = '✅';
                $border_color = '4px solid #28a745'; // зеленый
                $title = 'Заказ выполнен';
            } elseif ($order['status'] == 'Отменён') {
                $status_icon = '❌';
                $border_color = '4px solid #dc3545'; // красный
                $title = 'Заказ отменен';
            }
            // } elseif ($order['status'] == 'Ожидание') {
            //     $status_icon = '❌';
            //     $border_color = '4px solid #dc3545'; // красный
            //     $title = 'Заказ отменен';
            // } elseif ($order['status'] == 'В процессе') {
            //     $status_icon = '❌';
            //     $border_color = '4px solid #dc3545'; // красный
            //     $title = 'Заказ отменен';
            // } elseif ($order['status'] == 'Принят') {
            //     $status_icon = '❌';
            //     $border_color = '4px solid #dc3545'; // красный
            //     $title = 'Заказ принят';
            // }
            
            echo '<div class="notification" style="border: 1px solid #ddd; border-left: '.$border_color.'; padding: 15px; margin-bottom: 15px; border-radius: 5px; background-color: #f8f9fa;">';
            echo '<h3>'.$status_icon.' '.$title.' №' . htmlspecialchars($order['id']) . '</h3>';
            echo '<p><strong>Статус:</strong> ' . htmlspecialchars($order['status']) . '</p>';
            echo '<p><strong>Дата заказа:</strong> ' . htmlspecialchars($order_date) . '</p>';
            echo '<p><strong>Способ доставки:</strong> ' . htmlspecialchars($order['delivery_method']) . '</p>';
            
            if ($order['delivery_method'] == 'На дом' && !empty($order['adres'])) {
                echo '<p><strong>Адрес доставки:</strong> ' . htmlspecialchars($order['adres']) . '</p>';
            }
            
            echo '<p><strong>Общая сумма:</strong> ' . htmlspecialchars($order['total_price']) . ' руб.</p>';
            
            // Выводим список товаров в заказе
            echo '<div style="margin-top: 10px;">';
            echo '<h4 style="margin-bottom: 5px;">Состав заказа:</h4>';
            echo '<ul style="padding-left: 20px;">';
            foreach ($order_items as $item) {
                echo '<li>';
                echo htmlspecialchars($item['nam_products']) . ' (' . htmlspecialchars($item['category_name']) . ')';
                echo ' - ' . htmlspecialchars($item['quantity']) . ' шт. × ' . htmlspecialchars($item['price']) . ' руб.';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
            
            if ($order['status'] == 'Отменён') {
                echo '<p style="margin-top: 10px; font-style: italic;">Если у вас есть вопросы по отмене заказа, пожалуйста, свяжитесь с нашей поддержкой</p>';
            }
            
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>У вас нет новых уведомлений.</p>';
    }
    ?>
                </div>
                
            </div>
        </div>
    </section>
    
    <script>
        // Сохраним первоначальный src аватара (если он был)
        let originalAvatarSrc = "";
        const avatarImage = document.getElementById('avatarImage');
        if (avatarImage && avatarImage.src) {
            originalAvatarSrc = avatarImage.src;
        }

        // Обработчик изменения выбранного файла
        document.getElementById('avatarUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                return;
            }
            // Проверка, что файл является изображением
            if (!file.type.startsWith('image/')) {
                alert('Пожалуйста, выберите изображение.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                // Обновляем изображение предпросмотра
                avatarImage.src = event.target.result;
                avatarImage.style.display = 'block';
                // Скрываем svg-иконку по умолчанию, если она есть
                const defaultAvatar = document.getElementById('defaultAvatar');
                if (defaultAvatar) {
                    defaultAvatar.style.display = 'none';
                }
                // Показываем кнопку "Отменить"
                document.getElementById('cancelAvatar').style.display = 'inline-block';
            };
            reader.readAsDataURL(file);
        });

        // Обработчик для кнопки "Отменить"
        document.getElementById('cancelAvatar').addEventListener('click', function() {
            // Сброс значения input file
            document.getElementById('avatarUpload').value = "";
            // Восстанавливаем исходное изображение или отображаем svg если нет исходного
            if (originalAvatarSrc) {
                avatarImage.src = originalAvatarSrc;
                avatarImage.style.display = 'block';
                const defaultAvatar = document.getElementById('defaultAvatar');
                if (defaultAvatar) {
                    defaultAvatar.style.display = 'none';
                }
            } else {
                avatarImage.src = "";
                avatarImage.style.display = 'none';
                const defaultAvatar = document.getElementById('defaultAvatar');
                if (defaultAvatar) {
                    defaultAvatar.style.display = 'block';
                }
            }
            // Скрываем кнопку "Отменить"
            this.style.display = 'none';
        });
</script>




    <script>
        // После загрузки документа устанавливаем обработчики кликов для переключения вкладок
        document.addEventListener('DOMContentLoaded', function(){
            var links = document.querySelectorAll('.tab-link');
            links.forEach(function(link){
                link.addEventListener('click', function(){
                    // Убираем активный класс у всех ссылок и скрываем все вкладки
                    links.forEach(function(l){
                        l.classList.remove('active');
                        document.getElementById(l.getAttribute('data-tab')).classList.remove('active');
                    });
                    // Добавляем активный класс для нажатой ссылки и показываем соответствующий блок
                    this.classList.add('active');
                    var tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
