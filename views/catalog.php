<?php
session_start();

// Добавляем уникальный ключ для хранения избранных
// if (!isset($_SESSION['user_favorite_key'])) {
//     if (isset($_SESSION['id'])) {
//         $_SESSION['user_favorite_key'] = 'user_' . $_SESSION['id'];
//     } else {
//         $_SESSION['user_favorite_key'] = 'guest_' . bin2hex(random_bytes(8));
//     }
// }

if (!isset($_SESSION['user_favorite_key'])) {
    if (isset($_SESSION['id'])) {
        $_SESSION['user_favorite_key'] = 'user_' . $_SESSION['id'];
    } else {
        $_SESSION['user_favorite_key'] = 'guest_' . bin2hex(random_bytes(8));
    }
}

// Формирование условий фильтрации
$filters = [];
$params = [];

if (!empty($_GET['search_products'])) {
    $filters[] = "p.nam_products LIKE :search";
    $params[':search'] = "%" . $_GET['search_products'] . "%";
}

if (!empty($_GET['min_price'])) {
    $filters[] = "p.price >= :min_price";
    $params[':min_price'] = (int)$_GET['min_price'];
}

if (!empty($_GET['max_price'])) {
    $filters[] = "p.price <= :max_price";
    $params[':max_price'] = (int)$_GET['max_price'];
}

if (!empty($_GET['category'])) {
    $filters[] = "p.categoryid = :categoryid";
    $params[':categoryid'] = (int)$_GET['category'];
}

// Запрос с объединением таблиц. Если у вас первичный ключ в categories называется id, оставляем так.
$query = "SELECT p.*, c.nam AS category_name 
          FROM products p 
          LEFT JOIN categories c ON p.categoryid = c.id";

if (!empty($filters)) {
    $query .= " WHERE " . implode(" AND ", $filters);
}

