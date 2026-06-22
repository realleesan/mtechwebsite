<?php
/**
 * FilterConfigService.php
 * 
 * Service quản lý cấu hình hiển thị và sắp xếp các mục trên Mega Menu (Dịch vụ, Dự án, Tin tức).
 */

class FilterConfigService
{
    /** @var PDO */
    private $db;

    private $table = 'filter_config';

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
     * Lấy cấu hình bộ lọc theo loại tiêu chí
     *
     * @param string $criteriaType ('services', 'project_categories', 'blog_categories')
     * @return array
     */
    public function getConfig(string $criteriaType): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, criteria_type, item_id, parent_id, sort_order, is_enabled
                 FROM `{$this->table}`
                 WHERE criteria_type = ?
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute([$criteriaType]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('FilterConfigService::getConfig() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lưu/Cập nhật toàn bộ cấu hình sắp xếp và hiển thị cho một loại tiêu chí
     * Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu.
     *
     * @param string $criteriaType
     * @param array $items Mảng các mục dạng: [['item_id' => X, 'parent_id' => Y, 'sort_order' => Z, 'is_enabled' => 0|1], ...]
     * @return bool
     */
    public function saveConfig(string $criteriaType, array $items): bool
    {
        try {
            $this->db->beginTransaction();

            // Xóa cấu hình cũ của loại tiêu chí này
            $stmtDel = $this->db->prepare("DELETE FROM `{$this->table}` WHERE criteria_type = ?");
            $stmtDel->execute([$criteriaType]);

            // Thêm cấu hình mới
            $stmtIns = $this->db->prepare(
                "INSERT INTO `{$this->table}` (criteria_type, item_id, parent_id, sort_order, is_enabled)
                 VALUES (?, ?, ?, ?, ?)"
            );

            foreach ($items as $item) {
                $itemId = (int)$item['item_id'];
                $parentId = !empty($item['parent_id']) ? (int)$item['parent_id'] : null;
                $sortOrder = (int)($item['sort_order'] ?? 0);
                $isEnabled = isset($item['is_enabled']) ? (int)$item['is_enabled'] : 1;

                $stmtIns->execute([
                    $criteriaType,
                    $itemId,
                    $parentId,
                    $sortOrder,
                    $isEnabled
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('FilterConfigService::saveConfig() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Dựng cấu trúc cây dữ liệu đã được áp dụng bộ lọc (FilterConfig) cho Menu
     *
     * @param string $criteriaType
     * @param array $originalItems Mảng phẳng các item gốc từ DB (phải chứa id, name, slug, parent_id)
     * @return array Mảng cây lồng nhau sau khi sắp xếp và lọc theo cấu hình
     */
    public function getFilteredMenuTree(string $criteriaType, array $originalItems): array
    {
        // Lấy cấu hình hiện tại
        $config = $this->getConfig($criteriaType);
        
        // Tạo map config theo item_id để tra cứu nhanh
        $configMap = [];
        foreach ($config as $c) {
            $configMap[(int)$c['item_id']] = $c;
        }

        // Tạo map nguyên bản theo ID
        $originalMap = [];
        foreach ($originalItems as $item) {
            $originalMap[(int)$item['id']] = $item;
        }

        $processedItems = [];

        foreach ($originalItems as $item) {
            $itemId = (int)$item['id'];
            
            // Nếu có cấu hình, ghi đè parent_id, sort_order, is_enabled
            if (isset($configMap[$itemId])) {
                $c = $configMap[$itemId];
                
                // Nếu bị ẩn, bỏ qua không hiển thị trong menu
                if ((int)$c['is_enabled'] === 0) {
                    continue;
                }
                
                $item['parent_id'] = $c['parent_id'] !== null ? (int)$c['parent_id'] : null;
                $item['sort_order'] = (int)$c['sort_order'];
            } else {
                // Nếu chưa cấu hình, mặc định hiển thị theo gốc
                $item['sort_order'] = isset($item['sort_order']) ? (int)$item['sort_order'] : 0;
            }

            $processedItems[] = $item;
        }

        // Sắp xếp các mục theo sort_order tăng dần
        usort($processedItems, function ($a, $b) {
            if ($a['sort_order'] === $b['sort_order']) {
                return $a['id'] <=> $b['id'];
            }
            return $a['sort_order'] <=> $b['sort_order'];
        });

        // Dựng cây phân cấp
        return $this->buildTree($processedItems);
    }

    /**
     * Hàm dựng cây đệ quy
     */
    private function buildTree(array $elements, $parentId = null, $maxDepth = 10, $currentDepth = 0, $visitedIds = []): array
    {
        if ($currentDepth >= $maxDepth) return [];
        $branch = [];
        foreach ($elements as $element) {
            $elementParentId = empty($element['parent_id']) ? null : (int)$element['parent_id'];
            $checkParentId = empty($parentId) ? null : (int)$parentId;
            
            if ($elementParentId === $checkParentId) {
                if (in_array($element['id'], $visitedIds)) {
                    continue;
                }
                $newVisitedIds = array_merge($visitedIds, [$element['id']]);
                $children = $this->buildTree($elements, $element['id'], $maxDepth, $currentDepth + 1, $newVisitedIds);
                $element['children'] = $children ?: [];
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
