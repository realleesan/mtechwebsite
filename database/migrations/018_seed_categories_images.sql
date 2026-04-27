-- ============================================================
-- Migration: 018_seed_categories_images.sql
-- Description: UPDATE ảnh cho các categories đã có trong DB
--              Chạy script này nếu bảng đã tạo trước đó với image = NULL
--              Ảnh lấy từ: docs/template/categories/code/categories.html
-- ============================================================

UPDATE `categories` SET `image` = 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img1.jpg' WHERE `slug` = 'agricultural-processing';
UPDATE `categories` SET `image` = 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img2.jpg' WHERE `slug` = 'chemical-industry';
UPDATE `categories` SET `image` = 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img3.jpg' WHERE `slug` = 'civil-engineering';
UPDATE `categories` SET `image` = 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img4.jpg' WHERE `slug` = 'energy-power';
UPDATE `categories` SET `image` = 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img5.jpg' WHERE `slug` = 'mechanical-engineering';
UPDATE `categories` SET `image` = 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img6.jpg' WHERE `slug` = 'oil-gas-engineering';
