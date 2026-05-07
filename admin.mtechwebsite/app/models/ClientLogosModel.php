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

    /**
     * Lấy tất cả logos cho admin.
     */
    public function getAll()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, logo, url, status, sort_order
                 FROM `{$this->table}`
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ClientLogosModel::getAll() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy logo theo ID.
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('ClientLogosModel::getById() - ' . $e->getMessage());
            return null;
        }
    }
}
