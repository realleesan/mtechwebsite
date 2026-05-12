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

    /** Trả về PDO instance (dùng cho controller khi cần truy vấn trực tiếp) */
    public function getDb(): \PDO
    {
        return $this->db;
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
                 WHERE status = 1 AND deleted_at IS NULL
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
     * Lấy tất cả team members cho admin (chưa bị xóa mềm).
     */
    public function getAll()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, position, image, bio, status, sort_order, created_at
                 FROM `{$this->table}`
                 WHERE deleted_at IS NULL
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
     * Lấy team member theo ID (chưa bị xóa mềm).
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` WHERE id = ? AND deleted_at IS NULL LIMIT 1"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('TeamsModel::getById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo team member mới. Trả về ID vừa insert, hoặc false nếu lỗi.
     */
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}` (name, position, image, bio, status, sort_order, show_in_about)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $ok = $stmt->execute([
                $data['name'],
                $data['position'],
                $data['image'] ?? '',
                $data['bio'] ?? '',
                $data['status'] ?? 1,
                $data['sort_order'] ?? 0,
                $data['show_in_about'] ?? 0,
            ]);
            return $ok ? (int)$this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log('TeamsModel::create() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật team member.
     * Nếu $data không chứa key 'image' thì giữ nguyên ảnh cũ trong DB.
     */
    public function update($id, $data)
    {
        try {
            if (array_key_exists('image', $data)) {
                $stmt = $this->db->prepare(
                    "UPDATE `{$this->table}`
                     SET name = ?, position = ?, image = ?, bio = ?, status = ?, sort_order = ?, show_in_about = ?
                     WHERE id = ?"
                );
                return $stmt->execute([
                    $data['name'],
                    $data['position'],
                    $data['image'],
                    $data['bio'] ?? '',
                    $data['status'] ?? 1,
                    $data['sort_order'] ?? 0,
                    $data['show_in_about'] ?? 0,
                    $id
                ]);
            } else {
                $stmt = $this->db->prepare(
                    "UPDATE `{$this->table}`
                     SET name = ?, position = ?, bio = ?, status = ?, sort_order = ?, show_in_about = ?
                     WHERE id = ?"
                );
                return $stmt->execute([
                    $data['name'],
                    $data['position'],
                    $data['bio'] ?? '',
                    $data['status'] ?? 1,
                    $data['sort_order'] ?? 0,
                    $data['show_in_about'] ?? 0,
                    $id
                ]);
            }
        } catch (PDOException $e) {
            error_log('TeamsModel::update() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa mềm team member (chuyển vào thùng rác).
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('TeamsModel::delete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách thành viên trong thùng rác.
     */
    public function getTrashed(int $limit = 20, int $offset = 0): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, position, image, deleted_at
                 FROM `{$this->table}`
                 WHERE deleted_at IS NOT NULL
                 ORDER BY deleted_at DESC
                 LIMIT ? OFFSET ?"
            );
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('TeamsModel::getTrashed() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số thành viên trong thùng rác.
     */
    public function countTrashed(): int
    {
        try {
            $stmt = $this->db->query(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE deleted_at IS NOT NULL"
            );
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('TeamsModel::countTrashed() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Khôi phục thành viên từ thùng rác.
     */
    public function restore(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET deleted_at = NULL WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('TeamsModel::restore() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa vĩnh viễn thành viên khỏi database.
     */
    public function hardDelete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM `{$this->table}` WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('TeamsModel::hardDelete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số thành viên đang được hiển thị trên trang Giới thiệu.
     * Dùng để kiểm tra giới hạn tối đa 4.
     *
     * @param int|null $excludeId  Bỏ qua ID này khi đếm (dùng khi update)
     */
    public function countShowInAbout(?int $excludeId = null): int
    {
        try {
            if ($excludeId !== null) {
                $stmt = $this->db->prepare(
                    "SELECT COUNT(*) FROM `{$this->table}`
                     WHERE show_in_about = 1 AND deleted_at IS NULL AND id != ?"
                );
                $stmt->execute([$excludeId]);
            } else {
                $stmt = $this->db->query(
                    "SELECT COUNT(*) FROM `{$this->table}`
                     WHERE show_in_about = 1 AND deleted_at IS NULL"
                );
            }
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('TeamsModel::countShowInAbout() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đẩy các bản ghi có sort_order >= $fromOrder lên 1 để nhường chỗ.
     * Chỉ tác động lên bản ghi chưa bị xóa mềm.
     */
    public function shiftOrdersDown(int $excludeId, int $fromOrder): void
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}`
                 SET sort_order = sort_order + 1
                 WHERE id != ? AND sort_order >= ? AND deleted_at IS NULL"
            );
            $stmt->execute([$excludeId, $fromOrder]);
        } catch (PDOException $e) {
            error_log('TeamsModel::shiftOrdersDown() - ' . $e->getMessage());
        }
    }

    /**
     * Chuẩn hóa lại sort_order thành dãy liên tục 1, 2, 3, ...
     * Chỉ cập nhật các bản ghi chưa bị xóa mềm (deleted_at IS NULL).
     * Gọi SAU mỗi thao tác insert/update/delete để lấp khoảng trống.
     */
    public function compactOrders(): void
    {
        try {
            // Chỉ lấy bản ghi chưa xóa mềm
            $stmt = $this->db->query(
                "SELECT id FROM `{$this->table}`
                 WHERE deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC"
            );
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $update = $this->db->prepare(
                "UPDATE `{$this->table}` SET sort_order = ? WHERE id = ?"
            );
            foreach ($ids as $i => $id) {
                $update->execute([$i + 1, $id]);
            }
        } catch (PDOException $e) {
            error_log('TeamsModel::compactOrders() - ' . $e->getMessage());
        }
    }

    /** @deprecated */
    public function reorderTeams(int $excludeId, int $newOrder, ?int $oldOrder = null): void
    {
        $this->shiftOrdersDown($excludeId, $newOrder);
    }

    /** @deprecated */
    public function normalizeOrders(): void
    {
        $this->compactOrders();
    }
}