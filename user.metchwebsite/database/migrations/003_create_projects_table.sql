-- ==========================================
-- Migration: 003_create_projects_table.sql
-- Description: Tạo bảng projects cho trang dự án
-- ==========================================

-- Xóa bảng nếu đã tồn tại
DROP TABLE IF EXISTS `projects`;

-- Tạo bảng projects
CREATE TABLE `projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL COMMENT 'Tên dự án',
    `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'URL thân thiện',
    `category` VARCHAR(100) NOT NULL COMMENT 'Danh mục (Power & Energy, Mechanical Engineering,...)',
    `description` TEXT COMMENT 'Mô tả ngắn',
    `content` LONGTEXT COMMENT 'Nội dung chi tiết',
    `image` VARCHAR(255) COMMENT 'Đường dẫn ảnh đại diện',
    `gallery` TEXT COMMENT 'JSON array các ảnh gallery',
    `client` VARCHAR(255) COMMENT 'Tên khách hàng',
    `location` VARCHAR(255) COMMENT 'Địa điểm',
    `project_date` DATE COMMENT 'Ngày thực hiện',
    `status` TINYINT UNSIGNED DEFAULT 1 COMMENT '0: inactive, 1: active, 2: featured',
    `sort_order` INT UNSIGNED DEFAULT 0 COMMENT 'Thứ tự sắp xếp',
    `meta_title` VARCHAR(255) COMMENT 'SEO title',
    `meta_description` TEXT COMMENT 'SEO description',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft delete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu trữ dự án';

-- Tạo index cho các trường thường dùng để tìm kiếm
CREATE INDEX `idx_projects_status` ON `projects`(`status`);
CREATE INDEX `idx_projects_category` ON `projects`(`category`);
CREATE INDEX `idx_projects_sort` ON `projects`(`sort_order`);

-- Thêm dữ liệu mẫu (sử dụng link ảnh từ template HTML)
INSERT INTO `projects` (`title`, `slug`, `category`, `description`, `image`, `status`, `sort_order`) VALUES
('Chemical Chamber', 'chemical-chamber', 'Power & Energy', 'Dự án buồng hóa chất hiện đại', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_1-1.jpg', 1, 1),
('Welding Processing', 'welding-processing', 'Mechanical Engineering', 'Hệ thống xử lý hàn chuyên nghiệp', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_2-1.jpg', 1, 2),
('Railway Project', 'railway-project', 'Material Engineering', 'Dự án đường sắt công nghiệp', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_3-1.jpg', 1, 3),
('Material Engineering', 'material-engineering', 'Architecture Engineering', 'Kỹ thuật vật liệu xây dựng', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_4-1.jpg', 1, 4),
('Wind Power Project', 'wind-power-project', 'Power & Energy', 'Dự án điện gió tái tạo', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_5-1.jpg', 1, 5),
('Robot Engineering', 'robot-engineering', 'Mechanical Engineering', 'Kỹ thuật robot tự động', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_6-1.jpg', 1, 6),
('Flyover Bridge', 'flyover-bridge', 'Material Engineering', 'Cầu vượt giao thông', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_7-1.jpg', 1, 7),
('Welding Processing II', 'welding-processing-ii', 'Iron Sector', 'Hệ thống hàn kim loại tiên tiến', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_8-3.jpg', 1, 8),
('Petroleum Chamber', 'petroleum-chamber', 'Iron Sector', 'Buồng chứa dầu khí', 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/pr_10-1.jpg', 1, 9);
