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
    }

    // ----------------------------------------------------------------
    // PUBLIC METHODS
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả categories đang hoạt động (status = 1),
     * sắp xếp theo sort_order tăng dần, sau đó theo id tăng dần.
     *
     * @return array Mảng các category đang active
     */
    public function getAllCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, image, description, sort_order
                 FROM `{$this->table}`
                 WHERE status = 1
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                "SELECT * FROM `{$this->table}` WHERE slug = ? AND status = 1 LIMIT 1"
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
                 WHERE slug = ? AND status = 1
                 LIMIT 1"
            );
            $stmt->execute([$slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            // Decode JSON fields
            $row['benefit_items'] = !empty($row['benefit_items'])
                ? json_decode($row['benefit_items'], true)
                : [];
            $row['faq_items'] = !empty($row['faq_items'])
                ? json_decode($row['faq_items'], true)
                : [];

            return $row;
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
                "SELECT COUNT(*) FROM `{$this->table}` WHERE status = 1"
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
                 WHERE status = 1 AND show_on_home = 1
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
                 WHERE status = 1 AND show_in_menu = 1
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
                 WHERE status = 1 AND show_in_footer = 1
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
}
