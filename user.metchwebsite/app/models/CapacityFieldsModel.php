<?php
/**
 * CapacityFieldsModel.php (user side)
 * Lấy dữ liệu chứng chỉ năng lực để hiển thị bảng ra ngoài website.
 */

class CapacityFieldsModel
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../../core/database.php';
        $this->db = getDBConnection();
    }

    /**
     * Lấy tất cả lĩnh vực cha (active) kèm danh sách mục con (active).
     */
    public function getAllWithItems(): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, sort_order, name
                 FROM capacity_fields
                 WHERE status = 1 AND deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($fields as &$field) {
                $items = $this->db->prepare(
                    "SELECT name, `rank`, sort_order
                     FROM capacity_field_items
                     WHERE field_id = ? AND status = 1 AND deleted_at IS NULL
                     ORDER BY sort_order ASC, id ASC"
                );
                $items->execute([$field['id']]);
                $field['items'] = $items->fetchAll(PDO::FETCH_ASSOC);
            }

            return $fields;
        } catch (PDOException $e) {
            error_log('CapacityFieldsModel(user)::getAllWithItems() - ' . $e->getMessage());
            return [];
        }
    }
}
