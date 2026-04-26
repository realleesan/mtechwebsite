-- Migration 016: Thêm cột show_in_footer vào bảng categories
-- Để kiểm soát categories nào hiển thị ở footer (1 = hiển thị, 0 = ẩn)

ALTER TABLE `categories`
ADD COLUMN IF NOT EXISTS `show_in_footer` TINYINT(1) DEFAULT 1 AFTER `show_on_home`;