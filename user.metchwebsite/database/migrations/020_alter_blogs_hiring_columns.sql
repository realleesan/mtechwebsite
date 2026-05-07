--
-- Migration 020: Thêm các cột tuyển dụng vào bảng blogs
-- Các cột này chỉ có ý nghĩa khi blog thuộc category "Tuyển dụng" (cat=7)
--

ALTER TABLE `blogs`
    ADD COLUMN `position` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Vị trí ứng tuyển (vd: Quản lý dự án công nghiệp)',
    ADD COLUMN `expires_in_days` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Số ngày hết hạn tuyển dụng tính từ ngày tạo',
    ADD COLUMN `hiring_status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Trạng thái tuyển dụng: 1=đang tuyển, 0=ngừng tuyển';

-- Cập nhật dữ liệu hiện có cho 3 bài tuyển dụng (category_id = 7)
-- Blog ID 5: Tuyển Dụng Kỹ Sư Cơ Khí
UPDATE `blogs` SET 
    `position` = 'Kỹ Sư Cơ Khí',
    `expires_in_days` = 30,
    `hiring_status` = 1
WHERE `id` = 5 AND `category_id` = 7;

-- Blog ID 6: Tuyển Dụng Công Nhân Hàn
UPDATE `blogs` SET 
    `position` = 'Công Nhân Hàn Chuyên Nghiệp',
    `expires_in_days` = 20,
    `hiring_status` = 1
WHERE `id` = 6 AND `category_id` = 7;

-- Blog ID 7: Tuyển Dụng Quản Lý Dự Án
UPDATE `blogs` SET 
    `position` = 'Quản Lý Dự Án Công Nghiệp',
    `expires_in_days` = 15,
    `hiring_status` = 1
WHERE `id` = 7 AND `category_id` = 7;
