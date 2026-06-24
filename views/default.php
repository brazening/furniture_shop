<?php
// Подключение к базе (если ещё не подключено)
require_once 'connect.php'; // путь до подключения к БД

// Получение 3 случайных товаров из БД
$stmt = $db->dbs->query("SELECT * FROM products WHERE stock > 0 ORDER BY RAND() LIMIT 4");
$randomProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* Стили секции преимуществ */
.features {
            padding: 60px 0;
            background-color: #f9f9f9;
        }
        
        .features .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 30px;
        }
        
        .feature-item {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 30px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .feature-item i {
            font-size: 40px;
            color: #A6B1B4;
            margin-bottom: 20px;
        }
        
        .feature-item h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .feature-item p {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }
        
        /* Адаптивность */
        @media (max-width: 768px) {
            .features .container {
                flex-direction: column;
                gap: 20px;
            }
            
            .feature-item {
                min-width: 100%;
            }
        }

        .product-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        object-position: center;
    }
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>


<main class="about-store" style="width: 100%; margin: 60px 0; padding: 0 40px; color: #444; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-sizing: border-box;">
  <div style="max-width: 1400px; margin: 0 auto; display: flex; gap: 50px; align-items: flex-start;">
    <!-- Текстовая часть -->
    <div style="flex: 1.2; min-width: 500px;">
      <h2 style="color:#222; margin-bottom: 30px; font-size: 2.2em; line-height: 1.3;">О нашем магазине мебели</h2>
      <div style="display: flex; flex-direction: column; gap: 25px; font-size: 1.1em; line-height: 1.6;">
        <p>Мы предлагаем широкий выбор качественной и стильной мебели для дома и офиса. У нас вы найдете современные и классические модели, которые помогут создать уют и комфорт в любом интерьере.</p>
        <p>Гарантируем высокое качество, удобные условия доставки и профессиональную поддержку на каждом этапе покупки.</p>
        <p>Откройте для себя мебель, которая сочетает в себе функциональность и дизайн, и сделайте свой дом по-настоящему особенным.</p>
      </div>
    </div>

    <!-- Изображение -->
    <div style="flex: 1; max-width: 700px; position: relative;">
      <img src="https://images.unsplash.com/photo-1616627561839-074385245ff6?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" 
           alt="Современная гостиная с мебелью" 
           style="width: 100%; height: 500px; object-fit: cover; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
    </div>
  </div>
</main>

<style>
  @media (max-width: 1024px) {
    .about-store {
      padding: 0 25px !important;
    }
    .about-store > div {
      flex-direction: column;
      gap: 40px !important;
    }
    .about-store img {
      height: 400px !important;
    }
    .about-store div[style*="min-width:500px"] {
      min-width: 100% !important;
    }
  }

  @media (max-width: 768px) {
    .about-store {
      padding: 0 15px !important;
      margin: 40px 0 !important;
    }
    .about-store img {
      height: 300px !important;
    }
    h2 {
      font-size: 1.8em !important;
    }
  }
</style>





<!-- Преимущества -->
    <section class="features">
        <div class="container">
            <div class="feature-item">
                <i class="fas fa-truck"></i>
                <h3>Бесплатная доставка</h3>
                <p>При заказе от 10 000 ₽</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-undo"></i>
                <h3>Возврат</h3>
                <p>В течение 14 дней</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-gem"></i>
                <h3>Качество</h3>
                <p>Гарантия на все товары</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <h3>Поддержка</h3>
                <p>24/7</p>
            </div>
        </div>
    </section>









<section class="py-5 bg-light">
  <div class="container">
    <h2 class="mb-4 text-center">Рекомендуем вам</h2>
    <div class="row g-3">
      <?php foreach ($randomProducts as $product): ?>
        <div class="col-6 col-md-3 mb-3"> <!-- Изменили колонки на 3 для 4 элементов -->
          <div class="card h-100 shadow-sm">
            <img src="<?= htmlspecialchars($product['image_product']) ?>" class="product-image card-img-top" alt="<?= htmlspecialchars($product['nam_products']) ?>">
            <div class="card-body d-flex flex-column p-3">
              <h5 class="card-title fs-6"><?= htmlspecialchars($product['nam_products']) ?></h5>
              <p class="card-text small text-muted flex-grow-1"><?= htmlspecialchars($product['description']) ?></p>
              <!-- <p class="card-text fw-bold mb-2"><?= number_format($product['price'], 2) ?> ₽</p> -->
              <p class="card-text fw-bold mb-2"><?= htmlspecialchars($product['price']) ?> ₽</p>
              <a href="index.php?page=catalog" class="btn btn-primary btn-sm mt-auto">Подробнее</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>



