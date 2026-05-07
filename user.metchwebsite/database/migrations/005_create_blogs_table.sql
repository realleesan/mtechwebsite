-- ============================================================
-- Migration: 005_create_blogs_table.sql
-- Description: Tạo bảng blogs, blog_tags, blog_tag_map
-- ============================================================

-- ------------------------------------------------------------
-- 1. Bảng blogs - lưu bài viết
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blogs` (
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`         VARCHAR(500)     NOT NULL COMMENT 'Tiêu đề bài viết',
    `slug`          VARCHAR(500)     NOT NULL COMMENT 'Slug URL thân thiện',
    `image`         VARCHAR(500)     DEFAULT NULL COMMENT 'Ảnh đại diện bài viết',
    `excerpt`       TEXT             DEFAULT NULL COMMENT 'Tóm tắt ngắn',
    `content`       LONGTEXT         DEFAULT NULL COMMENT 'Nội dung đầy đủ',
    `category_id`   INT(11) UNSIGNED DEFAULT NULL COMMENT 'Danh mục blog (FK -> blog_categories)',
    `author`        VARCHAR(255)     NOT NULL DEFAULT 'admin' COMMENT 'Tên tác giả',
    `status`        TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '1=hiển thị, 0=ẩn',
    `views`         INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Lượt xem',
    `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_blogs_slug` (`slug`),
    KEY `idx_blogs_status` (`status`),
    KEY `idx_blogs_category` (`category_id`),
    KEY `idx_blogs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu bài viết blog';

-- ------------------------------------------------------------
-- 2. Bảng blog_categories - danh mục riêng cho blog
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_categories` (
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)     NOT NULL COMMENT 'Tên danh mục',
    `slug`        VARCHAR(255)     NOT NULL COMMENT 'Slug URL',
    `status`      TINYINT(1)       NOT NULL DEFAULT 1,
    `sort_order`  INT(11)          NOT NULL DEFAULT 0,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_blog_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Danh mục bài viết blog';

-- ------------------------------------------------------------
-- 3. Bảng blog_tags - các tag
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_tags` (
    `id`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`  VARCHAR(100)     NOT NULL COMMENT 'Tên tag',
    `slug`  VARCHAR(100)     NOT NULL COMMENT 'Slug tag',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_blog_tags_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng tag bài viết';

-- ------------------------------------------------------------
-- 4. Bảng blog_tag_map - quan hệ nhiều-nhiều blog <-> tag
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_tag_map` (
    `blog_id` INT(11) UNSIGNED NOT NULL,
    `tag_id`  INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`blog_id`, `tag_id`),
    KEY `idx_tag_map_tag` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Quan hệ blog - tag';

-- ------------------------------------------------------------
-- 5. Foreign Keys
-- ------------------------------------------------------------
ALTER TABLE `blogs`
    ADD CONSTRAINT `fk_blogs_category`
        FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `blog_tag_map`
    ADD CONSTRAINT `fk_tag_map_blog`
        FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_tag_map_tag`
        FOREIGN KEY (`tag_id`) REFERENCES `blog_tags` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE;

-- ------------------------------------------------------------
-- 6. Seed data mẫu
-- ------------------------------------------------------------
INSERT INTO `blog_categories` (`name`, `slug`, `sort_order`) VALUES
('Fabrication',   'fabrication',   1),
('Industry',      'industry',      2),
('Innovation',    'innovation',    3),
('Manufacturing', 'manufacturing', 4),
('Projects',      'projects',      5),
('Treatment',     'treatment',     6);

INSERT INTO `blog_tags` (`name`, `slug`) VALUES
('Chemical',   'chemical'),
('Fabrication','fabrication'),
('Factory',    'factory'),
('Fixes',      'fixes'),
('Industrial', 'industrial'),
('Workers',    'workers');

INSERT INTO `blogs` (`title`, `slug`, `image`, `excerpt`, `content`, `category_id`, `author`, `status`, `created_at`, `updated_at`) VALUES
(
    'Capitalize on low hanging fruit to identify',
    'capitalize-on-low-hanging-fruit-1',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/blog_single.jpg',
    'Podcasting operational change management inside of workflows to establish a framework. Taking seamless key performance indicators offline to maximise the long tail. Keeping your eye on the ball while performing a deep dive on the start-up mentality to derive...',
    '<p>Podcasting operational change management inside of workflows to establish a framework. Taking seamless key performance indicators offline to maximise the long tail. Keeping your eye on the ball while performing a deep dive on the start-up mentality to derive convergence on cross-platform integration.</p>',
    2, 'admin', 5, 1, '2019-12-12 08:00:00', '2019-12-12 08:00:00'
),
(
    'Taking seamless key performance indicators offline',
    'taking-seamless-key-performance-indicators',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/blog_img_02.jpg',
    'Podcasting operational change management inside of workflows to establish a framework. Taking seamless key performance indicators offline to maximise the long tail. Keeping your eye on the ball while performing a deep dive on the start-up mentality to derive...',
    '<p>Podcasting operational change management inside of workflows to establish a framework. Taking seamless key performance indicators offline to maximise the long tail.</p>',
    4, 'admin', 0, 1, '2019-12-12 09:00:00', '2019-12-12 09:00:00'
),
(
    'Keeping your eye on the ball while performing',
    'keeping-your-eye-on-the-ball',
    'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/recent-post3-1.jpg',
    'Podcasting operational change management inside of workflows to establish a framework. Taking seamless key performance indicators offline to maximise the long tail. Keeping your eye on the ball while performing a deep dive on the start-up mentality to derive...',
    '<p>Keeping your eye on the ball while performing a deep dive on the start-up mentality to derive convergence on cross-platform integration.</p>',
    3, 'admin', 2, 1, '2019-12-12 10:00:00', '2019-12-12 10:00:00'
),
(
    'Derive convergence on cross-platform integration',
    'derive-convergence-cross-platform',
    'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/recent-post4-1.jpg',
    'Podcasting operational change management inside of workflows to establish a framework. Taking seamless key performance indicators offline to maximise the long tail. Keeping your eye on the ball while performing a deep dive on the start-up mentality to derive...',
    '<p>Derive convergence on cross-platform integration. Podcasting operational change management inside of workflows to establish a framework.</p>',
    1, 'admin', 3, 1, '2019-12-11 08:00:00', '2019-12-11 08:00:00'
);

-- Gán tags cho blog 1
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) VALUES (1, 5), (1, 2), (1, 6);
-- Gán tags cho blog 2
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) VALUES (2, 4), (2, 3);
-- Gán tags cho blog 3
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) VALUES (3, 1), (3, 5);
-- Gán tags cho blog 4
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) VALUES (4, 2), (4, 3), (4, 4);
