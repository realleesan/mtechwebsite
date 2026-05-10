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

    /**
     * Điều chỉnh thứ tự tự động khi thêm/sửa logo.
     * Nếu sort_order trùng với logo khác, tự động lùi các logo sau đó.
     * Sau đó normalize lại tất cả thứ tự từ 1 đến n.
     * 
     * Ví dụ: logo 1,2,3 → sửa logo 3 thành 1 → kết quả: 3,1,2
     * 
     * @param int $logoId ID của logo đang được thêm/sửa
     * @param int $newSortOrder Thứ tự mới
     * @param int|null $oldSortOrder Thứ tự cũ (null nếu thêm mới)
     */
    public function reorderLogos(int $logoId, int $newSortOrder, ?int $oldSortOrder = null): bool
    {
        try {
            // Nếu thứ tự không thay đổi, không cần làm gì
            if ($oldSortOrder !== null && $oldSortOrder === $newSortOrder) {
                return true;
            }

            // Lấy tất cả logo khác (không phải logo đang sửa)
            $stmt = $this->db->prepare(
                "SELECT id, sort_order FROM `{$this->table}` WHERE id != ? ORDER BY sort_order ASC"
            );
            $stmt->execute([$logoId]);
            $otherLogos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Tạo mảng thứ tự mới
            $newOrders = [];
            $inserted = false;

            foreach ($otherLogos as $logo) {
                // Nếu chưa insert logo mới và thứ tự hiện tại >= thứ tự mới
                if (!$inserted && $logo['sort_order'] >= $newSortOrder) {
                    $newOrders[$logoId] = $newSortOrder;
                    $inserted = true;
                }

                // Nếu đã insert, tăng thứ tự của logo khác lên 1
                if ($inserted) {
                    $newOrders[$logo['id']] = $logo['sort_order'] + 1;
                } else {
                    $newOrders[$logo['id']] = $logo['sort_order'];
                }
            }

            // Nếu chưa insert (logo mới có thứ tự cao nhất)
            if (!$inserted) {
                $newOrders[$logoId] = $newSortOrder;
            }

            // Cập nhật tất cả thứ tự
            foreach ($newOrders as $id => $order) {
                $updateStmt = $this->db->prepare(
                    "UPDATE `{$this->table}` SET sort_order = ? WHERE id = ?"
                );
                $updateStmt->execute([$order, $id]);
            }

            return true;
        } catch (PDOException $e) {
            error_log('ClientLogosModel::reorderLogos() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalize thứ tự của tất cả logo từ 1 đến n.
     * Gọi sau khi thêm/sửa/xóa để đảm bảo thứ tự luôn liên tục.
     */
    public function normalizeOrders(): bool
    {
        try {
            // Lấy tất cả logo sắp xếp theo sort_order hiện tại
            $stmt = $this->db->prepare(
                "SELECT id FROM `{$this->table}` ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            $logos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cập nhật thứ tự từ 1 đến n
            $newOrder = 1;
            foreach ($logos as $logo) {
                $updateStmt = $this->db->prepare(
                    "UPDATE `{$this->table}` SET sort_order = ? WHERE id = ?"
                );
                $updateStmt->execute([$newOrder, $logo['id']]);
                $newOrder++;
            }

            return true;
        } catch (PDOException $e) {
            error_log('ClientLogosModel::normalizeOrders() - ' . $e->getMessage());
            return false;
        }
    }
}
