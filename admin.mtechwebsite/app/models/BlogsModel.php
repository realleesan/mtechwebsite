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

            $baseJoin = "FROM `blogs` b
                         LEFT JOIN `blog_categories` bc ON b.category_id = bc.id";

            $where = "WHERE deleted_at IS NULL"; // Admin xem tất cả, nhưng không bao gồm soft deleted

            // Add category filter
            if ($catId > 0) {
                $where .= " AND b.category_id = ?";
                $params[] = $catId;
            }

            // Add search filter
            if (!empty($search)) {
                $where .= " AND (b.title LIKE ? OR b.excerpt LIKE ? OR bc.name LIKE ?)";
                $like = "%{$search}%";
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }

            // Count total
            $countSql = "SELECT COUNT(b.id) {$baseJoin} {$where}";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = (int) $countStmt->fetchColumn();

            // Fetch blogs - bỏ sort_order vì không tồn tại
            $sql = "SELECT b.id, b.title, b.slug, b.image, b.excerpt,
                           b.author, b.created_at, b.views, b.category_id, b.status,
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
     * Lấy tất cả blog categories cho admin (không filter status)
     */
    public function getAdminBlogCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, status, show_in_menu, sort_order, created_at
                 FROM `blog_categories`
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

            $hiringStatus  = isset($data['hiring_status']) ? (int) $data['hiring_status'] : 1;
            $expiresRaw    = $data['expires_in_days'] ?? null;
            $expiresInDays = ($expiresRaw !== '' && $expiresRaw !== null) ? (int) $expiresRaw : null;

            // Khớp đúng với cấu trúc bảng thực tế (DESCRIBE blogs)
            $sql = "INSERT INTO blogs (
                title, slug, category_id, excerpt, content, image, author,
                status, views, hiring_status, position,
                expires_in_days, contact_email, contact_phone
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['title']         ?? '',
                $data['slug']          ?? '',
                $data['category_id']   ?? null,
                $data['excerpt']       ?? '',
                $data['content']       ?? '',
                $data['image']         ?? '',
                $data['author']        ?? 'Admin',
                $data['status']        ?? 1,
                $data['views']         ?? 0,
                $hiringStatus,
                $data['position']      ?? '',
                $expiresInDays,
                $data['contact_email'] ?? null,
                $data['contact_phone'] ?? null,
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
}
