-- ============================================================
-- Migration: 001_create_categories_table.sql
-- Description: Tạo bảng categories để lưu các dịch vụ (services)
--              hiển thị trên trang Our Services
-- ============================================================

CREATE TABLE IF NOT EXISTS `categories` (
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)     NOT NULL COMMENT 'Tên dịch vụ',
    `slug`        VARCHAR(255)     NOT NULL COMMENT 'Slug URL thân thiện',
    `image`       VARCHAR(500)     DEFAULT NULL COMMENT 'Đường dẫn hình ảnh (assets/images/categories/...)',
    `description` TEXT             DEFAULT NULL COMMENT 'Mô tả ngắn về dịch vụ',
    `status`      TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '1 = hiển thị, 0 = ẩn',
    `sort_order`  INT(11)          NOT NULL DEFAULT 0 COMMENT 'Thứ tự sắp xếp',
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`),
    KEY `idx_categories_status` (`status`),
    KEY `idx_categories_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu danh mục dịch vụ hiển thị trên trang Our Services';

-- ============================================================
-- Seed data: 6 dịch vụ mẫu tương ứng với template
-- Ảnh lấy từ: docs/template/categories/code/categories.html
-- ============================================================

INSERT INTO `categories` (`name`, `slug`, `image`, `description`, `status`, `sort_order`) VALUES
(
    'Agricultural Processing',
    'agricultural-processing',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img1.jpg',
    'We provide comprehensive agricultural processing solutions to optimize production efficiency and quality.',
    1,
    1
),
(
    'Chemical Industry',
    'chemical-industry',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img2.jpg',
    'Advanced chemical industry services with a focus on safety, sustainability, and innovation.',
    1,
    2
),
(
    'Civil Engineering',
    'civil-engineering',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img3.jpg',
    'Expert civil engineering services for infrastructure, construction, and urban development projects.',
    1,
    3
),
(
    'Energy & Power',
    'energy-power',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img4.jpg',
    'Reliable energy and power solutions covering renewable sources, grid management, and efficiency.',
    1,
    4
),
(
    'Mechanical Engineering',
    'mechanical-engineering',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img5.jpg',
    'Precision mechanical engineering services for design, manufacturing, and maintenance.',
    1,
    5
),
(
    'Oil & Gas Engineering',
    'oil-gas-engineering',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img6.jpg',
    'Specialized oil and gas engineering services covering exploration, extraction, and refining.',
    1,
    6
);
