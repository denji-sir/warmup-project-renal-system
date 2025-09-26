-- =========================================
-- MIGRATION: 004_create_property_images_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание таблицы изображений для объектов недвижимости
-- =========================================

CREATE TABLE IF NOT EXISTS `property_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `path` varchar(500) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `thumbnail_path` varchar(500) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `size` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Размер файла в байтах',
  `width` int UNSIGNED DEFAULT NULL,
  `height` int UNSIGNED DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `caption` text COLLATE utf8mb4_0900_ai_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Главное фото',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `uploaded_by` bigint UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, загрузившего фото',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `property_images_property_id_index` (`property_id`),
  KEY `property_images_is_primary_index` (`is_primary`),
  KEY `property_images_sort_order_index` (`sort_order`),
  KEY `property_images_is_active_index` (`is_active`),
  KEY `property_images_uploaded_by_index` (`uploaded_by`),
  
  CONSTRAINT `property_images_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_images_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Изображения для объектов недвижимости';