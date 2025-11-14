-- ReadWise AI - Esquema MySQL
-- Charset/engine padrão
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `readwise_ai` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `readwise_ai`;

-- Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(160) NOT NULL,
  `email` VARCHAR(160) NOT NULL,
  `telefone` VARCHAR(32) NULL,
  `universidade` VARCHAR(160) NULL,
  `curso` VARCHAR(160) NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `plano_atual` ENUM('gratuito','mensal','payg') NOT NULL DEFAULT 'gratuito',
  `uso_gratuito_usado` TINYINT(1) NOT NULL DEFAULT 0,
  `asaas_customer_id` VARCHAR(64) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`),
  KEY `idx_plano` (`plano_atual`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- File uploads
CREATE TABLE IF NOT EXISTS `file_uploads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `mime` VARCHAR(100) NOT NULL,
  `pages` INT NULL,
  `size_bytes` BIGINT NULL,
  `text_ref` TEXT NULL,
  `status` ENUM('uploaded','processing','ready','error') NOT NULL DEFAULT 'uploaded',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_upload_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Analyses
CREATE TABLE IF NOT EXISTS `analyses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `upload_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `prompt_used` TEXT NULL,
  `model_version` VARCHAR(50) NOT NULL DEFAULT 'gpt-5',
  `tokens_in` INT NULL,
  `tokens_out` INT NULL,
  `tokens_total` INT NULL,
  `cost_estimated` DECIMAL(10,4) NULL,
  `cost_actual` DECIMAL(10,4) NULL,
  `status` ENUM('queued','running','completed','failed') NOT NULL DEFAULT 'queued',
  `result_json` LONGTEXT NULL,
  `pdf_url` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_created` (`user_id`, `created_at`),
  KEY `idx_upload` (`upload_id`),
  CONSTRAINT `fk_analysis_upload` FOREIGN KEY (`upload_id`) REFERENCES `file_uploads`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_analysis_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Billing records
CREATE TABLE IF NOT EXISTS `billing_records` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `method` ENUM('asaas') NOT NULL DEFAULT 'asaas',
  `asaas_charge_id` VARCHAR(80) NULL,
  `type` ENUM('subscription','credit','usage') NOT NULL DEFAULT 'usage',
  `status` ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_status` (`user_id`, `status`),
  CONSTRAINT `fk_bill_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Embedding chunks (para busca semântica)
CREATE TABLE IF NOT EXISTS `embedding_chunks` (
  `chunk_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `upload_id` BIGINT UNSIGNED NOT NULL,
  `chunk_text` MEDIUMTEXT NOT NULL,
  `vector_ref` VARCHAR(100) NULL,
  `position` INT NULL,
  PRIMARY KEY (`chunk_id`),
  KEY `idx_upload_pos` (`upload_id`, `position`),
  FULLTEXT KEY `ft_chunk_text` (`chunk_text`),
  CONSTRAINT `fk_chunk_upload` FOREIGN KEY (`upload_id`) REFERENCES `file_uploads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Webhook logs (Asaas)
CREATE TABLE IF NOT EXISTS `webhook_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider` VARCHAR(40) NOT NULL DEFAULT 'asaas',
  `event` VARCHAR(80) NOT NULL,
  `payload` JSON NULL,
  `received_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_event_time` (`event`,`received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices auxiliares
CREATE INDEX IF NOT EXISTS `idx_file_status` ON `file_uploads`(`status`);
CREATE INDEX IF NOT EXISTS `idx_analysis_status` ON `analyses`(`status`);
