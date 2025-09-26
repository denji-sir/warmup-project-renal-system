-- =========================================
-- MIGRATION: 003_create_properties_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание основной таблицы объектов недвижимости
-- =========================================

CREATE TABLE IF NOT EXISTS `properties` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'ID риелтора/владельца',
  `category_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text COLLATE utf8mb4_0900_ai_ci,
  `short_description` varchar(500) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  
  -- Тип операции и цена
  `operation_type` enum('sale','rent') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'sale',
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `price_per_month` decimal(15,2) DEFAULT NULL COMMENT 'Цена аренды за месяц',
  `deposit` decimal(15,2) DEFAULT NULL COMMENT 'Залог за аренду',
  `currency` varchar(3) COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'RUB',
  `price_negotiable` tinyint(1) NOT NULL DEFAULT '0',
  
  -- Адрес и геолокация
  `country` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT 'Россия',
  `region` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `district` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `house_number` varchar(20) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `apartment` varchar(20) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `full_address` varchar(500) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  
  -- Характеристики недвижимости
  `property_type` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'apartment, house, commercial, etc.',
  `rooms` int DEFAULT NULL COMMENT 'Количество комнат',
  `bedrooms` int DEFAULT NULL COMMENT 'Количество спален',
  `bathrooms` int DEFAULT NULL COMMENT 'Количество ванных',
  `area_total` decimal(8,2) DEFAULT NULL COMMENT 'Общая площадь в кв.м',
  `area_living` decimal(8,2) DEFAULT NULL COMMENT 'Жилая площадь в кв.м',
  `area_kitchen` decimal(8,2) DEFAULT NULL COMMENT 'Площадь кухни в кв.м',
  `floor` int DEFAULT NULL COMMENT 'Этаж',
  `total_floors` int DEFAULT NULL COMMENT 'Всего этажей в доме',
  `year_built` int DEFAULT NULL COMMENT 'Год постройки',
  `condition_type` enum('new','excellent','good','fair','renovation_needed') COLLATE utf8mb4_0900_ai_ci DEFAULT 'good',
  
  -- Дополнительные характеристики (JSON)
  `features` json DEFAULT NULL COMMENT 'Дополнительные удобства и особенности',
  
  -- Статус и метаданные
  `status` enum('draft','active','sold','rented','archived','moderation') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Премиум объявление',
  `is_urgent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Срочная продажа',
  `available_from` date DEFAULT NULL COMMENT 'Доступно с даты',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Истекает',
  
  -- Статистика
  `views_count` int UNSIGNED NOT NULL DEFAULT '0',
  `favorites_count` int UNSIGNED NOT NULL DEFAULT '0',
  `contacts_count` int UNSIGNED NOT NULL DEFAULT '0',
  
  -- SEO
  `seo_title` varchar(191) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `seo_description` text COLLATE utf8mb4_0900_ai_ci,
  `seo_keywords` text COLLATE utf8mb4_0900_ai_ci,
  
  -- Временные метки
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `properties_slug_unique` (`slug`),
  KEY `properties_user_id_index` (`user_id`),
  KEY `properties_category_id_index` (`category_id`),
  KEY `properties_operation_type_index` (`operation_type`),
  KEY `properties_status_index` (`status`),
  KEY `properties_city_index` (`city`),
  KEY `properties_price_index` (`price`),
  KEY `properties_is_featured_index` (`is_featured`),
  KEY `properties_published_at_index` (`published_at`),
  KEY `properties_location_index` (`latitude`,`longitude`),
  KEY `properties_area_rooms_index` (`area_total`,`rooms`),
  
  -- Полнотекстовые индексы для поиска
  FULLTEXT KEY `properties_fulltext_search` (`title`,`description`,`short_description`,`full_address`),
  
  CONSTRAINT `properties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `properties_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Основная таблица объектов недвижимости';