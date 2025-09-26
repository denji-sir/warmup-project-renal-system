-- =========================================
-- MIGRATION: 006_create_user_favorites_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание таблицы избранных объектов пользователей
-- =========================================

CREATE TABLE IF NOT EXISTS `user_favorites` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `property_id` bigint UNSIGNED NOT NULL,
  `notes` text COLLATE utf8mb4_0900_ai_ci COMMENT 'Заметки пользователя об объекте',
  `priority` enum('low','medium','high') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'medium',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `viewed_at` timestamp NULL DEFAULT NULL COMMENT 'Когда пользователь последний раз просматривал',
  `contacted_realtor` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Связывался ли с риелтором',
  `contacted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_favorites_user_property_unique` (`user_id`,`property_id`),
  KEY `user_favorites_user_id_index` (`user_id`),
  KEY `user_favorites_property_id_index` (`property_id`),
  KEY `user_favorites_priority_index` (`priority`),
  KEY `user_favorites_is_active_index` (`is_active`),
  KEY `user_favorites_created_at_index` (`created_at`),
  
  CONSTRAINT `user_favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_favorites_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Избранные объекты недвижимости пользователей';