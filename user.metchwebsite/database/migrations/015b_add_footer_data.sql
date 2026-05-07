-- Migration 015b: Thêm dữ liệu mẫu cho footer
-- Dữ liệu khớp với hardcoded data trong footer.php

-- 1. Cập nhật tiêu đề useful links sang tiếng Việt
UPDATE `footer_settings` SET `useful_links_title` = 'Liên kết' WHERE `id` = 1;

-- 2. Thêm dữ liệu useful links (khớp với fallback links trong footer.php)
INSERT INTO `footer_links` (`title`, `url`, `sort_order`, `is_active`) VALUES
('Trang chủ', './', 1, 1),
('Giới thiệu', '?page=about', 2, 1),
('Dịch vụ', '?page=categories', 3, 1),
('Dự án', '?page=projects', 4, 1),
('Liên hệ', '?page=contact', 5, 1);

-- 3. Cập nhật social links với giá trị mặc định (có thể sửa sau)
-- Facebook, LinkedIn để trống, Twitter/Google ẩn đi
UPDATE `footer_social` SET `url` = NULL, `is_visible` = 0 WHERE `platform` = 'twitter';
UPDATE `footer_social` SET `url` = NULL, `is_visible` = 0 WHERE `platform` = 'google';
UPDATE `footer_social` SET `url` = NULL WHERE `platform` = 'facebook';
UPDATE `footer_social` SET `url` = NULL WHERE `platform` = 'linkedin';
