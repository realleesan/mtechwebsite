<?php
/**
 * ClientLogosModel.php
 * Model xử lý dữ liệu bảng `client_logos`.
 */

class ClientLogosModel
{
    /** @var PDO */
    private $db;
    private $table = 'client_logos';

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
     * Lấy tất cả logo đang active, sắp xếp theo sort_order.
     */
    public function getAllActive()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, logo, url
                 FROM `{$this->table}`
                 WHERE status = 1
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ClientLogosModel::getAllActive() - ' . $e->getMessage());
            return [];
        }
    }
}
