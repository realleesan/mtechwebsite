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
                "SELECT *
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

    /**
     * Lấy tất cả awards (kể cả inactive) cho admin.
     */
    public function getAll()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT *
                 FROM `{$this->table}`
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('AwardsModel::getAll() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy award theo ID.
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
            error_log('AwardsModel::getById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Thêm award mới.
     */
    public function create(array $data): int|false
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}` (name, certificate, image, status, sort_order)
                 VALUES (:name, :certificate, :image, :status, :sort_order)"
            );
            $stmt->execute([
                ':name'        => $data['name'],
                ':certificate' => $data['certificate'] ?? '',
                ':image'       => $data['image']       ?? '',
                ':status'      => $data['status']       ?? 1,
                ':sort_order'  => $data['sort_order']   ?? 0,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('AwardsModel::create() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật award.
     */
    public function update(int $id, array $data): bool
    {
        try {
            $fields = [
                'name = :name',
                'certificate = :certificate',
                'status = :status',
                'sort_order = :sort_order',
            ];
            $params = [
                ':id'          => $id,
                ':name'        => $data['name'],
                ':certificate' => $data['certificate'] ?? '',
                ':status'      => $data['status']       ?? 1,
                ':sort_order'  => $data['sort_order']   ?? 0,
            ];

            if (isset($data['image']) && $data['image'] !== '') {
                $fields[]         = 'image = :image';
                $params[':image'] = $data['image'];
            } elseif (isset($data['image']) && $data['image'] === '') {
                // Xóa ảnh: set image = '' trong DB
                $fields[]         = 'image = :image';
                $params[':image'] = '';
            }

            $sql  = "UPDATE `{$this->table}` SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log('AwardsModel::update() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa award.
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('AwardsModel::delete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Điều chỉnh thứ tự tự động khi thêm/sửa award.
     * Nếu sort_order trùng với award khác, tự động lùi các award sau đó.
     *
     * @param int      $awardId      ID của award đang được thêm/sửa
     * @param int      $newSortOrder Thứ tự mới
     * @param int|null $oldSortOrder Thứ tự cũ (null nếu thêm mới)
     */
    public function reorderAwards(int $awardId, int $newSortOrder, ?int $oldSortOrder = null): bool
    {
        try {
            if ($oldSortOrder !== null && $oldSortOrder === $newSortOrder) {
                return true;
            }

            $stmt = $this->db->prepare(
                "SELECT id, sort_order FROM `{$this->table}` WHERE id != ? ORDER BY sort_order ASC"
            );
            $stmt->execute([$awardId]);
            $others = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $newOrders = [];
            $inserted  = false;

            foreach ($others as $item) {
                if (!$inserted && $item['sort_order'] >= $newSortOrder) {
                    $newOrders[$awardId] = $newSortOrder;
                    $inserted = true;
                }
                $newOrders[$item['id']] = $inserted
                    ? $item['sort_order'] + 1
                    : $item['sort_order'];
            }

            if (!$inserted) {
                $newOrders[$awardId] = $newSortOrder;
            }

            foreach ($newOrders as $id => $order) {
                $upd = $this->db->prepare("UPDATE `{$this->table}` SET sort_order = ? WHERE id = ?");
                $upd->execute([$order, $id]);
            }

            return true;
        } catch (PDOException $e) {
            error_log('AwardsModel::reorderAwards() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalize thứ tự của tất cả awards từ 1 đến n.
     */
    public function normalizeOrders(): bool
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id FROM `{$this->table}` ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            $awards = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $order = 1;
            foreach ($awards as $award) {
                $upd = $this->db->prepare("UPDATE `{$this->table}` SET sort_order = ? WHERE id = ?");
                $upd->execute([$order, $award['id']]);
                $order++;
            }

            return true;
        } catch (PDOException $e) {
            error_log('AwardsModel::normalizeOrders() - ' . $e->getMessage());
            return false;
        }
    }
}
