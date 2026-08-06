<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/AwardsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AwardsController extends BaseController
{
    private $model;

    /** Upload directory — lưu trong admin site, DB lưu URL tuyệt đối */
    private const UPLOAD_DIR     = '/assets/uploads/awards/';
    private const ADMIN_BASE_URL = 'https://adminmtechjsc.gt.tc';
    private const MAX_FILE_SIZE  = 2 * 1024 * 1024; // 2MB
    private const ALLOWED_TYPES  = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new AwardsModel();
    }

    // ----------------------------------------
    // Index
    // ----------------------------------------

    public function index()
    {
        $awards = $this->model->getAll();
        $this->view('awards/index', [
            'title'  => 'Quản lý Giải thưởng - Admin MTech',
            'page'   => 'awards',
            'awards' => $awards,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // Create
    // ----------------------------------------

    public function create()
    {
        $this->view('awards/create', [
            'title' => 'Thêm giải thưởng - Admin MTech',
            'page'  => 'award.create',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards/create');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên giải thưởng';
            $this->redirect('/awards/create');
            return;
        }

        $data = [
            'name'        => $name,
            'certificate' => trim($_POST['certificate'] ?? ''),
            'status'      => (int)($_POST['status']     ?? 1),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'image'       => '',
        ];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->handleImageUpload($_FILES['image']);
            if ($imagePath === false) {
                $_SESSION['error'] = 'Ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF, WEBP và tối đa 2MB';
                $this->redirect('/awards/create');
                return;
            }
            $data['image'] = self::ADMIN_BASE_URL . self::UPLOAD_DIR . $imagePath;
        }

        $id = $this->model->create($data);
        if ($id) {
            $this->model->reorderAwards($id, $data['sort_order']);
            $this->model->normalizeOrders();
            $_SESSION['success'] = 'Thêm giải thưởng thành công!';
            $this->redirect('/awards');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/awards/create');
        }
    }

    // ----------------------------------------
    // Edit
    // ----------------------------------------

    public function edit($id)
    {
        $award = $this->model->getById($id);
        if (!$award) {
            $_SESSION['error'] = 'Không tìm thấy giải thưởng';
            $this->redirect('/awards');
            return;
        }
        $this->view('awards/edit', [
            'title' => 'Chỉnh sửa giải thưởng - Admin MTech',
            'page'  => 'award.edit',
            'award' => $award,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards/edit/' . $id);
            return;
        }

        $award = $this->model->getById($id);
        if (!$award) {
            $_SESSION['error'] = 'Không tìm thấy giải thưởng';
            $this->redirect('/awards');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên giải thưởng';
            $this->redirect('/awards/edit/' . $id);
            return;
        }

        $data = [
            'name'        => $name,
            'certificate' => trim($_POST['certificate'] ?? ''),
            'status'      => (int)($_POST['status']     ?? 1),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        ];

        // Handle image upload (chỉ cập nhật nếu có file mới)
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->handleImageUpload($_FILES['image']);
            if ($imagePath === false) {
                $_SESSION['error'] = 'Ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF, WEBP và tối đa 2MB';
                $this->redirect('/awards/edit/' . $id);
                return;
            }
            // Xóa ảnh cũ nếu có
            $this->deleteOldImage($award['image'] ?? '');
            $data['image'] = self::ADMIN_BASE_URL . self::UPLOAD_DIR . $imagePath;
        } elseif (!empty($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            // Admin bấm "Xóa ảnh" mà không upload ảnh mới → xóa ảnh cũ, set image = ''
            $this->deleteOldImage($award['image'] ?? '');
            $data['image'] = '';
        }

        if ($this->model->update($id, $data)) {
            $this->model->reorderAwards($id, $data['sort_order'], $award['sort_order']);
            $this->model->normalizeOrders();
            $_SESSION['success'] = 'Cập nhật giải thưởng thành công!';
            $this->redirect('/awards');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/awards/edit/' . $id);
        }
    }

    // ----------------------------------------
    // Delete — Soft delete (chuyển vào thùng rác)
    // ----------------------------------------

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards');
            return;
        }

        $award = $this->model->getById($id);
        if (!$award) {
            $_SESSION['error'] = 'Không tìm thấy giải thưởng';
            $this->redirect('/awards');
            return;
        }

        if ($this->model->delete($id)) {
            $_SESSION['success'] = 'Đã chuyển giải thưởng vào thùng rác';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }

        $this->redirect('/awards');
    }

    // ----------------------------------------
    // Trash — Danh sách đã xóa
    // ----------------------------------------

    public function trash()
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $awards     = $this->model->getTrashed($perPage, $offset);
        $total      = $this->model->countTrashed();
        $totalPages = (int)ceil($total / $perPage);

        $this->view('awards/trash', [
            'title'       => 'Thùng rác - Giải thưởng - Admin MTech',
            'page'        => 'awards',
            'awards'      => $awards,
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
            $this->redirect('/awards/trash');
            return;
        }

        if ($this->model->restore((int)$id)) {
            $_SESSION['success'] = 'Đã khôi phục giải thưởng thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi khôi phục';
        }

        $this->redirect('/awards/trash');
    }

    // ----------------------------------------
    // Hard Delete — Xóa vĩnh viễn
    // ----------------------------------------

    public function hardDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards/trash');
            return;
        }

        $award = $this->model->getById($id);
        if (!$award) {
            $_SESSION['error'] = 'Không tìm thấy giải thưởng';
            $this->redirect('/awards/trash');
            return;
        }

        if ($this->model->hardDelete((int)$id)) {
            $this->deleteOldImage($award['image'] ?? '');
            $this->model->normalizeOrders();
            $_SESSION['success'] = 'Đã xóa vĩnh viễn giải thưởng';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa vĩnh viễn';
        }

        $this->redirect('/awards/trash');
    }

    // ----------------------------------------
    // Helpers
    // ----------------------------------------

    /**
     * Xử lý upload file ảnh giải thưởng.
     * @return string|false  Tên file nếu thành công, false nếu thất bại
     */
    private function handleImageUpload(array $file): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return false;
        }

        // Validate MIME type thực sự
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            return false;
        }

        $uploadDir = __DIR__ . '/../../assets/uploads/awards/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename  = 'award_' . uniqid('', true) . '.' . $extension;
        $filepath  = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }

    /**
     * Xóa file ảnh cũ khỏi disk.
     * Chỉ xóa nếu là ảnh do admin upload (URL tuyệt đối của admin site).
     */
    private function deleteOldImage(string $imagePath): void
    {
        if (empty($imagePath)) return;

        if (strpos($imagePath, self::ADMIN_BASE_URL) === false) return;

        $filename = basename($imagePath);
        $fullPath = __DIR__ . '/../../assets/uploads/awards/' . $filename;

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}
