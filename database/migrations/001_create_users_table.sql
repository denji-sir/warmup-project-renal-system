-- =========================================
-- MIGRATION: 001_create_users_table
-- DATE: 2024-01-01
-- DESCRIPTION: Создание таблицы пользователей с ролевой системой
-- =========================================

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` enum('admin','realtor','tenant') COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'tenant',
  `first_name` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_0900_ai_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password_reset_token` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password_reset_expires` timestamp NULL DEFAULT NULL,
  `email_verification_token` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `login_attempts` int NOT NULL DEFAULT '0',
  `locked_until` timestamp NULL DEFAULT NULL,
  `preferences` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_email_verified_at_index` (`email_verified_at`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_password_reset_token_index` (`password_reset_token`),
  KEY `users_email_verification_token_index` (`email_verification_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Пользователи системы с ролями admin/realtor/tenant';