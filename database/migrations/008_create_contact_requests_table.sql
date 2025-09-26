-- =========================================
-- MIGRATION: 008_create_contact_requests_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание таблицы заявок с формы обратной связи
-- =========================================

CREATE TABLE IF NOT EXISTS `contact_requests` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'ID пользователя (если авторизован)',
  `property_id` bigint UNSIGNED DEFAULT NULL COMMENT 'ID объекта (если заявка по конкретному объекту)',
  `realtor_id` bigint UNSIGNED DEFAULT NULL COMMENT 'ID риелтора, которому адресована заявка',
  
  -- Контактные данные
  `name` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  
  -- Тип и содержание заявки
  `request_type` enum('general','property_inquiry','viewing_request','callback','complaint','suggestion') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'general',
  `subject` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `message` text COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `preferred_contact_method` enum('email','phone','any') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'any',
  `preferred_time` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  
  -- Статус обработки
  `status` enum('new','in_progress','resolved','closed','spam') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'new',
  `priority` enum('low','normal','high','urgent') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'normal',
  
  -- Обработка заявки
  `assigned_to` bigint UNSIGNED DEFAULT NULL COMMENT 'Кому назначена заявка',
  `response` text COLLATE utf8mb4_0900_ai_ci COMMENT 'Ответ на заявку',
  `internal_notes` text COLLATE utf8mb4_0900_ai_ci COMMENT 'Внутренние заметки',
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint UNSIGNED DEFAULT NULL,
  
  -- Техническая информация
  `ip_address` varchar(45) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_0900_ai_ci,
  `referer` varchar(500) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `utm_source` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `utm_medium` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `utm_campaign` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  
  -- Файлы (JSON массив путей к файлам)
  `attachments` json DEFAULT NULL,
  
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `contact_requests_user_id_index` (`user_id`),
  KEY `contact_requests_property_id_index` (`property_id`),
  KEY `contact_requests_realtor_id_index` (`realtor_id`),
  KEY `contact_requests_request_type_index` (`request_type`),
  KEY `contact_requests_status_index` (`status`),
  KEY `contact_requests_priority_index` (`priority`),
  KEY `contact_requests_assigned_to_index` (`assigned_to`),
  KEY `contact_requests_resolved_by_index` (`resolved_by`),
  KEY `contact_requests_email_index` (`email`),
  KEY `contact_requests_created_at_index` (`created_at`),
  
  -- Полнотекстовый поиск
  FULLTEXT KEY `contact_requests_fulltext_search` (`subject`,`message`),
  
  CONSTRAINT `contact_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `contact_requests_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `contact_requests_realtor_id_foreign` FOREIGN KEY (`realtor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `contact_requests_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `contact_requests_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Заявки с форм обратной связи';