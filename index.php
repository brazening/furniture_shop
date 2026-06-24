<?php
require_once "connect.php";
require_once "action.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мебельный Рай</title>
    <!-- <link rel="website icon" type="svg" href="img/website_icon/sofa_icons_red.svg"> -->
    <link rel="website icon" type="png" href="img/website_icon/sofa.png">

    <link rel="stylesheet" href="/css/style.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <!-- Стили Bootstrap -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <!-- Скрипты Bootstrap -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>  -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <style>
        /* дальше тут идут стили, связанные с конкретно этой страницей */
        
        /* .welcome {
            display: flex;
            margin: 0;
            font-family: "Snell Roundhand", cursive;
            font-size: 200%;
        }

        .text-welcome {
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 16px;
            margin: 0;
            text-align: left;
        }

        .left_photo {
            float: left;
            margin: 4px 10px 2px 0px;
            border: 3px solid #111;
        }

        .container {
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
        }
        .welcome-box {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(3px);
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100px;
            width: auto;
        } */

        /* .auth-button, .admin-button, .exit-button {
            background-color: #fcd228;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: active;
            color: black;
            position: relative;
            z-index: 1000;
        } */

        .auth-button:hover, .admin-button:hover, .exit-button:hover {
            /* background-color: #ffd700; */
            color: #333;
            text-decoration-line: none;
        }

        .dropdown-auth, .dropdown-admin, .dropdown-exit {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            z-index: 1001;
            margin-top: 5px;
            padding: 10px;
        }

        .auth-container:hover .dropdown-auth,
        .admin-container:hover .dropdown-admin,
        .exit-container:hover .dropdown-exit {
            display: block;
        }

        .dropdown-auth ul, .dropdown-admin ul, .dropdown-exit ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .dropdown-auth li, .dropdown-admin li, .dropdown-exit li {
            margin-bottom: 10px;
        }

        .dropdown-auth a, .dropdown-admin a, .dropdown-exit a {
            text-decoration: none;
            color: black;
        }

        .auth-container, .admin-container, .exit-container {
            position: relative;
        }




        .admin-button, .exit-button {
            /* background-color: #fcd228; */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            color: black;
        }

        .admin-button {
            background-color: #34C759; /* Зеленый цвет для админки */
            text-decoration: none;
        }

        .exit-button {
            background-color: #DC3545; /* Красный цвет для выхода */
            text-decoration: none;
        }

        .admin-button:hover, .exit-button:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

    </style>
    
</head>
<body>
    <header>
        <h1>Мебельный рай</h1>
        <div class="navigation">
            <a href="index.php" class="btn menu_site">Главная</a>
            <a href="index.php?page=catalog" class="btn menu_site">Каталог</a>
            <a href="index.php?page=basket" class="btn menu_site">Корзина</a>
            <a href="index.php?page=favorites" class="btn menu_site">Избранное</a>

            <?php
            if (!isset($_SESSION['id'])){?>
                <a href="index.php?page=auth" class="btn menu_site">Войти</a>
                <a href="index.php?page=reg" class="btn menu_site">Зарегистрироваться</a>
            <?}
            elseif ($_SESSION['status']==100){ ?>
                <a href="index.php?page=personal_account" class="btn menu_site">Личный кабинет</a>
                <a href="index.php?page=admin" class="admin-button">Админка</a>
                <a href="index.php?action=quit" class="exit-button">Выход</a>
            <?}
            elseif ($_SESSION['status']==1){ ?>
                <a href="index.php?page=personal_account" class="btn menu_site">Личный кабинет</a>
                <a href="index.php?action=quit" class="exit-button">Выход</a>
            <?}?>

        </div> 
    </header>

    <div>
        <?
        (isset($message))? print $db->message($message):print '';
        if (isset($_REQUEST['page'])){
            require("views/".$_REQUEST['page'].".php");
        }else require("views/default.php");     

        // (isset($message))? print $db->message($message):print '';
        // (isset($_REQUEST['page']))?require_once "views/".$_REQUEST['page'].".php":require_once "views/default.php";

        ?>
    </div>
</body>
</html>