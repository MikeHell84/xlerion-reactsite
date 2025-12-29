-- Detailed migration stubs generated on 2025-12-28T21:26:10+00:00
-- Review before applying on any environment.

-- Table `admin_notifications`: column differences detected (review before apply)
-- Differences for `admin_notifications`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `admin_notifications` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `admin_notifications`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `admin_notifications` MODIFY COLUMN `created_at` datetime NULL DEFAULT current_timestamp();

-- Table `admins`: column differences detected (review before apply)
-- Differences for `admins`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `admins` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `admins`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `admins` MODIFY COLUMN `created_at` timestamp NULL DEFAULT current_timestamp();

-- Differences for `admins`.`updated_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `admins` MODIFY COLUMN `updated_at` timestamp NULL DEFAULT current_timestamp();


-- Table `backups`: column differences detected (review before apply)
-- Differences for `backups`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `backups` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `backups`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `backups` MODIFY COLUMN `created_at` timestamp NULL DEFAULT current_timestamp();

-- Table `cache`: column differences detected (review before apply)
-- Differences for `cache`.`expiration`: type: local=int remote=int(11)
-- ALTER TABLE `cache` MODIFY COLUMN `expiration` int(11) NOT NULL;

-- Table `cache_locks`: column differences detected (review before apply)
-- Differences for `cache_locks`.`expiration`: type: local=int remote=int(11)
-- ALTER TABLE `cache_locks` MODIFY COLUMN `expiration` int(11) NOT NULL;

-- Table `client_finances`: column differences detected (review before apply)
-- Differences for `client_finances`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `client_finances` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `client_finances`.`client_id`: type: local=int remote=int(11)
-- ALTER TABLE `client_finances` MODIFY COLUMN `client_id` int(11) NOT NULL;

-- Differences for `client_finances`.`fecha`: default: local=NULL remote=curdate()
-- ALTER TABLE `client_finances` MODIFY COLUMN `fecha` date NULL DEFAULT 'curdate()';

-- Table `client_interactions`: column differences detected (review before apply)
-- Differences for `client_interactions`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `client_interactions` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `client_interactions`.`client_id`: type: local=int remote=int(11)
-- ALTER TABLE `client_interactions` MODIFY COLUMN `client_id` int(11) NOT NULL;

-- Differences for `client_interactions`.`fecha`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `client_interactions` MODIFY COLUMN `fecha` datetime NULL DEFAULT current_timestamp();

-- Table `client_notes`: column differences detected (review before apply)
-- Differences for `client_notes`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `client_notes` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `client_notes`.`client_id`: type: local=int remote=int(11)
-- ALTER TABLE `client_notes` MODIFY COLUMN `client_id` int(11) NOT NULL;

-- Differences for `client_notes`.`fecha`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `client_notes` MODIFY COLUMN `fecha` datetime NULL DEFAULT current_timestamp();

-- Table `client_projects`: column differences detected (review before apply)
-- Differences for `client_projects`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `client_projects` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `client_projects`.`client_id`: type: local=int remote=int(11)
-- ALTER TABLE `client_projects` MODIFY COLUMN `client_id` int(11) NOT NULL;

-- Differences for `client_projects`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `client_projects` MODIFY COLUMN `created_at` datetime NULL DEFAULT current_timestamp();

-- Table `clients`: column differences detected (review before apply)
-- Differences for `clients`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `clients` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `clients`.`pipeline_opportunities`: type: local=int remote=int(11)
-- ALTER TABLE `clients` MODIFY COLUMN `pipeline_opportunities` int(11) NULL DEFAULT '0';

-- Differences for `clients`.`pipeline_progress`: type: local=int remote=int(11)
-- ALTER TABLE `clients` MODIFY COLUMN `pipeline_progress` int(11) NULL DEFAULT '0';

-- Differences for `clients`.`created_by`: type: local=int remote=int(11)
-- ALTER TABLE `clients` MODIFY COLUMN `created_by` int(11) NULL DEFAULT '1';

-- Differences for `clients`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `clients` MODIFY COLUMN `created_at` timestamp NULL DEFAULT current_timestamp();

-- Table `cms_blocks`: column differences detected (review before apply)
-- Differences for `cms_blocks`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `cms_blocks` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;

-- Differences for `cms_blocks`.`page_id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `cms_blocks` MODIFY COLUMN `page_id` bigint(20) unsigned NULL;

