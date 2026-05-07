-- Migration 014: Tạo bảng cho header dynamic
-- Bao gồm: header_settings

-- Bảng header_settings: Lưu các thông tin cấu hình hiển thị trên header
CREATE TABLE IF NOT EXISTS `header_settings` (
    `id`                  INT AUTO_INCREMENT PRIMARY KEY,
    `logo_path`           VARCHAR(500)  DEFAULT 'assets/images/logo.png'   COMMENT 'Đường dẫn logo',
    `logo_alt`            VARCHAR(255)  DEFAULT 'Logo'                      COMMENT 'Alt text của logo',
    `phone`               VARCHAR(50)   DEFAULT '0123 456 789'              COMMENT 'Số điện thoại topbar',
    `phone_href`          VARCHAR(50)   DEFAULT '0123456789'                COMMENT 'Số điện thoại dạng href (không dấu cách)',
    `iso_text`            VARCHAR(100)  DEFAULT 'ISO 9001 - 2010'           COMMENT 'Text ISO hiển thị topbar',
    `profile_pdf_path`    VARCHAR(500)  DEFAULT 'assets/files/ho-so-nang-luc.pdf' COMMENT 'Đường dẫn file PDF hồ sơ năng lực',
    `profile_pdf_label`   VARCHAR(100)  DEFAULT 'Hồ Sơ Năng Lực'           COMMENT 'Label nút tải PDF',
    `created_at`          TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default data
INSERT INTO `header_settings` (
    `logo_path`, `logo_alt`, `phone`, `phone_href`,
    `iso_text`, `profile_pdf_path`, `profile_pdf_label`
) VALUES (
    'assets/images/logo.png',
    'Wokrate Industrial',
    '0123 456 789',
    '0123456789',
    'ISO 9001 - 2010',
    'assets/files/ho-so-nang-luc.pdf',
    'Hồ Sơ Năng Lực'
);
