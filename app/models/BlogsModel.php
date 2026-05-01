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

            $where = "WHERE b.status = 1";

            if ($catId > 0) {
                $where .= " AND b.category_id = ?";
                $params[] = $catId;
            }

            if (!empty($tagSlug)) {
                $baseJoin .= " INNER JOIN `blog_tag_map` btm ON btm.blog_id = b.id
                               INNER JOIN `blog_tags` bt ON bt.id = btm.tag_id AND bt.slug = ?";
                // Rebuild params: tag slug phải đứng đúng vị trí trong JOIN
                $params = [];
                if (!empty($tagSlug)) $params[] = $tagSlug;
                if ($catId > 0)       $params[] = $catId;
            }

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
                           b.author, b.created_at, b.views,
                           bc.name AS category_name, bc.slug AS category_slug
                    {$baseJoin}
                    {$where}
                    ORDER BY b.created_at DESC
                    LIMIT ? OFFSET ?";

            $fetchParams = array_merge($params, [$perPage, $offset]);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($fetchParams);
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
                 WHERE b.slug = ? AND b.status = 1
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
                 WHERE b.id = ? AND b.status = 1
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
     */
    public function getAllBlogCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT bc.id, bc.name, bc.slug,
                        COUNT(b.id) AS post_count
                 FROM `blog_categories` bc
                 LEFT JOIN `blogs` b ON b.category_id = bc.id AND b.status = 1
                 WHERE bc.status = 1
                 GROUP BY bc.id
                 ORDER BY bc.sort_order ASC, bc.id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getAllBlogCategories() - ' . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------------
    // TAGS
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả tags kèm số lượng bài viết.
     */
    public function getAllTags()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT bt.id, bt.name, bt.slug,
                        COUNT(btm.blog_id) AS post_count
                 FROM `blog_tags` bt
                 LEFT JOIN `blog_tag_map` btm ON btm.tag_id = bt.id
                 GROUP BY bt.id
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
                 WHERE status = 1
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
                 WHERE b.status = 1
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
                 WHERE b.slug = ? AND b.status = 1
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
    public function getMenuBlogCategories($limit = 10)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug
                 FROM `blog_categories`
                 WHERE status = 1 AND show_in_menu = 1
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('BlogsModel::getMenuBlogCategories() - ' . $e->getMessage());
            return [];
        }
    }
}