-- Differences for `cms_blocks`.`order`: type: local=int remote=int(11)
-- ALTER TABLE `cms_blocks` MODIFY COLUMN `order` int(11) NULL DEFAULT '0';


-- Table `cms_pages`: column differences detected (review before apply)
-- Differences for `cms_pages`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `cms_pages` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;

-- Differences for `cms_pages`.`created_by`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `cms_pages` MODIFY COLUMN `created_by` bigint(20) unsigned NULL;

-- Differences for `cms_pages`.`updated_by`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `cms_pages` MODIFY COLUMN `updated_by` bigint(20) unsigned NULL;


-- Table `contacts`: column differences detected (review before apply)
-- Differences for `contacts`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `contacts` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `contacts`.`status`: default: local=NULL remote='\'active\''
-- ALTER TABLE `contacts` MODIFY COLUMN `status` text NULL DEFAULT '''\''active\''''';

-- Differences for `contacts`.`created_by`: type: local=int remote=int(11)
-- ALTER TABLE `contacts` MODIFY COLUMN `created_by` int(11) NULL;

-- Table `crm_audit`: column differences detected (review before apply)
-- Differences for `crm_audit`.`id`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `crm_audit` MODIFY COLUMN `id` int(10) unsigned NOT NULL;

-- Differences for `crm_audit`.`entity_id`: type: local=int remote=int(11)
-- ALTER TABLE `crm_audit` MODIFY COLUMN `entity_id` int(11) NULL;

-- Differences for `crm_audit`.`user_id`: type: local=int remote=int(11)
-- ALTER TABLE `crm_audit` MODIFY COLUMN `user_id` int(11) NULL;

-- Differences for `crm_audit`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `crm_audit` MODIFY COLUMN `created_at` datetime NOT NULL DEFAULT current_timestamp();

-- Table `crm_customers`: column differences detected (review before apply)
-- Differences for `crm_customers`.`id`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `crm_customers` MODIFY COLUMN `id` int(10) unsigned NOT NULL;

-- Differences for `crm_customers`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `crm_customers` MODIFY COLUMN `created_at` datetime NOT NULL DEFAULT current_timestamp();

-- Differences for `crm_customers`.`updated_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `crm_customers` MODIFY COLUMN `updated_at` datetime NOT NULL DEFAULT current_timestamp();

-- Table `crm_leads`: column differences detected (review before apply)
-- Differences for `crm_leads`.`id`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `crm_leads` MODIFY COLUMN `id` int(10) unsigned NOT NULL;

-- Differences for `crm_leads`.`score`: type: local=int remote=int(11)
-- ALTER TABLE `crm_leads` MODIFY COLUMN `score` int(11) NULL DEFAULT '0';

-- Differences for `crm_leads`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `crm_leads` MODIFY COLUMN `created_at` datetime NOT NULL DEFAULT current_timestamp();

-- Differences for `crm_leads`.`updated_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `crm_leads` MODIFY COLUMN `updated_at` datetime NOT NULL DEFAULT current_timestamp();

-- Table `failed_jobs`: column differences detected (review before apply)
-- Differences for `failed_jobs`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `failed_jobs` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;

-- Differences for `failed_jobs`.`failed_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `failed_jobs` MODIFY COLUMN `failed_at` timestamp NOT NULL DEFAULT current_timestamp();


-- Table `forms_submissions`: column differences detected (review before apply)
-- Differences for `forms_submissions`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `forms_submissions` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `forms_submissions`.`is_read`: type: local=int remote=int(11)
-- ALTER TABLE `forms_submissions` MODIFY COLUMN `is_read` int(11) NULL DEFAULT '0';

-- Table `interactions`: column differences detected (review before apply)
-- Differences for `interactions`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `interactions` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `interactions`.`performed_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `interactions` MODIFY COLUMN `performed_at` datetime NULL DEFAULT current_timestamp();

