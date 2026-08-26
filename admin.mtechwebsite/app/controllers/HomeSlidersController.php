<?php
/**
 * HomeSlidersController.php
 * 
 * Controller quản lý Hero Slider trang chủ.
 */

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/HomeSliderModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class HomeSlidersController extends BaseController
{
    private $model;

    private function getAdminBaseUrl(): string
    {
        return env('ADMIN_BASE_URL', 'https://admin.mtechjsc.com');
    }

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new HomeSliderModel();
    }

    public function index()
    {
        $slides = $this->model->getAll();
        $this->view('home_sliders/index', [
            'title'  => 'Quản lý Hero Slider - Admin MTech',
            'page'   => 'home_sliders',
            'slides' => $slides,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    public function create()
    {
        $this->view('home_sliders/create', [
            'title' => 'Thêm Slide Hero - Admin MTech',
            'page'  => 'home_sliders',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/home-sliders');
            return;
        }

        $data = [
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status'     => (int)($_POST['status'] ?? 1),
        ];

        $uploaded = $this->processImageUploads();
        if ($uploaded === false) {
            $_SESSION['error'] = 'Vui lòng tải lên đủ 3 ảnh cho slide (ảnh 1, ảnh 2, ảnh 3)';
            $this->redirect('/home-sliders/create');
            return;
        }

        foreach ($uploaded as $field => $filename) {
            $uploaded[$field] = $this->getAdminBaseUrl() . '/assets/uploads/home-sliders/' . $filename;
        }

        $data = array_merge($data, $uploaded);

        $id = $this->model->create($data);
        if ($id) {
            $_SESSION['success'] = 'Đã thêm slide thành công';
            $this->redirect('/home-sliders');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm slide';
            $this->redirect('/home-sliders/create');
        }
    }

    public function edit($id)
    {
        $slide = $this->model->getById($id);
        if (!$slide) {
            $_SESSION['error'] = 'Không tìm thấy slide';
            $this->redirect('/home-sliders');
            return;
        }

        $this->view('home_sliders/edit', [
            'title' => 'Chỉnh sửa Slide Hero - Admin MTech',
            'page'  => 'home_sliders',
            'slide' => $slide,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/home-sliders');
            return;
        }

        $slide = $this->model->getById($id);
        if (!$slide) {
            $_SESSION['error'] = 'Không tìm thấy slide';
            $this->redirect('/home-sliders');
            return;
        }

        $data = [
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status'     => (int)($_POST['status'] ?? 1),
        ];

        if (!empty($_FILES['image_1']['name']) || !empty($_FILES['image_2']['name']) || !empty($_FILES['image_3']['name'])) {
            $uploaded = $this->processImageUploads(false);
            if ($uploaded !== false) {
                if (!empty($uploaded['image_1'])) {
                    $this->deleteOldImage($slide['image_1']);
                    $data['image_1'] = $this->getAdminBaseUrl() . '/assets/uploads/home-sliders/' . $uploaded['image_1'];
                }
                if (!empty($uploaded['image_2'])) {
                    $this->deleteOldImage($slide['image_2']);
                    $data['image_2'] = $this->getAdminBaseUrl() . '/assets/uploads/home-sliders/' . $uploaded['image_2'];
                }
                if (!empty($uploaded['image_3'])) {
                    $this->deleteOldImage($slide['image_3']);
                    $data['image_3'] = $this->getAdminBaseUrl() . '/assets/uploads/home-sliders/' . $uploaded['image_3'];
                }
            }
        }

        if ($this->model->update((int)$id, $data)) {
            $_SESSION['success'] = 'Đã cập nhật slide thành công';
            $this->redirect('/home-sliders');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật slide';
            $this->redirect('/home-sliders/edit/' . $id);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/home-sliders');
            return;
        }

        if ($this->model->delete((int)$id)) {
            $_SESSION['success'] = 'Đã chuyển slide vào thùng rác';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa slide';
        }

        $this->redirect('/home-sliders');
    }

    public function trash()
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $slides  = $this->model->getTrashed($perPage, $offset);
        $total   = $this->model->countTrashed();
        $totalPages = (int)ceil($total / $perPage);

        $this->view('home_sliders/trash', [
            'title'       => 'Thùng rác - Hero Slider - Admin MTech',
            'page'        => 'home_sliders',
            'slides'      => $slides,
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    public function restore($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/home-sliders/trash');
            return;
        }

        if ($this->model->restore((int)$id)) {
            $_SESSION['success'] = 'Đã khôi phục slide thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi khôi phục slide';
        }

        $this->redirect('/home-sliders/trash');
    }

    public function hardDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/home-sliders/trash');
            return;
        }

        if ($this->model->hardDelete((int)$id)) {
            $_SESSION['success'] = 'Đã xóa vĩnh viễn slide';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa slide';
        }

        $this->redirect('/home-sliders/trash');
    }

    private function processImageUploads($requireAll = true)
    {
        $model = new HomeSliderModel();
        $result = [];
        $allUploaded = true;

        $imageFields = ['image_1', 'image_2', 'image_3'];
        foreach ($imageFields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $uploaded = $this->uploadImage($_FILES[$field]);
                if ($uploaded === false) {
                    $allUploaded = false;
                    continue;
                }
                $result[$field] = $uploaded;
            } elseif ($requireAll) {
                $allUploaded = false;
            }
        }

        if ($requireAll && !$allUploaded) {
            return false;
        }

        return $result;
    }

    private function uploadImage(array $file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return false;
        if ($file['size'] > 5 * 1024 * 1024) return false;

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowedTypes)) return false;

        $uploadDir = __DIR__ . '/../../assets/uploads/home-sliders/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename  = 'slider_' . uniqid('', true) . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return $filename;
        }

        return false;
    }

    private function deleteOldImage($imagePath)
    {
        if (empty($imagePath)) return;
        if (strpos($imagePath, $this->getAdminBaseUrl()) === false &&
            strpos($imagePath, 'adminmtechjsc.gt.tc') === false &&
            strpos($imagePath, '/assets/uploads/home-sliders/') === false) return;

        $filename = basename($imagePath);
        $fullPath = __DIR__ . '/../../assets/uploads/home-sliders/' . $filename;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}
