<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/FooterModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class FooterController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new FooterModel();
    }

    public function index()
    {
        $footer = $this->model->getFooterDataForAdmin();
        $this->view('footer/index', [
            'title'  => 'Quản lý Footer - Admin MTech',
            'page'   => 'footer',
            'footer' => $footer,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    public function edit($id = null)
    {
        if (!$id) {
            $this->redirect('/footer');
            return;
        }
        
        $link = $this->model->getLinkById($id);
        if (!$link) {
            $_SESSION['error'] = 'Không tìm thấy liên kết này';
            $this->redirect('/footer');
            return;
        }
        
        $this->view('footer/edit', [
            'title'  => 'Chỉnh sửa Footer Link - Admin MTech',
            'page'   => 'footer-edit',
            'link'   => $link,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/footer');
            return;
        }
        
        $link = $this->model->getLinkById($id);
        if (!$link) {
            $_SESSION['error'] = 'Không tìm thấy liên kết này';
            $this->redirect('/footer');
            return;
        }
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($title) || empty($url)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ tiêu đề và URL';
            $this->redirect("/footer/edit/$id");
            return;
        }
        
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = 'URL không hợp lệ. Vui lòng nhập URL đầy đủ (http:// hoặc https://)';
            $this->redirect("/footer/edit/$id");
            return;
        }
        
        $data = [
            'title' => $title,
            'url' => $url,
            'sort_order' => $sortOrder,
            'is_active' => $isActive
        ];
        
        if ($this->model->updateLink($id, $data)) {
            $_SESSION['success'] = 'Cập nhật liên kết thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật liên kết';
        }
        
        $this->redirect('/footer');
    }

    /**
     * Xóa footer link (soft delete)
     */
    public function delete($id = null)
    {
        if (!$id) {
            $this->redirect('/footer');
            return;
        }
        
        if ($this->model->deleteLink($id)) {
            $_SESSION['success'] = 'Xóa liên kết thành công';
        } else {
            $_SESSION['error'] = 'Không tìm thấy liên kết này';
        }
        
        $this->redirect('/footer');
    }

    public function add()
    {
        $this->view('footer/add', [
            'title'  => 'Thêm Footer Link - Admin MTech',
            'page'   => 'footer-add',
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/footer');
            return;
        }
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($title) || empty($url)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ tiêu đề và URL';
            $this->redirect('/footer/add');
            return;
        }
        
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = 'URL không hợp lệ. Vui lòng nhập URL đầy đủ (http:// hoặc https://)';
            $this->redirect('/footer/add');
            return;
        }
        
        $data = [
            'title' => $title,
            'url' => $url,
            'sort_order' => $sortOrder,
            'is_active' => $isActive
        ];
        
        if ($this->model->addLink($data)) {
            $_SESSION['success'] = 'Thêm liên kết thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm liên kết';
        }
        
        $this->redirect('/footer');
    }

    /**
     * Hiển thị trang thùng rác
     */
    public function trash()
    {
        $limit = 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        
        $links = $this->model->getTrashed($limit, $offset);
        $total = $this->model->countTrashed();
        $totalPages = ceil($total / $limit);
        
        $this->view('footer/trash', [
            'title'  => 'Thùng rác Footer - Admin MTech',
            'page'   => 'footer-trash',
            'links'  => $links,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'limit' => $limit,
                'total_items' => $total
            ],
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    /**
     * Khôi phục link từ thùng rác
     */
    public function restore($id = null)
    {
        if (!$id) {
            $this->redirect('/footer/trash');
            return;
        }
        
        if ($this->model->restore($id)) {
            $_SESSION['success'] = 'Khôi phục liên kết thành công';
        } else {
            $_SESSION['error'] = 'Không tìm thấy liên kết này';
        }
        
        $this->redirect('/footer/trash');
    }

    /**
     * Xóa vĩnh viễn link
     */
    public function hardDelete($id = null)
    {
        if (!$id) {
            $this->redirect('/footer/trash');
            return;
        }
        
        if ($this->model->hardDelete($id)) {
            $_SESSION['success'] = 'Xóa vĩnh viễn liên kết thành công';
        } else {
            $_SESSION['error'] = 'Không tìm thấy liên kết này';
        }
        
        $this->redirect('/footer/trash');
    }
}
