-- ============================================
-- Migration: 006_create_contacts_table.sql
-- Description: Tạo bảng contacts để lưu thông tin liên hệ từ form
-- ============================================

CREATE TABLE IF NOT EXISTS `contacts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'Họ tên người gửi',
    `email` VARCHAR(255) NOT NULL COMMENT 'Email người gửi',
    `phone` VARCHAR(20) NULL COMMENT 'Số điện thoại',
    `message` TEXT NOT NULL COMMENT 'Nội dung tin nhắn',
    `status` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Trạng thái: 0=chưa đọc, 1=đã đọc, 2=đã phản hồi',
    `ip_address` VARCHAR(45) NULL COMMENT 'Địa chỉ IP người gửi',
    `user_agent` VARCHAR(255) NULL COMMENT 'Thông tin trình duyệt',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian gửi',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật',
    
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu thông tin liên hệ';
