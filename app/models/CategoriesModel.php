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
}
