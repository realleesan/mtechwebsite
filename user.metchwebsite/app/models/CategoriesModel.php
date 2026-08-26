<?php
/**
 * CategoriesModel.php
 * 
 * Model xử lý dữ liệu bảng `categories`.
 * Chịu trách nhiệm truy vấn và trả về dữ liệu cho View.
 */

class CategoriesModel
{
    /** @var PDO */
    private $db;

    /** @var string Tên bảng */
    private $table = 'categories';

    /**
     * Constructor - Khởi tạo kết nối database
     * @param PDO|null $database Inject PDO từ ngoài (optional)
     */
    public function __construct($database = null)
    {
        if ($database) {
            $this->db = $database;
        } else {
            require_once __DIR__ . '/../../core/database.php';
            $this->db = getDBConnection();
        }
        $this->ensureColumns();
    }

    private function columnExists(string $column): bool
    {
        try {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM `{$this->table}` LIKE ?");
            $stmt->execute([$column]);
            return (bool)$stmt->fetch();
        } catch (\Exception $e) {
            try {
                $this->db->query("SELECT `{$column}` FROM `{$this->table}` LIMIT 0");
                return true;
            } catch (\Exception $e2) {
                return false;
            }
        }
    }

    public function ensureColumns(): void
    {
        $columns = [
            'parent_id'           => "ALTER TABLE `{$this->table}` ADD COLUMN `parent_id` INT NULL DEFAULT NULL",
            'detail_description'  => "ALTER TABLE `{$this->table}` ADD COLUMN `detail_description` LONGTEXT NULL DEFAULT NULL",
            'image_1'             => "ALTER TABLE `{$this->table}` ADD COLUMN `image_1` VARCHAR(255) NULL DEFAULT NULL",
            'image_2'             => "ALTER TABLE `{$this->table}` ADD COLUMN `image_2` VARCHAR(255) NULL DEFAULT NULL",
            'image_3'             => "ALTER TABLE `{$this->table}` ADD COLUMN `image_3` VARCHAR(255) NULL DEFAULT NULL",
            'benefit_image'       => "ALTER TABLE `{$this->table}` ADD COLUMN `benefit_image` VARCHAR(255) NULL DEFAULT NULL",
            'benefit_title'       => "ALTER TABLE `{$this->table}` ADD COLUMN `benefit_title` VARCHAR(255) NULL DEFAULT NULL",
            'benefit_description' => "ALTER TABLE `{$this->table}` ADD COLUMN `benefit_description` TEXT NULL DEFAULT NULL",
            'benefit_items'       => "ALTER TABLE `{$this->table}` ADD COLUMN `benefit_items` LONGTEXT NULL DEFAULT NULL",
            'feature_image'       => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_image` VARCHAR(255) NULL DEFAULT NULL",
            'feature_1_icon'      => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_1_icon` VARCHAR(255) NULL DEFAULT NULL",
            'feature_1_title'     => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_1_title` VARCHAR(255) NULL DEFAULT NULL",
            'feature_1_text'      => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_1_text` TEXT NULL DEFAULT NULL",
            'feature_2_icon'      => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_2_icon` VARCHAR(255) NULL DEFAULT NULL",
            'feature_2_title'     => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_2_title` VARCHAR(255) NULL DEFAULT NULL",
            'feature_2_text'      => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_2_text` TEXT NULL DEFAULT NULL",
            'faq_items'           => "ALTER TABLE `{$this->table}` ADD COLUMN `faq_items` LONGTEXT NULL DEFAULT NULL",
            'show_in_footer'      => "ALTER TABLE `{$this->table}` ADD COLUMN `show_in_footer` TINYINT DEFAULT 0",
            'featured_project_id' => "ALTER TABLE `{$this->table}` ADD COLUMN `featured_project_id` INT NULL DEFAULT NULL",
            'deleted_at'          => "ALTER TABLE `{$this->table}` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL",
            'updated_at'          => "ALTER TABLE `{$this->table}` ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL",
        ];

        foreach ($columns as $col => $sql) {
            if (!$this->columnExists($col)) {
                try {
                    $this->db->exec($sql);
                } catch (\Exception $e) {
                    error_log("CategoriesModel::ensureColumns() failed for {$col}: " . $e->getMessage());
                }
            }
        }
    }

    // ----------------------------------------------------------------
    // PUBLIC METHODS
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả categories đang hoạt động (status = 1),
     * sắp xếp theo sort_order tăng dần, sau đó theo id tăng dần.
     *
     * @return array Mảng các category đang active kèm dự án được gán
     */
    public function getAllCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT c.id, c.parent_id, c.name, c.slug, c.image, c.description, c.status, 
                        c.sort_order, c.show_in_footer, c.featured_project_id,
                        p.id as project_id, p.title as project_title, p.slug as project_slug
                 FROM `{$this->table}` c
                 LEFT JOIN projects p ON c.featured_project_id = p.id
                 WHERE c.status = 1 AND c.deleted_at IS NULL
                 ORDER BY c.sort_order ASC, c.id ASC"
            );
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Chỉ lấy dự án được admin chọn (featured_project_id) - không lấy toàn bộ project_services
            foreach ($categories as &$cat) {
                if (!empty($cat['featured_project_id']) && !empty($cat['project_id'])) {
                    $cat['projects'] = [
                        [
                            'id' => $cat['project_id'],
                            'title' => $cat['project_title'],
                            'slug' => $cat['project_slug'],
                        ]
                    ];
                } else {
                    $cat['projects'] = [];
                }
            }

            return $categories;
        } catch (PDOException $e) {
            error_log('CategoriesModel::getAllCategories() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy một category theo ID.
     *
     * @param  int        $id
     * @return array|null
     */
    public function getCategoryById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('CategoriesModel::getCategoryById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy một category theo slug.
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getCategoryBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` WHERE slug = ? AND status = 1 AND deleted_at IS NULL LIMIT 1"
            );
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('CategoriesModel::getCategoryBySlug() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy chi tiết đầy đủ một category theo slug (bao gồm các cột detail).
     * Dùng cho trang categories_details.php
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getCategoryDetailBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, image, image_1, image_2, image_3,
                        description, detail_description,
                        benefit_image, benefit_title, benefit_description, benefit_items,
                        feature_image, feature_1_icon, feature_1_title, feature_1_text,
                        feature_2_icon, feature_2_title, feature_2_text,
                        faq_items, sort_order
                 FROM `{$this->table}`
                 WHERE slug = ? AND status = 1 AND deleted_at IS NULL
                 LIMIT 1"
            );
            $stmt->execute([$slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // Decode JSON fields
                $row['benefit_items'] = !empty($row['benefit_items'])
                    ? json_decode($row['benefit_items'], true)
                    : [];
                $row['faq_items'] = !empty($row['faq_items'])
                    ? json_decode($row['faq_items'], true)
                    : [];
                return $row;
            }

            // Không tìm thấy trong DB (đã xóa hoặc không tồn tại) → trả về null
            return null;

        } catch (PDOException $e) {
            error_log('CategoriesModel::getCategoryDetailBySlug() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Đếm tổng số categories đang hoạt động.
     *
     * @return int
     */
    public function countActiveCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE status = 1 AND deleted_at IS NULL"
            );
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('CategoriesModel::countActiveCategories() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy services hiển thị trên trang chủ (show_on_home=1).
     * Giới hạn tối đa 6 services.
     *
     * @param int $limit Số lượng tối đa (mặc định 6)
     * @return array Mảng services cho trang chủ
     */
    public function getHomeServices($limit = 6)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, image, description, sort_order
                 FROM `{$this->table}`
                 WHERE status = 1 AND show_on_home = 1 AND deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CategoriesModel::getHomeServices() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy services hiển thị trong dropdown menu header (show_in_menu=1).
     *
     * @param int $limit Số lượng tối đa (mặc định 10)
     * @return array Mảng services cho menu dropdown
     */
    public function getMenuServices($limit = 10)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug
                 FROM `{$this->table}`
                 WHERE status = 1 AND show_in_menu = 1 AND deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CategoriesModel::getMenuServices() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy services hiển thị ở footer (show_in_footer=1).
     * Các mục này sẽ hiển thị trong cột Services của footer.
     *
     * @param int $limit Số lượng tối đa (mặc định 10)
     * @return array Mảng services cho footer
     */
    public function getFooterServices($limit = 10)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, sort_order
                 FROM `{$this->table}`
                 WHERE status = 1 AND show_in_footer = 1 AND deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CategoriesModel::getFooterServices() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Dựng cấu trúc cây đệ quy cho danh mục
     *
     * @param array $elements
     * @param int|null $parentId
     * @return array
     */
    public function buildTree(array $elements, $parentId = null, $maxDepth = 10, $currentDepth = 0, $visitedIds = []): array
    {
        if ($currentDepth >= $maxDepth) return [];
        $branch = [];
        foreach ($elements as $element) {
            $elementParentId = empty($element['parent_id']) ? null : (int)$element['parent_id'];
            $checkParentId = empty($parentId) ? null : (int)$parentId;
            
            if ($elementParentId === $checkParentId) {
                if (in_array($element['id'], $visitedIds)) {
                    continue;
                }
                $newVisitedIds = array_merge($visitedIds, [$element['id']]);
                $children = $this->buildTree($elements, $element['id'], $maxDepth, $currentDepth + 1, $newVisitedIds);
                $element['children'] = $children ?: [];
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
