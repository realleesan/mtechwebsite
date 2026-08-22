<?php
/**
 * CapacityFieldsModel.php
 * Model cho bảng chứng chỉ năng lực hoạt động xây dựng.
 * Quản lý 2 bảng: capacity_fields (cha) và capacity_field_items (con).
 */

class CapacityFieldsModel
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

    // ============================================================
    // CAPACITY FIELDS (lĩnh vực cha)
    // ============================================================

    /**
     * Lấy tất cả lĩnh vực cha kèm danh sách con (active).
     * Dùng cho trang user hiển thị bảng.
     */
    public function getAllWithItems(): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT cf.id, cf.sort_order, cf.name
                 FROM capacity_fields cf
                 WHERE cf.status = 1 AND cf.deleted_at IS NULL
                 ORDER BY cf.sort_order ASC, cf.id ASC"
            );
            $stmt->execute();
            $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($fields as &$field) {
                $field['items'] = $this->getItemsByField($field['id'], true);
            }

            return $fields;
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::getAllWithItems() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả lĩnh vực cha cho admin (bao gồm inactive, không bao gồm deleted).
     */
    public function getAllFields(): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM capacity_fields
                 WHERE deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::getAllFields() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy lĩnh vực cha theo ID (kể cả deleted để edit/restore).
     */
    public function getFieldById(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM capacity_fields WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::getFieldById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo lĩnh vực cha mới.
     */
    public function createField(array $data): int|false
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO capacity_fields (sort_order, name, status)
                 VALUES (:sort_order, :name, :status)"
            );
            $stmt->execute([
                ':sort_order' => (int)($data['sort_order'] ?? 1),
                ':name'       => $data['name'],
                ':status'     => (int)($data['status'] ?? 1),
            ]);
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::createField() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật lĩnh vực cha.
     */
    public function updateField(int $id, array $data): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE capacity_fields
                 SET sort_order = :sort_order, name = :name, status = :status
                 WHERE id = :id"
            );
            $stmt->execute([
                ':sort_order' => (int)($data['sort_order'] ?? 1),
                ':name'       => $data['name'],
                ':status'     => (int)($data['status'] ?? 1),
                ':id'         => $id,
            ]);
            return true;
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::updateField() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa mềm lĩnh vực cha (và tự động cascade xóa items qua FK, hoặc soft-delete riêng).
     */
    public function deleteField(int $id): bool
    {
        try {
            $this->db->prepare(
                "UPDATE capacity_field_items SET deleted_at = CURRENT_TIMESTAMP WHERE field_id = ? AND deleted_at IS NULL"
            )->execute([$id]);

            $stmt = $this->db->prepare(
                "UPDATE capacity_fields SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::deleteField() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa vĩnh viễn lĩnh vực cha (cascade xóa items).
     */
    public function hardDeleteField(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM capacity_fields WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::hardDeleteField() - ' . $e->getMessage());
            return false;
        }
    }

    // ============================================================
    // CAPACITY FIELD ITEMS (lĩnh vực con)
    // ============================================================

    /**
     * Lấy danh sách items theo field_id.
     * @param bool $activeOnly  true = chỉ lấy active, false = tất cả chưa bị xóa mềm
     */
    public function getItemsByField(int $fieldId, bool $activeOnly = false): array
    {
        try {
            $sql = "SELECT * FROM capacity_field_items
                    WHERE field_id = ? AND deleted_at IS NULL";
            if ($activeOnly) {
                $sql .= " AND status = 1";
            }
            $sql .= " ORDER BY sort_order ASC, id ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$fieldId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::getItemsByField() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy item theo ID.
     */
    public function getItemById(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM capacity_field_items WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::getItemById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo item mới.
     */
    public function createItem(array $data): int|false
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO capacity_field_items (field_id, name, `rank`, sort_order, status)
                 VALUES (:field_id, :name, :rank, :sort_order, :status)"
            );
            $stmt->execute([
                ':field_id'   => (int)$data['field_id'],
                ':name'       => $data['name'],
                ':rank'       => $data['rank']       ?? '',
                ':sort_order' => (int)($data['sort_order'] ?? 1),
                ':status'     => (int)($data['status']     ?? 1),
            ]);
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::createItem() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật item.
     */
    public function updateItem(int $id, array $data): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE capacity_field_items
                 SET name = :name, `rank` = :rank, sort_order = :sort_order, status = :status
                 WHERE id = :id"
            );
            $stmt->execute([
                ':name'       => $data['name'],
                ':rank'       => $data['rank']       ?? '',
                ':sort_order' => (int)($data['sort_order'] ?? 1),
                ':status'     => (int)($data['status']     ?? 1),
                ':id'         => $id,
            ]);
            return true;
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::updateItem() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa mềm item.
     */
    public function deleteItem(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE capacity_field_items SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::deleteItem() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa vĩnh viễn item.
     */
    public function hardDeleteItem(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM capacity_field_items WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel::hardDeleteItem() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy số thứ tự tiếp theo cho lĩnh vực cha mới.
     */
    public function getNextFieldOrder(): int
    {
        try {
            $stmt = $this->db->query(
                "SELECT MAX(sort_order) FROM capacity_fields WHERE deleted_at IS NULL"
            );
            return (int)$stmt->fetchColumn() + 1;
        } catch (PDOException $e) {
            return 1;
        }
    }

    /**
     * Lấy số thứ tự tiếp theo cho item mới trong một lĩnh vực cha.
     */
    public function getNextItemOrder(int $fieldId): int
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT MAX(sort_order) FROM capacity_field_items WHERE field_id = ? AND deleted_at IS NULL"
            );
            $stmt->execute([$fieldId]);
            return (int)$stmt->fetchColumn() + 1;
        } catch (PDOException $e) {
            return 1;
        }
    }
}
