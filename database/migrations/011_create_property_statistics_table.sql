-- =========================================
-- MIGRATION: 011_create_property_statistics_table
-- DATE: 2024-01-02
-- DESCRIPTION: Создание таблиц для статистики и аналитики
-- =========================================

CREATE TABLE IF NOT EXISTS `property_statistics` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `views` int UNSIGNED NOT NULL DEFAULT '0',
  `contacts` int UNSIGNED NOT NULL DEFAULT '0',
  `favorites` int UNSIGNED NOT NULL DEFAULT '0',
  `shares` int UNSIGNED NOT NULL DEFAULT '0',
  `unique_visitors` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `property_statistics_property_date_unique` (`property_id`, `date`),
  KEY `property_statistics_date_index` (`date`),
  
  CONSTRAINT `property_statistics_property_id_foreign` 
    FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Ежедневная статистика по объектам';

-- Таблица для отслеживания действий пользователей
CREATE TABLE IF NOT EXISTS `user_activity_log` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_0900_ai_ci,
  `action` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `resource_type` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `resource_id` bigint UNSIGNED DEFAULT NULL,
  `details` json DEFAULT NULL COMMENT 'Дополнительная информация о действии',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `user_activity_log_user_id_index` (`user_id`),
  KEY `user_activity_log_action_index` (`action`),
  KEY `user_activity_log_resource_index` (`resource_type`, `resource_id`),
  KEY `user_activity_log_created_at_index` (`created_at`),
  
  CONSTRAINT `user_activity_log_user_id_foreign` 
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Лог действий пользователей';

-- Таблица настроек системы
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_0900_ai_ci,
  `type` enum('string','integer','boolean','json','text') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'string',
  `group` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT 'general',
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Доступно через публичный API',
  `description` text COLLATE utf8mb4_0900_ai_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`),
  KEY `system_settings_group_index` (`group`),
  KEY `system_settings_is_public_index` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Настройки системы';

-- Вставка базовых настроек
INSERT INTO `system_settings` (`key`, `value`, `type`, `group`, `is_public`, `description`) VALUES
('site_name', 'Система управления недвижимостью', 'string', 'general', 1, 'Название сайта'),
('site_description', 'Профессиональная платформа для управления недвижимостью', 'text', 'general', 1, 'Описание сайта'),
('contact_email', 'info@realestate.local', 'string', 'contact', 1, 'Email для связи'),
('contact_phone', '+7 (495) 123-45-67', 'string', 'contact', 1, 'Телефон для связи'),
('max_upload_size', '10485760', 'integer', 'uploads', 0, 'Максимальный размер загружаемого файла в байтах'),
('allowed_image_types', '["jpg", "jpeg", "png", "webp"]', 'json', 'uploads', 0, 'Разрешенные типы изображений'),
('properties_per_page', '12', 'integer', 'display', 1, 'Количество объектов на странице'),
('enable_registration', 'true', 'boolean', 'auth', 1, 'Разрешить регистрацию новых пользователей'),
('require_email_verification', 'false', 'boolean', 'auth', 0, 'Требовать подтверждение email'),
('default_currency', 'RUB', 'string', 'localization', 1, 'Валюта по умолчанию'),
('default_country', 'Россия', 'string', 'localization', 1, 'Страна по умолчанию'),
('google_analytics_id', '', 'string', 'analytics', 1, 'ID Google Analytics'),
('yandex_metrika_id', '', 'string', 'analytics', 1, 'ID Яндекс.Метрики');