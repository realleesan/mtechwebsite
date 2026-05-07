<?php
/**
 * TeamsModel.php
 * Model xử lý dữ liệu bảng `teams`.
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
     * Lấy tất cả team members đang active, sắp xếp theo sort_order.
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

    /**
     * Lấy tất cả team members cho admin.
     */
    public function getAll()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, position, image, bio, status, sort_order
                 FROM `{$this->table}`
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('TeamsModel::getAll() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy team member theo ID.
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
            error_log('TeamsModel::getById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo team member mới.
     */
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}` (name, position, image, bio, status, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['name'],
                $data['position'],
                $data['image'] ?? '',
                $data['bio'] ?? '',
                $data['status'] ?? 1,
                $data['sort_order'] ?? 0
            ]);
        } catch (PDOException $e) {
            error_log('TeamsModel::create() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật team member.
     */
    public function update($id, $data)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` 
                 SET name = ?, position = ?, image = ?, bio = ?, status = ?, sort_order = ?
                 WHERE id = ?"
            );
            return $stmt->execute([
                $data['name'],
                $data['position'],
                $data['image'] ?? '',
                $data['bio'] ?? '',
                $data['status'] ?? 1,
                $data['sort_order'] ?? 0,
                $id
            ]);
        } catch (PDOException $e) {
            error_log('TeamsModel::update() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa team member.
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('TeamsModel::delete() - ' . $e->getMessage());
            return false;
        }
    }
}