-- Table `job_batches`: column differences detected (review before apply)
-- Differences for `job_batches`.`total_jobs`: type: local=int remote=int(11)
-- ALTER TABLE `job_batches` MODIFY COLUMN `total_jobs` int(11) NOT NULL;

-- Differences for `job_batches`.`pending_jobs`: type: local=int remote=int(11)
-- ALTER TABLE `job_batches` MODIFY COLUMN `pending_jobs` int(11) NOT NULL;

-- Differences for `job_batches`.`failed_jobs`: type: local=int remote=int(11)
-- ALTER TABLE `job_batches` MODIFY COLUMN `failed_jobs` int(11) NOT NULL;

-- Differences for `job_batches`.`cancelled_at`: type: local=int remote=int(11)
-- ALTER TABLE `job_batches` MODIFY COLUMN `cancelled_at` int(11) NULL;

-- Differences for `job_batches`.`created_at`: type: local=int remote=int(11)
-- ALTER TABLE `job_batches` MODIFY COLUMN `created_at` int(11) NOT NULL;

-- Differences for `job_batches`.`finished_at`: type: local=int remote=int(11)
-- ALTER TABLE `job_batches` MODIFY COLUMN `finished_at` int(11) NULL;

-- Table `jobs`: column differences detected (review before apply)
-- Differences for `jobs`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `jobs` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;

-- Differences for `jobs`.`attempts`: type: local=tinyint unsigned remote=tinyint(3) unsigned
-- ALTER TABLE `jobs` MODIFY COLUMN `attempts` tinyint(3) unsigned NOT NULL;

-- Differences for `jobs`.`reserved_at`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `jobs` MODIFY COLUMN `reserved_at` int(10) unsigned NULL;

-- Differences for `jobs`.`available_at`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `jobs` MODIFY COLUMN `available_at` int(10) unsigned NOT NULL;

-- Differences for `jobs`.`created_at`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `jobs` MODIFY COLUMN `created_at` int(10) unsigned NOT NULL;

-- Table `media_files`: column differences detected (review before apply)
-- Differences for `media_files`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `media_files` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;

-- Differences for `media_files`.`size`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `media_files` MODIFY COLUMN `size` int(10) unsigned NULL;

-- Differences for `media_files`.`uploaded_by`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `media_files` MODIFY COLUMN `uploaded_by` bigint(20) unsigned NULL;

-- Table `menu_items`: column differences detected (review before apply)
-- Differences for `menu_items`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `menu_items` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;

-- Differences for `menu_items`.`parent_id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `menu_items` MODIFY COLUMN `parent_id` bigint(20) unsigned NULL;

-- Differences for `menu_items`.`order`: type: local=int remote=int(11)
-- ALTER TABLE `menu_items` MODIFY COLUMN `order` int(11) NOT NULL DEFAULT '0';

-- Table `menu_sections`: column differences detected (review before apply)
-- Differences for `menu_sections`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `menu_sections` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `menu_sections`.`parent_id`: type: local=int remote=int(11)
-- ALTER TABLE `menu_sections` MODIFY COLUMN `parent_id` int(11) NULL;

-- Differences for `menu_sections`.`is_visible`: type: local=tinyint remote=tinyint(4)
-- ALTER TABLE `menu_sections` MODIFY COLUMN `is_visible` tinyint(4) NULL DEFAULT '1';

-- Differences for `menu_sections`.`sort_order`: type: local=int remote=int(11)
-- ALTER TABLE `menu_sections` MODIFY COLUMN `sort_order` int(11) NULL DEFAULT '0';

-- Differences for `menu_sections`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `menu_sections` MODIFY COLUMN `created_at` timestamp NULL DEFAULT current_timestamp();

-- Differences for `menu_sections`.`updated_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `menu_sections` MODIFY COLUMN `updated_at` timestamp NULL DEFAULT current_timestamp();


-- Table `migrations`: column differences detected (review before apply)
-- Differences for `migrations`.`id`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `migrations` MODIFY COLUMN `id` int(10) unsigned NOT NULL;

