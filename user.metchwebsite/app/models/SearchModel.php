<?php
/**
 * SearchModel.php
 *
 * Model tìm kiếm toàn site: blogs, categories (services), projects.
 */

class SearchModel
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

    /**
     * Tìm kiếm toàn site với phân trang, sort theo created_at DESC.
     *
     * @param string $keyword   Từ khóa tìm kiếm
     * @param int    $page      Trang hiện tại (bắt đầu từ 1)
     * @param int    $perPage   Số kết quả mỗi trang
     * @param string $type      Lọc theo loại: 'blog' | 'service' | 'project' | '' (tất cả)
     * @return array ['results' => [...], 'total' => int]
     */
    public function search($keyword, $page = 1, $perPage = 10, $type = '')
    {
        try {
            $like    = '%' . $keyword . '%';
            $offset  = ($page - 1) * $perPage;
            $parts   = [];
            $params  = [];
            $countParams = [];

            // --- Nhánh blogs ---
            if ($type === '' || $type === 'blog') {
                $parts[] = "SELECT 'blog' AS type, b.id, b.title, b.slug, b.image,
                                   b.excerpt AS excerpt, b.created_at,
                                   b.category_id, b.expires_in_days, b.hiring_status
                            FROM `blogs` b
                            WHERE b.status = 1 AND b.deleted_at IS NULL
                              AND (b.title LIKE ? OR b.excerpt LIKE ?)";
                $params[]      = $like;
                $params[]      = $like;
                $countParams[] = $like;
                $countParams[] = $like;
            }

            // --- Nhánh categories (services) ---
            if ($type === '' || $type === 'service') {
                $parts[] = "SELECT 'service' AS type, c.id, c.name AS title, c.slug, c.image,
                                   c.description AS excerpt, c.created_at,
                                   NULL AS category_id, NULL AS expires_in_days, NULL AS hiring_status
                            FROM `categories` c
                            WHERE c.status = 1
                              AND c.deleted_at IS NULL
                              AND (c.name LIKE ? OR c.description LIKE ?)";
                $params[]      = $like;
                $params[]      = $like;
                $countParams[] = $like;
                $countParams[] = $like;
            }

            // --- Nhánh projects ---
            if ($type === '' || $type === 'project') {
                $parts[] = "SELECT 'project' AS type, p.id, p.title, p.slug, p.image,
                                   p.description AS excerpt, p.created_at,
                                   NULL AS category_id, NULL AS expires_in_days, NULL AS hiring_status
                            FROM `projects` p
                            WHERE p.status = 1
                              AND p.deleted_at IS NULL
                              AND (p.title LIKE ? OR p.description LIKE ?)";
                $params[]      = $like;
                $params[]      = $like;
                $countParams[] = $like;
                $countParams[] = $like;
            }

            // Không có nhánh nào phù hợp
            if (empty($parts)) {
                return ['results' => [], 'total' => 0];
            }

            $unionSql = implode(' UNION ALL ', $parts);

            // Count total
            $countSql  = "SELECT COUNT(*) FROM ({$unionSql}) AS search_union";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($countParams);
            $total = (int) $countStmt->fetchColumn();

            // Fetch kết quả có phân trang, sort mới nhất lên đầu
            $sql  = "SELECT * FROM ({$unionSql}) AS search_union
                     ORDER BY created_at DESC
                     LIMIT ? OFFSET ?";
            $fetchParams = array_merge($params, [$perPage, $offset]);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($fetchParams);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['results' => $results, 'total' => $total];

        } catch (PDOException $e) {
            error_log('SearchModel::search() - ' . $e->getMessage());
            return ['results' => [], 'total' => 0];
        }
    }

    /**
     * Trả về URL detail theo loại kết quả.
     * Bài tuyển dụng (blog category_id=7) dùng /chi-tiet-{slug}
     */
    public static function getDetailUrl($type, $slug, $categoryId = null)
    {
        switch ($type) {
            case 'blog':
                // Tuyển dụng dùng /chi-tiet-{slug}
                if ($categoryId == 7) {
                    return '/chi-tiet-' . urlencode($slug);
                }
                return '/chi-tiet-tin-tuc-' . urlencode($slug);
            case 'service': return '/chi-tiet-linh-vuc-' . urlencode($slug);
            case 'project': return '/chi-tiet-du-an-'   . urlencode($slug);
            default:        return '#';
        }
    }

    /**
     * Trả về label hiển thị theo loại.
     */
    public static function getTypeLabel($type)
    {
        switch ($type) {
            case 'blog':    return 'Tin tức';
            case 'service': return 'Lĩnh vực';
            case 'project': return 'Dự án';
            default:        return '';
        }
    }
}
