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

    // =================================================================
    // SOCIAL LINKS MANAGEMENT
    // =================================================================

    /**
     * Hiển thị danh sách mạng xã hội
     */
    public function social()
    {
        $socialLinks = $this->model->getAllSocialLinks();
        $this->view('footer/social', [
            'title'       => 'Quản lý Mạng xã hội - Admin MTech',
            'page'        => 'footer-social',
            'socialLinks' => $socialLinks,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    /**
     * Trang chỉnh sửa một mạng xã hội
     */
    public function editSocial($platform = null)
    {
        if (!$platform) {
            $this->redirect('/footer/social');
            return;
        }

        $socialLink = $this->model->getSocialLinkByPlatform($platform);
        if (!$socialLink) {
            $_SESSION['error'] = 'Không tìm thấy mạng xã hội này';
            $this->redirect('/footer/social');
            return;
        }

        $this->view('footer/edit-social', [
            'title'      => 'Chỉnh sửa ' . ucfirst($platform) . ' - Admin MTech',
            'page'       => 'footer-social-edit',
            'socialLink' => $socialLink,
            'admin'      => AuthMiddleware::getAdmin(),
        ]);
    }

    /**
     * Xử lý cập nhật mạng xã hội
     */
    public function updateSocial()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/footer/social');
            return;
        }

        $platform = $_POST['platform'] ?? '';
        $url = trim($_POST['url'] ?? '');
        $isVisible = isset($_POST['is_visible']) ? 1 : 0;

        if (empty($platform)) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ';
            $this->redirect('/footer/social');
            return;
        }

        $data = [
            'url' => !empty($url) ? $url : null,
            'is_visible' => $isVisible
        ];

        if ($this->model->updateSocialLink($platform, $data)) {
            $_SESSION['success'] = 'Cập nhật ' . ucfirst($platform) . ' thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật';
        }

        $this->redirect('/footer/social');
    }

    /**
     * Bật/tắt hàng loạt hiển thị mạng xã hội
     */
    public function bulkToggleSocial()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/footer/social');
            return;
        }

        $platforms = explode(',', $_POST['platforms'] ?? '');
        $isVisible = (int) ($_POST['is_visible'] ?? 0);

        $successCount = 0;
        foreach ($platforms as $platform) {
            $platform = trim($platform);
            if (empty($platform)) continue;

            $current = $this->model->getSocialLinkByPlatform($platform);
            if ($current) {
                $data = [
                    'url' => $current['url'],
                    'is_visible' => $isVisible
                ];
                if ($this->model->updateSocialLink($platform, $data)) {
                    $successCount++;
                }
            }
        }

        $_SESSION['success'] = "Đã cập nhật trạng thái cho $successCount mạng xã hội";
        $this->redirect('/footer/social');
    }

    /**
     * Xóa trắng URL hàng loạt
     */
    public function clearSocialUrls()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/footer/social');
            return;
        }

        $platforms = explode(',', $_POST['platforms'] ?? '');
        
        $successCount = 0;
        foreach ($platforms as $platform) {
            $platform = trim($platform);
            if (empty($platform)) continue;

            $data = [
                'url' => null,
                'is_visible' => 0
            ];
            if ($this->model->updateSocialLink($platform, $data)) {
                $successCount++;
            }
        }

        $_SESSION['success'] = "Đã xóa URL của $successCount mạng xã hội";
        $this->redirect('/footer/social');
    }

    // =================================================================
    // FOOTER SETTINGS
    // =================================================================

    /**
     * Trang cài đặt chung footer
     */
    public function settings()
    {
        $settings = $this->model->getSettings();
        $this->view('footer/settings', [
            'title'    => 'Cài đặt Footer - Admin MTech',
            'page'     => 'footer-settings',
            'settings' => $settings,
            'admin'    => AuthMiddleware::getAdmin(),
        ]);
    }

    /**
     * Cập nhật cài đặt footer
     */
    public function updateSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/footer/settings');
            return;
        }

        $title = trim($_POST['useful_links_title'] ?? '');
        
        if (empty($title)) {
            $_SESSION['error'] = 'Tiêu đề không được để trống';
            $this->redirect('/footer/settings');
            return;
        }

        if ($this->model->updateUsefulLinksTitle($title)) {
            $_SESSION['success'] = 'Cập nhật cài đặt thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật';
        }

        $this->redirect('/footer/settings');
    }
}
