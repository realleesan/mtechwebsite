# Kế hoạch Triển khai Hệ thống Danh mục Đa cấp, Bộ lọc & Header Mega Menu cho MTech (Bản Cập nhật)

Tài liệu này cung cấp phương án kiến trúc và kế hoạch triển khai chi tiết cho việc nâng cấp hệ thống MTech Website. Hệ thống hỗ trợ danh mục đa cấp độ sâu vô hạn, quan hệ cha-con đối với chính thực thể Dự án và Tin tức, đồng thời cho phép liên kết chéo tự do không giới hạn cấp độ.

---

## 1. Tổng quan & Mục tiêu nâng cấp

1. **Danh mục đa cấp độ sâu vô hạn (Infinite Depth):**
   - Sử dụng mô hình **Adjacency List** (`parent_id`) cho:
     - Dịch vụ (`categories`)
     - Dự án (tạo bảng danh mục riêng `project_categories`)
     - Tin tức (`blog_categories`)
   - Cho phép phân cấp đến cấp 3, cấp 4,... không giới hạn thông qua hàm đệ quy dựng cây.
2. **Quan hệ cha-con cho Dự án & Tin tức:**
   - Hỗ trợ dự án cha - dự án con (ví dụ: dự án lớn chứa các dự án thành phần).
   - Hỗ trợ bài viết tin tức cha - bài viết con (ví dụ: loạt bài viết nhiều kỳ).
3. **Liên kết chéo nhiều-nhiều tự do:**
   - Một dự án có thể thuộc nhiều danh mục dịch vụ. Dự án cha và dự án con có thể liên kết độc lập với các danh mục ở các cấp khác nhau (ví dụ: dự án cha thuộc danh mục con, dự án con thuộc danh mục cha).
   - Một bài viết tin tức có thể thuộc nhiều danh mục tin tức.
4. **Cấu hình bộ lọc Admin (Filter Config):**
   - Lưu trữ thứ tự và trạng thái hiển thị của các danh mục trên Mega Menu thông qua bảng trung gian `filter_config`.
5. **Mega Menu động (Dynamic Mega Menu):**
   - Thay thế dropdown cũ bằng giao diện Mega Menu dạng CSS Grid sang trọng, tự động render theo cây danh mục đã lọc.

---

## 2. Thiết kế Cơ sở Dữ liệu (Database Schema)

Chúng ta sẽ chuẩn bị các file Migration SQL để thực hiện các thay đổi cấu trúc bảng dưới đây:

### Migration 1: Bổ sung cấu trúc đa cấp cho Danh mục Dịch vụ & Tin tức

