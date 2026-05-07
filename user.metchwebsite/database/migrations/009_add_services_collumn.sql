-- ============================================================
-- Migration: 009_add_services_collumn.sql
-- Description: Thêm cột show_on_home cho bảng categories (services)
--              để quản lý hiển thị trên trang chủ
-- ============================================================

-- Thêm cột show_on_home cho bảng categories
ALTER TABLE `categories`
    ADD COLUMN `show_on_home` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = hiển thị trên trang chủ, 0 = không hiển thị' AFTER `status`;

-- Tạo index cho cột show_on_home để tối ưu truy vấn
CREATE INDEX `idx_categories_show_on_home` ON `categories`(`show_on_home`);

-- Cập nhật dữ liệu mẫu: tất cả services đều hiển thị trên trang chủ
UPDATE `categories` SET `show_on_home` = 1 WHERE `status` = 1;
