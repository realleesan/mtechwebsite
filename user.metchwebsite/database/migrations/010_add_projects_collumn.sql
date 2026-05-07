-- ============================================================
-- Migration: 010_add_projects_collumn.sql
-- Description: Thêm cột show_on_home cho bảng projects
--              để quản lý hiển thị trên trang chủ
-- ============================================================

-- Thêm cột show_on_home cho bảng projects
ALTER TABLE `projects`
    ADD COLUMN `show_on_home` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = hiển thị trên trang chủ, 0 = không hiển thị' AFTER `status`;

-- Tạo index cho cột show_on_home để tối ưu truy vấn
CREATE INDEX `idx_projects_show_on_home` ON `projects`(`show_on_home`);

-- Cập nhật dữ liệu mẫu: chỉ 5 projects đầu tiên hiển thị trên trang chủ
UPDATE `projects` SET `show_on_home` = 1 WHERE `status` = 1 AND `sort_order` <= 5;
UPDATE `projects` SET `show_on_home` = 0 WHERE `status` = 1 AND `sort_order` > 5;
