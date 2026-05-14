<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/CategoriesModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CategoriesController extends BaseController
{
    private $model;

    private const ADMIN_BASE_URL = 'https://admin.truongvinalogistics.com.vn';
    private const UPLOAD_DIR     = '/assets/uploads/categories/';
    private const MAX_FILE_SIZE  = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES  = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    // Tất cả các field ảnh trong bảng categories
    private const IMAGE_FIELDS = ['image', 'image_1', 'image_2', 'image_3', 'benefit_image', 'feature_image'];

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new CategoriesModel();
    }

    public function index()
    {
        $categories = $this->model->getAllCategories();
        $this->view('categories/index', [
            'title'      => 'Quản lý Dịch vụ - Admin MTech',
            'page'       => 'categories',
            'categories' => $categories,
            'admin'      => AuthMiddleware::getAdmin(),
        ]);
    }

    public function create()
    {
        $this->view('categories/create', [
            'title' => 'Thêm dịch vụ - Admin MTech',
            'page'  => 'category.create',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categories');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        if (empty($name) || empty($slug)) {
            $_SESSION['error'] = 'Vui lòng nhập tên và slug dịch vụ';
            $this->redirect('/categories/create');
            return;
        }

        // Validate ảnh bắt buộc (server-side)
        if (empty($_FILES['image']['name'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh đại diện dịch vụ';
            $this->redirect('/categories/create');
            return;
        }
        if (empty($_FILES['image_1']['name'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh 1 trong gallery chi tiết';
            $this->redirect('/categories/create');
            return;
        }
        if (empty($_FILES['benefit_image']['name'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh minh họa Benefit';
            $this->redirect('/categories/create');
            return;
        }
        if (empty($_FILES['feature_image']['name'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh minh họa Dự án';
            $this->redirect('/categories/create');
            return;
        }

        $data = $this->buildData();

        // Xử lý upload tất cả ảnh
        foreach (self::IMAGE_FIELDS as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $uploaded = $this->handleImageUpload($_FILES[$field]);
                if ($uploaded === false) {
                    $_SESSION['error'] = "Ảnh '{$field}' không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF, WEBP và tối đa 5MB";
                    $this->redirect('/categories/create');
                    return;
                }
                $data[$field] = self::ADMIN_BASE_URL . self::UPLOAD_DIR . $uploaded;
            }
        }

        $id = $this->model->create($data);
        if ($id) {
            $_SESSION['success'] = 'Đã thêm dịch vụ thành công';
            $this->redirect('/categories');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm dịch vụ';
            $this->redirect('/categories/create');
        }
    }

    public function edit($id)
    {
        $category = $this->model->getCategoryById($id);
        if (!$category) {
            $_SESSION['error'] = 'Không tìm thấy dịch vụ';
            $this->redirect('/categories');
            return;
        }
        $this->view('categories/edit', [
            'title'    => 'Chỉnh sửa dịch vụ - Admin MTech',
            'page'     => 'category.edit',
            'category' => $category,
            'admin'    => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categories/edit/' . $id);
            return;
        }

        $category = $this->model->getCategoryById($id);
        if (!$category) {
            $_SESSION['error'] = 'Không tìm thấy dịch vụ';
            $this->redirect('/categories');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        if (empty($name) || empty($slug)) {
            $_SESSION['error'] = 'Vui lòng nhập tên và slug dịch vụ';
            $this->redirect('/categories/edit/' . $id);
            return;
        }

        $data = $this->buildData();

        // Xử lý upload ảnh mới — giữ ảnh cũ nếu không upload
        foreach (self::IMAGE_FIELDS as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $uploaded = $this->handleImageUpload($_FILES[$field]);
                if ($uploaded === false) {
                    $_SESSION['error'] = "Ảnh '{$field}' không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF, WEBP và tối đa 5MB";
                    $this->redirect('/categories/edit/' . $id);
                    return;
                }
                $this->deleteOldImage($category[$field] ?? '');
                $data[$field] = self::ADMIN_BASE_URL . self::UPLOAD_DIR . $uploaded;
            } else {
                // Không upload mới → giữ nguyên ảnh cũ từ DB
                $data[$field] = $category[$field] ?? '';
            }
        }

        // Validate ảnh bắt buộc sau khi merge với ảnh cũ
        if (empty($data['image'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh đại diện dịch vụ';
            $this->redirect('/categories/edit/' . $id);
            return;
        }
        if (empty($data['image_1'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh 1 trong gallery chi tiết';
            $this->redirect('/categories/edit/' . $id);
            return;
        }
        if (empty($data['benefit_image'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh minh họa Benefit';
            $this->redirect('/categories/edit/' . $id);
            return;
        }
        if (empty($data['feature_image'])) {
            $_SESSION['error'] = 'Vui lòng tải lên ảnh minh họa Dự án';
            $this->redirect('/categories/edit/' . $id);
            return;
        }

        if ($this->model->update((int)$id, $data)) {
            $_SESSION['success'] = 'Đã cập nhật dịch vụ thành công';
            $this->redirect('/categories');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật dịch vụ';
            $this->redirect('/categories/edit/' . $id);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categories');
            return;
        }

        if ($this->model->delete((int)$id)) {
            $_SESSION['success'] = 'Đã chuyển dịch vụ vào thùng rác';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa dịch vụ';
        }

        $this->redirect('/categories');
    }

    public function trash()
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $categories = $this->model->getTrashed($perPage, $offset);
        $total      = $this->model->countTrashed();
        $totalPages = (int)ceil($total / $perPage);

        $this->view('categories/trash', [
            'title'       => 'Thùng rác - Dịch vụ - Admin MTech',
            'page'        => 'categories',
            'categories'  => $categories,
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    public function restore($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categories/trash');
            return;
        }

        if ($this->model->restore((int)$id)) {
            $_SESSION['success'] = 'Đã khôi phục dịch vụ thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi khôi phục dịch vụ';
        }

        $this->redirect('/categories/trash');
    }

    public function hardDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categories/trash');
            return;
        }

        if ($this->model->hardDelete((int)$id)) {
            $_SESSION['success'] = 'Đã xóa vĩnh viễn dịch vụ';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa vĩnh viễn';
        }

        $this->redirect('/categories/trash');
    }

    // ----------------------------------------
    // Helpers
    // ----------------------------------------

    /**
     * Build $data array từ $_POST (không bao gồm image fields — xử lý riêng).
     */
    private function buildData(): array
    {
        $benefitItemsRaw = trim($_POST['benefit_items'] ?? '');
        $benefitItems    = null;
        if (!empty($benefitItemsRaw)) {
            $decoded = json_decode($benefitItemsRaw, true);
            $benefitItems = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                ? $benefitItemsRaw : null;
        }

        $faqItemsRaw = trim($_POST['faq_items'] ?? '');
        $faqItems    = null;
        if (!empty($faqItemsRaw)) {
            $decoded = json_decode($faqItemsRaw, true);
            $faqItems = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                ? $faqItemsRaw : null;
        }

        return [
            'name'                => trim($_POST['name']                ?? ''),
            'slug'                => trim($_POST['slug']                ?? ''),
            'image'               => '',  // sẽ được ghi đè bởi upload handler
            'description'         => trim($_POST['description']         ?? ''),
            'detail_description'  => trim($_POST['detail_description']  ?? ''),
            'image_1'             => '',
            'image_2'             => '',
            'image_3'             => '',
            'benefit_image'       => '',
            'benefit_title'       => trim($_POST['benefit_title']       ?? ''),
            'benefit_description' => trim($_POST['benefit_description'] ?? ''),
            'benefit_items'       => $benefitItems,
            'feature_image'       => '',
            'feature_1_icon'      => trim($_POST['feature_1_icon']      ?? ''),
            'feature_1_title'     => trim($_POST['feature_1_title']     ?? ''),
            'feature_1_text'      => trim($_POST['feature_1_text']      ?? ''),
            'feature_2_icon'      => trim($_POST['feature_2_icon']      ?? ''),
            'feature_2_title'     => trim($_POST['feature_2_title']     ?? ''),
            'feature_2_text'      => trim($_POST['feature_2_text']      ?? ''),
            'faq_items'           => $faqItems,
            'status'              => (int)($_POST['status']             ?? 1),
            'sort_order'          => (int)($_POST['sort_order']         ?? 0),
            'show_in_footer'      => isset($_POST['show_in_footer']) ? 1 : 0,
        ];
    }

    /**
     * Xử lý upload file ảnh.
     * @return string|false  Tên file nếu thành công, false nếu thất bại
     */
    private function handleImageUpload(array $file): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return false;
        if ($file['size'] > self::MAX_FILE_SIZE) return false;

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_TYPES)) return false;

        $uploadDir = __DIR__ . '/../../assets/uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename  = 'cat_' . uniqid('', true) . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return $filename;
        }

        return false;
    }

    /**
     * Xóa file ảnh cũ khỏi disk (chỉ xóa file upload nội bộ).
     */
    private function deleteOldImage(string $imagePath): void
    {
        if (empty($imagePath)) return;
        if (strpos($imagePath, self::ADMIN_BASE_URL) === false) return;

        $filename = basename($imagePath);
        $fullPath = __DIR__ . '/../../assets/uploads/categories/' . $filename;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}
