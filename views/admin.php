<?
if ($_SESSION['status']==100){ 
    
    
}
elseif ($_SESSION['status']==1){ 
    echo '<script>window.location.href="index.php";</script>';
    exit;
}
else {
    echo '<script>window.location.href="index.php?page=auth";</script>';
    exit;
}


?>

<style>
    /* Стили для модального окна */
    .modal {
        display: none; /* Скрыто по умолчанию */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    /* Контент модального окна */
    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 10px;
        width: 50%;
    }

    /* Кнопка закрытия */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

</style>

<body style="background-color: #ECF0F1">
<section>
<div class="row-12 h3 text-center">Админ панель</div>
<ul class="nav justify-content-center">
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="index.php?page=<?=$_REQUEST['page']?>&items=categ">Работа с категориями</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="index.php?page=<?=$_REQUEST['page']?>&items=prod">Работа с товаром</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="index.php?page=<?=$_REQUEST['page']?>&items=order">Работа с заказами</a>
    </li>
</ul>
<?php
if (isset($_REQUEST['items'])){
    if ($_REQUEST['items']=='categ'){
    $r=$db->dbs->prepare("SELECT * FROM categories;");
    $r->execute();
    if ($r->rowCount()!=0){
        ?>
        <table class="table caption-top">
            <thead class="bg-primary-subtle">
            <tr>
                <th scope="col">№</th>
                <th scope="col" class="w-auto">Наименование</th>
                <th scope="col" class="w-25">Действие</th>
             </tr>
             </thead>
             <tbody>
             <?
             $i=1;
             foreach ($r as $res){
                print "
                <tr>
                <th scope=\"row\">".$i."</th>
                <td>".$res['nam']."</td>
                <td><a href='index.php?page=".$_REQUEST['page']."&items=".$_REQUEST['items']."&edit=categ&id=".$res['id']."' class='btn btn-outline-success'>Изменить</a>&nbsp;&nbsp;&nbsp;
                <div class='vr'></div>&nbsp;&nbsp;&nbsp;
                <a href='index.php?page=".$_REQUEST['page']."&items=".$_REQUEST['items']."&action=delCat&id=".$res['id']."' class='btn btn-outline-danger'>Удалить</a></td></tr>";
                $i++;
            }
            ?>
            </tbody>
        </table>
        <?php
    }else print "<div class=\"row-12 h5 text-center\">Нет категорий</div>";
    if (!isset($_REQUEST['edit'])){
        ?>
        <div class="row-12 h5 text-center">Добавление категории</div>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="page" value="<?=$_REQUEST['page']?>">
            <input type="hidden" name="items" value="<?=$_REQUEST['items']?>">
            <input type="hidden" name="action" value="addCateg">
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fonts" viewBox="0 0 16 16">
                    <path d="M12.258 3h-8.51l-.083 2.46h.479c.26-1.544.758-1.783 2.693-1.845l.424-.013v7.827c0 .663-.144.82-1.3.923v.52h4.082v-.52c-1.162-.103-1.306-.26-1.306-.923V3.602l.431.013c1.934.062 2.434.301 2.693 1.846h.479z"/>
                </svg>
                </span>
                <div class="form-floating">
                    <input type="text" name="nam" class="form-control" placeholder="Наименование категории" aria-label="Наименование категории" aria-describedby="basic-addon1">
                    <label>Наименование категории</label>
                </div>
                <button class="btn btn-outline-secondary" type="submit">Добавить</button>
            </div>
        </form>
        <?php
    }
    }
    elseif ($_REQUEST['items']=='prod'){

        $r=$db->dbs->prepare("SELECT p.*, c.nam as category_name 
                        FROM products p 
                        INNER JOIN categories c ON p.categoryid = c.id;");
        $r->execute();
        if ($r->rowCount() != 0) {
            ?>
            <table class="table caption-top">
                <thead class="bg-primary-subtle">
                <tr>
                    <th scope="col">№</th>
                    <th scope="col" class="w-25">Фото</th>
                    <th scope="col" class="w-auto">Наименование</th>
                    <th scope="col" class="w-auto">Описание</th>
                    <th scope="col" class="w-auto">Цена</th>
                    <th scope="col" class="w-auto">Количество товара</th>
                    <th scope="col" class="w-auto">Категория</th>
                    <th scope="col" class="w-25">Действие</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($r as $res) {
                    print "<tr>
                    <th scope=\"row\">".$i."</th>
                    <td><img src='".$res['image_product']."' class='rounded float-start img-thumbnail' style='max-height: 150px; max-width: 150px'></td>
                    <td>".$res['nam_products']."</td>
                    <td>".$res['description']."</td>
                    <td>".$res['price']."</td>
                    <td>".$res['stock']."</td>
                    <td>".$res['category_name']."</td>
                    <td><a href='index.php?page=".$_REQUEST['page']."&items=".$_REQUEST['items']."&edit=prod&id=".$res['id']."' class='btn btn-outline-success'>Изменить</a>&nbsp;&nbsp;&nbsp;<div class='vr'></div>&nbsp;&nbsp;&nbsp;<a href='index.php?page=".$_REQUEST['page']."&items=".$_REQUEST['items']."&action=delProduct&id=".$res['id']."' class='btn btn-outline-danger'>Удалить</a></td>
                    </tr>";
                    $i++;
                }
                ?>
                </tbody>
            </table>
            <?php
        } else {
            print "<div class=\"row-12 h5 text-center\">Нет продуктов</div>";
        }

        if (!isset($_REQUEST['edit'])){
            ?>
            <div class="row-12 h5 text-center">Добавление товара</div>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="page" value="<?=$_REQUEST['page']?>">
                <input type="hidden" name="items" value="<?=$_REQUEST['items']?>">
                <input type="hidden" name="action" value="addProduct">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fonts" viewBox="0 0 16 16">
                        <path d="M12.258 3h-8.51l-.083 2.46h.479c.26-1.544.758-1.783 2.693-1.845l.424-.013v7.827c0 .663-.144.82-1.3.923v.52h4.082v-.52c-1.162-.103-1.306-.26-1.306-.923V3.602l.431.013c1.934.062 2.434.301 2.693 1.846h.479z"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <input type="text" name="nam_products" class="form-control" placeholder="Наименование товара" aria-label="Наименование товара" aria-describedby="basic-addon1">
                        <label>Наименование товара</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-text" viewBox="0 0 16 16">
                        <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                        <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <input type="text" name="description" class="form-control" placeholder="Описание товара" aria-label="Описание товара" aria-describedby="basic-addon1">
                        <label>Описание товара</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
                        <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                        <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <input type="text" name="price" class="form-control" placeholder="Цена товара" aria-label="Цена товара" aria-describedby="basic-addon1">
                        <label>Цена товара</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-123" viewBox="0 0 16 16">
                        <path d="M2.873 11.297V4.142H1.699L0 5.379v1.137l1.64-1.18h.06v5.961zm3.213-5.09v-.063c0-.618.44-1.169 1.196-1.169.676 0 1.174.44 1.174 1.106 0 .624-.42 1.101-.807 1.526L4.99 10.553v.744h4.78v-.99H6.643v-.069L8.41 8.252c.65-.724 1.237-1.332 1.237-2.27C9.646 4.849 8.723 4 7.308 4c-1.573 0-2.36 1.064-2.36 2.15v.057zm6.559 1.883h.786c.823 0 1.374.481 1.379 1.179.01.707-.55 1.216-1.421 1.21-.77-.005-1.326-.419-1.379-.953h-1.095c.042 1.053.938 1.918 2.464 1.918 1.478 0 2.642-.839 2.62-2.144-.02-1.143-.922-1.651-1.551-1.714v-.063c.535-.09 1.347-.66 1.326-1.678-.026-1.053-.933-1.855-2.359-1.845-1.5.005-2.317.88-2.348 1.898h1.116c.032-.498.498-.944 1.206-.944.703 0 1.206.435 1.206 1.07.005.64-.504 1.106-1.2 1.106h-.75z"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <input type="text" name="stock" class="form-control" placeholder="Количество товара" aria-label="Количество товара" aria-describedby="basic-addon1">
                        <label>Количество товара</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <select name="categoryid" class="form-control">
                        <?php
                            $r = $db->dbs->prepare('SELECT id, nam FROM categories;');
                            $r->execute();
                            foreach ($r as $res) {
                                echo "<option value = '".$res['id']."'> ".$res['nam']." </option>";
                            }
                        ?>
                        </select>    
                        <label>Категория</label>             
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                    </svg>
                    </span>
                    <input type="file" name="image_product" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Загрузка">
                    <button class="btn btn-outline-secondary" type="submit">Добавить</button>
                </div>
            </form>
            <?php
        }
    }


    elseif ($_REQUEST['items']=='order'){
        // Параметры страницы
        $page = htmlspecialchars($_REQUEST['page'] ?? '');
        $items = 'order';
        
        // Получение выбранного фильтра доставки (GET)
        $selectedMethod = isset($_GET['delivery_method']) ? $_GET['delivery_method'] : '';
        
        // Возможные способы доставки
        $methods = ['Самовывоз', 'На дом'];
        
        // Форма фильтрации по доставке
        echo "<form method='get' class='mb-3'>";
        echo "<input type='hidden' name='page' value='{$page}'>";
        echo "<input type='hidden' name='items' value='{$items}'>";
        echo "<label for='delivery_method'>Способ доставки:</label> ";
        echo "<select id='delivery_method' name='delivery_method' class='form-select d-inline-block w-auto me-2'>";
        echo "<option value=''" . ($selectedMethod === '' ? ' selected' : '') . ">Все</option>";
        foreach ($methods as $m) {
            $sel = ($selectedMethod === $m) ? ' selected' : '';
            echo "<option value='" . htmlspecialchars($m) . "'{$sel}>" . htmlspecialchars($m) . "</option>";
        }
        echo "</select>";
        echo "<button type='submit' class='btn btn-primary'>Применить</button>";
        echo "</form>";
    
        // Подготовка SQL (всегда выбираем, фильтрация по необходимости)
        $sql = "SELECT o.*, u.fio AS FIO_User
                FROM orders o
                INNER JOIN users u ON o.user_id = u.id";
        $params = [];
        if ($selectedMethod !== '') {
            $sql .= " WHERE o.delivery_method = ?";
            $params[] = $selectedMethod;
        }
        $sql .= " ORDER BY o.created_at DESC";
    
        // Выполнение запроса
        $stmt = $db->dbs->prepare($sql);
        $stmt->execute($params);
    
        // Вывод таблицы заказов
        echo '<table class="table caption-top">';
        echo '<thead class="bg-primary-subtle"><tr>' .
             '<th>№</th>' .
             '<th class="w-25">ФИО пользователя</th>' .
             '<th>Сумма заказа</th>' .
             '<th>Статус</th>' .
             '<th>Дата создания</th>' .
             '<th>Адрес</th>' .
             '<th>Доставка</th>' .
             '<th>Товары</th>' .
             '<th class="w-25">Действие</th>' .
             '</tr></thead>';
        echo '<tbody>';
        $i = 1;
        foreach ($stmt as $res) {
            $orderId = $res['id'];
            echo '<tr>';
            echo "<th scope='row'>{$i}</th>";
            echo "<td>" . htmlspecialchars($res['FIO_User']) . "</td>";
            echo "<td>" . htmlspecialchars($res['total_price']) . "</td>";
            echo "<td>" . htmlspecialchars($res['status']) . "</td>";
            echo "<td>" . htmlspecialchars($res['created_at']) . "</td>";
            echo "<td>" . htmlspecialchars($res['adres']) . "</td>";
            echo "<td>" . htmlspecialchars($res['delivery_method']) . "</td>";
            echo '<td>' .
                 "<a href='index.php?page={$page}&items={$items}&order_id={$orderId}' class='btn btn-outline-info' id='openModal' data-orderid='{$orderId}'>" .
                 "Товары №{$orderId}</a>" .
                 '</td>';
            echo '<td>' .
                 "<a href='index.php?page={$page}&items={$items}&edit=order&id={$orderId}' class='btn btn-outline-success'>Изменить</a>" .
                 "<span class='mx-2'>|</span>" .
                 "<a href='index.php?page={$page}&items={$items}&action=delOrder&id={$orderId}' class='btn btn-outline-danger'>Удалить</a>" .
                 '</td>';
            echo '</tr>';
            $i++;
        }
        echo '</tbody></table>';
    }
    ?>


        

    <?php
    // Получаем order_id из GET, либо null, если не задан
    $order_id = isset($_REQUEST['order_id']) ? (int)$_REQUEST['order_id'] : null;
        
    // Если order_id не пуст и > 0, то готовим список товаров
    if ($order_id > 0) {
        // Подготовим запрос на товары конкретного заказа
        $sql = $db->dbs->prepare("
            SELECT oi.*, p.nam_products AS Name_Products
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $sql->bindValue(1, $order_id, PDO::PARAM_INT);
        $sql->execute();
        
        // Модальное окно показываем только если перешли с конкретным order_id
        ?>
        <div id="modal" class="modal" style="display: block;"><!-- display: block - чтобы показать сразу -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Товары заказа № <?= $order_id ?></h2>
                <p>Ниже показаны товары для выбранного заказа.</p>
    
                <table class="table caption-top">
                    <thead class="bg-primary-subtle">
                        <tr>
                            <th scope="col">№</th>
                            <th scope="col" class="w-25">Название товара</th>
                            <th scope="col" class="w-auto">Кол-во</th>
                            <th scope="col" class="w-auto">Сумма</th>
                            <th scope="col" class="w-25">Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($sql->rowCount() > 0) {
                            $i = 1;
                            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $row['Name_Products'] ?></td>
                                    <td><?= $row['quantity'] ?></td>
                                    <td><?= $row['price'] ?></td>
                                    <td>
                                        <!-- Пример кнопки на удаление конкретной позиции -->
                                        <a href="index.php?page=<?= $_REQUEST['page'] ?>&items=order&action=delItem&id=<?= $row['id'] ?>&order_id=<?= $order_id ?>"
                                           class="btn btn-outline-danger btn-sm">
                                            Удалить
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>Нет товаров для данного заказа</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    } // if ($order_id > 0) — конец
    ?>



    <script>
        // Получаем по id модалку (если она есть на странице)
    const modal = document.getElementById("modal");
    if (modal) {
        // Кнопка закрытия внутри модального окна
        const closeBtn = modal.querySelector(".close");
        
        // Обработчик на кнопку закрыть
        closeBtn.onclick = function() {
            modal.style.display = "none";
        };

        // Закрытие при клике вне контента модалки
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    }
    </script>





    <?php
    if (isset($_REQUEST['edit'])){
        if ($_REQUEST['edit']=='categ'){
            $r=$db->dbs->prepare("SELECT * FROM categories WHERE id=:i");
            $r->execute([':i'=>$_REQUEST['id']]);
            foreach ($r as $res){
            ?>
            <div class="row-12 h5 text-center">Изменение категории</div>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="page" value="<?=$_REQUEST['page']?>">
                <input type="hidden" name="items" value="<?=$_REQUEST['items']?>">
                <input type="hidden" name="id" value="<?=$res['id']?>">
                <input type="hidden" name="action" value="editCateg">
                <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fonts" viewBox="0 0 16 16">
                        <path d="M12.258 3h-8.51l-.083 2.46h.479c.26-1.544.758-1.783 2.693-1.845l.424-.013v7.827c0 .663-.144.82-1.3.923v.52h4.082v-.52c-1.162-.103-1.306-.26-1.306-.923V3.602l.431.013c1.934.062 2.434.301 2.693 1.846h.479z"/>
                    </svg>
                </span>
                    <div class="form-floating">
                        <input type="text" name="nam" class="form-control" placeholder="Наименование категории" aria-label="Наименование категории" aria-describedby="basic-addon1" value="<?=$res['nam']?>">
                        <label>Наименование категории</label>
                    </div>
                    <button class="btn btn-outline-success" type="submit">Изменить</button>
                </div>
            </form>
            <?php
            }
        }
        elseif ($_REQUEST['edit']=='prod'){
            $r=$db->dbs->prepare("SELECT * FROM products WHERE id=:i");
            $r->execute([':i'=>$_REQUEST['id']]);
            foreach ($r as $res){
            ?>
            <div class="row-12 h5 text-center">Изменение товара</div>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="page" value="<?=$_REQUEST['page']?>">
                <input type="hidden" name="items" value="<?=$_REQUEST['items']?>">
                <input type="hidden" name="action" value="editProduct">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fonts" viewBox="0 0 16 16">
                        <path d="M12.258 3h-8.51l-.083 2.46h.479c.26-1.544.758-1.783 2.693-1.845l.424-.013v7.827c0 .663-.144.82-1.3.923v.52h4.082v-.52c-1.162-.103-1.306-.26-1.306-.923V3.602l.431.013c1.934.062 2.434.301 2.693 1.846h.479z"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <input type="text" name="nam_products" class="form-control" placeholder="Наименование товара" aria-label="Наименование товара" aria-describedby="basic-addon1" value="<?=$res['nam_products']?>">
                        <label>Наименование товара</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-text" viewBox="0 0 16 16">
                        <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                        <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <input type="text" name="description" class="form-control" placeholder="Описание товара" aria-label="Описание товара" aria-describedby="basic-addon1" value="<?=$res['description']?>">
                        <label>Описание товара</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
                        <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                        <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <input type="text" name="price" class="form-control" placeholder="Цена товара" aria-label="Цена товара" aria-describedby="basic-addon1" value="<?=$res['price']?>">
                        <label>Цена товара</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-123" viewBox="0 0 16 16">
                        <path d="M2.873 11.297V4.142H1.699L0 5.379v1.137l1.64-1.18h.06v5.961zm3.213-5.09v-.063c0-.618.44-1.169 1.196-1.169.676 0 1.174.44 1.174 1.106 0 .624-.42 1.101-.807 1.526L4.99 10.553v.744h4.78v-.99H6.643v-.069L8.41 8.252c.65-.724 1.237-1.332 1.237-2.27C9.646 4.849 8.723 4 7.308 4c-1.573 0-2.36 1.064-2.36 2.15v.057zm6.559 1.883h.786c.823 0 1.374.481 1.379 1.179.01.707-.55 1.216-1.421 1.21-.77-.005-1.326-.419-1.379-.953h-1.095c.042 1.053.938 1.918 2.464 1.918 1.478 0 2.642-.839 2.62-2.144-.02-1.143-.922-1.651-1.551-1.714v-.063c.535-.09 1.347-.66 1.326-1.678-.026-1.053-.933-1.855-2.359-1.845-1.5.005-2.317.88-2.348 1.898h1.116c.032-.498.498-.944 1.206-.944.703 0 1.206.435 1.206 1.07.005.64-.504 1.106-1.2 1.106h-.75z"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <input type="text" name="stock" class="form-control" placeholder="Количество товара" aria-label="Количество товара" aria-describedby="basic-addon1" value="<?=$res['stock']?>">
                        <label>Количество товара</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                    </svg>
                    </span>
                    <div class="form-floating">
                        <select name="categoryid" class="form-control">
                        <?
                            $q = $db->dbs->prepare('SELECT id, nam FROM categories;');
                            $q->execute();
                            foreach ($q as $qes) {
                                ($qes['id']==$res['categoryid'])?$str='selected':$str='';
                                echo "<option value = ".$qes['id']." ".$str."> ".$qes['nam']." </option>";
                            }   
                        ?>
                        </select> 
                        <label>Категория</label>                   
                    </div>
                </div>
                <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                    <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                </svg>
                </span>
                    <input type="file" name="image_product" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Загрузка">
                    <button class="btn btn-outline-success" type="submit">Изменить</button>
                </div>

            </form>
            <?php
            }
        }
        elseif ($_REQUEST['edit']=='order'){
            $r = $db->dbs->prepare("SELECT o.* FROM orders o INNER JOIN users u ON o.user_id=u.id WHERE o.id=:i");
            $r->execute([':i'=>$_REQUEST['id']]);
            foreach ($r as $res){
            ?>
            <div class="row-12 h5 text-center">Изменение заказа</div>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="page" value="<?=$_REQUEST['page']?>">
                <input type="hidden" name="items" value="<?=$_REQUEST['items']?>">
                <input type="hidden" name="id" value="<?=$res['id']?>">
                <input type="hidden" name="action" value="editOrder">
                
                <!-- Выбор пользователя -->
                <!-- <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    </span>
                    <div class="form-floating">
                        <select name="user_id" class="form-control">
                            <?php
                                $ir = $db->dbs->prepare("SELECT id, fio AS FIO_User FROM users");
                                $ir->execute();
                                foreach ($ir as $ires) {
                                    $selected = ($ires['id'] == $res['user_id']) ? 'selected' : '';
                                    echo "<option value='".$ires['id']."' $selected>".$ires['FIO_User']."</option>";
                                }
                            ?>
                        </select>
                        <label>Пользователь</label>
                    </div>
                </div> -->
                
                <!-- <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        
                    </span>
                    <div class="form-floating">
                        <input type="text" name="total_price" class="form-control" placeholder="Сумма заказа" aria-label="Сумма заказа" value="<?=$res['total_price']?>">
                        <label>Сумма заказа</label>
                    </div>
                </div> -->
                
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        
                    </span>
                    <div class="form-floating">
                        <select name="status" class="form-control" required>
                            <option value="Ожидание" <?=($res['status'] == 'Ожидание' ? 'selected' : '')?>>Ожидание</option>
                            <option value="В процессе" <?=($res['status'] == 'В процессе' ? 'selected' : '')?>>В процессе</option>
                            <option value="Отменён" <?=($res['status'] == 'Отменён' ? 'selected' : '')?>>Отменён</option>
                            <option value="Принят" <?=($res['status'] == 'Принят' ? 'selected' : '')?>>Принят</option>
                            <option value="Выполнен" <?=($res['status'] == 'Выполнен' ? 'selected' : '')?>>Выполнен</option>
                        </select>
                        <label>Статус</label>
                    </div>
                </div>
                
                <!-- <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        
                    </span>
                    <div class="form-floating">
                        <input type="date" name="data_zakaza_date" class="form-control" placeholder="Дата создания заказа" value="<?=date('Y-m-d', strtotime($res['created_at']))?>">
                        <label>Дата создания заказа</label>
                    </div>
                </div> -->
                
                <!-- <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        
                    </span>
                    <div class="form-floating">
                        <input type="time" name="data_zakaza_time" class="form-control" placeholder="Время создания заказа" value="<?=date('H:i', strtotime($res['created_at']))?>">
                        <label>Время создания заказа</label>
                    </div>
                </div> -->
                
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        
                    </span>
                    <div class="form-floating">
                        <select name="delivery_method" class="form-control" required>
                            <option value="Самовывоз" <?php if($res['delivery_method']=='Самовывоз') echo 'selected'; ?>>Самовывоз</option>
                            <option value="Доставка на дом" <?php if($res['delivery_method']=='Доставка на дом') echo 'selected'; ?>>Доставка на дом</option>
                        </select>
                        <label>Способ доставки</label>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        
                    </span>
                    <div class="form-floating">
                        <input type="text" name="adres" class="form-control" placeholder="Адрес" value="<?=$res['adres']?>">
                        <label>Адрес</label>
                    </div>
                    <button class="btn btn-outline-success" type="submit">Изменить</button>
                </div>
                
            </form>
            <?php
            }
        }              
    }
}else print "<div class=\"row-12 h5 text-center\">Выберите раздел</div>";
?>
</section>
</body>