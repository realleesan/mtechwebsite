<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/ClientLogosModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ClientLogosController extends BaseController
{
    private $model;

    /** Upload directory — lưu trong admin site, DB lưu URL tuyệt đối */
    private const UPLOAD_DIR     = '/assets/uploads/client-logos/';
    private const ADMIN_BASE_URL = 'https://admin.mtechjsc.com';
    private const MAX_FILE_SIZE  = 2 * 1024 * 1024; // 2MB
    private const ALLOWED_TYPES  = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

    private function getAdminBaseUrl(): string
    {
        return env('ADMIN_BASE_URL', self::ADMIN_BASE_URL);
    }

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new ClientLogosModel();
    }

    // ----------------------------------------
    // Index
    // ----------------------------------------

    public function index()
    {
        $logos = $this->model->getAll();
        $this->view('client-logos/index', [
            'title' => 'Quản lý Logo đối tác - Admin MTech',
            'page'  => 'client.logos',
            'logos' => $logos,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Create
    // ----------------------------------------

    public function create()
    {
        $this->view('client-logos/create', [
            'title' => 'Thêm logo đối tác - Admin MTech',
            'page'  => 'client.logo.create',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/client-logos/create');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên đối tác';
            $this->redirect('/client-logos/create');
            return;
        }

        $data = [
            'name'       => $name,
            'url'        => trim($_POST['url']        ?? ''),
            'status'     => (int)($_POST['status']     ?? 1),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'logo'       => '',
        ];

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $logoPath = $this->handleLogoUpload($_FILES['logo']);
            if ($logoPath === false) {
                $_SESSION['error'] = 'Ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, SVG, WEBP và tối đa 2MB';
                $this->redirect('/client-logos/create');
                return;
            }
            $data['logo'] = $this->getAdminBaseUrl() . self::UPLOAD_DIR . $logoPath;
        }

        $id = $this->model->create($data);
        if ($id) {
            // Điều chỉnh thứ tự tự động
            $this->model->reorderLogos($id, $data['sort_order']);
            // Normalize lại tất cả thứ tự từ 1 đến n
            $this->model->normalizeOrders();
            $_SESSION['success'] = 'Thêm logo đối tác thành công!';
            $this->redirect('/client-logos');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/client-logos/create');
        }
    }

    // ----------------------------------------
    // Edit
    // ----------------------------------------

    public function edit($id)
    {
        $logo = $this->model->getById($id);
        if (!$logo) {
            $_SESSION['error'] = 'Không tìm thấy logo';
            $this->redirect('/client-logos');
            return;
        }
        $this->view('client-logos/edit', [
            'title' => 'Chỉnh sửa logo - Admin MTech',
            'page'  => 'client.logo.edit',
            'logo'  => $logo,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/client-logos/edit/' . $id);
            return;
        }

        $logo = $this->model->getById($id);
        if (!$logo) {
            $_SESSION['error'] = 'Không tìm thấy logo';
            $this->redirect('/client-logos');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên đối tác';
            $this->redirect('/client-logos/edit/' . $id);
            return;
        }

        $data = [
            'name'       => $name,
            'url'        => trim($_POST['url']        ?? ''),
            'status'     => (int)($_POST['status']     ?? 1),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];

        // Handle logo upload (chỉ cập nhật nếu có file mới)
        if (!empty($_FILES['logo']['name'])) {
            $logoPath = $this->handleLogoUpload($_FILES['logo']);
            if ($logoPath === false) {
                $_SESSION['error'] = 'Ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, SVG, WEBP và tối đa 2MB';
                $this->redirect('/client-logos/edit/' . $id);
                return;
            }
            // Xóa ảnh cũ nếu có
            $this->deleteOldLogo($logo['logo'] ?? '');
            $data['logo'] = $this->getAdminBaseUrl() . self::UPLOAD_DIR . $logoPath;
        }

        if ($this->model->update($id, $data)) {
            // Điều chỉnh thứ tự tự động (truyền thứ tự cũ để so sánh)
            $this->model->reorderLogos($id, $data['sort_order'], $logo['sort_order']);
            // Normalize lại tất cả thứ tự từ 1 đến n
            $this->model->normalizeOrders();
            $_SESSION['success'] = 'Cập nhật logo đối tác thành công!';
            $this->redirect('/client-logos');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/client-logos/edit/' . $id);
        }
    }

    // ----------------------------------------
    // Delete — Soft delete (chuyển vào thùng rác)
    // ----------------------------------------

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/client-logos');
            return;
        }

        $logo = $this->model->getById($id);
        if (!$logo) {
            $_SESSION['error'] = 'Không tìm thấy logo';
            $this->redirect('/client-logos');
            return;
        }

        if ($this->model->delete($id)) {
            $_SESSION['success'] = 'Đã chuyển logo đối tác vào thùng rác';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }

        $this->redirect('/client-logos');
    }

    // ----------------------------------------
    // Trash — Danh sách đã xóa
    // ----------------------------------------

    public function trash()
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $logos      = $this->model->getTrashed($perPage, $offset);
        $total      = $this->model->countTrashed();
        $totalPages = (int)ceil($total / $perPage);

        $this->view('client-logos/trash', [
            'title'       => 'Thùng rác - Logo đối tác - Admin MTech',
            'page'        => 'client.logos',
            'logos'       => $logos,
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Restore — Khôi phục từ thùng rác
    // ----------------------------------------

    public function restore($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/client-logos/trash');
            return;
        }

        if ($this->model->restore((int)$id)) {
            $_SESSION['success'] = 'Đã khôi phục logo đối tác thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi khôi phục';
        }

        $this->redirect('/client-logos/trash');
    }

    // ----------------------------------------
    // Hard Delete — Xóa vĩnh viễn
    // ----------------------------------------

    public function hardDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/client-logos/trash');
            return;
        }

        $logo = $this->model->getById($id);
        if (!$logo) {
            $_SESSION['error'] = 'Không tìm thấy logo';
            $this->redirect('/client-logos/trash');
            return;
        }

        if ($this->model->hardDelete((int)$id)) {
            $this->deleteOldLogo($logo['logo'] ?? '');
            $this->model->normalizeOrders();
            $_SESSION['success'] = 'Đã xóa vĩnh viễn logo đối tác';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa vĩnh viễn';
        }

        $this->redirect('/client-logos/trash');
    }

    // ----------------------------------------
    // Helpers
    // ----------------------------------------

    /**
     * Xử lý upload file logo.
     * @return string|false  Tên file nếu thành công, false nếu thất bại
     */
    private function handleLogoUpload(array $file): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return false;
        }

        // Validate MIME type thực sự (không tin vào extension)
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        // SVG không detect được bằng finfo, fallback sang declared type
        if ($mimeType === 'text/html' || $mimeType === 'text/plain') {
            $mimeType = $file['type'];
        }

        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            return false;
        }

        $uploadDir = __DIR__ . '/../../assets/uploads/client-logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename  = 'logo_' . uniqid('', true) . '.' . $extension;
        $filepath  = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }

    /**
     * Xóa file logo cũ khỏi disk.
     */
    private function deleteOldLogo(string $logoPath): void
    {
        if (empty($logoPath)) return;

        // Chỉ xóa file nếu là ảnh do admin upload
        if (strpos($logoPath, $this->getAdminBaseUrl()) === false &&
            strpos($logoPath, 'adminmtechjsc.gt.tc') === false &&
            strpos($logoPath, self::UPLOAD_DIR) === false) return;

        $filename = basename($logoPath);
        $fullPath = __DIR__ . '/../../assets/uploads/client-logos/' . $filename;

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}
