-- ============================================================
-- Job Portal - MySQL schema + sample data
-- Import in phpMyAdmin (Import tab) or:  mysql -u root < job_portal.sql
-- Admin login: admin@example.com / password
-- ============================================================
CREATE DATABASE IF NOT EXISTS `job_portal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `job_portal`;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS applications, job_listing_skill, job_listings, companies, skills, locations, categories,
  failed_jobs, job_batches, jobs, cache_locks, cache, sessions, password_reset_tokens, users, migrations;

CREATE TABLE migrations (
  id int unsigned NOT NULL AUTO_INCREMENT, migration varchar(255) NOT NULL, batch int NOT NULL, PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL, email varchar(255) NOT NULL,
  role varchar(255) NOT NULL DEFAULT 'user',
  email_verified_at timestamp NULL DEFAULT NULL,
  password varchar(255) NOT NULL, remember_token varchar(100) DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id), UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
  email varchar(255) NOT NULL, token varchar(255) NOT NULL, created_at timestamp NULL DEFAULT NULL, PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
  id varchar(255) NOT NULL, user_id bigint unsigned DEFAULT NULL, ip_address varchar(45) DEFAULT NULL,
  user_agent text, payload longtext NOT NULL, last_activity int NOT NULL,
  PRIMARY KEY (id), KEY sessions_user_id_index (user_id), KEY sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache (
  `key` varchar(255) NOT NULL, value mediumtext NOT NULL, expiration int NOT NULL, PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE cache_locks (
  `key` varchar(255) NOT NULL, owner varchar(255) NOT NULL, expiration int NOT NULL, PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jobs (
  id bigint unsigned NOT NULL AUTO_INCREMENT, queue varchar(255) NOT NULL, payload longtext NOT NULL,
  attempts tinyint unsigned NOT NULL, reserved_at int unsigned DEFAULT NULL, available_at int unsigned NOT NULL,
  created_at int unsigned NOT NULL, PRIMARY KEY (id), KEY jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE job_batches (
  id varchar(255) NOT NULL, name varchar(255) NOT NULL, total_jobs int NOT NULL, pending_jobs int NOT NULL,
  failed_jobs int NOT NULL, failed_job_ids longtext NOT NULL, options mediumtext, cancelled_at int DEFAULT NULL,
  created_at int NOT NULL, finished_at int DEFAULT NULL, PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE failed_jobs (
  id bigint unsigned NOT NULL AUTO_INCREMENT, uuid varchar(255) NOT NULL, connection text NOT NULL, queue text NOT NULL,
  payload longtext NOT NULL, exception longtext NOT NULL, failed_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id), UNIQUE KEY failed_jobs_uuid_unique (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------- Job portal tables ----------------
CREATE TABLE categories (
  id bigint unsigned NOT NULL AUTO_INCREMENT, name varchar(255) NOT NULL,
  created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id), UNIQUE KEY categories_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE locations (
  id bigint unsigned NOT NULL AUTO_INCREMENT, name varchar(255) NOT NULL,
  created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id), UNIQUE KEY locations_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE skills (
  id bigint unsigned NOT NULL AUTO_INCREMENT, name varchar(255) NOT NULL,
  created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id), UNIQUE KEY skills_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE companies (
  id bigint unsigned NOT NULL AUTO_INCREMENT, name varchar(255) NOT NULL, website varchar(255) DEFAULT NULL,
  logo varchar(255) DEFAULT NULL, description text,
  created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL, PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_listings (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL, slug varchar(255) NOT NULL,
  company_id bigint unsigned NOT NULL, category_id bigint unsigned NOT NULL, location_id bigint unsigned NOT NULL,
  job_type varchar(255) NOT NULL DEFAULT 'Full Time',
  salary_min int unsigned DEFAULT NULL, salary_max int unsigned DEFAULT NULL, experience varchar(255) DEFAULT NULL,
  description text NOT NULL, deadline date DEFAULT NULL, is_active tinyint(1) NOT NULL DEFAULT 1,
  created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id), UNIQUE KEY job_listings_slug_unique (slug),
  CONSTRAINT jl_company_fk  FOREIGN KEY (company_id)  REFERENCES companies (id)  ON DELETE CASCADE,
  CONSTRAINT jl_category_fk FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT,
  CONSTRAINT jl_location_fk FOREIGN KEY (location_id) REFERENCES locations (id)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_listing_skill (
  id bigint unsigned NOT NULL AUTO_INCREMENT, job_listing_id bigint unsigned NOT NULL, skill_id bigint unsigned NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT jls_job_fk   FOREIGN KEY (job_listing_id) REFERENCES job_listings (id) ON DELETE CASCADE,
  CONSTRAINT jls_skill_fk FOREIGN KEY (skill_id)       REFERENCES skills (id)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE applications (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  job_listing_id bigint unsigned NOT NULL, user_id bigint unsigned DEFAULT NULL,
  name varchar(255) NOT NULL, email varchar(255) NOT NULL, phone varchar(255) DEFAULT NULL,
  cover_letter text, resume_path varchar(255) DEFAULT NULL, status varchar(255) NOT NULL DEFAULT 'pending',
  created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  CONSTRAINT app_job_fk  FOREIGN KEY (job_listing_id) REFERENCES job_listings (id) ON DELETE CASCADE,
  CONSTRAINT app_user_fk FOREIGN KEY (user_id)        REFERENCES users (id)        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Tell Laravel these migrations are already applied (so "php artisan migrate" won't recreate tables)
INSERT INTO migrations (migration, batch) VALUES
 ('0001_01_01_000000_create_users_table', 1),
 ('0001_01_01_000001_create_cache_table', 1),
 ('0001_01_01_000002_create_jobs_table', 1),
 ('2024_01_01_000001_add_role_to_users_table', 1),
 ('2024_01_01_000002_create_job_portal_tables', 1);

-- ---------------- Sample data ----------------
INSERT INTO users (name, email, role, password, created_at, updated_at) VALUES
 ('Admin', 'admin@example.com', 'admin', '$2y$12$AQU/vzUfZJgm9uH3oEO.keTRochw/q5ugCgZ4YZ6l3Swy6NUdOzvG', NOW(), NOW());

INSERT INTO categories (name, created_at, updated_at) VALUES
 ('IT & Software',NOW(),NOW()),('Marketing',NOW(),NOW()),('Finance',NOW(),NOW()),('Design',NOW(),NOW()),('Sales',NOW(),NOW()),('HR',NOW(),NOW());
INSERT INTO locations (name, created_at, updated_at) VALUES
 ('Jaipur',NOW(),NOW()),('Delhi',NOW(),NOW()),('Mumbai',NOW(),NOW()),('Bangalore',NOW(),NOW()),('Remote',NOW(),NOW());
INSERT INTO skills (name, created_at, updated_at) VALUES
 ('PHP',NOW(),NOW()),('Laravel',NOW(),NOW()),('JavaScript',NOW(),NOW()),('React',NOW(),NOW()),('MySQL',NOW(),NOW()),
 ('Figma',NOW(),NOW()),('SEO',NOW(),NOW()),('Excel',NOW(),NOW()),('Communication',NOW(),NOW());
INSERT INTO companies (name, website, description, created_at, updated_at) VALUES
 ('TechNova','https://example.com','TechNova is a growing software company.',NOW(),NOW()),
 ('PinkCity Solutions','https://example.com','PinkCity Solutions is a growing company in Jaipur.',NOW(),NOW()),
 ('BrightPixel','https://example.com','BrightPixel is a creative design studio.',NOW(),NOW()),
 ('FinEdge','https://example.com','FinEdge provides finance services.',NOW(),NOW());

INSERT INTO job_listings (title, slug, company_id, category_id, location_id, job_type, salary_min, salary_max, experience, description, deadline, is_active, created_at, updated_at) VALUES
 ('Laravel Developer','laravel-developer-a1b2c',1,1,1,'Full Time',20000,40000,'0-2 years','We are hiring a Laravel Developer.\n\nResponsibilities:\n- Build and maintain web apps\n- Collaborate with the team\n\nRequirements:\n- PHP, Laravel, MySQL',DATE_ADD(CURDATE(), INTERVAL 30 DAY),1,NOW(),NOW()),
 ('React Frontend Developer','react-frontend-developer-b2c3d',2,1,4,'Remote',25000,48000,'1-3 years','We are hiring a React Frontend Developer.\n\nRequirements:\n- JavaScript, React',DATE_ADD(CURDATE(), INTERVAL 30 DAY),1,NOW(),NOW()),
 ('UI/UX Designer','ui-ux-designer-c3d4e',3,4,2,'Contract',30000,56000,'0-2 years','We are hiring a UI/UX Designer.\n\nRequirements:\n- Figma, creativity',DATE_ADD(CURDATE(), INTERVAL 30 DAY),1,NOW(),NOW()),
 ('SEO Executive','seo-executive-d4e5f',4,2,1,'Part Time',35000,64000,'1-3 years','We are hiring an SEO Executive.\n\nRequirements:\n- SEO, communication',DATE_ADD(CURDATE(), INTERVAL 30 DAY),1,NOW(),NOW()),
 ('Accounts Executive','accounts-executive-e5f6g',1,3,3,'Full Time',40000,72000,'0-2 years','We are hiring an Accounts Executive.\n\nRequirements:\n- Excel',DATE_ADD(CURDATE(), INTERVAL 30 DAY),1,NOW(),NOW()),
 ('Full Stack Intern','full-stack-intern-f6g7h',2,1,5,'Internship',45000,80000,'1-3 years','We are hiring a Full Stack Intern.\n\nRequirements:\n- PHP, JavaScript',DATE_ADD(CURDATE(), INTERVAL 30 DAY),1,NOW(),NOW());

INSERT INTO job_listing_skill (job_listing_id, skill_id) VALUES
 (1,1),(1,2),(1,5),(2,3),(2,4),(3,6),(4,7),(4,9),(5,8),(6,1),(6,3);
