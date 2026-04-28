<?php
/**
 * TestimonialsModel.php
 * Model xử lý dữ liệu bảng `testimonials`.
 */

class TestimonialsModel
{
    /** @var PDO */
    private $db;
    private $table = 'testimonials';

    public function __construct($database = null)
    {
        if ($database) {
            $this->db = $database;
        } else {
            require_once __DIR__ . '/../../core/database.php';
            $this->db = getDBConnection();
        }
    }

    /**
     * Lấy N testimonials mới nhất / ưu tiên nhất cho trang Home.
     * Chỉ lấy tối đa $limit items, sắp xếp theo sort_order rồi id.
     *
     * @param int $limit Mặc định 3
     * @return array  Mảng phẳng các testimonial item
     */
    public function getHomeTestimonials($limit = 3)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, company_name, company_logo,
                        location_city, location_country, review_content
                 FROM `{$this->table}`
                 WHERE status = 1
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('TestimonialsModel::getHomeTestimonials() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả testimonials đang active, có phân trang.
     *
     * @param int $page
     * @param int $perPage  0 = lấy tất cả
     * @return array ['items' => [...], 'total' => int]
     */
    public function getTestimonials($page = 1, $perPage = 9)
    {
        try {
            $countStmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE status = 1"
            );
            $countStmt->execute();
            $total = (int) $countStmt->fetchColumn();

            if ($perPage > 0) {
                $offset = ($page - 1) * $perPage;
                $stmt = $this->db->prepare(
                    "SELECT * FROM `{$this->table}`
                     WHERE status = 1
                     ORDER BY sort_order ASC, id ASC
                     LIMIT ? OFFSET ?"
                );
                $stmt->execute([$perPage, $offset]);
            } else {
                $stmt = $this->db->prepare(
                    "SELECT * FROM `{$this->table}`
                     WHERE status = 1
                     ORDER BY sort_order ASC, id ASC"
                );
                $stmt->execute();
            }

            return [
                'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $total
            ];
        } catch (PDOException $e) {
            error_log('TestimonialsModel::getTestimonials() - ' . $e->getMessage());
            return ['items' => [], 'total' => 0];
        }
    }

    /**
     * Lấy testimonial theo ID.
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` WHERE id = ? AND status = 1 LIMIT 1"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('TestimonialsModel::getById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Đếm tổng số testimonials active.
     */
    public function countActive()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE status = 1"
            );
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('TestimonialsModel::countActive() - ' . $e->getMessage());
            return 0;
        }
    }
}
