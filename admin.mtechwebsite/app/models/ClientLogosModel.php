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

    /**
     * Thêm logo mới.
     */
    public function create(array $data): int|false
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}` (name, logo, url, status, sort_order)
                 VALUES (:name, :logo, :url, :status, :sort_order)"
            );
            $stmt->execute([
                ':name'       => $data['name'],
                ':logo'       => $data['logo']       ?? '',
                ':url'        => $data['url']         ?? '',
                ':status'     => $data['status']      ?? 1,
                ':sort_order' => $data['sort_order']  ?? 0,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('ClientLogosModel::create() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật logo.
     */
    public function update(int $id, array $data): bool
    {
        try {
            // Build SET clause dynamically (logo có thể không thay đổi)
            $fields = ['name = :name', 'url = :url', 'status = :status', 'sort_order = :sort_order'];
            $params = [
                ':id'         => $id,
                ':name'       => $data['name'],
                ':url'        => $data['url']        ?? '',
                ':status'     => $data['status']     ?? 1,
                ':sort_order' => $data['sort_order'] ?? 0,
            ];

            if (isset($data['logo']) && $data['logo'] !== '') {
                $fields[]        = 'logo = :logo';
                $params[':logo'] = $data['logo'];
            }

            $sql  = "UPDATE `{$this->table}` SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log('ClientLogosModel::update() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa logo.
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('ClientLogosModel::delete() - ' . $e->getMessage());
            return false;
        }
    }
}
