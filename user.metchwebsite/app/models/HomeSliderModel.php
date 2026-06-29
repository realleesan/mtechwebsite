<?php
/**
 * HomeSliderModel.php - User side
 * 
 * Model truy vấn dữ liệu hero slider từ bảng `home_sliders`.
 */

class HomeSliderModel
{
    /** @var PDO */
    private $db;

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
     * Lấy tất cả slide đang hoạt động (status=1, chưa xóa)
     * @return array
     */
    public function getActiveSlides()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, sort_order, status, image_1, image_2, image_3
                 FROM `home_sliders`
                 WHERE status = 1 AND deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HomeSliderModel::getActiveSlides() - ' . $e->getMessage());
            return [];
        }
    }
}
