-- =========================================
-- MIGRATION: 009_create_audit_log_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание таблицы журнала аудита всех действий в системе
-- =========================================

CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'ID пользователя (NULL для системных действий)',
  `user_type` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Тип пользователя в момент действия',
  
  -- Описание действия
  `action` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Тип действия (create, update, delete, login, etc.)',
  `entity_type` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Тип сущности (User, Property, Post, etc.)',
  `entity_id` bigint UNSIGNED DEFAULT NULL COMMENT 'ID сущности',
  `description` text COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Описание действия',
  
  -- Данные до и после изменения
  `old_values` json DEFAULT NULL COMMENT 'Значения до изменения',
  `new_values` json DEFAULT NULL COMMENT 'Новые значения',
  `changed_fields` json DEFAULT NULL COMMENT 'Список измененных полей',
  
  -- Контекст действия
  `context` json DEFAULT NULL COMMENT 'Дополнительный контекст действия',
  `tags` json DEFAULT NULL COMMENT 'Теги для категоризации (security, admin, user_action, etc.)',
  `severity` enum('low','medium','high','critical') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'medium',
  
  -- Техническая информация
  `ip_address` varchar(45) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_0900_ai_ci,
  `session_id` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `request_id` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Уникальный ID запроса',
  `url` varchar(500) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `method` varchar(10) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'HTTP метод',
  `referer` varchar(500) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  
  -- Результат действия
  `status` enum('success','failed','error','warning') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'success',
  `error_message` text COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `response_time` int UNSIGNED DEFAULT NULL COMMENT 'Время выполнения в миллисекундах',
  
  -- Дополнительная информация для безопасности
  `suspicious` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Подозрительное действие',
  `risk_level` enum('none','low','medium','high','critical') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'none',
  
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `audit_log_user_id_index` (`user_id`),
  KEY `audit_log_action_index` (`action`),
  KEY `audit_log_entity_type_index` (`entity_type`),
  KEY `audit_log_entity_id_index` (`entity_id`),
  KEY `audit_log_severity_index` (`severity`),
  KEY `audit_log_status_index` (`status`),
  KEY `audit_log_suspicious_index` (`suspicious`),
  KEY `audit_log_risk_level_index` (`risk_level`),
  KEY `audit_log_ip_address_index` (`ip_address`),
  KEY `audit_log_session_id_index` (`session_id`),
  KEY `audit_log_created_at_index` (`created_at`),
  KEY `audit_log_composite_search_index` (`user_id`,`action`,`created_at`),
  
  -- Полнотекстовый поиск по описанию
  FULLTEXT KEY `audit_log_fulltext_search` (`description`),
  
  CONSTRAINT `audit_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Журнал аудита всех действий в системе';