-- =========================================
-- MIGRATION: 007_create_property_views_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание таблицы просмотров объектов недвижимости
-- =========================================

CREATE TABLE IF NOT EXISTS `property_views` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'NULL для анонимных пользователей',
  `ip_address` varchar(45) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_0900_ai_ci,
  `referer` varchar(500) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `view_duration` int UNSIGNED DEFAULT NULL COMMENT 'Время просмотра в секундах',
  `device_type` enum('desktop','tablet','mobile','bot') COLLATE utf8mb4_0900_ai_ci DEFAULT 'desktop',
  `browser` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `os` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_unique` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Уникальный просмотр от этого пользователя/IP',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `property_views_property_id_index` (`property_id`),
  KEY `property_views_user_id_index` (`user_id`),
  KEY `property_views_ip_address_index` (`ip_address`),
  KEY `property_views_created_at_index` (`created_at`),
  KEY `property_views_is_unique_index` (`is_unique`),
  KEY `property_views_device_type_index` (`device_type`),
  KEY `property_views_session_property_index` (`session_id`,`property_id`),
  
  CONSTRAINT `property_views_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_views_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Статистика просмотров объектов недвижимости';