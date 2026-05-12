<?php
/**
 * BlogsController - Quản lý Tin tức / Tuyển dụng
 */

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/BlogsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class BlogsController extends BaseController
{
    private $blogsModel;
    
    // Upload constants
    private const UPLOAD_DIR     = '/assets/uploads/blogs/';
    private const ADMIN_BASE_URL = 'https://admin.truongvinalogistics.com.vn';
    private const MAX_FILE_SIZE  = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES  = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->blogsModel = new BlogsModel();
        
        // Ensure upload directory exists
        $uploadPath = __DIR__ . '/../../assets/uploads/blogs';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
    }

    // ----------------------------------------
    // Index - Danh sách blogs
    // ----------------------------------------

    public function index()
    {
        $page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage  = 20;
        $search   = trim($_GET['search'] ?? '');
        $catId    = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

        $result = $this->blogsModel->getBlogs($page, $perPage, $catId, '', $search);
        $blogs  = $result['blogs'];
        $total  = $result['total'];

        $totalPages = ceil($total / $perPage);
        $categories = $this->blogsModel->getAllBlogCategories();

        // Debug: Log the request parameters
        error_log("BlogsController::index() - page: {$page}, search: '{$search}', catId: {$catId}");

        $result = $this->blogsModel->getAdminBlogs($page, $perPage, $catId, $search);
        $blogs  = $result['blogs'];
        $total  = $result['total'];

        // Debug: Log the results
        error_log("BlogsController::index() - Found {$total} total blogs, " . count($blogs) . " on current page");

        $totalPages = ceil($total / $perPage);
        $categories = $this->blogsModel->getAdminBlogCategories();

        // Debug: Log categories
        error_log("BlogsController::index() - Found " . count($categories) . " categories");

        $this->view('blogs/index', [
            'title'      => 'Quản lý Tin tức - Admin MTech',
            'page'       => 'blogs',
            'blogs'      => $blogs,
            'categories' => $categories,
            'currentPage'=> $page,
            'totalPages' => $totalPages,
            'total'      => $total,
            'search'     => $search,
            'catId'      => $catId,
            'admin'      => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Create - Form tạo blog mới
    // ----------------------------------------

    public function create()
    {
        $categories = $this->blogsModel->getAdminBlogCategories();

        $this->view('blogs/create', [
            'title'      => 'Tạo tin tức mới - Admin MTech',
            'page'       => 'blog.create',
            'categories' => $categories,
            'admin'      => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Store - Lưu blog mới
    // ----------------------------------------

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/blogs');
            return;
        }

        // TODO: Implement store logic
        // - Validate input
        // - Upload image
        // - Insert vào database
        // - Redirect về /blogs với success message

        // Validate required fields
        $required = ['title', 'slug', 'category_id'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc";
                $this->redirect('/blogs/create');
                return;
            }
        }

        try {
            $db = getDBConnection();
            
            // Check if slug already exists
            $stmt = $db->prepare("SELECT id FROM blogs WHERE slug = ?");
            $stmt->execute([$_POST['slug']]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Slug đã tồn tại, vui lòng chọn slug khác';
                $this->redirect('/blogs/create');
                return;
            }

            // Handle image upload
            $imagePath = '';
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['path'];
                } else {
                    $_SESSION['error'] = $uploadResult['error'];
                    $this->redirect('/blogs/create');
                    return;
                }
            }

            // Prepare blog data
            $blogData = [
                'title' => $this->clampUtf8($_POST['title'] ?? '', 500),
                'slug' => $this->clampUtf8($_POST['slug'] ?? '', 500),
                'category_id' => $_POST['category_id'],
                'excerpt' => $this->clampUtf8($_POST['excerpt'] ?? '', 65535),
                'content' => $_POST['content'] ?? '',
                'image' => $imagePath,
                'author' => $this->clampUtf8($_POST['author'] ?? 'Admin', 255),
                'status' => $_POST['status'] ?? 1,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'views' => 0
            ];

            // Add recruitment fields if category is recruitment (ID = 7)
            if ($_POST['category_id'] == 7) {
                $blogData['hiring_status'] = $_POST['hiring_status'] ?? 1;
                $blogData['position'] = $this->clampUtf8($_POST['position'] ?? '', 255);
                $blogData['expires_in_days'] = !empty($_POST['expires_in_days']) ? $_POST['expires_in_days'] : null;
                $blogData['contact_email'] = !empty($_POST['contact_email'])
                    ? $this->clampUtf8($_POST['contact_email'], 255) : null;
                $blogData['contact_phone'] = !empty($_POST['contact_phone'])
                    ? $this->clampUtf8($_POST['contact_phone'], 50) : null;
            }

            // Một transaction: nếu tags/blog_details lỗi thì rollback cả bài — tránh 500 nhưng slug đã lưu
            $db->beginTransaction();

            $blogId = $this->blogsModel->createBlog($blogData);

            if (!empty($_POST['tags'])) {
                $this->handleTags($blogId, $_POST['tags']);
            }

            if ($blogId) {
                $this->handleBlogDetails($blogId, [
                    'meta_title' => $_POST['meta_title'] ?? null,
                    'meta_description' => $_POST['meta_description'] ?? null,
                    'meta_keywords' => $_POST['meta_keywords'] ?? null,
                    'full_content' => $_POST['content'] ?? ''
                ]);
            }

            $db->commit();

            $_SESSION['success'] = 'Thêm tin tức thành công';
            $this->redirect('/blogs');

        } catch (Throwable $e) {
            if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
                $db->rollBack();
            }
            $this->logAdminThrowable('BlogsController::store', $e);
            $detail = $this->formatThrowableForAdmin($e);
            $_SESSION['error'] = 'Không lưu được tin tức. ' . $detail
                . ' File log: admin.mtechwebsite/storage/logs/admin-errors.log (tải qua FTP/cPanel).';
            $this->redirect('/blogs/create');
        }
    }

    // ----------------------------------------
    // Edit - Form chỉnh sửa blog
    // ----------------------------------------

    public function edit($id)
    {
        $blog = $this->blogsModel->getAdminBlogById($id);

        if (!$blog) {
            $_SESSION['error'] = 'Không tìm thấy tin tức';
            $this->redirect('/blogs');
            return;
        }

        // Get blog details (SEO metadata)
        $blogDetails = $this->getBlogDetails($id);
        $blog = $this->mergeBlogDetailsIntoBlog($blog, $blogDetails);

        $categories = $this->blogsModel->getAdminBlogCategories();

        $this->view('blogs/edit', [
            'title'      => 'Chỉnh sửa tin tức - Admin MTech',
            'page'       => 'blog.edit',
            'blog'       => $blog,
            'categories' => $categories,
            'admin'      => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Update - Cập nhật blog
    // ----------------------------------------

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/blogs');
            return;
        }

        // TODO: Implement update logic
        // - Validate input
        // - Upload image (nếu có)
        // - Update database
        // - Redirect về /blogs với success message
        $id = (int) $id;

        // Validate required fields
        $required = ['title', 'slug', 'category_id'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc";
                $this->redirect('/blogs/edit/' . $id);
                return;
            }
        }

        try {
            $db = getDBConnection();
            
            // Get current blog
            $currentBlog = $this->blogsModel->getAdminBlogById($id);
            if (!$currentBlog) {
                $_SESSION['error'] = 'Không tìm thấy tin tức';
                $this->redirect('/blogs');
                return;
            }

            // Check if slug already exists (exclude current record)
            $stmt = $db->prepare("SELECT id FROM blogs WHERE slug = ? AND id != ?");
            $stmt->execute([$_POST['slug'], $id]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Slug đã tồn tại, vui lòng chọn slug khác';
                $this->redirect('/blogs/edit/' . $id);
                return;
            }

            // Prepare blog data
            $blogData = [
                'title' => $this->clampUtf8($_POST['title'] ?? '', 500),
                'slug' => $this->clampUtf8($_POST['slug'] ?? '', 500),
                'category_id' => $_POST['category_id'],
                'excerpt' => $this->clampUtf8($_POST['excerpt'] ?? '', 65535),
                'content' => $_POST['content'] ?? '',
                'author' => $this->clampUtf8($_POST['author'] ?? 'Admin', 255),
                'status' => $_POST['status'] ?? 1,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0
            ];

            // Add recruitment fields if category is recruitment (ID = 7)
            if ($_POST['category_id'] == 7) {
                $blogData['hiring_status'] = $_POST['hiring_status'] ?? 1;
                $blogData['position'] = $this->clampUtf8($_POST['position'] ?? '', 255);
                $blogData['expires_in_days'] = !empty($_POST['expires_in_days']) ? $_POST['expires_in_days'] : null;
                $blogData['contact_email'] = !empty($_POST['contact_email'])
                    ? $this->clampUtf8($_POST['contact_email'], 255) : null;
                $blogData['contact_phone'] = !empty($_POST['contact_phone'])
                    ? $this->clampUtf8($_POST['contact_phone'], 50) : null;
            }

            // Handle image upload/removal
            if (!empty($_FILES['image']['name'])) {
                // New image uploaded
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if ($uploadResult['success']) {
                    // Delete old image
                    $this->deleteOldImage($currentBlog['image']);
                    $blogData['image'] = $uploadResult['path'];
                } else {
                    $_SESSION['error'] = $uploadResult['error'];
                    $this->redirect('/blogs/edit/' . $id);
                    return;
                }
            } elseif (!empty($_POST['remove_image']) && $_POST['remove_image'] === '1') {
                // Remove current image
                $this->deleteOldImage($currentBlog['image']);
                $blogData['image'] = '';
            }
            // If no new image and no remove flag, keep current image (don't set image in $blogData)

            $db->beginTransaction();

            $this->blogsModel->updateBlog($id, $blogData);

            $this->handleTags($id, $_POST['tags'] ?? '');

            $this->handleBlogDetails($id, [
                'meta_title' => $_POST['meta_title'] ?? null,
                'meta_description' => $_POST['meta_description'] ?? null,
                'meta_keywords' => $_POST['meta_keywords'] ?? null,
                'full_content' => $_POST['content'] ?? ''
            ]);

            $db->commit();

            $_SESSION['success'] = 'Cập nhật tin tức thành công';
            $this->redirect('/blogs');

        } catch (Throwable $e) {
            if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
                $db->rollBack();
            }
            $this->logAdminThrowable('BlogsController::update', $e);
            $detail = $this->formatThrowableForAdmin($e);
            $_SESSION['error'] = 'Không cập nhật được tin. ' . $detail
                . ' File log: admin.mtechwebsite/storage/logs/admin-errors.log';
            $this->redirect('/blogs/edit/' . $id);
        }
    }

    // ----------------------------------------
    // Delete - Xóa blog
    // ----------------------------------------

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/blogs');
            return;
        }

        // TODO: Implement delete logic
        // - Xóa khỏi database (soft delete: status = 0)
        // - Redirect về /blogs với success message

        try {
            $blog = $this->blogsModel->getAdminBlogById($id);
            if (!$blog) {
                $_SESSION['error'] = 'Không tìm thấy tin tức';
                $this->redirect('/blogs');
                return;
            }

            // Delete blog
            $this->blogsModel->deleteBlog($id);

            // Delete image
            $this->deleteOldImage($blog['image']);

            // Delete tags
            $this->deleteBlogTags($id);

            // Delete blog details
            $this->deleteBlogDetails($id);

            $_SESSION['success'] = 'Xóa tin tức thành công';
            $this->redirect('/blogs');

        } catch (PDOException $e) {
            error_log('BlogsController::delete() - ' . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tin tức';
            $this->redirect('/blogs');
        }
    }

    // ----------------------------------------
    // View - Xem chi tiết blog (for admin)
    // ----------------------------------------

    public function viewBlog($id)
    {
        $blog = $this->blogsModel->getAdminBlogById($id);

        if (!$blog) {
            $_SESSION['error'] = 'Không tìm thấy tin tức';
            $this->redirect('/blogs');
            return;
        }

        // Get blog details
        $blogDetails = $this->getBlogDetails($id);
        $blog = $this->mergeBlogDetailsIntoBlog($blog, $blogDetails);

        $this->view('blogs/view', [
            'title' => 'Chi tiết tin tức - Admin MTech',
            'page'  => 'blog.view',
            'blog'  => $blog,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Helper Methods
    // ----------------------------------------

    private function handleImageUpload($file)
    {
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Lỗi upload file'];
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'File quá lớn (tối đa 5MB)'];
        }

        // Validate MIME type (real check, not just extension)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return ['success' => false, 'error' => 'Máy chủ không bật extension fileinfo; không kiểm tra được MIME. Liên hệ hosting.'];
        }
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if ($mimeType === false) {
            return ['success' => false, 'error' => 'Không đọc được file upload'];
        }

        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            return ['success' => false, 'error' => 'Định dạng file không được hỗ trợ'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'blog_' . time() . '_' . uniqid() . '.' . $extension;
        
        // Upload path
        $uploadPath = __DIR__ . '/../../assets/uploads/blogs/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => false, 'error' => 'Không thể lưu file'];
        }

        // Return absolute URL
        $absoluteUrl = self::ADMIN_BASE_URL . self::UPLOAD_DIR . $filename;
        return ['success' => true, 'path' => $absoluteUrl];
    }

    private function deleteOldImage($imagePath)
    {
        if (empty($imagePath)) return;
        
        // Only delete images uploaded to admin site
        if (strpos($imagePath, self::ADMIN_BASE_URL) === false) return;
        
        $filename = basename($imagePath);
        $fullPath = __DIR__ . '/../../assets/uploads/blogs/' . $filename;
        
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function handleTags($blogId, $tagsInput)
    {
        $db = getDBConnection();
        $blogId = (int) $blogId;

        $stmt = $db->prepare("DELETE FROM blog_tag_map WHERE blog_id = ?");
        $stmt->execute([$blogId]);

        if (is_array($tagsInput)) {
            $tagsInput = implode(',', $tagsInput);
        }
        $tagsString = trim((string) $tagsInput);
        if ($tagsString === '') {
            return;
        }

        $tagNames = array_map('trim', explode(',', $tagsString));
        $tagNames = array_filter($tagNames); // Remove empty values

        foreach ($tagNames as $tagName) {
            // Get or create tag
            $stmt = $db->prepare("SELECT id FROM blog_tags WHERE name = ?");
            $stmt->execute([$tagName]);
            $tag = $stmt->fetch();

            if ($tag) {
                $tagId = $tag['id'];
            } else {
                // Create new tag
                $slug = $this->generateSlug($tagName);
                $stmt = $db->prepare("INSERT INTO blog_tags (name, slug) VALUES (?, ?)");
                $stmt->execute([$tagName, $slug]);
                $tagId = $db->lastInsertId();
            }

            // Link tag to blog
            $stmt = $db->prepare("INSERT IGNORE INTO blog_tag_map (blog_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$blogId, $tagId]);
        }
    }

    private function handleBlogDetails($blogId, $details)
    {
        $db = getDBConnection();

        $metaTitle = $this->clampUtf8($details['meta_title'] ?? null, 255);
        $metaDescription = $this->clampUtf8($details['meta_description'] ?? null, 65535);
        $metaKeywords = $this->clampUtf8($details['meta_keywords'] ?? null, 500);
        $fullContent = $details['full_content'] ?? '';

        // Check if details exist
        $stmt = $db->prepare("SELECT id FROM blog_details WHERE blog_id = ?");
        $stmt->execute([$blogId]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update
            $stmt = $db->prepare("
                UPDATE blog_details 
                SET meta_title = ?, meta_description = ?, meta_keywords = ?, full_content = ?, updated_at = NOW()
                WHERE blog_id = ?
            ");
            $stmt->execute([
                $metaTitle,
                $metaDescription,
                $metaKeywords,
                $fullContent,
                $blogId
            ]);
        } else {
            // Insert
            $stmt = $db->prepare("
                INSERT INTO blog_details (blog_id, meta_title, meta_description, meta_keywords, full_content, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $blogId,
                $metaTitle,
                $metaDescription,
                $metaKeywords,
                $fullContent
            ]);
        }
    }

    /**
     * Cắt chuỗi theo độ dài ký tự UTF-8 (tránh lỗi INSERT khi vượt VARCHAR / strict mode).
     */
    private function clampUtf8($value, int $maxLen): string
    {
        $value = (string) ($value ?? '');
        if ($value !== '' && function_exists('iconv')) {
            $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            if ($clean !== false) {
                $value = $clean;
            }
        }
        if ($maxLen <= 0) {
            return '';
        }
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            $len = @mb_strlen($value, 'UTF-8');
            if ($len !== false && $len > $maxLen) {
                $cut = @mb_substr($value, 0, $maxLen, 'UTF-8');
                return $cut !== false ? $cut : substr($value, 0, $maxLen);
            }
            if ($len !== false) {
                return $value;
            }
        }
        return strlen($value) > $maxLen ? substr($value, 0, $maxLen) : $value;
    }

    /**
     * Ghi lỗi ra file trong dự án (không phụ thuộc cấu hình error_log của hosting).
     */
    private function logAdminThrowable(string $context, Throwable $e): void
    {
        $dir = __DIR__ . '/../../storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $file = $dir . '/admin-errors.log';
        $line = sprintf(
            "[%s] %s | %s @ %s:%d\n",
            date('Y-m-d H:i:s'),
            $context,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
        error_log($context . ' - ' . $e->getMessage() . ' @' . $e->getFile() . ':' . $e->getLine());
    }

    /** Một dòng ngắn để hiển thị trong flash (tránh vỡ layout). */
    private function formatThrowableForAdmin(Throwable $e): string
    {
        $msg = preg_replace('/\s+/u', ' ', $e->getMessage());
        $msg = trim((string) $msg);
        if (function_exists('mb_substr')) {
            $msg = mb_substr($msg, 0, 400, 'UTF-8');
        } else {
            $msg = substr($msg, 0, 400);
        }
        return $msg !== '' ? 'Chi tiết: ' . $msg : '';
    }

    private function getBlogDetails($blogId)
    {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT * FROM blog_details WHERE blog_id = ?");
            $stmt->execute([$blogId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Gộp blog_details vào bản ghi blog mà không ghi đè khóa blogs.id
     * (blog_details cũng có cột id — array_merge cũ làm sai URL /blogs/update/{id} và kiểm tra slug).
     */
    private function mergeBlogDetailsIntoBlog(array $blog, ?array $details): array
    {
        if (!$details) {
            return $blog;
        }
        unset($details['id'], $details['blog_id'], $details['created_at'], $details['updated_at']);
        return array_merge($blog, $details);
    }

    private function deleteBlogTags($blogId)
    {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("DELETE FROM blog_tag_map WHERE blog_id = ?");
            $stmt->execute([$blogId]);
        } catch (PDOException $e) {
            error_log('Error deleting blog tags: ' . $e->getMessage());
        }
    }

    private function deleteBlogDetails($blogId)
    {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("DELETE FROM blog_details WHERE blog_id = ?");
            $stmt->execute([$blogId]);
        } catch (PDOException $e) {
            error_log('Error deleting blog details: ' . $e->getMessage());
        }
    }

    private function generateSlug($text)
    {
        $text = (string) $text;
        $text = str_replace(
            ['à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ'],
            'a',
            $text
        );
        $text = str_replace(['è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ'], 'e', $text);
        $text = str_replace(['ì', 'í', 'ị', 'ỉ', 'ĩ'], 'i', $text);
        $text = str_replace(
            ['ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ'],
            'o',
            $text
        );
        $text = str_replace(['ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ'], 'u', $text);
        $text = str_replace(['ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ'], 'y', $text);
        $text = str_replace('đ', 'd', $text);
        $text = preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
        $text = trim((string) $text, '-');
        return strtolower($text);
    }

    // ----------------------------------------
    // Count all blogs (for dashboard)
    // ----------------------------------------

    public function countAll()
    {
        return $this->blogsModel->countAll();
    }
}
