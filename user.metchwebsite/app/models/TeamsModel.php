<?php
/**
 * TeamsModel.php
 * Model xử lý dữ liệu bảng `teams` — đội ngũ chuyên gia.
 */

class TeamsModel
{
    /** @var PDO */
    private $db;
    private $table = 'teams';

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
     * Lấy tất cả thành viên đang active, sắp xếp theo sort_order.
     * Dùng cho trang hiển thị công khai.
     *
     * @return array
     */
    public function getAllActive()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, position, image, bio
                 FROM `{$this->table}`
                 WHERE status = 1
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('TeamsModel::getAllActive() - ' . $e->getMessage());
            return [];
        }
    }
}
