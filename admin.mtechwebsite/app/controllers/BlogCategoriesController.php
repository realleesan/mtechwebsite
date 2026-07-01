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

    // ----------------------------------------
    // Update - Cập nhật danh mục
    // ----------------------------------------

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/blogs/categories');
            return;
        }

        try {
            $db = getDBConnection();
            
            // Get current category
            $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$category) {
                $_SESSION['error'] = 'Không tìm thấy danh mục';
                $this->redirect('/blogs/categories');
                return;
            }
            
            // Validate required fields
            $required = ['name', 'slug'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc";
                    $this->redirect("/blogs/categories/edit/{$id}");
                    return;
                }
            }
            
            // Check if slug already exists (excluding current category)
            $stmt = $db->prepare("SELECT id FROM blog_categories WHERE slug = ? AND id != ?");
            $stmt->execute([$_POST['slug'], $id]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Slug đã tồn tại, vui lòng chọn slug khác';
                $this->redirect("/blogs/categories/edit/{$id}");
                return;
            }
            
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            // Prevent setting a category as parent of itself or its parent chain
            if ($parentId == $id) {
                $_SESSION['error'] = 'Không thể đặt danh mục này làm danh mục cha của chính nó';
                $this->redirect("/blogs/categories/edit/{$id}");
                return;
            }
            
            // Calculate level based on parent chain
            $level = $this->blogsModel->calculateCategoryLevel($parentId);
            
            // Update category
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
            
            // Update level recursively for all children
            $this->blogsModel->updateCategoryLevelRecursive($id);
            
            $_SESSION['success'] = 'Cập nhật danh mục thành công';
            $this->redirect('/blogs/categories');
            
        } catch (PDOException $e) {
            error_log('BlogCategoriesController::update() - ' . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật danh mục';
            $this->redirect("/blogs/categories/edit/{$id}");
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
            
            // Get category
            $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$category) {
                $_SESSION['error'] = 'Không tìm thấy danh mục';
                $this->redirect('/blogs/categories');
                return;
            }
            
            // Check if category has children
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM blog_categories WHERE parent_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                $_SESSION['error'] = 'Không thể xóa danh mục có danh mục con. Vui lòng xóa danh mục con trước.';
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

    // ----------------------------------------
    // API - Get All Categories (AJAX)
    // ----------------------------------------

    public function getCategories()
    {
        try {
            $categories = $this->blogsModel->getAdminBlogCategories();
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $categories
            ]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Lỗi khi tải danh mục'
            ]);
            exit;
        }
    }

    // ----------------------------------------
    // API - Store Category (AJAX)
    // ----------------------------------------

    public function storeAjax()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            exit;
        }

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['name']) || empty($input['slug'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Name and slug are required']);
                exit;
            }

            $db = getDBConnection();
            
            // Check if slug already exists
            $stmt = $db->prepare("SELECT id FROM blog_categories WHERE slug = ?");
            $stmt->execute([$input['slug']]);
            if ($stmt->fetch()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Slug already exists']);
                exit;
            }

            $parentId = !empty($input['parent_id']) ? (int)$input['parent_id'] : null;
            $level = $this->blogsModel->calculateCategoryLevel($parentId);

            $stmt = $db->prepare("
                INSERT INTO blog_categories (parent_id, name, slug, status, show_in_menu, sort_order, level, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $parentId,
                $input['name'],
                $input['slug'],
                $input['status'] ?? 1,
                $input['show_in_menu'] ?? 0,
                $input['sort_order'] ?? 0,
                $level
            ]);
            
            $newCategoryId = $db->lastInsertId();
            $this->blogsModel->updateCategoryLevelRecursive($newCategoryId);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully',
                'id' => $newCategoryId,
                'parent_id' => $parentId
            ]);
            exit;

        } catch (Exception $e) {
            error_log('BlogCategoriesController::storeAjax() - ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error creating category']);
            exit;
        }
    }

    // ----------------------------------------
    // API - Update Category (AJAX)
    // ----------------------------------------

    public function updateAjax($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $db = getDBConnection();
            
            // Get current category
            $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$category) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Category not found']);
                exit;
            }
            
            if (empty($input['name']) || empty($input['slug'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Name and slug are required']);
                exit;
            }
            
            // Check if slug already exists
            $stmt = $db->prepare("SELECT id FROM blog_categories WHERE slug = ? AND id != ?");
            $stmt->execute([$input['slug'], $id]);
            if ($stmt->fetch()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Slug already exists']);
                exit;
            }
            
            $parentId = !empty($input['parent_id']) ? (int)$input['parent_id'] : null;
            
            if ($parentId == $id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Cannot set category as parent of itself']);
                exit;
            }
            
            $level = $this->blogsModel->calculateCategoryLevel($parentId);
            
            $stmt = $db->prepare("
                UPDATE blog_categories 
                SET parent_id = ?, name = ?, slug = ?, status = ?, show_in_menu = ?, sort_order = ?, level = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([
                $parentId,
                $input['name'],
                $input['slug'],
                $input['status'] ?? 1,
                $input['show_in_menu'] ?? 0,
                $input['sort_order'] ?? 0,
                $level,
                $id
            ]);
            
            $this->blogsModel->updateCategoryLevelRecursive($id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);
            exit;

        } catch (Exception $e) {
            error_log('BlogCategoriesController::updateAjax() - ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error updating category']);
            exit;
        }
    }

    // ----------------------------------------
    // API - Delete Category (AJAX)
    // ----------------------------------------

    public function deleteAjax($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            exit;
        }

        try {
            $db = getDBConnection();
            
            // Get category
            $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$category) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Category not found']);
                exit;
            }
            
            // Check if category has children
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM blog_categories WHERE parent_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Cannot delete category with children']);
                exit;
            }
            
            // Delete category
            $stmt = $db->prepare("DELETE FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Category deleted successfully',
                'id' => $id
            ]);
            exit;

        } catch (Exception $e) {
            error_log('BlogCategoriesController::deleteAjax() - ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error deleting category']);
            exit;
        }
    }
}