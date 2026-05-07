<?php
/**
 * AwardsModel.php
 * Model xử lý dữ liệu bảng `awards` — giải thưởng & chứng chỉ.
 */

class AwardsModel
{
    /** @var PDO */
    private $db;
    private $table = 'awards';

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
     * Lấy tất cả awards đang active, sắp xếp theo sort_order.
     */
    public function getAllActive()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, certificate, image
                 FROM `{$this->table}`
                 WHERE status = 1
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('AwardsModel::getAllActive() - ' . $e->getMessage());
            return [];
        }
    }
}
