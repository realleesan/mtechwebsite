-- ============================================================
-- Migration: 008_create_testimonials_table.sql
-- Description: Tạo bảng testimonials - đánh giá từ khách hàng
-- ============================================================

CREATE TABLE IF NOT EXISTS `testimonials` (
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_name`  VARCHAR(255)     NOT NULL COMMENT 'Tên doanh nghiệp / khách hàng',
    `company_logo`  VARCHAR(500)     DEFAULT NULL COMMENT 'Ảnh logo/profile doanh nghiệp (URL hoặc path)',
    `location_city` VARCHAR(100)     DEFAULT NULL COMMENT 'Thành phố',
    `location_country` VARCHAR(100)  DEFAULT NULL COMMENT 'Quốc gia',
    `review_content` TEXT            NOT NULL COMMENT 'Nội dung nhận xét dành cho MTECHJSC',
    `rating`        TINYINT(1)       NOT NULL DEFAULT 5 COMMENT 'Đánh giá sao (1-5)',
    `status`        TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '1=hiển thị, 0=ẩn',
    `sort_order`    INT(11)          NOT NULL DEFAULT 0,
    `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_testimonials_status` (`status`),
    KEY `idx_testimonials_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu đánh giá khách hàng dành cho MTECHJSC';

-- ============================================================
-- Seed thêm 9 testimonials để test carousel (trang 2)
-- ============================================================
INSERT INTO `testimonials`
    (`company_name`, `company_logo`, `location_city`, `location_country`, `review_content`, `rating`, `status`, `sort_order`)
VALUES
(
    'Sarah Mitchell',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img2.png',
    'Sydney', 'Australia',
    'Collaboratively administrate empowered markets via plug-and-play networks. Dynamically procrastinate B2C users after installed base benefits.',
    5, 1, 10
),
(
    'James Carter',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img3.png',
    'London', 'United Kingdom',
    'Efficiently unleash cross-media information without cross-media value. Quickly maximize timely deliverables for real-time schemas.',
    5, 1, 11
),
(
    'Yuki Tanaka',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img1.png',
    'Tokyo', 'Japan',
    'Completely synergize resource taxing relationships via premier niche markets. Professionally cultivate one-to-one customer service.',
    5, 1, 12
),
(
    'Emily Chen',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img2.png',
    'Singapore', 'Singapore',
    'Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures.',
    5, 1, 13
),
(
    'Marco Rossi',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img3.png',
    'Milan', 'Italy',
    'Proactively envisioned multimedia based expertise and cross-media growth strategies. Seamlessly visualize quality intellectual capital.',
    5, 1, 14
),
(
    'Anna Kowalski',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img1.png',
    'Warsaw', 'Poland',
    'Interactively coordinate proactive e-commerce via process-centric outside the box thinking. Completely pursue scalable customer service.',
    5, 1, 15
),
(
    'David Park',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img2.png',
    'Seoul', 'South Korea',
    'Dynamically target high-payoff intellectual capital for customized technologies. Objectively integrate emerging core competencies.',
    5, 1, 16
),
(
    'Sofia Andrade',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img3.png',
    'São Paulo', 'Brazil',
    'Credibly reintermediate backend ideas for cross-platform models. Continually reintermediate integrated processes through technically sound intellectual capital.',
    5, 1, 17
),
(
    'Liam O\'Brien',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img1.png',
    'Dublin', 'Ireland',
    'Enthusiastically mesh long-term high-impact infrastructures vis-a-vis efficient customer service. Professionally fashion wireless leadership.',
    5, 1, 18
);
