-- ============================================================
-- Migration: 011_add_services_collumn.sql
-- Description: Thêm cột show_in_menu vào bảng categories
--              để kiểm soát hiển thị trong dropdown menu header
-- ============================================================

ALTER TABLE `categories`
    ADD COLUMN `show_in_menu` TINYINT(1) NOT NULL DEFAULT 0
        COMMENT '1 = hiển thị trong dropdown menu header, 0 = ẩn'
        AFTER `show_in_footer`;

-- Index để query nhanh hơn
CREATE INDEX `idx_categories_show_in_menu` ON `categories` (`show_in_menu`);

-- ============================================================
-- Cập nhật dữ liệu mẫu: bật show_in_menu cho tất cả categories active
-- ============================================================
UPDATE `categories` SET `show_in_menu` = 1 WHERE `status` = 1;
