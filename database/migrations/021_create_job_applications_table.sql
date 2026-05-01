--
-- Migration 021: Tạo bảng lưu CV ứng tuyển
--

CREATE TABLE IF NOT EXISTS `job_applications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `blog_id` INT UNSIGNED NOT NULL COMMENT 'ID của bài tuyển dụng',
    `full_name` VARCHAR(255) NOT NULL COMMENT 'Họ và tên',
    `email` VARCHAR(255) NOT NULL COMMENT 'Email',
    `phone` VARCHAR(20) NOT NULL COMMENT 'Số điện thoại',
    `position` VARCHAR(255) NOT NULL COMMENT 'Vị trí ứng tuyển',
    `cv_file` VARCHAR(500) NOT NULL COMMENT 'Đường dẫn file CV đã upload',
    `message` TEXT NULL COMMENT 'Nội dung/Nội dung ứng tuyển',
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' 
        COMMENT 'Trạng thái: pending=chờ xử lý, approved=đã xử lý, rejected=đã hủy',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Khóa ngoại
    CONSTRAINT `fk_job_applications_blog` 
        FOREIGN KEY (`blog_id`) REFERENCES `blogs`(`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Index để tìm kiếm nhanh
    INDEX `idx_blog_id` (`blog_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_email` (`email`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu CV ứng tuyển cho các vị trí tuyển dụng';
