-- ============================================================
-- Migration: 017_create_client_logo_table.sql
-- Description: Bảng lưu logo đối tác chiến lược
-- ============================================================

CREATE TABLE IF NOT EXISTS `client_logos` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)     NOT NULL COMMENT 'Tên đối tác',
    `logo`       VARCHAR(500)     NOT NULL COMMENT 'URL hoặc path ảnh logo',
    `url`        VARCHAR(500)     DEFAULT '#' COMMENT 'Link website đối tác',
    `status`     TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '1=hiển thị, 0=ẩn',
    `sort_order` INT(11)          NOT NULL DEFAULT 0,
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_client_logos_status` (`status`),
    KEY `idx_client_logos_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu logo đối tác chiến lược';

-- Seed data
INSERT INTO `client_logos` (`name`, `logo`, `url`, `status`, `sort_order`) VALUES
('Partner 1', 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo1.png', '#', 1, 1),
('Partner 2', 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo2.png', '#', 1, 2),
('Partner 3', 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo2.png', '#', 1, 3),
('Partner 4', 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo1.png', '#', 1, 4),
('Partner 5', 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo1.png', '#', 1, 5);