// Подготовка и выполнение запроса
$stmt = $db->dbs->prepare($query);
$stmt->execute($params);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог товаров</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Стили для карточек товаров */
        #gallery {
            padding: 20px 0;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-8px);
        }
        .card-img-top {
            height: 200px; /* уменьшили высоту изображения */
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-text {
            flex-grow: 1;
            color: #666;
        }
        .fw-bold {
            color: #e74c3c;
        }
        /* Стили для фильтра */
        .filter-panel {
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        /* Стили для сердечка */
        .bi-heart {
    color: #ccc;
    transition: color 0.2s;
    font-size: 1.5rem;
    cursor: pointer;
}

.bi-heart.active {
    color: #e74c3c;
}

.favorite-wrapper {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }
    </style>
</head>
<body>
    <section>
        <h2 class="mt-4 mb-4 text-center">Каталог</h2>
    </section>

    <section>
        <div class="container-fluid">
            <div class="row">
                <!-- Фильтр в левой колонке -->
                <div class="col-md-3">
                    <div class="filter-panel">
                        <h4>Фильтр</h4>
                        <form method="GET" id="filterForm">
                            <!-- Скрытый параметр для страницы каталога -->
                            <input type="hidden" name="page" value="catalog">
                            <!-- Поиск по названию -->
                            <div class="mb-3">
                                <label for="search_products" class="form-label">Название товара</label>
                                <input type="text" name="search_products" id="search_products" class="form-control" 
                                       placeholder="Поиск товара..." value="<?= isset($_GET['search_products']) ? htmlspecialchars($_GET['search_products']) : '' ?>">
                            </div>
                            <!-- Ползунок цен -->
                            <div class="mb-3">
                                <label for="price_range" class="form-label">Диапазон цен (₽)</label>
                                <!-- Скрытые поля для min и max цены -->
                                <input type="hidden" name="min_price" id="min_price" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : 0 ?>">
                                <input type="hidden" name="max_price" id="max_price" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : 100000 ?>">
                                <!-- Вывод текущего диапазона -->
                                <p id="price_range_display">
                                    <?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : 0 ?> ₽ - <?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : 100000 ?> ₽
                                </p>
                                <div id="price-slider"></div>
                            </div>
                            <!-- Фильтр по категории -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Категория</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="">Все категории</option>
                                    <?php
                                    $cats = $db->dbs->query("SELECT id, nam FROM categories")->fetchAll();
                                    foreach ($cats as $cat) {
                                        $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($cat['id']) . "' $selected>" . htmlspecialchars($cat['nam']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Применить</button>
                            <a href="?page=catalog" class="btn btn-secondary">Сбросить фильтр</a>                            
                        </form>
                    </div>
                </div>
                <!-- Товары в правой колонке -->
                <div class="col-md-9">
                <section id="gallery">
                        <!-- Используем row-cols-1 для мобильных, row-cols-sm-2 и row-cols-md-3 для больших экранов -->
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                            <?php
                            if ($stmt->rowCount() > 0) {
                                while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="favorite-wrapper">
                                                <button class="btn btn-link btn-favorite" data-product-id="<?= htmlspecialchars($res['id']) ?>">
                                                    <i class="bi bi-heart"></i>
                                                </button>
                                            </div>
                                            <img src="<?= htmlspecialchars($res['image_product']) ?>" class="card-img-top" 
                                                 alt="<?= htmlspecialchars($res['nam_products']) ?>">
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title"><?= htmlspecialchars($res['nam_products']) ?></h5>
                                                <?php if (!empty($res['category_name'])): ?>
                                                    <p class="text-muted mb-1"><?= htmlspecialchars($res['category_name']) ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($res['description'])): ?>
                                                    <p class="card-text"><?= htmlspecialchars($res['description']) ?></p>
                                                <?php else: ?>
                                                    <p class="card-text" style="min-height:30px;"></p>
                                                <?php endif; ?>
                                                    <p class="text-success mb-1">Количество товара: <?= htmlspecialchars($res['stock']) ?> шт.</p>                                                <div class="mt-auto">
                                                    <p class="fw-bold"><?= htmlspecialchars($res['price']) ?> ₽</p>
                                                    <?php if (!isset($_SESSION['id'])): ?>
                                                        <a href="index.php?page=auth" class="text-muted">Авторизуйтесь для покупки</a>
                                                    <?php elseif ($res['stock'] <= 0): ?>
                                                        <p><strong>ТОВАРА НЕТ В НАЛИЧИИ</strong></p>
                                                    <?php else: ?>
                                                        <a href="index.php?page=basket&action=add&id=<?= htmlspecialchars($res['id']) ?>" class="btn btn-success btn-sm">Добавить в корзину</a>
                                                    <?php endif; ?>
                                                    
                                                    
                                                        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } else {
                                echo '<p>Нет доступных товаров.</p>';
                            }
                            ?>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </section>

    <!-- Скрипт для jQuery UI слайдера -->
    <script>
        $(function() {
            var minPrice = <?= isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0 ?>;
            var maxPrice = <?= isset($_GET['max_price']) ? (int)$_GET['max_price'] : 100000 ?>;
            var overallMin = 0;
            var overallMax = 100000;
            
            $("#price-slider").slider({
                range: true,
                min: overallMin,
                max: overallMax,
                values: [minPrice, maxPrice],
                slide: function(event, ui) {
                    $("#price_range_display").text(ui.values[0] + " ₽ - " + ui.values[1] + " ₽");
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
        });
    </script>

<!-- Скрипт для клика по сердечку (избранное) -->
<script>
        $(document).ready(function() {
            const userKey = '<?= $_SESSION['user_favorite_key'] ?>';
            
            // Функция для обновления состояния иконок
            function updateFavorites() {
                $('.btn-favorite').each(function() {
                    const productId = $(this).data('product-id');
                    // Если в localStorage ещё ничего нет, используем строку "[]"
                    const favorites = JSON.parse(localStorage.getItem(userKey) || "[]");
                    // Если productId присутствует в массиве избранного, добавляем класс active
                    $(this).find('.bi-heart').toggleClass('active', favorites.includes(productId.toString()));
                });
            }

            // Обработчик клика по кнопке избранного
            $('.btn-favorite').on('click', function(e) {
                e.preventDefault();
                const productId = $(this).data('product-id').toString();
                let favorites = JSON.parse(localStorage.getItem(userKey) || "[]");

                if (favorites.includes(productId)) {
                    // Удаление из избранного
                    favorites = favorites.filter(id => id !== productId);
                } else {
                    // Добавление в избранное
                    favorites.push(productId);
                }

                localStorage.setItem(userKey, JSON.stringify(favorites));
                updateFavorites();
            });

            updateFavorites();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>
</body>
</html>