-- Differences for `migrations`.`batch`: type: local=int remote=int(11)
-- ALTER TABLE `migrations` MODIFY COLUMN `batch` int(11) NOT NULL;

-- Table `modules`: column differences detected (review before apply)
-- Differences for `modules`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `modules` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `modules`.`page_id`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `modules` MODIFY COLUMN `page_id` int(10) unsigned NOT NULL;

-- Differences for `modules`.`order_index`: type: local=int remote=int(11)
-- ALTER TABLE `modules` MODIFY COLUMN `order_index` int(11) NULL DEFAULT '0';

-- Differences for `modules`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `modules` MODIFY COLUMN `created_at` timestamp NULL DEFAULT current_timestamp();

-- Differences for `modules`.`updated_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `modules` MODIFY COLUMN `updated_at` timestamp NULL DEFAULT current_timestamp();

-- Table `opportunities`: column differences detected (review before apply)
-- Differences for `opportunities`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `opportunities` MODIFY COLUMN `id` int(11) NOT NULL;

-- Table `page_views`: column differences detected (review before apply)
-- Differences for `page_views`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `page_views` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;

-- Differences for `page_views`.`page_id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `page_views` MODIFY COLUMN `page_id` bigint(20) unsigned NULL;

-- Table `pages`: column differences detected (review before apply)
-- Differences for `pages`.`id`: type: local=int unsigned remote=int(10) unsigned
-- ALTER TABLE `pages` MODIFY COLUMN `id` int(10) unsigned NOT NULL;

-- Differences for `pages`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `pages` MODIFY COLUMN `created_at` datetime NOT NULL DEFAULT current_timestamp();

-- Differences for `pages`.`updated_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `pages` MODIFY COLUMN `updated_at` datetime NOT NULL DEFAULT current_timestamp();


-- Table `password_resets`: column differences detected (review before apply)
-- Differences for `password_resets`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `password_resets` MODIFY COLUMN `id` int(11) NOT NULL;

-- Table `resources`: column differences detected (review before apply)
-- Differences for `resources`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `resources` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `resources`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `resources` MODIFY COLUMN `created_at` datetime NULL DEFAULT current_timestamp();


-- Table `sections`: column differences detected (review before apply)
-- Differences for `sections`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `sections` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;


-- Table `sessions`: column differences detected (review before apply)
-- Differences for `sessions`.`user_id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `sessions` MODIFY COLUMN `user_id` bigint(20) unsigned NULL;

-- Differences for `sessions`.`last_activity`: type: local=int remote=int(11)
-- ALTER TABLE `sessions` MODIFY COLUMN `last_activity` int(11) NOT NULL;

-- Table `tasks`: column differences detected (review before apply)
-- Differences for `tasks`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `tasks` MODIFY COLUMN `id` int(11) NOT NULL;

-- Table `td_interactions`: column differences detected (review before apply)
-- Differences for `td_interactions`.`id`: type: local=int remote=int(11)
-- ALTER TABLE `td_interactions` MODIFY COLUMN `id` int(11) NOT NULL;

-- Differences for `td_interactions`.`rating`: type: local=int remote=int(11)
-- ALTER TABLE `td_interactions` MODIFY COLUMN `rating` int(11) NULL;

-- Differences for `td_interactions`.`created_at`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `td_interactions` MODIFY COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp();

-- Table `td_projects`: column differences detected (review before apply)
-- Differences for `td_projects`.`last_updated`: default: local=CURRENT_TIMESTAMP remote=current_timestamp()
-- ALTER TABLE `td_projects` MODIFY COLUMN `last_updated` timestamp NOT NULL DEFAULT current_timestamp();

-- Table `users`: column differences detected (review before apply)
-- Differences for `users`.`id`: type: local=bigint unsigned remote=bigint(20) unsigned
-- ALTER TABLE `users` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL;

-- Differences for `users`.`reset_token_expires`: type: local=int remote=int(11)
-- ALTER TABLE `users` MODIFY COLUMN `reset_token_expires` int(11) NULL;


