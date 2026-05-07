-- ============================================================
-- Migration: 008_convert_from_testimon_to_awards_table.sql
-- Description: Xóa bảng testimonials, tạo bảng awards thay thế
-- ============================================================

-- Xóa bảng testimonials cũ
DROP TABLE IF EXISTS `testimonials`;

-- Tạo bảng awards mới
CREATE TABLE IF NOT EXISTS `awards` (
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)     NOT NULL COMMENT 'Tên giải thưởng / chứng chỉ',
    `certificate` VARCHAR(255)     DEFAULT NULL COMMENT 'Tên chứng chỉ / tổ chức cấp',
    `image`       VARCHAR(500)     DEFAULT NULL COMMENT 'Ảnh giải thưởng (URL hoặc path)',
    `status`      TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '1=hiển thị, 0=ẩn',
    `sort_order`  INT(11)          NOT NULL DEFAULT 0,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_awards_status` (`status`),
    KEY `idx_awards_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu giải thưởng và chứng chỉ của MTECHJSC';

-- Seed data placeholder (thay ảnh thực tế sau khi upload)
INSERT INTO `awards` (`name`, `certificate`, `image`, `status`, `sort_order`) VALUES
('Giải thưởng Chất lượng Quốc gia', 'Bộ Khoa học & Công nghệ',  NULL, 1, 1),
('Chứng chỉ ISO 9001:2015',         'Bureau Veritas',            NULL, 1, 2),
('Top 10 Doanh nghiệp Tiêu biểu',   'VCCI',                     NULL, 1, 3),
('Chứng chỉ ISO 14001:2015',        'TÜV Rheinland',             NULL, 1, 4),
('Giải thưởng Sao Vàng Đất Việt',   'Hội Doanh nghiệp trẻ VN',  NULL, 1, 5),
('Chứng nhận Nhà thầu Uy tín',      'Bộ Xây dựng',              NULL, 1, 6);
