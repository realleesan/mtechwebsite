<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/CategoriesModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CategoriesController extends BaseController
{
    private $model;

    private const ADMIN_BASE_URL = 'https://admin.mtechjsc.com';
    private const UPLOAD_DIR     = '/assets/uploads/categories/';
    private const MAX_FILE_SIZE  = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES  = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    private function getAdminBaseUrl(): string
    {
        return env('ADMIN_BASE_URL', self::ADMIN_BASE_URL);
    }

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
            'title'      => 'Quản lý Lĩnh vực - Admin MTech',
            'page'       => 'categories',
            'categories' => $categories,
            'admin'      => AuthMiddleware::getAdmin(),
        ]);
    }

    public function create()
    {
        $allCategories = $this->model->getAllCategories();
        $categoryOptions = $this->model->getFormattedTreeOptions($allCategories);
        
        $this->view('categories/create', [
            'title' => 'Thêm lĩnh vực - Admin MTech',
            'page'  => 'category.create',
            'categories' => $categoryOptions,
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
            $_SESSION['error'] = 'Vui lòng nhập tên và slug lĩnh vực';
            $this->redirect('/categories/create');
            return;
        }

        // Validate ảnh bắt buộc khi tạo mới (server-side)
        $requiredImages = ['image', 'image_1', 'image_2', 'image_3', 'benefit_image', 'feature_image'];
        $imageLabels = [
            'image' => 'ảnh đại diện lĩnh vực',
            'image_1' => 'ảnh 1 trong gallery',
            'image_2' => 'ảnh 2 trong gallery',
            'image_3' => 'ảnh 3 trong gallery',
            'benefit_image' => 'ảnh minh họa Benefit',
            'feature_image' => 'ảnh minh họa Dự án'
        ];

        foreach ($requiredImages as $field) {
            if (empty($_FILES[$field]['name'])) {
                $_SESSION['error'] = 'Vui lòng tải lên ' . $imageLabels[$field];
                $this->redirect('/categories/create');
                return;
            }
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
                $data[$field] = $this->getAdminBaseUrl() . self::UPLOAD_DIR . $uploaded;
            }
        }

        $id = $this->model->create($data);
        if ($id) {
            $_SESSION['success'] = 'Đã thêm lĩnh vực thành công';
            $this->redirect('/categories');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm lĩnh vực';
            $this->redirect('/categories/create');
        }
    }

    public function edit($id)
    {
        $category = $this->model->getCategoryById($id);
        if (!$category) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/categories');
            return;
        }
        $allCategories = $this->model->getAllCategories();
        $categoryOptions = $this->model->getFormattedTreeOptions($allCategories, $id);
        
        // Lấy danh sách dự án THUỘC lĩnh vực này (qua project_services)
        require_once __DIR__ . '/../models/ProjectsModel.php';
        $projectsModel = new ProjectsModel();
        $categoryProjects = $projectsModel->getProjectsByCategory((int)$id);
        
        // Lấy dự án đã được gán làm featured (nếu có)
        $featuredProject = $this->model->getFeaturedProject((int)$id);
        
        $this->view('categories/edit', [
            'title'    => 'Chỉnh sửa lĩnh vực - Admin MTech',
            'page'     => 'category.edit',
            'category' => $category,
            'categories' => $categoryOptions,
            'projects' => $categoryProjects,
            'featured_project' => $featuredProject,
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
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/categories');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        if (empty($name) || empty($slug)) {
            $_SESSION['error'] = 'Vui lòng nhập tên và slug lĩnh vực';
            $this->redirect('/categories/edit/' . $id);
            return;
        }

        $data = $this->buildData();

        // Xử lý upload ảnh - CHỈ upload nếu có file mới, nếu không giữ nguyên ảnh cũ
        foreach (self::IMAGE_FIELDS as $field) {
            if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                // Có file mới được upload
                $uploaded = $this->handleImageUpload($_FILES[$field]);
                if ($uploaded === false) {
                    $_SESSION['error'] = "Ảnh '{$field}' không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF, WEBP và tối đa 5MB";
                    $this->redirect('/categories/edit/' . $id);
                    return;
                }
                // Xóa ảnh cũ nếu có
                $this->deleteOldImage($category[$field] ?? '');
                // Gán URL ảnh mới
                $data[$field] = $this->getAdminBaseUrl() . self::UPLOAD_DIR . $uploaded;
            } else {
                // KHÔNG có file mới - GIỮ NGUYÊN ảnh cũ từ database
                $data[$field] = $category[$field] ?? '';
            }
        }

        // Không cần validate ảnh bắt buộc khi EDIT vì đã có ảnh cũ rồi
        // User chỉ cần thay đổi các trường text, không bắt buộc phải upload lại ảnh

        if ($this->model->update((int)$id, $data)) {
            $_SESSION['success'] = 'Đã cập nhật lĩnh vực thành công';
            $this->redirect('/categories');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật lĩnh vực';
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
            $_SESSION['success'] = 'Đã chuyển lĩnh vực vào thùng rác';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa lĩnh vực';
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
            'title'       => 'Thùng rác - Lĩnh vực - Admin MTech',
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
            $_SESSION['success'] = 'Đã khôi phục lĩnh vực thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi khôi phục lĩnh vực';
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
            $_SESSION['success'] = 'Đã xóa vĩnh viễn lĩnh vực';
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
     * CHÚ Ý: KHÔNG khởi tạo image fields ở đây để tránh ghi đè giá trị cũ khi update
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
            'parent_id'           => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'name'                => trim($_POST['name']                ?? ''),
            'slug'                => trim($_POST['slug']                ?? ''),
            'description'         => trim($_POST['description']         ?? ''),
            'detail_description'  => trim($_POST['detail_description']  ?? ''),
            'benefit_title'       => trim($_POST['benefit_title']       ?? ''),
            'benefit_description' => trim($_POST['benefit_description'] ?? ''),
            'benefit_items'       => $benefitItems,
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
            'featured_project_id' => !empty($_POST['featured_project_id']) ? (int)$_POST['featured_project_id'] : null,
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
        if (strpos($imagePath, $this->getAdminBaseUrl()) === false &&
            strpos($imagePath, 'adminmtechjsc.gt.tc') === false &&
            strpos($imagePath, self::UPLOAD_DIR) === false) return;

        $filename = basename($imagePath);
        $fullPath = __DIR__ . '/../../assets/uploads/categories/' . $filename;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}
