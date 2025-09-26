-- =========================================
-- MIGRATION: 002_create_categories_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание таблицы категорий недвижимости
-- =========================================

CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text COLLATE utf8mb4_0900_ai_ci,
  `icon` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `properties_count` int UNSIGNED NOT NULL DEFAULT '0',
  `seo_title` varchar(191) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `seo_description` text COLLATE utf8mb4_0900_ai_ci,
  `seo_keywords` text COLLATE utf8mb4_0900_ai_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_name_index` (`name`),
  KEY `categories_parent_id_index` (`parent_id`),
  KEY `categories_is_active_index` (`is_active`),
  KEY `categories_sort_order_index` (`sort_order`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Категории недвижимости (квартиры, дома, коммерческая и т.д.)';