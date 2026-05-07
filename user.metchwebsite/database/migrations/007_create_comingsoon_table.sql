-- Migration: Create comingsoon settings table
-- Description: Bảng cấu hình trang Coming Soon / Maintenance Mode

CREATE TABLE IF NOT EXISTS comingsoon_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    is_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = Coming Soon active, 0 = normal',
    target_date DATETIME NOT NULL COMMENT 'Ngày giờ website sẽ launch',
    title VARCHAR(255) DEFAULT 'We\'re Coming Soon...' COMMENT 'Tiêu đề trang',
    description TEXT DEFAULT 'Our website is under construction. We\'ll be here soon with our new awesome site.' COMMENT 'Mô tả',
    subscribe_text VARCHAR(255) DEFAULT 'Subscribe to be notified.' COMMENT 'Text kêu gọi subscribe',
    email_placeholder VARCHAR(100) DEFAULT 'Enter your email address' COMMENT 'Placeholder input email',
    button_text VARCHAR(100) DEFAULT 'Subscribe now' COMMENT 'Text nút subscribe',
    background_color VARCHAR(20) DEFAULT '#ffc107' COMMENT 'Màu nền chính',
    text_color VARCHAR(20) DEFAULT '#333333' COMMENT 'Màu chữ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings (Coming Soon disabled by default)
INSERT INTO comingsoon_settings (is_active, target_date, title, description) 
VALUES (0, DATE_ADD(NOW(), INTERVAL 30 DAY), 'We\'re Coming Soon...', 'Our website is under construction. We\'ll be here soon with our new awesome site, Subscribe to be notified.');

-- Table for storing subscriber emails
CREATE TABLE IF NOT EXISTS comingsoon_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notified_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời điểm đã gửi email thông báo launch'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;