```sql
-- 1. Bổ sung cột parent_id cho bảng categories (Dịch vụ)
ALTER TABLE `categories` 
ADD COLUMN `parent_id` INT(11) UNSIGNED DEFAULT NULL AFTER `id`,
ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 2. Bổ sung cột parent_id cho bảng blog_categories (Tin tức)
ALTER TABLE `blog_categories` 
ADD COLUMN `parent_id` INT(11) UNSIGNED DEFAULT NULL AFTER `id`,
ADD CONSTRAINT `fk_blog_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
```

### Migration 2: Cập nhật quan hệ cha-con và nhiều-nhiều cho Dự án (Projects)

```sql
-- 1. Tạo bảng danh mục dự án project_categories đa cấp
CREATE TABLE IF NOT EXISTS `project_categories` (
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id`   INT(11) UNSIGNED DEFAULT NULL,
    `name`        VARCHAR(255)     NOT NULL COMMENT 'Tên danh mục dự án',
    `slug`        VARCHAR(255)     NOT NULL COMMENT 'Slug URL',
    `status`      TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '1 = Hiển thị, 0 = Ẩn',
    `sort_order`  INT(11)          NOT NULL DEFAULT 0,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_project_categories_slug` (`slug`),
    CONSTRAINT `fk_project_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `project_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Danh mục dự án đa cấp';

-- 2. Bổ sung cột parent_id vào bảng projects để hỗ trợ dự án cha-con
ALTER TABLE `projects` 
ADD COLUMN `parent_id` INT(11) UNSIGNED DEFAULT NULL AFTER `id`,
ADD COLUMN `project_category_id` INT(11) UNSIGNED DEFAULT NULL AFTER `parent_id`,
ADD CONSTRAINT `fk_projects_parent` FOREIGN KEY (`parent_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_projects_category` FOREIGN KEY (`project_category_id`) REFERENCES `project_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
```

### Migration 3: Cập nhật quan hệ cha-con và nhiều-nhiều cho Tin tức (Blogs)

```sql
-- 1. Bổ sung cột parent_id cho bảng blogs để hỗ trợ bài viết cha-con
ALTER TABLE `blogs`
ADD COLUMN `parent_id` INT(11) UNSIGNED DEFAULT NULL AFTER `id`,
ADD CONSTRAINT `fk_blogs_parent` FOREIGN KEY (`parent_id`) REFERENCES `blogs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 2. Tạo bảng trung gian blog_category_map cho quan hệ nhiều-nhiều giữa bài viết và danh mục
CREATE TABLE IF NOT EXISTS `blog_category_map` (
    `blog_id`     INT(11) UNSIGNED NOT NULL,
    `category_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`blog_id`, `category_id`),
    KEY `idx_blog_category_map_blog` (`blog_id`),
    KEY `idx_blog_category_map_category` (`category_id`),
    CONSTRAINT `fk_map_blog` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_map_category` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng map nhiều-nhiều bài viết và danh mục tin tức';

-- 3. Di chuyển dữ liệu cũ từ blogs.category_id sang bảng map và xóa cột cũ
INSERT INTO `blog_category_map` (`blog_id`, `category_id`)
SELECT `id`, `category_id` FROM `blogs` WHERE `category_id` IS NOT NULL;

ALTER TABLE `blogs` DROP FOREIGN KEY `fk_blogs_category`;
ALTER TABLE `blogs` DROP COLUMN `category_id`;
```

### Migration 4: Tạo bảng cấu hình bộ lọc Filter

```sql
CREATE TABLE IF NOT EXISTS `filter_config` (
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `criteria_type` VARCHAR(50)      NOT NULL COMMENT 'services, project_categories, blog_categories',
    `item_id`       INT(11) UNSIGNED NOT NULL COMMENT 'ID của danh mục tương ứng',
    `parent_id`     INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID danh mục cha trong cấu hình',
    `sort_order`    INT(11)          NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị tùy chỉnh',
    `is_enabled`    TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '1 = Hiện trên Mega Menu, 0 = Ẩn',
    `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_criteria_item` (`criteria_type`, `item_id`),
    KEY `idx_filter_config_lookup` (`criteria_type`, `is_enabled`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cấu hình bộ lọc và Mega Menu từ Admin';
```

---

## 3. Logic Nghiệp vụ Core (Core Services & Algorithms)

### 3.1. Thuật toán Đệ quy dựng Cây (`buildTree`)

Thuật toán đệ quy dưới đây hỗ trợ xử lý độ sâu vô hạn cho mọi cấu trúc phân cấp (Danh mục, Dự án, Tin tức):

```php
/**
 * Dựng cấu trúc cây phân cấp từ mảng phẳng
 * 
 * @param array $elements Mảng phẳng các đối tượng
 * @param int|null $parentId ID của cha hiện tại
 * @return array Mảng cây lồng nhau
 */
function buildTree(array $elements, $parentId = null): array {
    $branch = [];
    foreach ($elements as $element) {
        $elementParentId = empty($element['parent_id']) ? null : (int)$element['parent_id'];
        $checkParentId = empty($parentId) ? null : (int)$parentId;
      
        if ($elementParentId === $checkParentId) {
            $children = buildTree($elements, $element['id']);
            $element['children'] = $children ?: [];
            $branch[] = $element;
        }
    }
    return $branch;
}
```

---

## 4. Thiết kế Giao diện Mega Menu (Front-end UI/UX)

Mega Menu sẽ sử dụng CSS Grid kết hợp các cột nhóm danh mục và Showcase Spotlight ở bên phải:

```css
/* Kích hoạt trạng thái Mega Menu */
.nav-item.has-megamenu {
    position: static;
}

.megamenu-panel {
    display: block;
    position: absolute;
    top: calc(100% + 15px);
    left: 0;
    right: 0;
    width: 100%;
    background: rgba(13, 27, 62, 0.98);
    backdrop-filter: blur(10px);
    border-top: 3px solid #1A3FBF;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.35);
    padding: 35px 50px;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: opacity 0.35s ease, transform 0.35s ease, visibility 0.35s ease;
}

.nav-item.has-megamenu:hover .megamenu-panel {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.megamenu-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    gap: 30px;
}

.megamenu-grid {
    flex: 1;
    display: grid;
    gap: 25px;
    grid-template-columns: repeat(4, 1fr); /* 4 cột cân đối */
}
```

---

## 5. Phân công Công việc Cập nhật (Task Division)

### 👤 Developer 1: Phụ trách Dịch vụ (Services Module)

* **Nhiệm vụ:**
  1. Tạo và chạy SQL Migration cập nhật cột `parent_id` trong bảng `categories`.
  2. Cập nhật `CategoriesModel.php` và giao diện Admin (Create/Edit) để quản lý cấu trúc cha-con (dropdown chọn danh mục cha hỗ trợ n-cấp thụt lề).
  3. Cập nhật danh sách dịch vụ ngoài User Front-end để hiển thị theo cây phân cấp.

### 👤 Developer 2: Phụ trách Dự án (Projects Module)

* **Nhiệm vụ:**
  1. Tạo file migration xây dựng bảng `project_categories` đa cấp và thêm cột `parent_id` cùng `project_category_id` vào bảng `projects`.
  2. Cập nhật `ProjectsModel.php` hỗ trợ quan hệ cha-con của dự án và quan hệ Nhiều-Nhiều với dịch vụ/danh mục thông qua bảng liên kết.
  3. Cập nhật giao diện Admin quản lý Dự án:
     - Thêm dropdown chọn dự án cha.
     - Sử dụng hộp chọn **Checkbox cây phân cấp** để tích chọn nhiều dịch vụ liên kết cho dự án.

### 👤 Developer 3: Phụ trách Tin tức (News/Blogs Module)

* **Nhiệm vụ:**
  1. Tạo file migration thêm cột `parent_id` cho bảng `blog_categories` và `blogs`. Tạo bảng liên kết `blog_category_map`.
  2. Cập nhật `BlogsModel.php` hỗ trợ bài viết cha-con và liên kết Nhiều-Nhiều với danh mục tin tức.
  3. Cập nhật giao diện Admin quản lý bài viết:
     - Thêm dropdown chọn bài viết cha.
     - Sử dụng giao diện checkbox cây danh mục phân cấp để chọn nhiều danh mục cho một bài viết.

### 👑 Lead Developer / Fullstack (Integration & Mega Menu)

* **Nhiệm vụ:**
  1. Xây dựng dịch vụ cấu hình bộ lọc `FilterConfigService.php` và giao diện kéo thả Admin sử dụng **HTML5 Drag and Drop API (Vanilla JS)** — không phụ thuộc thư viện bên ngoài, nhất quán với tech stack hiện tại của dự án.
  2. Sửa file layout `header.php` và `header.css` để render Mega Menu động 4 mục lớn (Về chúng tôi, Dịch vụ, Dự án, Tin tức).
  3. Tích hợp khối "Spotlight Card" đẹp mắt cho mục "Về chúng tôi" và các mục khác để tăng tính thẩm mỹ.
