-- =========================================
-- MIGRATION: 005_create_posts_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание таблицы публикаций (новости, статьи, блог)
-- =========================================

CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'Автор поста',
  `category_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Категория поста',
  `title` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_0900_ai_ci COMMENT 'Краткое описание',
  `content` longtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content_html` longtext COLLATE utf8mb4_0900_ai_ci COMMENT 'HTML версия контента',
  `featured_image` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `featured_image_alt` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  
  -- Тип и статус
  `post_type` enum('article','news','blog','guide','review') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'article',
  `status` enum('draft','published','archived','scheduled') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','private','protected') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'public',
  `password` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Пароль для protected постов',
  
  -- Планирование публикации
  `published_at` timestamp NULL DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  
  -- Настройки комментариев и взаимодействий
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `comments_count` int UNSIGNED NOT NULL DEFAULT '0',
  `likes_count` int UNSIGNED NOT NULL DEFAULT '0',
  `views_count` int UNSIGNED NOT NULL DEFAULT '0',
  `shares_count` int UNSIGNED NOT NULL DEFAULT '0',
  
  -- SEO метаданные
  `seo_title` varchar(191) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `seo_description` text COLLATE utf8mb4_0900_ai_ci,
  `seo_keywords` text COLLATE utf8mb4_0900_ai_ci,
  `seo_canonical_url` varchar(500) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  
  -- Дополнительные метаданные
  `meta_data` json DEFAULT NULL COMMENT 'Дополнительные данные поста',
  `reading_time` int UNSIGNED DEFAULT NULL COMMENT 'Время чтения в минутах',
  
  -- Модерация
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_sticky` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Закрепленный пост',
  `moderated_at` timestamp NULL DEFAULT NULL,
  `moderated_by` bigint UNSIGNED DEFAULT NULL,
  
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`),
  KEY `posts_user_id_index` (`user_id`),
  KEY `posts_category_id_index` (`category_id`),
  KEY `posts_post_type_index` (`post_type`),
  KEY `posts_status_index` (`status`),
  KEY `posts_published_at_index` (`published_at`),
  KEY `posts_is_featured_index` (`is_featured`),
  KEY `posts_is_sticky_index` (`is_sticky`),
  KEY `posts_moderated_by_index` (`moderated_by`),
  
  -- Полнотекстовый поиск
  FULLTEXT KEY `posts_fulltext_search` (`title`,`excerpt`,`content`),
  
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `posts_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Публикации: статьи, новости, блог постыги';