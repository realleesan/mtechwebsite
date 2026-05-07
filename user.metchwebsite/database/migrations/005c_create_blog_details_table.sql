-- ============================================================
-- Migration: 005c_create_blog_details_table.sql
-- Description: Tạo bảng blog_details để lưu nội dung chi tiết bài viết
-- ============================================================

-- ------------------------------------------------------------
-- 1. Bảng blog_details - Lưu nội dung chi tiết mở rộng
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_details` (
    `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `blog_id`         INT(11) UNSIGNED NOT NULL COMMENT 'FK -> blogs.id',
    `full_content`    LONGTEXT         DEFAULT NULL COMMENT 'Nội dung đầy đủ HTML',
    `meta_title`      VARCHAR(255)     DEFAULT NULL COMMENT 'SEO title',
    `meta_description` TEXT            DEFAULT NULL COMMENT 'SEO description',
    `meta_keywords`   VARCHAR(500)     DEFAULT NULL COMMENT 'SEO keywords',
    `reading_time`    INT(11)          DEFAULT 0 COMMENT 'Thời gian đọc (phút)',
    `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_blog_details_blog_id` (`blog_id`),
    CONSTRAINT `fk_blog_details_blog`
        FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu nội dung chi tiết blog';

-- ------------------------------------------------------------
-- 2. Seed data mẫu cho blog_details
-- ------------------------------------------------------------
INSERT INTO `blog_details` (`blog_id`, `full_content`, `meta_title`, `meta_description`, `reading_time`) VALUES
(
    1,
    '<div class="blog-text">
        <p>Podcasting operational change management inside of workflows to establish a framework. Taking seamless key performance indicators offline to maximise the long tail. Keeping your eye on the ball while performing a deep dive on the start-up mentality to derive convergence on cross-platform integration. Collaboratively administrate empowered markets via plug-and-play networks.</p>
    </div>
    <div class="s_main_text">
        <h3 class="f_600 title_color">Main Content</h3>
        <p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
        <p>Here is main text quis nostrud exercitation ullamco laboris nisi here is itealic text ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla rure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat <a href="#">here is link</a> cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
    </div>',
    'Capitalize on low hanging fruit to identify - MTECHJSC',
    'Podcasting operational change management inside of workflows to establish a framework. Taking seamless key performance indicators offline to maximise the long tail.',
    5
);
