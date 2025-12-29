-- Generated CREATE TABLE statements from data/remote_schema.json

SET SQL_MODE = 'NO_ENGINE_SUBSTITUTION';
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `admin_notifications`;
CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `body` text NULL,
  `type` varchar(32) NULL DEFAULT 'info',
  `is_read` tinyint(1) NULL DEFAULT '0',
  `created_at` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'editor',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() on update current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `backups`;
CREATE TABLE `backups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `path` varchar(1024) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `client_finances`;
CREATE TABLE `client_finances` (
  `id` int(11) NOT NULL auto_increment,
  `client_id` int(11) NOT NULL,
  `tipo` enum('factura','presupuesto','pago') NULL DEFAULT 'factura',
  `monto` decimal(10,2) NOT NULL,
  `concepto` text NULL,
  `fecha` date NULL,
  `estado` varchar(50) NULL DEFAULT 'pendiente',
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `client_interactions`;
CREATE TABLE `client_interactions` (
  `id` int(11) NOT NULL auto_increment,
  `client_id` int(11) NOT NULL,
  `tipo` varchar(20) NULL DEFAULT 'nota',
  `contenido` text NOT NULL,
  `responsable` varchar(255) NULL,
  `fecha` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `client_notes`;
CREATE TABLE `client_notes` (
  `id` int(11) NOT NULL auto_increment,
  `client_id` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `responsable` text NULL,
  `fecha` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `client_projects`;
CREATE TABLE `client_projects` (
  `id` int(11) NOT NULL auto_increment,
  `client_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text NULL,
  `estado` varchar(20) NULL DEFAULT 'activo',
  `fecha_inicio` date NULL,
  `fecha_vencimiento` date NULL,
  `responsable` varchar(255) NULL,
  `created_at` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(255) NULL,
  `correo` varchar(255) NULL,
  `telefono` varchar(100) NULL,
  `empresa` varchar(255) NULL,
  `estado` varchar(50) NULL,
  `notas` text NULL,
  `pipeline_stage` varchar(50) NULL,
  `pipeline_opportunities` int(11) NULL DEFAULT '0',
  `pipeline_value` decimal(10,2) NULL DEFAULT '0.00',
  `pipeline_progress` int(11) NULL DEFAULT '0',
  `logo_url` text NULL,
  `created_by` int(11) NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL on update current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cms_blocks`;
CREATE TABLE `cms_blocks` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `key_name` varchar(191) NOT NULL,
  `title` varchar(191) NULL,
  `content` longtext NULL,
  `page_id` bigint(20) unsigned NULL,
  `order` int(11) NULL DEFAULT '0',
  `is_active` tinyint(1) NULL DEFAULT '1',
  `meta` longtext NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_key_name` (`key_name`),
  KEY `idx_page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE `cms_pages` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `slug` varchar(191) NOT NULL,
  `title` varchar(191) NOT NULL,
  `excerpt` text NULL,
  `content` longtext NULL,
  `meta` longtext NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint(20) unsigned NULL,
  `updated_by` bigint(20) unsigned NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_is_published` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL auto_increment,
  `first_name` text NULL,
  `last_name` text NULL,
  `email` text NULL,
  `phone` text NULL,
  `phone_alt` text NULL,
  `source` text NULL,
  `status` text NULL,
  `notes` text NULL,
  `created_by` int(11) NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `crm_audit`;
CREATE TABLE `crm_audit` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `entity` varchar(100) NOT NULL,
  `entity_id` int(11) NULL,
  `action` varchar(100) NOT NULL,
  `user_id` int(11) NULL,
  `meta` longtext NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `crm_customers`;
CREATE TABLE `crm_customers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NULL,
  `status` varchar(50) NULL DEFAULT 'active',
  `tags` longtext NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `crm_leads`;
CREATE TABLE `crm_leads` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `source` varchar(100) NULL DEFAULT 'web',
  `name` varchar(255) NULL,
  `email` varchar(255) NULL,
  `score` int(11) NULL DEFAULT '0',
  `status` varchar(50) NULL DEFAULT 'new',
  `metadata` longtext NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `forms_submissions`;
CREATE TABLE `forms_submissions` (
  `id` int(11) NOT NULL auto_increment,
  `form_name` text NOT NULL,
  `payload` text NULL,
  `ip` text NULL,
  `user_agent` text NULL,
  `is_read` int(11) NULL DEFAULT '0',
  `created_at` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `interactions`;
CREATE TABLE `interactions` (
  `id` int(11) NOT NULL auto_increment,
  `performed_at` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext NULL,
  `cancelled_at` int(11) NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_queue` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `media_files`;
CREATE TABLE `media_files` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `filename` varchar(255) NOT NULL,
  `path` varchar(1024) NOT NULL,
  `mime` varchar(100) NULL,
  `size` int(10) unsigned NULL,
  `uploaded_by` bigint(20) unsigned NULL,
  `usages` longtext NULL,
  `created_at` datetime NULL,
  PRIMARY KEY (`id`),
  KEY `idx_filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `short_title` varchar(255) NOT NULL,
  `href` varchar(255) NOT NULL,
  `parent_id` bigint(20) unsigned NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `icon` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `menu_sections`;
CREATE TABLE `menu_sections` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NULL,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(100) NULL,
  `href` varchar(500) NOT NULL,
  `description` text NULL,
  `is_visible` tinyint(4) NULL DEFAULT '1',
  `sort_order` int(11) NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() on update current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_is_visible` (`is_visible`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(10) unsigned NOT NULL,
  `type` varchar(100) NOT NULL,
  `content` mediumtext NULL,
  `order_index` int(11) NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() on update current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `opportunities`;
CREATE TABLE `opportunities` (
  `id` int(11) NOT NULL auto_increment,
  `stage` varchar(50) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `page_views`;
CREATE TABLE `page_views` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `page_id` bigint(20) unsigned NULL,
  `slug` varchar(191) NULL,
  `ip` varchar(45) NULL,
  `user_agent` varchar(255) NULL,
  `created_at` datetime NULL,
  PRIMARY KEY (`id`),
  KEY `idx_page_id` (`page_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `slug` varchar(191) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL auto_increment,
  `email` text NOT NULL,
  `token` text NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `resources`;
CREATE TABLE `resources` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NULL,
  `body` longtext NULL,
  `status` varchar(50) NULL DEFAULT 'draft',
  `created_at` datetime NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL on update current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL auto_increment,
  `status` varchar(50) NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `td_interactions`;
CREATE TABLE `td_interactions` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` varchar(255) NOT NULL,
  `item_id` varchar(255) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `rating` int(11) NULL,
  `comment` text NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `td_projects`;
CREATE TABLE `td_projects` (
  `id` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `totp_secret` varchar(255) NULL,
  `two_factor_enabled` tinyint(1) NULL DEFAULT '0',
  `last_login_at` datetime NULL,
  `is_admin` tinyint(1) NULL DEFAULT '0',
  `reset_token` varchar(255) NULL,
  `reset_token_expires` int(11) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
