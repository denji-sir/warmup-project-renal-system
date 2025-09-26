-- =========================================
-- MIGRATION: 010_create_system_roles_table
-- DATE: 2024-01-02
-- DESCRIPTION: Создание системы ролей и прав доступа
-- =========================================

CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text COLLATE utf8mb4_0900_ai_ci,
  `permissions` json DEFAULT NULL COMMENT 'Права доступа в JSON формате',
  `level` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Уровень доступа (1-10)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`),
  KEY `roles_name_index` (`name`),
  KEY `roles_level_index` (`level`),
  KEY `roles_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Роли пользователей в системе';

-- Добавление столбца role_id в таблицу users
ALTER TABLE `users` ADD COLUMN `role_id` bigint UNSIGNED DEFAULT NULL AFTER `role`;
ALTER TABLE `users` ADD KEY `users_role_id_index` (`role_id`);

-- Внешний ключ для связи пользователей с ролями
ALTER TABLE `users` ADD CONSTRAINT `users_role_id_foreign` 
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Вставка базовых ролей
INSERT INTO `roles` (`name`, `slug`, `description`, `permissions`, `level`) VALUES
('Администратор', 'admin', 'Полный доступ ко всем функциям системы', 
 '["manage_users", "manage_properties", "manage_categories", "manage_system", "view_analytics", "moderate_content"]', 10),

('Риелтор', 'realtor', 'Управление объектами недвижимости', 
 '["create_properties", "edit_own_properties", "view_properties", "upload_images", "view_contacts", "moderate_own"]', 5),

('Покупатель', 'buyer', 'Поиск и просмотр недвижимости', 
 '["view_properties", "add_favorites", "send_requests", "view_contacts"]', 2),

('Гость', 'guest', 'Ограниченный просмотр контента', 
 '["view_properties", "view_public"]', 1);

-- Обновление существующих пользователей с назначением ролей
UPDATE `users` SET `role_id` = (SELECT id FROM roles WHERE slug = 'admin') WHERE `role` = 'admin';
UPDATE `users` SET `role_id` = (SELECT id FROM roles WHERE slug = 'realtor') WHERE `role` = 'realtor';
UPDATE `users` SET `role_id` = (SELECT id FROM roles WHERE slug = 'buyer') WHERE `role` = 'buyer';
UPDATE `users` SET `role_id` = (SELECT id FROM roles WHERE slug = 'guest') WHERE `role` = 'guest' OR `role` IS NULL;