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

            $baseJoin = "FROM `blogs` b
                         LEFT JOIN `blog_categories` bc ON b.category_id = bc.id";

            // Add tag join if filtering by tag
            if (!empty($tagSlug)) {
                $baseJoin .= " INNER JOIN `blog_tag_map` btm ON btm.blog_id = b.id
                               INNER JOIN `blog_tags` bt ON bt.id = btm.tag_id";
            }

            $where = "WHERE b.status = 1 AND b.deleted_at IS NULL";

            // Add tag filter
            if (!empty($tagSlug)) {
                $where .= " AND bt.slug = ?";
                $params[] = $tagSlug;
            }

            // Add category filter
            if ($catId > 0) {
                $where .= " AND b.category_id = ?";
                $params[] = $catId;
            }

            // Add search filter
            if (!empty($search)) {
                // Tìm theo title, excerpt, category name, và tag name
                $where .= " AND (
                    b.title    LIKE ? OR
                    b.excerpt  LIKE ? OR
                    bc.name    LIKE ? OR
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

            // Count total
            $countSql = "SELECT COUNT(DISTINCT b.id) {$baseJoin} {$where}";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = (int) $countStmt->fetchColumn();

            // Fetch blogs
            $sql = "SELECT DISTINCT b.id, b.title, b.slug, b.image, b.excerpt,
                           b.author, b.created_at, b.views, b.category_id,
                           b.position, b.expires_in_days, b.hiring_status,
                           bc.name AS category_name, bc.slug AS category_slug
                    {$baseJoin}
                    {$where}
                    ORDER BY b.created_at DESC
                    LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Gắn tags cho từng blog
            foreach ($blogs as &$blog) {
                $blog['tags'] = $this->getTagsByBlogId($blog['id']);
            }

            return ['blogs' => $blogs, 'total' => $total];
        } catch (PDOException $e) {
            error_log('BlogsModel::getBlogs() - ' . $e->getMessage());
            return ['blogs' => [], 'total' => 0];
        }
    }

    /**
     * Lấy blog theo slug (cho URL thân thiện)
     * @param string $slug Slug dự án
     * @return array|null Thông tin dự án
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

    /**
     * Lấy các blog mới nhất cho trang chủ
     * @param int $limit Số lượng tối đa
     * @return array Danh sách blog
     */
    public function getHomeBlogs($limit = 4)
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
            error_log('BlogsModel::getHomeBlogs() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy các blog nổi bật cho trang chủ
     * @param int $limit Số lượng tối đa
     * @return array Danh sách blog nổi bật
     */
    public function getFeaturedBlogs($limit = 6)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, title, slug, image, excerpt, created_at
                 FROM `blogs`
                 WHERE status = 2 AND deleted_at IS NULL
                 ORDER BY created_at DESC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getFeaturedBlogs() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy các blog theo category
     * @param int $categoryId ID category
     * @param int $limit Số lượng tối đa
     * @return array Danh sách blog
     */
    public function getBlogsByCategory($categoryId, $limit = 6)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, title, slug, image, excerpt, created_at
                 FROM `blogs`
                 WHERE category_id = ? AND status = 1 AND deleted_at IS NULL
                 ORDER BY created_at DESC
                 LIMIT ?"
            );
            $stmt->execute([$categoryId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getBlogsByCategory() - ' . $e->getMessage());
            return [];
        }
    }

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
     * @param string     $slug
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
    // BLOG CATEGORIES - Danh mục
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả blog categories cho dropdown menu header
     */
    public function getAllBlogCategories()
    {
        try {
            $stmt = $this->db->query(
                "SELECT id, name, slug, status, show_in_menu, sort_order, created_at
                 FROM `blog_categories`
                 ORDER BY sort_order ASC, id ASC"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getAllBlogCategories() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy category theo slug
     */
    public function getCategoryBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `blog_categories` WHERE slug = ? LIMIT 1"
            );
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getCategoryBySlug() - ' . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------------
    // BLOG TAGS - Tags
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả tags
     */
    public function getAllTags()
    {
        try {
            $stmt = $this->db->query(
                "SELECT id, name, slug FROM `blog_tags` ORDER BY name ASC"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getAllTags() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tags theo blog ID
     */
    public function getTagsByBlogId($blogId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT bt.id, bt.name, bt.slug
                 FROM `blog_tags` bt
                 INNER JOIN `blog_tag_map` btm ON bt.id = btm.tag_id
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

    /**
     * Lấy categories theo blog ID (multiple categories via many-to-many)
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
     * Lấy categories in hierarchy format for multi-select/checkboxes
     * @return array Categories organized with hierarchy
     */
    public function getCategoriesForMultiSelect()
    {
        try {
            // Get all categories including level
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, parent_id, level, status, sort_order
                 FROM `blog_categories`
                 ORDER BY parent_id IS NULL DESC, parent_id ASC, sort_order ASC"
            );
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Build hierarchy
            return $this->buildCategoryHierarchy($categories);
        } catch (PDOException $e) {
            error_log('BlogsModel::getCategoriesForMultiSelect() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Build category hierarchy from flat array
     * @param array $categories Flat array of categories
     * @return array Hierarchical array
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

    /**
     * Link blog to multiple categories (many-to-many)
     * @param int $blogId Blog ID
     * @param array $categoryIds Array of category IDs
     * @return bool Success
     */
    public function linkBlogToCategories($blogId, $categoryIds = [])
    {
        try {
            error_log("BlogsModel::linkBlogToCategories() - blogId: {$blogId}, categoryIds: " . json_encode($categoryIds));
            
            // Delete existing mappings
            $deleteStmt = $this->db->prepare("DELETE FROM `blog_category_map` WHERE blog_id = ?");
            $deleteStmt->execute([$blogId]);
            error_log("BlogsModel::linkBlogToCategories() - Deleted existing mappings for blog {$blogId}");
            
            // If no categories, we're done
            if (empty($categoryIds)) {
                error_log("BlogsModel::linkBlogToCategories() - No categories to link, returning");
                return true;
            }
            
            // Insert new mappings
            $insertStmt = $this->db->prepare(
                "INSERT INTO `blog_category_map` (blog_id, category_id, sort_order) 
                 VALUES (?, ?, ?)"
            );
            
            foreach ($categoryIds as $index => $catId) {
                error_log("BlogsModel::linkBlogToCategories() - Linking blog {$blogId} to category {$catId}");
                $insertStmt->execute([$blogId, $catId, $index]);
            }
            
            error_log("BlogsModel::linkBlogToCategories() - Success!");
            return true;
        } catch (PDOException $e) {
            error_log('BlogsModel::linkBlogToCategories() - ERROR: ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------------
    // ADMIN METHODS - Không filter status
    // ----------------------------------------------------------------

    /**
     * Lấy các blog mới nhất
     * @param int $limit Số lượng tối đa
     * @return array Danh sách blog
     */
    public function getRecentBlogs($limit = 5)
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

    /**
     * Lấy danh sách blogs cho admin với phân trang và filter
     * @param int $page Trang hiện tại
     * @param int $perPage Số bài mỗi trang
     * @param int $catId Lọc theo category_id (0 = tất cả)
     * @param string $search Từ khóa tìm kiếm
     * @return array ['blogs' => [...], 'total' => int]
     */
    public function getAdminBlogs($page = 1, $perPage = 20, $catId = 0, $search = '')
    {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];

            // Base query without category join since we use many-to-many now
            $baseFrom = "FROM `blogs` b";
            $baseJoin = "";

            $where = "WHERE b.deleted_at IS NULL"; // Admin xem tất cả, nhưng không bao gồm soft deleted

            // Add category filter through blog_category_map
            if ($catId > 0) {
                $baseJoin .= " INNER JOIN `blog_category_map` bcm ON b.id = bcm.blog_id";
                $where .= " AND bcm.category_id = ?";
                $params[] = $catId;
            }

            // Add search filter - now searches in title, excerpt, and categories via mapping
            if (!empty($search)) {
                $where .= " AND (
                    b.title LIKE ? OR 
                    b.excerpt LIKE ? OR
                    EXISTS (
                        SELECT 1 FROM `blog_category_map` search_bcm
                        INNER JOIN `blog_categories` search_bc ON search_bcm.category_id = search_bc.id
                        WHERE search_bcm.blog_id = b.id AND search_bc.name LIKE ?
                    )
                )";
                $like = "%{$search}%";
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }

            // Count total
            $countSql = "SELECT COUNT(DISTINCT b.id) {$baseFrom} {$baseJoin} {$where}";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = (int) $countStmt->fetchColumn();

            // Fetch blogs - remove old category fields
            $sql = "SELECT DISTINCT b.id, b.title, b.slug, b.image, b.excerpt,
                           b.author, b.created_at, b.views, b.status
                    {$baseFrom} {$baseJoin}
                    {$where}
                    ORDER BY b.created_at DESC
                    LIMIT ? OFFSET ?";

            $fetchParams = array_merge($params, [$perPage, $offset]);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($fetchParams);
            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Load multiple categories and tags for each blog
            foreach ($blogs as &$blog) {
                $blog['categories'] = $this->getCategoriesByBlogId($blog['id']);
                $blog['tags'] = $this->getTagsByBlogId($blog['id']);
                
                // For backward compatibility, set first category as primary
                if (!empty($blog['categories'])) {
                    $blog['category_name'] = $blog['categories'][0]['name'];
                    $blog['category_slug'] = $blog['categories'][0]['slug'];
                } else {
                    $blog['category_name'] = null;
                    $blog['category_slug'] = null;
                }
            }

            return ['blogs' => $blogs, 'total' => $total];
        } catch (PDOException $e) {
            error_log('BlogsModel::getAdminBlogs() - ' . $e->getMessage());
            return ['blogs' => [], 'total' => 0];
        }
    }

    /**
     * Lấy blog theo ID cho admin (không filter status)
     */
    public function getAdminBlogById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT b.*, bc.name AS category_name, bc.slug AS category_slug
                 FROM `blogs` b
                 LEFT JOIN `blog_categories` bc ON b.category_id = bc.id
                 WHERE b.id = ? AND deleted_at IS NULL
                 LIMIT 1"
            );
            $stmt->execute([$id]);
            $blog = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$blog) return null;
            $blog['tags'] = $this->getTagsByBlogId($blog['id']);
            return $blog;
        } catch (PDOException $e) {
            error_log('BlogsModel::getAdminBlogById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate category level by tracing parent chain to root
     * Root = 1, Child of root = 2, Grandchild = 3, etc.
     * 
     * @param int|null $parentId Parent category ID
     * @return int Calculated level
     */
    public function calculateCategoryLevel($parentId)
    {
        if (!$parentId || $parentId === null) {
            return 1; // Root category
        }

        try {
            // Get parent's current level (chứ không lấy parent_id)
            $stmt = $this->db->prepare("SELECT level FROM blog_categories WHERE id = ?");
            $stmt->execute([$parentId]);
            $parent = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$parent || empty($parent['level'])) {
                // If parent not found or level is empty, fallback to tracing parent chain
                $stmt = $this->db->prepare("SELECT parent_id FROM blog_categories WHERE id = ?");
                $stmt->execute([$parentId]);
                $parentData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$parentData) {
                    return 1; // Parent not found, treat as root
                }
                
                // Recursively calculate parent's level, then add 1
                $parentLevel = $this->calculateCategoryLevel($parentData['parent_id']);
                return $parentLevel + 1;
            }

            // Parent level is stored, return parent_level + 1
            return (int)$parent['level'] + 1;
        } catch (PDOException $e) {
            error_log('BlogsModel::calculateCategoryLevel() - ' . $e->getMessage());
            return 1; // Default to root on error
        }
    }

    /**
     * Cập nhật level của một danh mục và tất cả danh mục con (recursive update)
     * Gọi khi parent_id thay đổi để đảm bảo level chính xác cho cả cây con
     * 
     * @param int $categoryId ID của danh mục cần cập nhật
     * @return bool Kết quả cập nhật
     */
    public function updateCategoryLevelRecursive($categoryId)
    {
        try {
            // Bước 1: Cập nhật level của danh mục hiện tại
            $level = $this->calculateCategoryLevel(
                $this->getParentIdOfCategory($categoryId)
            );
            
            $stmt = $this->db->prepare("UPDATE blog_categories SET level = ? WHERE id = ?");
            $stmt->execute([$level, $categoryId]);
            
            // Bước 2: Cập nhật level của tất cả danh mục con (recursive)
            $stmt = $this->db->prepare("SELECT id FROM blog_categories WHERE parent_id = ?");
            $stmt->execute([$categoryId]);
            $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($children as $child) {
                $this->updateCategoryLevelRecursive($child['id']);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log('BlogsModel::updateCategoryLevelRecursive() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh mục theo ID
     * @param int $id Category ID
     * @return array|null Danh mục hoặc null
     */
    public function getCategoryById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT id, name, parent_id FROM blog_categories WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getCategoryById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy chuỗi danh mục cha từ 1 danh mục (đến root)
     * Kết quả: [categoryId, parentId, grandparentId, ..., rootId]
     * 
     * @param int $categoryId ID của danh mục
     * @return array Danh sách các category IDs từ category đó đến root
     */
    public function getParentChain($categoryId)
    {
        $chain = [(int)$categoryId];
        $current = $categoryId;
        
        while ($current) {
            $cat = $this->getCategoryById($current);
            if (!$cat || !$cat['parent_id']) break;
            $current = (int)$cat['parent_id'];
            $chain[] = $current;
        }
        
        return $chain;
    }
    
    /**
     * Helper: Lấy parent_id của một danh mục
     * 
     * @param int $categoryId ID của danh mục
     * @return int|null parent_id hoặc null nếu không tìm thấy
     */
    private function getParentIdOfCategory($categoryId)
    {
        try {
            $stmt = $this->db->prepare("SELECT parent_id FROM blog_categories WHERE id = ?");
            $stmt->execute([$categoryId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['parent_id'] : null;
        } catch (PDOException $e) {
            error_log('BlogsModel::getParentIdOfCategory() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy tất cả blog categories cho admin (không filter status)
     */
    public function getAdminBlogCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT bc.id, bc.name, bc.slug, bc.status, bc.show_in_menu, 
                        bc.sort_order, bc.created_at, bc.parent_id, bc.level,
                        parent.name as parent_name
                 FROM `blog_categories` bc
                 LEFT JOIN `blog_categories` parent ON bc.parent_id = parent.id
                 ORDER BY bc.parent_id IS NULL DESC, bc.parent_id ASC, bc.sort_order ASC, bc.id ASC"
            );
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Recalculate level based on parent chain (depth-based)
            foreach ($categories as &$cat) {
                $cat['level'] = $this->calculateCategoryLevel($cat['parent_id']);
            }
            
            return $categories;
        } catch (PDOException $e) {
            error_log('BlogsModel::getAdminBlogCategories() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tạo blog mới
     * @param array $data Dữ liệu blog
     * @return int Blog ID
     */
    public function createBlog($data)
    {
        try {
            $db = $this->db;

            // Chuẩn bị dữ liệu
            $title         = $data['title'] ?? '';
            $slug          = $data['slug'] ?? '';
            $categoryId    = $data['category_id'] ?? null;
            $excerpt       = $data['excerpt'] ?? '';
            $content       = $data['content'] ?? '';
            $image         = $data['image'] ?? '';
            $author        = $data['author'] ?? 'Admin';
            $status        = $data['status'] ?? 1;
            $views         = $data['views'] ?? 0;
            $showInMenu    = $data['show_in_menu'] ?? 0;
            $isFeatured    = $data['is_featured'] ?? 0;
            
            // Recruitment fields
            $hiringStatus  = isset($data['hiring_status']) ? (int) $data['hiring_status'] : 1;
            $position      = $data['position'] ?? '';
            $expiresRaw    = $data['expires_in_days'] ?? null;
            $expiresInDays = ($expiresRaw !== '' && $expiresRaw !== null) ? (int) $expiresRaw : null;
            $contactEmail  = $data['contact_email'] ?? null;
            $contactPhone  = $data['contact_phone'] ?? null;

            // Khớp với cấu trúc bảng thực tế (tất cả các cột)
            $sql = "INSERT INTO blogs (
                title, slug, category_id, excerpt, content, image, author,
                status, show_in_menu, views, is_featured, hiring_status, position,
                expires_in_days, contact_email, contact_phone
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $title,
                $slug,
                $categoryId,
                $excerpt,
                $content,
                $image,
                $author,
                $status,
                $showInMenu,
                $views,
                $isFeatured,
                $hiringStatus,
                $position,
                $expiresInDays,
                $contactEmail,
                $contactPhone,
            ]);

            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log('BlogsModel::createBlog() - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật blog
     * @param int $id Blog ID
     * @param array $data Dữ liệu cập nhật
     * @return bool
     */
    public function updateBlog($id, $data)
    {
        try {
            $fields = [];
            $params = [];
            
            // Dynamic SET clause - chỉ update các field có trong $data
            foreach ($data as $key => $value) {
                $fields[] = "{$key} = ?";
                $params[] = $value;
            }
            
            if (empty($fields)) return false;
            
            $sql = "UPDATE blogs SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('BlogsModel::updateBlog() - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Xóa blog (soft delete)
     * @param int $id Blog ID
     * @return bool
     */
    public function deleteBlog($id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE blogs SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('BlogsModel::deleteBlog() - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Đếm tổng số blogs cho admin (kể cả draft)
     */
    public function countAll(): int
    {
        try {
            return (int) $this->db->query("SELECT COUNT(*) FROM `blogs` WHERE deleted_at IS NULL")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    // ----------------------------------------
    // SOFT DELETE METHODS
    // ----------------------------------------

    /**
     * Lấy danh sách blogs đã bị xóa (soft deleted)
     * @param int $limit Số lượng bản ghi mỗi trang
     * @param int $offset Vị trí bắt đầu
     * @return array Danh sách blogs đã xóa
     */
    public function getTrashed($limit = 20, $offset = 0)
    {
        try {
            $sql = "SELECT b.*, bc.name AS category_name, bc.slug AS category_slug
                    FROM `blogs` b
                    LEFT JOIN `blog_categories` bc ON b.category_id = bc.id
                    WHERE b.deleted_at IS NOT NULL 
                    ORDER BY b.deleted_at DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Gắn tags cho từng blog
            foreach ($blogs as &$blog) {
                $blog['tags'] = $this->getTagsByBlogId($blog['id']);
            }

            return $blogs;
        } catch (PDOException $e) {
            error_log('BlogsModel::getTrashed() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số lượng blogs đã bị xóa
     * @return int Số lượng blogs đã xóa
     */
    public function countTrashed()
    {
        try {
            $sql = "SELECT COUNT(*) FROM `blogs` WHERE deleted_at IS NOT NULL";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('BlogsModel::countTrashed() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy blog đã bị xóa theo ID
     * @param int $id Blog ID
     * @return array|null Thông tin blog đã xóa
     */
    public function getTrashedById($id)
    {
        try {
            $sql = "SELECT b.*, bc.name AS category_name, bc.slug AS category_slug
                    FROM `blogs` b
                    LEFT JOIN `blog_categories` bc ON b.category_id = bc.id
                    WHERE b.id = ? AND b.deleted_at IS NOT NULL
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $blog = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$blog) return null;
            $blog['tags'] = $this->getTagsByBlogId($blog['id']);
            return $blog;
        } catch (PDOException $e) {
            error_log('BlogsModel::getTrashedById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Khôi phục blog đã bị xóa
     * @param int $id Blog ID
     * @return bool Kết quả
     */
    public function restore($id)
    {
        try {
            $sql = "UPDATE blogs SET deleted_at = NULL WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('BlogsModel::restore() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa vĩnh viễn blog (hard delete)
     * @param int $id Blog ID
     * @return bool Kết quả
     */
    public function hardDelete($id)
    {
        try {
            $sql = "DELETE FROM blogs WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('BlogsModel::hardDelete() - ' . $e->getMessage());
            return false;
        }
    }

    // ================================================================
    // HIRING POSITIONS - Tuyển dụng
    // ================================================================

    /**
     * Lấy tất cả vị trí tuyển dụng từ blogs
     * @return array Danh sách các vị trí tuyển dụng
     */
    public function getAllHiringPositions()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT DISTINCT b.id, b.title, b.position, b.hiring_status, b.expires_in_days, b.created_at
                 FROM `blogs` b
                 WHERE b.hiring_status = 1 AND b.status = 1 AND b.deleted_at IS NULL
                   AND b.position IS NOT NULL AND b.position != ''
                 ORDER BY b.created_at DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getAllHiringPositions() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra blog có phải tin tuyển dụng
     * @param int $blogId Blog ID
     * @return bool True nếu là tin tuyển dụng
     */
    public function isHiringBlog($blogId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT 1 FROM `blogs` 
                 WHERE id = ? AND hiring_status = 1 AND status = 1 AND deleted_at IS NULL
                 LIMIT 1"
            );
            $stmt->execute([$blogId]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log('BlogsModel::isHiringBlog() - ' . $e->getMessage());
            return false;
        }
    }
    
}
