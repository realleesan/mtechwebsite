-- Migration 015: Tạo các bảng cho footer dynamic
-- Bao gồm: footer_settings, footer_links, footer_newsletter, footer_social

-- 1. Bảng footer_settings: Lưu tiêu đề useful links và các cấu hình chung
CREATE TABLE IF NOT EXISTS `footer_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `useful_links_title` VARCHAR(255) DEFAULT 'Useful Links',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default data
INSERT INTO `footer_settings` (`useful_links_title`) VALUES ('Useful Links');

-- 2. Bảng footer_links: Lưu các link useful links (title + url)
CREATE TABLE IF NOT EXISTS `footer_links` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng footer_newsletter: Lưu email đăng ký newsletter
CREATE TABLE IF NOT EXISTS `footer_newsletter` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng footer_social: Lưu 4 social icons (facebook, linkedin, twitter, google)
CREATE TABLE IF NOT EXISTS `footer_social` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `platform` VARCHAR(50) NOT NULL UNIQUE COMMENT 'facebook, linkedin, twitter, google',
    `url` VARCHAR(500) DEFAULT NULL,
    `is_visible` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default data cho 4 social platforms
INSERT INTO `footer_social` (`platform`, `url`, `is_visible`, `sort_order`) VALUES
('facebook', '#', 1, 1),
('linkedin', '#', 1, 2),
('twitter', '#', 1, 3),
('google', '#', 1, 4);