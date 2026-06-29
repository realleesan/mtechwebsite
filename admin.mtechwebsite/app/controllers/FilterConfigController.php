<?php
/**
 * FilterConfigController.php
 * 
 * Controller quản lý cấu hình bộ lọc và sắp xếp Mega Menu.
 */

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/CategoriesModel.php';
require_once __DIR__ . '/../models/BlogsModel.php';
require_once __DIR__ . '/../services/FilterConfigService.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class FilterConfigController extends BaseController
{
    private $filterService;
    private $categoriesModel;
    private $blogsModel;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->filterService = new FilterConfigService();
        $this->categoriesModel = new CategoriesModel();
        $this->blogsModel = new BlogsModel();
    }

    /**
     * Hiển thị giao diện cấu hình bộ lọc kéo thả
     */
    public function index()
    {
        $db = getDBConnection();

        // 1. Lấy danh sách Lĩnh vực (Categories)
        $services = $this->categoriesModel->getAllCategories();

        // 2. Lấy danh sách Danh mục Dự án (Project Categories)
        $projectCategories = [];
        try {
            $stmt = $db->query("SELECT id, name, parent_id, slug, status, sort_order FROM project_categories ORDER BY sort_order ASC, id ASC");
            $projectCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('FilterConfigController::index() project_categories - ' . $e->getMessage());
        }

        // 3. Lấy danh sách Danh mục Tin tức (Blog Categories)
        $blogCategories = $this->blogsModel->getAdminBlogCategories();

        // 4. Lấy cấu hình filter_config hiện tại của từng loại
        $servicesConfig = $this->filterService->getConfig('services');
        $projectCategoriesConfig = $this->filterService->getConfig('project_categories');
        $blogCategoriesConfig = $this->filterService->getConfig('blog_categories');

        $this->view('filter_config/index', [
            'title' => 'Cấu hình bộ lọc Mega Menu - Admin MTech',
            'page'  => 'filter_config',
            'admin' => AuthMiddleware::getAdmin(),
            'services' => $services,
            'projectCategories' => $projectCategories,
            'blogCategories' => $blogCategories,
            'servicesConfig' => $servicesConfig,
            'projectCategoriesConfig' => $projectCategoriesConfig,
            'blogCategoriesConfig' => $blogCategoriesConfig,
        ]);
    }

    /**
     * API lưu cấu hình bộ lọc từ AJAX
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Đọc dữ liệu JSON từ body request
        $inputRaw = file_get_contents('php://input');
        $input = json_decode($inputRaw, true);

        if (!$input || empty($input['criteria_type']) || !isset($input['items'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }

        $criteriaType = trim($input['criteria_type']);
        $items = $input['items'];

        // Validate criteria type
        $allowedTypes = ['services', 'project_categories', 'blog_categories'];
        if (!in_array($criteriaType, $allowedTypes, true)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Loại tiêu chí không hợp lệ']);
            return;
        }

        // Lưu cấu hình
        $success = $this->filterService->saveConfig($criteriaType, $items);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Cấu hình đã được lưu thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi lưu cấu hình vào cơ sở dữ liệu']);
        }
    }
}
