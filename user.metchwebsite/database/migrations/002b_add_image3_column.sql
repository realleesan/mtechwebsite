-- ============================================================
-- Migration: 002b_add_image3_column.sql
-- Description: Chỉ thêm cột image_3 (các cột khác đã tồn tại từ 002)
-- Chạy file này nếu 002 đã được chạy trước đó và báo lỗi duplicate
-- ============================================================

ALTER TABLE `categories`
    ADD COLUMN `image_3` VARCHAR(500) DEFAULT NULL COMMENT 'Ảnh slider 3' AFTER `image_2`;

-- Cập nhật image_3 cho tất cả 6 categories
UPDATE `categories` SET `image_3` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_3.jpg'
WHERE `slug` IN ('agricultural-processing', 'chemical-industry', 'civil-engineering', 'energy-power', 'mechanical-engineering', 'oil-gas-engineering');
