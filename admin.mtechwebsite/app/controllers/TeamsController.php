<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/TeamsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class TeamsController extends BaseController
{
    private $model;

<<<<<<< HEAD
=======
    /** Upload directory — lưu trong admin site, DB lưu URL tuyệt đối */
    private const UPLOAD_DIR     = '/assets/uploads/teams/';
    private const ADMIN_BASE_URL = 'https://admin.truongvinalogistics.com.vn';
    private const MAX_FILE_SIZE  = 2 * 1024 * 1024;
    private const ALLOWED_TYPES  = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new TeamsModel();
    }

    public function index()
    {
        $teams = $this->model->getAll();
<<<<<<< HEAD

        $this->view('teams/index', [
            'title' => 'Quản lý Đội ngũ - Admin MTech',
            'page'  => 'teams',
            'teams' => $teams,
            'admin' => AuthMiddleware::getAdmin(),
=======
        $this->view('teams/index', [
            'title'        => 'Quản lý Đội ngũ - Admin MTech',
            'page'         => 'teams',
            'teams'        => $teams,
            'trashedCount' => $this->model->countTrashed(),
            'admin'        => AuthMiddleware::getAdmin(),
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
        ]);
    }

    public function create()
    {
        $this->view('teams/create', [
            'title' => 'Thêm thành viên - Admin MTech',
            'page'  => 'team.create',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teams');
            return;
        }

<<<<<<< HEAD
        $data = [
            'name'       => trim($_POST['name'] ?? ''),
            'position'   => trim($_POST['position'] ?? ''),
            'image'      => trim($_POST['image'] ?? ''),
            'bio'        => trim($_POST['bio'] ?? ''),
            'status'     => (int)($_POST['status'] ?? 1),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];

        if (empty($data['name']) || empty($data['position'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc';
=======
        $name     = trim($_POST['name']     ?? '');
        $position = trim($_POST['position'] ?? '');

        if (empty($name) || empty($position)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ Họ tên và Chức vụ';
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
            $this->redirect('/teams/create');
            return;
        }

<<<<<<< HEAD
        if ($this->model->create($data)) {
=======
        // Bắt buộc phải upload file ảnh
        if (empty($_FILES['image_file']['name'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh đại diện';
            $this->redirect('/teams/create');
            return;
        }

        $uploaded = $this->handleImageUpload($_FILES['image_file']);
        if ($uploaded === false) {
            $_SESSION['error'] = 'Ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF, WEBP và tối đa 2MB';
            $this->redirect('/teams/create');
            return;
        }
        $imageValue = self::ADMIN_BASE_URL . self::UPLOAD_DIR . $uploaded;

        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        $showInAbout = isset($_POST['show_in_about']) ? 1 : 0;

        // Kiểm tra giới hạn 4 thành viên trên trang Giới thiệu
        if ($showInAbout === 1 && $this->model->countShowInAbout() >= 4) {
            $_SESSION['error'] = 'Trang Giới thiệu đã đủ 4 thành viên. Vui lòng bỏ chọn một thành viên khác trước khi thêm mới.';
            $this->redirect('/teams/create');
            return;
        }

        $data = [
            'name'          => $name,
            'position'      => $position,
            'image'         => $imageValue,
            'bio'           => trim($_POST['bio'] ?? ''),
            'status'        => (int)($_POST['status'] ?? 1),
            'sort_order'    => $sortOrder,
            'show_in_about' => $showInAbout,
        ];

        // Đẩy các bản ghi khác nhường chỗ TRƯỚC khi insert
        $this->model->shiftOrdersDown(0, $sortOrder);

        $id = $this->model->create($data);
        if ($id) {
            // Lấp khoảng trống sau khi insert
            $this->model->compactOrders();
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
            $_SESSION['success'] = 'Đã thêm thành viên thành công';
            $this->redirect('/teams');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm thành viên';
            $this->redirect('/teams/create');
        }
    }

    public function edit($id)
    {
        $team = $this->model->getById($id);
<<<<<<< HEAD

=======
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
        if (!$team) {
            $_SESSION['error'] = 'Không tìm thấy thành viên';
            $this->redirect('/teams');
            return;
        }
<<<<<<< HEAD

=======
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
        $this->view('teams/edit', [
            'title' => 'Chỉnh sửa thành viên - Admin MTech',
            'page'  => 'team.edit',
            'team'  => $team,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teams');
            return;
        }

<<<<<<< HEAD
        $data = [
            'name'       => trim($_POST['name'] ?? ''),
            'position'   => trim($_POST['position'] ?? ''),
            'image'      => trim($_POST['image'] ?? ''),
            'bio'        => trim($_POST['bio'] ?? ''),
            'status'     => (int)($_POST['status'] ?? 1),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];

        if (empty($data['name']) || empty($data['position'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc';
=======
        $team = $this->model->getById($id);
        if (!$team) {
            $_SESSION['error'] = 'Không tìm thấy thành viên';
            $this->redirect('/teams');
            return;
        }

        $name     = trim($_POST['name']     ?? '');
        $position = trim($_POST['position'] ?? '');

        if (empty($name) || empty($position)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ Họ tên và Chức vụ';
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
            $this->redirect('/teams/edit/' . $id);
            return;
        }

<<<<<<< HEAD
        if ($this->model->update($id, $data)) {
=======
        $newOrder    = (int)($_POST['sort_order'] ?? 0);
        $oldOrder    = (int)($team['sort_order'] ?? 0);
        $showInAbout = isset($_POST['show_in_about']) ? 1 : 0;

        // Kiểm tra giới hạn 4 thành viên trên trang Giới thiệu
        // Chỉ check khi: muốn bật show_in_about VÀ bản ghi hiện tại chưa bật
        $wasShowInAbout = (int)($team['show_in_about'] ?? 0);
        if ($showInAbout === 1 && $wasShowInAbout === 0 && $this->model->countShowInAbout((int)$id) >= 4) {
            $_SESSION['error'] = 'Trang Giới thiệu đã đủ 4 thành viên. Vui lòng bỏ chọn một thành viên khác trước khi thêm mới.';
            $this->redirect('/teams/edit/' . $id);
            return;
        }

        $data = [
            'name'          => $name,
            'position'      => $position,
            'bio'           => trim($_POST['bio'] ?? ''),
            'status'        => (int)($_POST['status'] ?? 1),
            'sort_order'    => $newOrder,
            'show_in_about' => $showInAbout,
        ];

        if (!empty($_FILES['image_file']['name'])) {
            $uploaded = $this->handleImageUpload($_FILES['image_file']);
            if ($uploaded === false) {
                $_SESSION['error'] = 'Ảnh không hợp lệ. Chỉ nhập JPG, PNG, GIF, WEBP và tối đa 2MB';
                $this->redirect('/teams/edit/' . $id);
                return;
            }
            $this->deleteOldImage($team['image'] ?? '');
            $data['image'] = self::ADMIN_BASE_URL . self::UPLOAD_DIR . $uploaded;
        } elseif (!empty($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            $this->deleteOldImage($team['image'] ?? '');
            $data['image'] = '';
        }
        // Không có thay đổi ảnh → không set key 'image', model giữ nguyên

        // Nếu bản ghi chưa có ảnh và không upload ảnh mới → block
        $willHaveImage = isset($data['image']) ? !empty($data['image']) : !empty($team['image']);
        if (!$willHaveImage) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh đại diện';
            $this->redirect('/teams/edit/' . $id);
            return;
        }

        // Đẩy các bản ghi khác nhường chỗ TRƯỚC khi update (chỉ khi thứ tự thay đổi)
        if ($newOrder !== $oldOrder) {
            $this->model->shiftOrdersDown((int)$id, $newOrder);
        }

        if ($this->model->update($id, $data)) {
            // Lấp khoảng trống sau khi update
            $this->model->compactOrders();
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
            $_SESSION['success'] = 'Đã cập nhật thành viên thành công';
            $this->redirect('/teams');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật thành viên';
            $this->redirect('/teams/edit/' . $id);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teams');
            return;
        }

<<<<<<< HEAD
        if ($this->model->delete($id)) {
            $_SESSION['success'] = 'Đã xóa thành viên thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa thành viên';
        }
        
        $this->redirect('/teams');
    }
=======
        $team = $this->model->getById($id);
        if (!$team) {
            $_SESSION['error'] = 'Không tìm thấy thành viên';
            $this->redirect('/teams');
            return;
        }

        // Soft delete — chuyển vào thùng rác
        if ($this->model->delete($id)) {
            $this->model->compactOrders();
            $_SESSION['success'] = 'Đã chuyển thành viên vào thùng rác';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa thành viên';
        }

        $this->redirect('/teams');
    }

    public function trash()
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $teams      = $this->model->getTrashed($perPage, $offset);
        $total      = $this->model->countTrashed();
        $totalPages = (int)ceil($total / $perPage);

        $this->view('teams/trash', [
            'title'       => 'Thùng rác - Đội ngũ - Admin MTech',
            'page'        => 'teams',
            'teams'       => $teams,
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    public function restore($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teams/trash');
            return;
        }

        if ($this->model->restore((int)$id)) {
            $_SESSION['success'] = 'Đã khôi phục thành viên thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi khôi phục thành viên';
        }

        $this->redirect('/teams/trash');
    }

    public function hardDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teams/trash');
            return;
        }

        // Lấy thông tin để xóa ảnh vật lý nếu cần
        $stmt = null;
        try {
            $db   = $this->model->getDb();
            $stmt = $db->prepare("SELECT image FROM teams WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                $this->deleteOldImage($row['image'] ?? '');
            }
        } catch (\Throwable $e) {
            // bỏ qua nếu không lấy được
        }

        if ($this->model->hardDelete((int)$id)) {
            $_SESSION['success'] = 'Đã xóa vĩnh viễn thành viên';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa vĩnh viễn';
        }

        $this->redirect('/teams/trash');
    }

    // ----------------------------------------
    // Helpers
    // ----------------------------------------

    private function handleImageUpload(array $file): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return false;
        if ($file['size'] > self::MAX_FILE_SIZE) return false;

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_TYPES)) return false;

        $uploadDir = __DIR__ . '/../../assets/uploads/teams/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename  = 'team_' . uniqid('', true) . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return $filename;
        }

        return false;
    }

    private function deleteOldImage(string $imagePath): void
    {
        if (empty($imagePath)) return;
        // Chỉ xóa nếu là file upload nội bộ (URL tuyệt đối của admin site)
        if (strpos($imagePath, self::ADMIN_BASE_URL) === false) return;

        $filename = basename($imagePath);
        $fullPath = __DIR__ . '/../../assets/uploads/teams/' . $filename;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
}
