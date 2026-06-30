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
        $hierarchyCategories = $this->blogsModel->getCategoriesForMultiSelect();
        
        // Flatten categories for dropdown - preserve level from database
        $flatCategories = [];
        $this->flattenCategoriesForSelect($hierarchyCategories, $flatCategories);

        // Get parent info if parent_id exists in query string
        $parentId = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : null;
        $initialLevel = 1;
        
        if ($parentId) {
            foreach ($flatCategories as $cat) {
                if ($cat['id'] == $parentId) {
                    $initialLevel = ($cat['level'] ?? 1) + 1;
                    break;
                }
            }
        }

        $this->view('blog-categories/create', [
            'title' => 'Thêm danh mục tin tức - Admin MTech',
            'page'  => 'blog.category.create',
            'categories' => $flatCategories,
            'initialLevel' => $initialLevel,
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
            $hierarchyCategories = $this->blogsModel->getCategoriesForMultiSelect();
            
            // Exclude current category and all its descendants recursively
            $availableParents = $this->filterOutCategoryAndDescendants($hierarchyCategories, $id);
            
            // Flatten categories for dropdown - preserve level from database
            $flatCategories = [];
            $this->flattenCategoriesForSelect($availableParents, $flatCategories);
            
            // Get parent info if parent_id exists
            $parentInfo = null;
            if ($category['parent_id']) {
                foreach ($flatCategories as $cat) {
                    if ($cat['id'] == $category['parent_id']) {
                        $parentInfo = $cat;
                        break;
                    }
                }
            }
            
            // For root categories (level 1), don't show parent selector
            $showParentSelector = ($category['level'] ?? 1) > 1;

            $this->view('blog-categories/edit', [
                'title'    => 'Chỉnh sửa danh mục - Admin MTech',
                'page'     => 'blog.category.edit',
                'category' => $category,
                'categories' => $flatCategories,
                'parentInfo' => $parentInfo,
                'showParentSelector' => $showParentSelector,
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

    /**
     * Flatten hierarchy into flat array with display_name (prefix with — for indentation)
     */
    private function flattenCategoriesForSelect($categories, &$result = [], $prefix = '')
    {
        foreach ($categories as $cat) {
            $cat['display_name'] = $prefix . $cat['name'];
            $result[] = $cat;
            if (!empty($cat['children'])) {
                $this->flattenCategoriesForSelect($cat['children'], $result, $prefix . '— ');
            }
        }
    }
}