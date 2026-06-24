<?php
global $db;
if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'reg') {
        if (empty($_REQUEST['login']) || empty($_REQUEST['pass']) || empty($_REQUEST['pass'])) {
            $message = 'Не введены поля логин/пароль';
        } else {
            $r = $db->dbs->prepare('SELECT * FROM users WHERE login=:i');
            $r->execute([':i' => $_REQUEST['login']]);
            if ($r->rowCount() == 0) {
                $r = $db->dbs->prepare('SELECT * FROM users;');
                $r->execute();
                ($r->rowCount() != 0) ? $status = 1 : $status = 100;
                $mas = [
                    'login' => $_REQUEST['login'],
                    'pass' => $_REQUEST['pass'],
                    'fio' => $_REQUEST['fio'],
                    'status' => $status
                ];
                ($db->actionTable('add', $mas, 'users')) ? $message = 'Регистрация прошла успешно' : $message = 'Произошла ошибка в момент регистрации';
            } else $message = 'Пользователь с таким логином уже существует';
        }
    }
    if ($_REQUEST['action']=='auth'){
        if (empty($_REQUEST['login']) || empty($_REQUEST['pass'])){
            $message='Не введены поля логин/пароль';
        }else{
            $r=$db->dbs->prepare('SELECT * FROM users WHERE login=:i AND pass=:i1');
            $r->execute([':i'=>$_REQUEST['login'], ':i1'=>md5($_REQUEST['pass'])]);
            if ($r->rowCount()!=0){
                foreach ($r as $res){
                    $_SESSION['id']=$res['id'];
                    $_SESSION['login']=$res['login'];
                    $_SESSION['status']=$res['status'];
                    $message='Вы успешно авторизовались';
                }
            }else $message="Не найдет пользователь с такими логин/пароль";
        }
    }
    if ($_REQUEST['action']=='quit'){
        unset($_SESSION['id']);
        unset($_SESSION['login']);
        unset($_SESSION['status']);
        session_destroy();
        $message='Вы вышли из системы';
    }



    // личный кабинет
    // Изменение данных профиля (логин, ФИО, аватар)
    if ($_REQUEST['action'] == 'save_profile') {
        // Проверка обязательных полей
        if (empty($_REQUEST['fio']) || empty($_REQUEST['login'])) {
            $message = 'Не введены поля ФИО или логин';
        } else {
            $fio = $_REQUEST['fio'];
            $login = $_REQUEST['login'];
            $id = $_SESSION['id'];
            $imageFileName = '';

            // Получаем данные текущего пользователя, чтобы использовать существующий аватар, если новый не загружен
            $r = $db->dbs->prepare("SELECT * FROM users WHERE id=:i");
            $r->execute([':i' => $id]);
            $user = $r->fetch();
            $imageFileName = $user['image_profile'] ?? '';

            // Если загружен новый аватар
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = basename($_FILES['avatar']['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExt, $allowedExts)) {
                    $newFileName = 'avatar_' . $login . '.' . $fileExt;
                    $uploadDir = 'img/avatar_users/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $destPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imageFileName = $newFileName;
                    }
                }
            }
            
            // Подготовка данных для обновления
            $updateData = [
                'id'    => $id,
                'fio'   => $fio,
                'login' => $login,
                'image_profile'   => $imageFileName
            ];

            // Обновление в базе данных
            if ($db->actionTable('edit', $updateData, 'users')) {
                $message = 'Изменения профиля успешно сохранены';
            } else {
                $message = 'Произошла ошибка при сохранении изменений профиля';
            }
        }
    }
    
    // Изменение пароля
    elseif ($_REQUEST['action'] == 'change_password') {
        // Проверка обязательных полей
        if (empty($_REQUEST['old_pass']) || empty($_REQUEST['new_pass'])) {
            $message = 'Введите и старый, и новый пароль';
        } else {
            $id = $_SESSION['id'];
            // Получаем данные пользователя
            $r = $db->dbs->prepare('SELECT * FROM users WHERE id=:i');
            $r->execute([':i' => $id]);
            $user = $r->fetch();

            // Проверяем корректность старого пароля (сравнение через md5)
            if (md5($_REQUEST['old_pass']) !== $user['pass']) {
                $message = 'Неправильный старый пароль';
            } else {
                // Обновляем пароль
                $newPasswordHash = md5($_REQUEST['new_pass']);
                $updateData = [
                    'id'   => $id,
                    'pass' => $newPasswordHash
                ];
                if ($db->actionTable('edit', $updateData, 'users')) {
                    $message = 'Пароль успешно изменён';
                } else {
                    $message = 'Произошла ошибка при изменении пароля';
                }
            }
        }
    }

    // Здесь можно добавить редирект или вывод сообщения
    // echo "<script>alert('{$message}'); window.location.href='index.php?page=personal_account';</script>";
    

    
    // К админке
    if ($_REQUEST['action']=='addCateg'){
        $mas=[
            'nam'=>$_REQUEST['nam']
        ];
        ($r=$db->actionTable('add', $mas, 'categories'))?$message.="Категория успешно добавлена":$message="Произошла ошибка при добавлении категории";
    }
    if ($_REQUEST['action']=='editCateg'){
        $mas=[
            'id'=>$_REQUEST['id'],
            'nam'=>$_REQUEST['nam']
        ];
        ($r=$db->actionTable('edit', $mas, 'categories'))?$message.="Категория успешно изменена":$message="Произошла ошибка при изменении категории";
    }
    if ($_REQUEST['action']=='delCat'){
        ($db->actionTable('del', $_REQUEST['id'], 'categories'))?$message='Категория успешно удалена':$message='Произошла ошибка при удалении категории';
    }

    if ($_REQUEST['action']=='addProduct'){
        $mas_files=$db->uploading('image_product');
        if (count($mas_files)!=0){
            if ($_REQUEST['stock'] > 0) {
                foreach ($mas_files as $res){
                    $mas=[
                        'nam_products'=>$_REQUEST['nam_products'],
                        'description'=>$_REQUEST['description'],
                        'price'=>$_REQUEST['price'],
                        'stock'=>$_REQUEST['stock'],
                        'categoryid'=>$_REQUEST['categoryid'], // Изменили на categoriesid
                        'image_product'=>$res
                    ];
                    ($r=$db->actionTable('add', $mas, 'products'))?$message.="Продукт успешно добавлен":$message="Произошла ошибка при добавлении продукта";
                }    
            }
            else {
                $message="Кол-во товара не может быть отрицательным";
            }
        }
    }
    
    if ($_REQUEST['action']=='editProduct'){
        if (!empty($_FILES['image_product']['name'])) {
            $mas_files=$db->uploading('image_product');
            if (count($mas_files)!=0){
                if ($_REQUEST['stock'] >= 0) {
                    foreach ($mas_files as $res){
                        $mas=[
                            'id'=>$_REQUEST['id'],
                            'nam_products'=>$_REQUEST['nam_products'],
                            'description'=>$_REQUEST['description'],
                            'price'=>$_REQUEST['price'],
                            'stock'=>$_REQUEST['stock'],
                            'categoryid'=>$_REQUEST['categoryid'], // Добавили categoriesid
                            'image_product'=>$res
                        ];
                        ($r=$db->actionTable('edit', $mas, 'products'))?$message.="Продукт успешно изменен":$message="Произошла ошибка при изменении продукта";
                    }
                }
                else {
                    $message="Кол-во товара не может быть отрицательным";
                }
            }
        }else{
            if ($_REQUEST['stock'] >= 0) {
                $mas=[
                    'id'=>$_REQUEST['id'],
                    'nam_products'=>$_REQUEST['nam_products'],
                    'description'=>$_REQUEST['description'],
                    'price'=>$_REQUEST['price'],
                    'stock'=>$_REQUEST['stock'],
                    'categoryid'=>$_REQUEST['categoryid'], // Добавили categoriesid
                ];
                ($r=$db->actionTable('edit', $mas, 'products'))?$message.="Продукт успешно изменен":$message="Произошла ошибка при изменении продукта";
            }
            else {
                $message="Кол-во товара не может быть отрицательным";
            }
        }
    }
    
    if ($_REQUEST['action']=='delProduct'){
        ($db->actionTable('del', $_REQUEST['id'], 'products'))?$message='Продукт успешно удален':$message='Произошла ошибка при удалении продукта';
    }


    if ($_REQUEST['action']=='addOrder'){
        $date = $_REQUEST['created_at_date'];
        $time = $_REQUEST['created_at_time'];
    
        $data_zakaza = $date . ' ' . $time;
    
        $mas=[
            'user_id'=>$_REQUEST['user_id'],
            'total_price'=>$_REQUEST['total_price'],
            'status'=>$_REQUEST['status'],
            'created_at'=>$data_zakaza,
            'adres'=>$_REQUEST['adres'],
            'delivery_method'=>$_REQUEST['delivery_method'],
        ];
        ($r=$db->actionTable('add', $mas, 'orders'))?$message.="Заказ успешно добавлен":$message="Произошла ошибка при добавлении заказа";
    }
    
    if ($_REQUEST['action']=='editOrder'){
        // $date = $_REQUEST['data_zakaza_date'];
        // $time = $_REQUEST['data_zakaza_time'];
        // $data_zakaza = $date . ' ' . $time;
        $mas = [
            'id' => $_REQUEST['id'],
            // 'user_id' => $_REQUEST['user_id'],
            // 'total_price' => $_REQUEST['total_price'],
            'status' => $_REQUEST['status'],
            // 'created_at' => $data_zakaza,
            'adres' => $_REQUEST['adres'],
            'delivery_method' => $_REQUEST['delivery_method'],
        ];
        ($r = $db->actionTable('edit', $mas, 'orders')) ? $message .= "Заказ успешно изменен" : $message = "Произошла ошибка при изменении заказа";
    }
    
    if ($_REQUEST['action']=='delOrder'){
        ($db->actionTable('del', $_REQUEST['id'], 'orders'))?$message='Заказ успешно удален':$message='Произошла ошибка при удалении заказа';
    }

    
}