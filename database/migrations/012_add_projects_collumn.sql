-- Migration: Add show_in_menu column to projects table
-- Mục đích: Cho phép đánh dấu project có hiển thị trong dropdown menu hay không (1 = hiển thị, 0 = ẩn)

ALTER TABLE `projects` 
ADD COLUMN `show_in_menu` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Hiển thị trong dropdown menu: 1 = có, 0 = không' AFTER `status`;

-- Cập nhật index để tối ưu truy vấn
CREATE INDEX `idx_show_in_menu` ON `projects`(`show_in_menu`, `status`, `deleted_at`);