<?php
session_start();
// require_once 'connect.php'; // Подключите ваш файл с подключением к БД

// // Ключ для хранения избранного
// $userKey = $_SESSION['user_favorite_key'] ?? '';

// Редирект неавторизованных
if (!isset($_SESSION['id'])) {
    echo '<script>window.location.href="index.php?page=auth";</script>';

    exit();
}

// Генерируем ключ как в каталоге (на случай прямого захода)
if (!isset($_SESSION['user_favorite_key'])) {
    $_SESSION['user_favorite_key'] = 'user_' . $_SESSION['id'];
}

require_once 'connect.php';
$userKey = $_SESSION['user_favorite_key'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Избранное</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Стили карточек */
        .card-img-top { height: 200px; object-fit: cover; }
        .bi-heart.active { color: #e74c3c; }
        .favorite-wrapper {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }
        .card {
    transition: all 0.3s ease;
    position: relative;
}

.card.removing {
    transform: scale(0.9);
    opacity: 0;
}
    </style>
</head>
<body style="background-color: #ECF0F1">
    <section>
        <h2 class="text-center mb-4">Избранные товары</h2>
        <div id="favorites-container" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3"></div>
        <div id="empty-message" class="text-center" style="display: none;">
            <p class="text-muted">В избранном пока ничего нет</p>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    $(document).ready(function() {
        const userKey = '<?= $userKey ?>';

        // Миграция гостевых избранных в аккаунт при любом заходе (в том числе на favorites)
        // Миграция гостевых данных при первом заходе
if (userKey.startsWith('user_')) {
    // Находим все гостевые ключи
    const guestKeys = Object.keys(localStorage).filter(key => key.startsWith('guest_'));
    
    // Объединяем все избранное
    let mergedFavorites = JSON.parse(localStorage.getItem(userKey) || '[]');
    guestKeys.forEach(key => {
        const guestFavs = JSON.parse(localStorage.getItem(key) || '[]');
        mergedFavorites = [...new Set([...mergedFavorites, ...guestFavs])];
        localStorage.removeItem(key);
    });
    
    // Сохраняем объединенный список
    localStorage.setItem(userKey, JSON.stringify(mergedFavorites));
    
    // Синхронизируем с сервером (если нужно)
    $.post('sync_favorite.php', { 
        favorites: mergedFavorites 
    });
}

        function loadFavorites() {
            const favorites = JSON.parse(localStorage.getItem(userKey) || '[]');
            $('#favorites-container').empty();
            if (favorites.length === 0) {
                $('#empty-message').show();
                return;
            }
            $('#empty-message').hide();
            $.ajax({
                url: 'get_favorites.php',
                method: 'POST',
                data: { ids: favorites },
                success: function(response) {
                    if (response.data && response.data.length) {
                        renderProducts(response.data);
                    } else {
                        $('#empty-message').show();
                    }
                },
                error: function() {
                    $('#empty-message').show();
                }
            });
        }

        function renderProducts(products) {
            const container = $('#favorites-container');
            container.empty();
            products.forEach(product => {
                const id = product.id.toString();
                const card = `
                    <div class="col">
                        <div class="card h-100">
                            <div class="favorite-wrapper">
                                <button class="btn btn-link btn-favorite" data-product-id="${id}">
                                    <i class="bi bi-heart active"></i>
                                </button>
                            </div>
                            <img src="${product.image_product}" class="card-img-top" alt="${product.nam_products}">
                            <div class="card-body">
                                <h5 class="card-title">${product.nam_products}</h5>
                                ${product.category_name ? `<p class="text-muted">${product.category_name}</p>` : ''}
                                <p class="fw-bold">${product.price} ₽</p>
                            </div>
                        </div>
                    </div>
                `;
                container.append(card);
            });
        }

        // Обработчик удаления из избранного
        $('#favorites-container').on('click', '.btn-favorite', function() {
            const productId = $(this).data('product-id').toString();
            let favorites = JSON.parse(localStorage.getItem(userKey) || '[]');
            favorites = favorites.filter(id => id !== productId);
            localStorage.setItem(userKey, JSON.stringify(favorites));
            $(this).closest('.col').remove();
            if (!$('#favorites-container').children().length) {
                $('#empty-message').show();
            }
            const $card = $(this).closest('.col');
$card.addClass('removing');
setTimeout(() => {
    $card.remove();
    if (!$('#favorites-container').children().length) {
        $('#empty-message').show();
    }
}, 300);
        });

        loadFavorites();
    });
    </script>
</body>
</html>