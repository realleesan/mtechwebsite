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

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->blogsModel = new BlogsModel();
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
        $categories = $this->blogsModel->getAllBlogCategories();

        $this->view('blogs/create', [
            'title'      => 'Tạo tin tức mới - Admin MTech',
            'page'       => 'blogs',
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

        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/blogs/create');
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

        $categories = $this->blogsModel->getAllBlogCategories();

        $this->view('blogs/edit', [
            'title'      => 'Chỉnh sửa tin tức - Admin MTech',
            'page'       => 'blogs',
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

        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/blogs/edit/' . $id);
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

        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/blogs');
    }

    // ----------------------------------------
    // Count all blogs (for dashboard)
    // ----------------------------------------

    public function countAll()
    {
        try {
            $db = getDBConnection();
            $stmt = $db->query("SELECT COUNT(*) FROM blogs WHERE status = 1");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('BlogsController::countAll() - ' . $e->getMessage());
            return 0;
        }
    }
}
