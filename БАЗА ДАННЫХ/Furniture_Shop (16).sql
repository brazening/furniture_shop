-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 23 2025 г., 12:12
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `Furniture_Shop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `nam` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `nam`) VALUES
(2, 'Мебель для гостиной'),
(4, 'Мебель для спальни'),
(5, 'Мебель для столовой'),
(13, 'Офисная мебель'),
(14, 'Детская мебель'),
(15, 'Мебель для прихожей'),
(16, 'Кухонная мебель'),
(17, 'Садовая и уличная мебель'),
(20, 'Особые');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('Принят','В процессе','Отменён','Выполнен','Ожидание') DEFAULT 'Ожидание',
  `created_at` datetime DEFAULT NULL,
  `adres` text,
  `delivery_method` enum('Самовывоз','На дом') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`, `adres`, `delivery_method`) VALUES
(21, 6, '32000.00', 'Выполнен', '2025-04-22 05:54:15', '', 'Самовывоз'),
(22, 5, '32000.00', 'Ожидание', '2025-04-22 09:19:57', '', 'Самовывоз'),
(23, 13, '46700.00', 'Выполнен', '2025-04-23 02:45:52', '', 'Самовывоз'),
(24, 13, '34999.00', 'Ожидание', '2025-04-23 03:01:00', '', 'Самовывоз');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `categories_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `categories_id`) VALUES
(24, 21, 18, 1, '32000.00', 4),
(25, 22, 18, 1, '32000.00', 4),
(26, 23, 23, 1, '28900.00', 2),
(27, 23, 25, 2, '8900.00', 4),
(28, 24, 21, 1, '34999.00', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `nam_products` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `categoryid` int NOT NULL,
  `image_product` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `nam_products`, `description`, `price`, `stock`, `categoryid`, `image_product`) VALUES
(17, 'Шкаф Дубовый', 'Данный шкаф был сделан из самого лучшего дуба.', '55000.00', 0, 4, 'classes/images/wardrobe(1).jpg'),
(18, 'Двуспальная кровать Гармония', 'Кровать с массивом дуба и высококачественным ортопедическим основанием. ', '32000.00', 73, 4, 'classes/images/b(2).jpg'),
(21, 'Диван Престиж', 'Просторный диван с механизмом трансформации, обит износостойкой тканью', '34999.00', 14, 2, 'classes/images/i-(4).webp'),
(22, 'Журнальный столик Утик', 'Стеклянный столик с металлическим каркасом, современный дизайн', '12500.00', 20, 2, 'classes/images/tumby_pod_televizor_3_metra_dlinoj_2.webp'),
(23, 'Тумба под ТВ ', 'Вместительная тумба для телевизора с полками и отделениями для техники', '28900.00', 7, 2, 'classes/images/d33b5bd2ecbd34b0f71b960173df1536.jpg'),
(24, 'Комод \"Мод\"', 'Просторный комод с 5 ящиками, массив сосны', '18700.00', 12, 4, 'classes/images/JnbRwl0xvtQ.jpg'),
(25, 'Тумба прикроватная ', 'Компактная тумба с одним ящиком и открытой полкой', '8900.00', 23, 4, 'classes/images/666x444_85.jpeg'),
(26, 'Зеркало в раме ', 'Большое зеркало в деревянной резной раме для спальни', '14300.00', 7, 4, 'classes/images/4e1bd1961b607c4233058e8557cedbfd.jpg'),
(27, 'Обеденный стол ', 'Раздвижной стол из массива дерева, вмещает до 8 человек', '42500.00', 5, 5, 'classes/images/c7e6a69a871397151b69f9359bc5f6a8.jpg'),
(28, 'Буфет ', 'Вместительный буфет с витражными стеклянными дверцами', '38700.00', 3, 5, 'classes/images/a411b44b9bbd8d887f643d2a64d455b6.jpg'),
(29, 'Стул обеденный ', 'Эргономичный стул с мягким сиденьем и высокой спинкой', '11200.00', 30, 5, 'classes/images/UObG9fEUpAM.jpg'),
(30, 'Офисное кресло ', 'Эргономичное кресло с регулировкой высоты и поддержкой поясницы', '24500.00', 10, 13, 'classes/images/1056698.jpg'),
(31, 'Стол письменный ', 'Большой рабочий стол с ящиками и отсеком для системного блока', '52300.00', 6, 13, 'classes/images/666x444_85-(1).jpeg'),
(33, 'Детская кровать ', 'Односпальная кровать с бортиками и тематическим оформлением', '22800.00', 7, 14, 'classes/images/800x0.jpg'),
(35, 'Шкаф для игрушек ', 'Яркий пластиковый шкаф с контейнерами для хранения игрушек', '13400.00', 15, 14, 'classes/images/ebbd6c99e9dc92cb3d9b516cd830f066.jpg'),
(36, 'Вешалка напольная ', 'Металлическая вешалка с полкой для обуви и крючками', '8700.00', 18, 15, 'classes/images/1f12f4a3197d1099716413219c6212da.jpg'),
(38, 'Банкетка ', 'Мягкая банкетка с отсеком для хранения обуви', '15600.00', 6, 15, 'classes/images/e66809a1d02bc98235febefaa15fb57e.jpg'),
(39, 'Кухонный гарнитур ', 'Комплект из 5 модулей для небольшой кухни', '112000.00', 3, 16, 'classes/images/5d68a6b0226e4855f968eccd.jpg'),
(42, 'Садовая скамейка ', 'Деревянная скамейка со спинкой, обработанная влагостойким составом', '24700.00', 8, 17, 'classes/images/dada86da7791c3fb1bc8e0637182a0f2.jpg'),
(43, 'Стол складной ', 'Легкий алюминиевый стол с пластиковой столешницей', '13200.00', 12, 17, 'classes/images/Screenshot_9.jpg'),
(49, 'test', 'test', '15000.00', 15, 20, 'classes/images/7b48119b-2990-54c8-890f-c70b869c8556-(1).jpeg');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fio` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `image_profile` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `pass`, `fio`, `status`, `image_profile`) VALUES
(5, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Орехов Александр Павлович', 100, NULL),
(6, 'user', 'ee11cbb19052e40b07aac0ca060c23ee', 'Купитман Иван Натанович', 1, NULL),
(9, 'jew', '0505c8383fd19e8fd720a777738a57a1', 'Полимер Михаил Изральевич', 1, NULL),
(10, 'curdi', 'c0cdd82ce092b01267bdd88a8bfbb1f4', 'Махачев Дмитрий Сергеевич', 1, ''),
(12, 'serega', 'f0544a6185d7fa2c883e106f6efad5ff', 'Басистов Сергей Владимирович', 1, NULL),
(13, 'orehov', 'b3a07e91feb4ca35d047a6032ab66317', 'orehov alexander pavlovich', 1, '');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `categories_id` (`categories_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_ibfk_1` (`categoryid`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`categoryid`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
