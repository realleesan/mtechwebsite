-- ============================================================
-- Migration: 017b_update_client_logos_data.sql
-- Description: Cập nhật dữ liệu logo đối tác với 5 ảnh thực tế
-- Date: 2026-05-01
-- ============================================================

-- Xóa dữ liệu cũ (nếu có)
TRUNCATE TABLE `client_logos`;

-- Insert 5 logo đối tác thực tế từ assets/images/client_logo_pics
INSERT INTO `client_logos` (`name`, `logo`, `url`, `status`, `sort_order`) VALUES
('Coninomi',        'assets/images/client_logo_pics/coninomi.jpg',       'https://conincomi.vn/',                    1, 1),
('Long Son Cement', 'assets/images/client_logo_pics/longsoncement.png',  'https://longsoncement.com.vn/',            1, 2),
('Thang Long',      'assets/images/client_logo_pics/thanglong.png',      'https://www.thanglongcement.com.vn/',      1, 3),
('Vicem',           'assets/images/client_logo_pics/vicem.jpg',          'https://vicem.vn/',                        1, 4),
('Xuan Thanh',      'assets/images/client_logo_pics/xuanthanh.png',      'https://ximangxuanthanh.vn/',              1, 5);

-- Verify data
SELECT * FROM `client_logos` ORDER BY `sort_order` ASC;
