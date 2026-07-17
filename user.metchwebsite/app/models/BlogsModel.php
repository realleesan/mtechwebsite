<?php
/**
 * BlogsModel.php
 *
 * Model xử lý dữ liệu bảng `blogs`, `blog_categories`, `blog_tags`, `blog_tag_map`.
 */

class BlogsModel
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

    // ----------------------------------------------------------------
    // BLOGS - Danh sách & chi tiết
    // ----------------------------------------------------------------

    /**
     * Lấy danh sách blogs có phân trang, lọc theo category/tag/search.
     * Hỗ trợ many-to-many relationship qua blog_category_map table.
     * Hỗ trợ hierarchy: nếu lọc theo category cha, sẽ lấy blogs của category cha + tất cả con
     *
     * @param int    $page       Trang hiện tại (bắt đầu từ 1)
     * @param int    $perPage    Số bài mỗi trang
     * @param int    $catId      Lọc theo category_id (0 = tất cả)
     * @param string $tagSlug    Lọc theo tag slug ('' = tất cả)
     * @param string $search     Từ khóa tìm kiếm
     * @return array ['blogs' => [...], 'total' => int]
     */
    public function getBlogs($page = 1, $perPage = 6, $catId = 0, $tagSlug = '', $search = '')
    {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];

            // Build WHERE conditions for filtering
            $where = "b.status = 1 AND b.deleted_at IS NULL";

            // Add tag filter
            if (!empty($tagSlug)) {
                $where .= " AND EXISTS (
                    SELECT 1 FROM `blog_tag_map` btm
                    INNER JOIN `blog_tags` bt ON bt.id = btm.tag_id
                    WHERE btm.blog_id = b.id AND bt.slug = ?
                )";
                $params[] = $tagSlug;
            }

            // Add category filter - hỗ trợ hierarchy via blog_category_map
            if ($catId > 0) {
                // Lấy tất cả child categories của category này
                $childCatIds = $this->getChildCategoryIds($catId);
                $childCatIds[] = $catId; // Thêm chính category này
                
                $placeholders = implode(',', array_fill(0, count($childCatIds), '?'));
                $where .= " AND EXISTS (
                    SELECT 1 FROM `blog_category_map` bcm
                    WHERE bcm.blog_id = b.id AND bcm.category_id IN ({$placeholders})
                )";
                $params = array_merge($params, $childCatIds);
            }

            // Add search filter
            if (!empty($search)) {
                $where .= " AND (
                    b.title    LIKE ? OR
                    b.excerpt  LIKE ? OR
                    EXISTS (
                        SELECT 1 FROM `blog_category_map` bcm
                        INNER JOIN `blog_categories` bc ON bc.id = bcm.category_id
                        WHERE bcm.blog_id = b.id AND bc.name LIKE ?
                    ) OR
                    EXISTS (
                        SELECT 1 FROM `blog_tag_map` stm
                        INNER JOIN `blog_tags` st ON st.id = stm.tag_id
                        WHERE stm.blog_id = b.id AND (st.name LIKE ? OR st.slug LIKE ?)
                    )
                )";
                $like = "%{$search}%";
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }

            // Count total blogs (without JOIN duplicates)
            $countSql = "SELECT COUNT(*) FROM `blogs` b WHERE {$where}";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = (int) $countStmt->fetchColumn();

            // Fetch blogs without duplicates - use subquery for pagination
            $sql = "SELECT b.id, b.title, b.slug, b.image, b.excerpt,
                           b.author, b.created_at, b.views, b.category_id,
                           b.position, b.expires_in_days, b.hiring_status,
                           bc.name AS category_name, bc.slug AS category_slug
                    FROM `blogs` b
                    LEFT JOIN `blog_categories` bc ON b.category_id = bc.id
                    WHERE {$where}
                    ORDER BY b.created_at DESC
                    LIMIT ? OFFSET ?";

            $fetchParams = array_merge($params, [$perPage, $offset]);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($fetchParams);
            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Gắn tags cho từng blog
            foreach ($blogs as &$blog) {
                $blog['tags'] = $this->getTagsByBlogId($blog['id']);
                
                // Gắn tất cả categories từ blog_category_map
                $blog['categories'] = $this->getCategoriesByBlogId($blog['id']);
            }

            return ['blogs' => $blogs, 'total' => $total];
        } catch (PDOException $e) {
            error_log('BlogsModel::getBlogs() - ' . $e->getMessage());
            return ['blogs' => [], 'total' => 0];
        }
    }

    /**
     * Lấy tất cả child category IDs của một category (đệ quy)
     * @param int $parentId
     * @return array Mảng các ID của child categories
     */
    private function getChildCategoryIds($parentId)
    {
        try {
            $childIds = [];
            
            // Lấy trực tiếp child (cấp 1)
            $stmt = $this->db->prepare(
                "SELECT id FROM `blog_categories` 
                 WHERE parent_id = ? AND status = 1"
            );
            $stmt->execute([$parentId]);
            $directChildren = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Thêm vào mảng
            $childIds = array_merge($childIds, $directChildren);
            
            // Lặp qua từng child để lấy grandchildren (đệ quy)
            foreach ($directChildren as $childId) {
                $grandChildren = $this->getChildCategoryIds($childId);
                $childIds = array_merge($childIds, $grandChildren);
            }
            
            return $childIds;
        } catch (PDOException $e) {
            error_log('BlogsModel::getChildCategoryIds() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết một blog theo slug.
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getBlogBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT b.*, bc.name AS category_name, bc.slug AS category_slug
                 FROM `blogs` b
                 LEFT JOIN `blog_categories` bc ON b.category_id = bc.id
                 WHERE b.slug = ? AND b.status = 1 AND b.deleted_at IS NULL
                 LIMIT 1"
            );
            $stmt->execute([$slug]);
            $blog = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$blog) return null;

            $blog['tags'] = $this->getTagsByBlogId($blog['id']);
            return $blog;
        } catch (PDOException $e) {
            error_log('BlogsModel::getBlogBySlug() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy blog theo ID.
     */
    public function getBlogById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT b.*, bc.name AS category_name, bc.slug AS category_slug
                 FROM `blogs` b
                 LEFT JOIN `blog_categories` bc ON b.category_id = bc.id
                 WHERE b.id = ? AND b.status = 1 AND b.deleted_at IS NULL
                 LIMIT 1"
            );
            $stmt->execute([$id]);
            $blog = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$blog) return null;
            $blog['tags'] = $this->getTagsByBlogId($blog['id']);
            return $blog;
        } catch (PDOException $e) {
            error_log('BlogsModel::getBlogById() - ' . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------------
    // CATEGORIES
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả blog categories kèm số lượng bài viết.
     * Dùng many-to-many relationship via blog_category_map table.
     */
    public function getAllBlogCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT bc.id, bc.name, bc.slug,
                        COUNT(DISTINCT bcm.blog_id) AS post_count
                 FROM `blog_categories` bc
                 LEFT JOIN `blog_category_map` bcm ON bcm.category_id = bc.id
                 LEFT JOIN `blogs` b ON b.id = bcm.blog_id AND b.status = 1 AND b.deleted_at IS NULL
                 WHERE bc.status = 1
                 GROUP BY bc.id, bc.name, bc.slug
                 ORDER BY bc.sort_order ASC, bc.id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getAllBlogCategories() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy blog category theo slug.
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getCategoryBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug
                 FROM `blog_categories`
                 WHERE slug = ? AND status = 1
                 LIMIT 1"
            );
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getCategoryBySlug() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy danh sách categories dạng hierarchical (phân cấp) với level info.
     * Lọc theo show_in_menu = 1 (hiển thị trong menu)
     * Dùng cho hiển thị dropdown/list categories có collapsible.
     *
     * @return array Mảng categories với level, parent_id, icon prefix
     */
    public function getCategoriesHierarchy()
    {
        try {
            // Lấy tất cả categories active và show_in_menu=1, sắp xếp theo sort_order
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, parent_id, level, sort_order
                 FROM `blog_categories`
                 WHERE status = 1 AND show_in_menu = 1
                 ORDER BY parent_id IS NULL DESC, parent_id ASC, sort_order ASC, id ASC"
            );
            $stmt->execute();
            $allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Build hierarchical array with visual hierarchy info
            return $this->buildCategoryHierarchyWithLevel($allCategories);
        } catch (PDOException $e) {
            error_log('BlogsModel::getCategoriesHierarchy() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Build category hierarchy với level depth information.
     * Thêm 'level' (depth) và 'icon' prefix cho mỗi category.
     *
     * @param array $allCategories Flat array của tất cả categories
     * @return array Hierarchical array với visual level info
     */
    private function buildCategoryHierarchyWithLevel($allCategories)
    {
        $result = [];
        $indexed = [];

        // Index tất cả categories by id
        foreach ($allCategories as $cat) {
            $indexed[$cat['id']] = $cat;
            $indexed[$cat['id']]['children'] = [];
        }

        // Build parent-child relationships
        foreach ($indexed as $id => &$cat) {
            if ($cat['parent_id'] && isset($indexed[$cat['parent_id']])) {
                $indexed[$cat['parent_id']]['children'][] = &$cat;
            } else {
                // Root category
                $result[] = &$cat;
            }
        }

        return $result;
    }

    /**
     * Flatten hierarchical categories để render dễ hơn.
     * Thêm 'depth' (level) và 'prefix' (visual icon) cho mỗi category.
     *
     * @param array $hierarchical Hierarchical categories array
     * @param int $depth Depth level (bắt đầu từ 0)
     * @return array Flat array với depth và prefix info
     */
    public function flattenCategoriesHierarchy($hierarchical, $depth = 0)
    {
        $flat = [];

        foreach ($hierarchical as $cat) {
            // Tính icon prefix dựa trên depth
            $prefix = '';
            if ($depth > 0) {
                // Depth 1: "→ "
                // Depth 2+: "└─ "
                $prefix = ($depth === 1) ? '→ ' : '└─ ';
            }

            // Add indent info: (depth * 20px)
            $indent = $depth * 20;

            // Thêm category vào flat array với metadata
            $flat[] = [
                'id'       => $cat['id'],
                'name'     => $cat['name'],
                'slug'     => $cat['slug'],
                'parent_id' => $cat['parent_id'] ?? null,
                'depth'    => $depth,
                'indent'   => $indent,
                'prefix'   => $prefix,
                'post_count' => $cat['post_count'] ?? 0
            ];

            // Recursively flatten children
            if (!empty($cat['children'])) {
                $childFlat = $this->flattenCategoriesHierarchy($cat['children'], $depth + 1);
                $flat = array_merge($flat, $childFlat);
            }
        }

        return $flat;
    }

    // ----------------------------------------------------------------
    // TAGS
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả tags kèm số lượng bài viết còn active (chưa xóa).
     * Tags của bài đã xóa/soft-delete sẽ không được đếm.
     * Tags có post_count = 0 (toàn bộ bài đã xóa) sẽ bị ẩn.
     */
    public function getAllTags()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT bt.id, bt.name, bt.slug,
                        COUNT(b.id) AS post_count
                 FROM `blog_tags` bt
                 INNER JOIN `blog_tag_map` btm ON btm.tag_id = bt.id
                 INNER JOIN `blogs` b ON b.id = btm.blog_id
                                      AND b.status = 1
                                      AND b.deleted_at IS NULL
                 GROUP BY bt.id, bt.name, bt.slug
                 HAVING post_count > 0
                 ORDER BY post_count DESC, bt.name ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getAllTags() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả categories của một blog theo blog_id (many-to-many via blog_category_map).
     * @param int $blogId Blog ID
     * @return array Danh sách categories
     */
    public function getCategoriesByBlogId($blogId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT bc.id, bc.name, bc.slug, bc.parent_id, bcm.sort_order
                 FROM `blog_categories` bc
                 INNER JOIN `blog_category_map` bcm ON bc.id = bcm.category_id
                 WHERE bcm.blog_id = ?
                 ORDER BY bcm.sort_order ASC, bc.sort_order ASC"
            );
            $stmt->execute([$blogId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getCategoriesByBlogId() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tags của một blog theo blog_id.
     */
    public function getTagsByBlogId($blogId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT bt.id, bt.name, bt.slug
                 FROM `blog_tags` bt
                 INNER JOIN `blog_tag_map` btm ON btm.tag_id = bt.id
                 WHERE btm.blog_id = ?
                 ORDER BY bt.name ASC"
            );
            $stmt->execute([$blogId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getTagsByBlogId() - ' . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------------
    // RECENT NEWS (sidebar)
    // ----------------------------------------------------------------

    /**
     * Lấy N bài viết mới nhất cho sidebar Recent News.
     *
     * @param int $limit
     * @return array
     */
    public function getRecentBlogs($limit = 4)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, title, slug, image, created_at
                 FROM `blogs`
                 WHERE status = 1 AND deleted_at IS NULL
                 ORDER BY created_at DESC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getRecentBlogs() - ' . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------------
    // HOME PAGE - Latest News (tối đa 3 bài)
    // ----------------------------------------------------------------

    /**
     * Lấy N bài viết mới nhất cho section Latest News trên trang home.
     *
     * @param int $limit Mặc định 3
     * @return array
     */
    public function getHomeBlogs($limit = 3)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT b.id, b.title, b.slug, b.image, b.excerpt,
                        b.author, b.created_at
                 FROM `blogs` b
                 WHERE b.status = 1 AND b.deleted_at IS NULL
                 ORDER BY b.created_at DESC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getHomeBlogs() - ' . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------------
    // VIEWS counter
    // ----------------------------------------------------------------

    /**
     * Tăng lượt xem cho một blog.
     */
    public function incrementViews($blogId)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `blogs` SET views = views + 1 WHERE id = ?"
            );
            $stmt->execute([$blogId]);
        } catch (PDOException $e) {
            error_log('BlogsModel::incrementViews() - ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------
    // BLOG DETAILS - Chi tiết bài viết
    // ----------------------------------------------------------------

    /**
     * Lấy chi tiết đầy đủ của một blog bao gồm nội dung mở rộng.
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getBlogDetailsBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT b.*, 
                        bc.name AS category_name, bc.slug AS category_slug,
                        bd.full_content, bd.meta_title, bd.meta_description, 
                        bd.meta_keywords, bd.reading_time
                 FROM `blogs` b
                 LEFT JOIN `blog_categories` bc ON b.category_id = bc.id
                 LEFT JOIN `blog_details` bd ON bd.blog_id = b.id
                 WHERE b.slug = ? AND b.status = 1 AND b.deleted_at IS NULL
                 LIMIT 1"
            );
            $stmt->execute([$slug]);
            $blog = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$blog) return null;

            $blog['tags'] = $this->getTagsByBlogId($blog['id']);
            return $blog;
        } catch (PDOException $e) {
            error_log('BlogsModel::getBlogDetailsBySlug() - ' . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------------
    // MENU - Blog Categories hiển thị trong dropdown menu header
    // ----------------------------------------------------------------

    /**
     * Lấy blog categories hiển thị trong dropdown menu header (show_in_menu=1).
     * Chỉ lấy các category active.
     *
     * @param int $limit Số lượng tối đa (mặc định 10)
     * @return array Mảng blog categories cho menu dropdown
     */
    public function getMenuBlogCategories($limit = 50)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, parent_id, level, sort_order
                 FROM `blog_categories`
                 WHERE status = 1 AND show_in_menu = 1
                 ORDER BY parent_id IS NULL DESC, parent_id ASC, sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Build hierarchy
            return $this->buildCategoryHierarchy($categories);
        } catch (PDOException $e) {
            error_log('BlogsModel::getMenuBlogCategories() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NEW: Lấy blog categories dạng FLAT ARRAY cho FilterConfigService
     * Trả về mảng phẳng (không phải tree) - tương tự CategoriesModel::getAllCategories()
     * Dùng để đảm bảo cách xử lý giống như module "Lĩnh vực"
     * 
     * @param int $limit Số lượng tối đa (mặc định 50)
     * @return array Mảng flat blog categories
     */
    public function getAllBlogCategoriesFlat($limit = 50)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, parent_id, level, sort_order, status, show_in_menu
                 FROM `blog_categories`
                 WHERE status = 1 AND show_in_menu = 1
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getAllBlogCategoriesFlat() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Build category hierarchy from flat array
     */
    private function buildCategoryHierarchy($categories)
    {
        $hierarchy = [];
        $indexed = [];
        
        // First, index all categories
        foreach ($categories as $cat) {
            $indexed[$cat['id']] = $cat;
            $indexed[$cat['id']]['children'] = [];
        }
        
        // Then build parent-child relationships
        foreach ($indexed as $id => &$cat) {
            if ($cat['parent_id'] && isset($indexed[$cat['parent_id']])) {
                $indexed[$cat['parent_id']]['children'][] = &$cat;
            } else {
                $hierarchy[] = &$cat;
            }
        }
        
        return $hierarchy;
    }

    // ----------------------------------------------------------------
    // HIRING - Tuyển dụng (category_id = 7)
    // ----------------------------------------------------------------

    /**
     * Kiểm tra xem blog có phải là tin tuyển dụng không (category_id = 7).
     *
     * @param int $blogId
     * @return bool
     */
    public function isHiringBlog($blogId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT category_id FROM `blogs` WHERE id = ? AND status = 1 LIMIT 1"
            );
            $stmt->execute([$blogId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && $result['category_id'] == 7;
        } catch (PDOException $e) {
            error_log('BlogsModel::isHiringBlog() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra xem tin tuyển dụng đã hết hạn chưa.
     * Hết hạn khi: expires_in_days không null và (created_at + expires_in_days) < now
     *
     * @param array $blog Mảng chứa created_at, expires_in_days
     * @return bool
     */
    public function isExpired($blog)
    {
        if (empty($blog['expires_in_days']) || empty($blog['created_at'])) {
            return false; // Không có ngày hết hạn = không bao giờ hết hạn
        }

        $createdAt = strtotime($blog['created_at']);
        $expiresAt = strtotime($blog['created_at'] . ' + ' . $blog['expires_in_days'] . ' days');

        return time() > $expiresAt;
    }

    /**
     * Lấy số ngày còn lại để ứng tuyển.
     * Trả về int >= 0 (số ngày còn lại) hoặc null (không giới hạn thời gian).
     *
     * @param array $blog Mảng chứa created_at, expires_in_days
     * @return int|null
     */
    public function getDaysRemaining($blog)
    {
        if (empty($blog['expires_in_days']) || empty($blog['created_at'])) {
            return null; // Không giới hạn thời gian
        }

        $createdAt = strtotime($blog['created_at']);
        $expiresAt = strtotime($blog['created_at'] . ' + ' . $blog['expires_in_days'] . ' days');
        $remaining = ceil(($expiresAt - time()) / 86400); // 86400 = số giây trong 1 ngày

        return max(0, (int) $remaining);
    }

    /**
     * Kiểm tra xem tin tuyển dụng có đang mở để nhận CV không.
     * Điều kiện: hiring_status = 1 và chưa hết hạn.
     *
     * @param array $blog Mảng chứa hiring_status, created_at, expires_in_days
     * @return bool
     */
    public function isHiringOpen($blog)
    {
        // hiring_status = 0 (ngừng tuyển) -> không mở
        if (empty($blog['hiring_status']) || $blog['hiring_status'] != 1) {
            return false;
        }

        // Hết hạn -> không mở
        return !$this->isExpired($blog);
    }

    /**
     * Lấy tất cả vị trí tuyển dụng đang mở cho dropdown.
     * Chỉ lấy các blog tuyển dụng (cat=7) đang active và chưa hết hạn.
     *
     * @return array Mảng các vị trí tuyển dụng
     */
    public function getAllHiringPositions()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, title, position, slug
                 FROM `blogs`
                 WHERE category_id = 7 
                   AND status = 1 
                   AND hiring_status = 1
                   AND (expires_in_days IS NULL OR 
                        DATE_ADD(created_at, INTERVAL expires_in_days DAY) > NOW())
                 ORDER BY created_at DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getAllHiringPositions() - ' . $e->getMessage());
            return [];
        }
    }
}
