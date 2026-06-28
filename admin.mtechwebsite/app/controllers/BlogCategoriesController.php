<?php
/**
 * BlogCategoriesController - Quản lý Danh mục Tin tức
 */

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/BlogsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class BlogCategoriesController extends BaseController
{
    private $blogsModel;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->blogsModel = new BlogsModel();
    }

    // ----------------------------------------
    // Index - Danh sách danh mục
    // ----------------------------------------

    public function index()
    {
        $categories = $this->blogsModel->getAdminBlogCategories();

        $this->view('blog-categories/index', [
            'title'      => 'Quản lý Danh mục Tin tức - Admin MTech',
            'page'       => 'blog-categories',
            'categories' => $categories,
            'admin'      => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Create - Form tạo danh mục mới
    // ----------------------------------------

    public function create()
    {
        // Get existing categories for parent dropdown
        $categories = $this->blogsModel->getCategoriesForMultiSelect();

        $this->view('blog-categories/create', [
            'title' => 'Thêm danh mục tin tức - Admin MTech',
            'page'  => 'blog.category.create',
            'categories' => $categories,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Store - Lưu danh mục mới
    // ----------------------------------------

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/blogs/categories');
            return;
        }

        // Validate required fields
        $required = ['name', 'slug'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc";
                $this->redirect('/blogs/categories/create');
                return;
            }
        }

        try {
            $db = getDBConnection();
            
            // Check if slug already exists
            $stmt = $db->prepare("SELECT id FROM blog_categories WHERE slug = ?");
            $stmt->execute([$_POST['slug']]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Slug đã tồn tại, vui lòng chọn slug khác';
                $this->redirect('/blogs/categories/create');
                return;
            }

            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            // Calculate level based on parent chain
            $level = $this->blogsModel->calculateCategoryLevel($parentId);

            // Insert new category with parent_id support
            $stmt = $db->prepare("
                INSERT INTO blog_categories (parent_id, name, slug, status, show_in_menu, sort_order, level, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $parentId,
                $_POST['name'],
                $_POST['slug'],
                $_POST['status'] ?? 1,
                isset($_POST['show_in_menu']) ? 1 : 0,
                $_POST['sort_order'] ?? 0,
                $level
            ]);
            
            $newCategoryId = $db->lastInsertId();
            
            // Update level recursively if has children (shouldn't happen for new category)
            $this->blogsModel->updateCategoryLevelRecursive($newCategoryId);

            $_SESSION['success'] = 'Thêm danh mục thành công';
            $this->redirect('/blogs/categories');

        } catch (PDOException $e) {
            error_log('BlogCategoriesController::store() - ' . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm danh mục';
            $this->redirect('/blogs/categories/create');
        }
    }

    // ----------------------------------------
    // Edit - Form chỉnh sửa danh mục
    // ----------------------------------------

    public function edit($id)
    {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$category) {
                $_SESSION['error'] = 'Không tìm thấy danh mục';
                $this->redirect('/blogs/categories');
                return;
            }

            // Get all categories for parent dropdown (exclude current and its descendants)
            $categories = $this->blogsModel->getCategoriesForMultiSelect();
            
            // Exclude current category and all its descendants recursively
            $availableParents = $this->filterOutCategoryAndDescendants($categories, $id);

            $this->view('blog-categories/edit', [
                'title'    => 'Chỉnh sửa danh mục - Admin MTech',
                'page'     => 'blog.category.edit',
                'category' => $category,
                'categories' => $availableParents,
                'admin'    => AuthMiddleware::getAdmin(),
            ]);

        } catch (PDOException $e) {
            error_log('BlogCategoriesController::edit() - ' . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra';
            $this->redirect('/blogs/categories');
        }
    }
    
    /**
     * Recursively filter out a category and all its descendants from hierarchy
     */
    private function filterOutCategoryAndDescendants(&$categories, $excludeId)
    {
        $result = [];
        foreach ($categories as $cat) {
            if ($cat['id'] == $excludeId) {
                // Skip the excluded category
                continue;
            }
            
            // Recursively filter children
            if (!empty($cat['children'])) {
                $cat['children'] = $this->filterOutCategoryAndDescendants($cat['children'], $excludeId);
            }
            
            $result[] = $cat;
        }
        return $result;
    }

    // ----------------------------------------
    // Update - Cập nhật danh mục
    // ----------------------------------------

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/blogs/categories');
            return;
        }

        // Validate required fields
        $required = ['name', 'slug'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc";
                $this->redirect('/blogs/categories/edit/' . $id);
                return;
            }
        }

        try {
            $db = getDBConnection();
            
            // Check if slug already exists (exclude current record)
            $stmt = $db->prepare("SELECT id FROM blog_categories WHERE slug = ? AND id != ?");
            $stmt->execute([$_POST['slug'], $id]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Slug đã tồn tại, vui lòng chọn slug khác';
                $this->redirect('/blogs/categories/edit/' . $id);
                return;
            }

            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            // Calculate level based on parent chain (will update if parent changed)
            $level = $this->blogsModel->calculateCategoryLevel($parentId);

            // Update category with parent_id support
            $stmt = $db->prepare("
                UPDATE blog_categories 
                SET parent_id = ?, name = ?, slug = ?, status = ?, show_in_menu = ?, sort_order = ?, level = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([
                $parentId,
                $_POST['name'],
                $_POST['slug'],
                $_POST['status'] ?? 1,
                isset($_POST['show_in_menu']) ? 1 : 0,
                $_POST['sort_order'] ?? 0,
                $level,
                $id
            ]);
            
            // Update level recursively for all children if parent_id changed
            $this->blogsModel->updateCategoryLevelRecursive($id);

            $_SESSION['success'] = 'Cập nhật danh mục thành công';
            $this->redirect('/blogs/categories');

        } catch (PDOException $e) {
            error_log('BlogCategoriesController::update() - ' . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật danh mục';
            $this->redirect('/blogs/categories/edit/' . $id);
        }
    }

    // ----------------------------------------
    // Delete - Xóa danh mục
    // ----------------------------------------

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/blogs/categories');
            return;
        }

        try {
            $db = getDBConnection();
            
            // Check if category has children
            $stmt = $db->prepare("SELECT COUNT(*) FROM blog_categories WHERE parent_id = ?");
            $stmt->execute([$id]);
            $childCount = $stmt->fetchColumn();
            
            if ($childCount > 0) {
                $_SESSION['error'] = "Không thể xóa danh mục này vì còn {$childCount} danh mục con. Vui lòng xóa danh mục con trước.";
                $this->redirect('/blogs/categories');
                return;
            }
            
            // Check if category has blogs via blog_category_map
            $stmt = $db->prepare("SELECT COUNT(*) FROM blog_category_map WHERE category_id = ?");
            $stmt->execute([$id]);
            $blogCount = $stmt->fetchColumn();

            if ($blogCount > 0) {
                $_SESSION['error'] = "Không thể xóa danh mục này vì còn {$blogCount} tin tức đang sử dụng";
                $this->redirect('/blogs/categories');
                return;
            }

            // Delete category
            $stmt = $db->prepare("DELETE FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Xóa danh mục thành công';
            $this->redirect('/blogs/categories');

        } catch (PDOException $e) {
            error_log('BlogCategoriesController::delete() - ' . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục';
            $this->redirect('/blogs/categories');
        }
    }
}