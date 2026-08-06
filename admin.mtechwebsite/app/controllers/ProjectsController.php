<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/ProjectsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ProjectsController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new ProjectsModel();
    }

    public function index()
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;
        $search  = trim($_GET['search'] ?? '');

        // Admin should see ALL projects regardless of status
        $statusFilter = null;
        
        if (!empty($search)) {
            $projects = $this->model->search($search, $perPage, $statusFilter);
            $total    = count($projects);
        } else {
            $projects = $this->model->getAll($perPage, $offset, $statusFilter);
            $total    = $this->model->count($statusFilter);
        }
        $totalPages = ceil($total / $perPage);
        
        // Load services for projects
        if (!empty($projects)) {
            $projectIds = array_column($projects, 'id');
            $projectsServices = $this->model->getProjectsServicesList($projectIds);
            
            // Attach services to each project
            foreach ($projects as &$project) {
                $services = $projectsServices[$project['id']] ?? [];
                $project['category_names'] = !empty($services)
                    ? array_column($services, 'name')
                    : [];
                $project['category_name'] = !empty($project['category_names'])
                    ? implode(', ', $project['category_names'])
                    : 'Chưa có';
            }
            unset($project); // Quan trọng: bỏ reference sau foreach
        }

        $this->view('projects/index', [
            'title'       => 'Quản lý Dự án - Admin MTech',
            'page'        => 'projects',
            'projects'    => $projects,
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'search'      => $search,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    public function create()
    {
        // Load services for dropdown - build hierarchy
        $projectsModel = new ProjectsModel();
        $servicesFlat = $projectsModel->getServices();
        $servicesTree = $projectsModel->buildServicesTree($servicesFlat);
        
        $this->view('projects/create', [
            'title' => 'Thêm dự án - Admin MTech',
            'page'  => 'project-create',
            'admin' => AuthMiddleware::getAdmin(),
            'services' => $servicesTree,
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Method not allowed';
            $this->redirect('/projects/create');
            return;
        }

        // Validate required fields
        $required = ['title', 'slug'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc";
                $this->redirect('/projects/create');
                return;
            }
        }

        if (empty($_POST['service_ids'])) {
            $_SESSION['error'] = "Vui lòng chọn ít nhất một danh mục";
            $this->redirect('/projects/create');
            return;
        }

        // Prepare data
        $data = [
            'title' => $_POST['title'],
            'slug' => $_POST['slug'],
            'description' => $_POST['description'] ?? '',
            'content' => $_POST['content'] ?? '',
            'client' => $_POST['client'] ?? '',
            'location' => $_POST['location'] ?? '',
            'project_date' => $_POST['project_date'] ?? '',
            'status' => $_POST['status'] ?? 1,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'show_on_home' => isset($_POST['show_on_home']) ? 1 : 0,
            'show_in_menu' => isset($_POST['show_in_menu']) ? 1 : 0,
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'detail_image' => $_POST['detail_image'] ?? '',
            'status_label' => $_POST['status_label'] ?? 'Đã hoàn thành',
            'live_demo' => $_POST['live_demo'] ?? '',
            'tags' => $_POST['tags'] ?? '',
            'what_we_did_title' => $_POST['what_we_did_title'] ?? '',
            'what_we_did' => $_POST['what_we_did'] ?? '',
            'what_we_did_image' => $_POST['what_we_did_image'] ?? '',
            'results_title' => $_POST['results_title'] ?? '',
            'results' => $_POST['results'] ?? '',
            'result_items' => $_POST['result_items'] ?? ''
        ];

        // Handle file uploads
        $uploadDir = __DIR__ . '/../../assets/uploads/projects/';
        $baseUrl = 'https://adminmtechjsc.gt.tc/assets/uploads/projects/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Handle main image
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->handleFileUpload($_FILES['image'], $uploadDir);
            if ($imagePath) {
                $data['image'] = $baseUrl . $imagePath;
            }
        }

        // Handle detail image
        if (!empty($_FILES['detail_image']['name'])) {
            $detailImagePath = $this->handleFileUpload($_FILES['detail_image'], $uploadDir);
            if ($detailImagePath) {
                $data['detail_image'] = $baseUrl . $detailImagePath;
            }
        }

        // Handle what_we_did_image
        if (!empty($_FILES['what_we_did_image']['name'])) {
            $whatWeDidImagePath = $this->handleFileUpload($_FILES['what_we_did_image'], $uploadDir);
            if ($whatWeDidImagePath) {
                $data['what_we_did_image'] = $baseUrl . $whatWeDidImagePath;
            }
        }

        // Handle gallery images
        if (!empty($_FILES['gallery'])) {
            $galleryImages = [];
            foreach ($_FILES['gallery']['name'] as $key => $name) {
                if (!empty($name)) {
                    $file = [
                        'name' => $name,
                        'type' => $_FILES['gallery']['type'][$key],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$key],
                        'error' => $_FILES['gallery']['error'][$key],
                        'size' => $_FILES['gallery']['size'][$key]
                    ];
                    $galleryPath = $this->handleFileUpload($file, $uploadDir);
                    if ($galleryPath) {
                        $galleryImages[] = $baseUrl . $galleryPath;
                    }
                }
            }
            if (!empty($galleryImages)) {
                $data['gallery'] = json_encode($galleryImages);
            }
        }

        // Create project
        $projectId = $this->model->create($data);
        if ($projectId) {
            // Reorder projects
            $this->model->reorderProjects($projectId, $data['sort_order']);
            $this->model->normalizeOrders();
            
            // Save services for the project
            if (!empty($_POST['service_ids'])) {
                $this->model->addProjectServices($projectId, $_POST['service_ids']);
            }
            
            $_SESSION['success'] = 'Thêm dự án thành công!';
            $this->redirect('/projects');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/projects/create');
        }
    }

    public function edit($id)
    {
        $project = $this->model->getById($id);
        if (!$project) {
            $_SESSION['error'] = 'Không tìm thấy dự án';
            $this->redirect('/projects');
            return;
        }
        
        // Load services - build hierarchy
        $servicesFlat = $this->model->getServices();
        $servicesTree = $this->model->buildServicesTree($servicesFlat);
        $projectServices = $this->model->getProjectServices($id);
        
        $this->view('projects/edit', [
            'title'   => 'Chỉnh sửa dự án - Admin MTech',
            'page'    => 'project-edit',
            'project' => $project,
            'admin'   => AuthMiddleware::getAdmin(),
            'services' => $servicesTree,
            'projectServices' => $projectServices,
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Method not allowed';
            $this->redirect('/projects/edit/' . $id);
            return;
        }

        // Validate required fields
        $required = ['title', 'slug'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc";
                $this->redirect('/projects/edit/' . $id);
                return;
            }
        }

        if (empty($_POST['service_ids'])) {
            $_SESSION['error'] = "Vui lòng chọn ít nhất một danh mục";
            $this->redirect('/projects/edit/' . $id);
            return;
        }

        // Prepare data
        $data = [
            'title' => $_POST['title'],
            'slug' => $_POST['slug'],
            'description' => $_POST['description'] ?? '',
            'content' => $_POST['content'] ?? '',
            'client' => $_POST['client'] ?? '',
            'location' => $_POST['location'] ?? '',
            'project_date' => $_POST['project_date'] ?? '',
            'status' => $_POST['status'] ?? 1,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'show_on_home' => isset($_POST['show_on_home']) ? 1 : 0,
            'show_in_menu' => isset($_POST['show_in_menu']) ? 1 : 0,
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'status_label' => $_POST['status_label'] ?? 'Đã hoàn thành',
            'live_demo' => $_POST['live_demo'] ?? '',
            'tags' => $_POST['tags'] ?? '',
            'what_we_did_title' => $_POST['what_we_did_title'] ?? '',
            'what_we_did' => $_POST['what_we_did'] ?? '',
            'results_title' => $_POST['results_title'] ?? '',
            'results' => $_POST['results'] ?? '',
            'result_items' => $_POST['result_items'] ?? ''
        ];

        // Handle file uploads
        $uploadDir = __DIR__ . '/../../assets/uploads/projects/';
        $baseUrl = 'https://adminmtechjsc.gt.tc/assets/uploads/projects/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Handle main image - prioritize new upload, fallback to existing
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->handleFileUpload($_FILES['image'], $uploadDir);
            if ($imagePath) {
                $data['image'] = $baseUrl . $imagePath;
            }
        } elseif (!empty($_POST['existing_image'])) {
            $data['image'] = $_POST['existing_image'];
        }

        // Handle detail image - prioritize new upload, fallback to existing
        if (!empty($_FILES['detail_image']['name'])) {
            $detailImagePath = $this->handleFileUpload($_FILES['detail_image'], $uploadDir);
            if ($detailImagePath) {
                $data['detail_image'] = $baseUrl . $detailImagePath;
            }
        } elseif (!empty($_POST['existing_detail_image'])) {
            $data['detail_image'] = $_POST['existing_detail_image'];
        }

        // Handle what_we_did_image - prioritize new upload, fallback to existing
        if (!empty($_FILES['what_we_did_image']['name'])) {
            $whatWeDidImagePath = $this->handleFileUpload($_FILES['what_we_did_image'], $uploadDir);
            if ($whatWeDidImagePath) {
                $data['what_we_did_image'] = $baseUrl . $whatWeDidImagePath;
            }
        } elseif (!empty($_POST['existing_what_we_did_image'])) {
            $data['what_we_did_image'] = $_POST['existing_what_we_did_image'];
        }

        // Handle gallery images - get existing gallery first
        $existingProject = $this->model->getById($id);
        $existingGallery = [];
        if ($existingProject && !empty($existingProject['gallery'])) {
            $existingGallery = json_decode($existingProject['gallery'], true) ?: [];
        }

        $newGalleryImages = [];
        if (!empty($_FILES['gallery'])) {
            foreach ($_FILES['gallery']['name'] as $key => $name) {
                if (!empty($name)) {
                    $file = [
                        'name' => $name,
                        'type' => $_FILES['gallery']['type'][$key],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$key],
                        'error' => $_FILES['gallery']['error'][$key],
                        'size' => $_FILES['gallery']['size'][$key]
                    ];
                    $galleryPath = $this->handleFileUpload($file, $uploadDir);
                    if ($galleryPath) {
                        $newGalleryImages[] = $baseUrl . $galleryPath;
                    }
                }
            }
        }

        // Merge existing gallery with new images
        if (!empty($newGalleryImages)) {
            $allGallery = array_merge($existingGallery, $newGalleryImages);
            $data['gallery'] = json_encode($allGallery);
        } elseif (!empty($existingGallery)) {
            // Keep existing gallery if no new uploads
            $data['gallery'] = json_encode($existingGallery);
        }

        // Update project
        if ($this->model->update($id, $data)) {
            // Reorder projects
            $this->model->reorderProjects($id, $data['sort_order'], $project['sort_order'] ?? null);
            $this->model->normalizeOrders();
            
            // Save services for the project
            if (!empty($_POST['service_ids'])) {
                $this->model->addProjectServices($id, $_POST['service_ids']);
            }
            
            $_SESSION['success'] = 'Cập nhật dự án thành công!';
            $this->redirect('/projects');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/projects/edit/' . $id);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Method not allowed';
            $this->redirect('/projects');
            return;
        }

        // Check if project exists
        $project = $this->model->getById($id);
        if (!$project) {
            $_SESSION['error'] = 'Không tìm thấy dự án';
            $this->redirect('/projects');
            return;
        }

        // Delete project (soft delete)
        if ($this->model->delete($id)) {
            $this->model->normalizeOrders();
            $_SESSION['success'] = 'Xóa dự án thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }

        $this->redirect('/projects');
    }

    /**
     * Display trashed (soft-deleted) projects
     */
    public function trash()
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $projects = $this->model->getTrashed($perPage, $offset);
        $total    = $this->model->countTrashed();
        $totalPages = ceil($total / $perPage);

        $this->view('projects/trash', [
            'title'       => 'Thùng rác - Admin MTech',
            'page'        => 'projects',
            'projects'    => $projects,
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ]);
    }

    /**
     * Restore a soft-deleted project
     */
    public function restore($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Method not allowed';
            $this->redirect('/projects/trash');
            return;
        }

        if ($this->model->restore($id)) {
            $_SESSION['success'] = 'Khôi phục dự án thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }

        $this->redirect('/projects/trash');
    }

    /**
     * Permanently delete a project (hard delete)
     */
    public function hardDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Method not allowed';
            $this->redirect('/projects/trash');
            return;
        }

        if ($this->model->hardDelete($id)) {
            $_SESSION['success'] = 'Đã xóa vĩnh viễn dự án!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }

        $this->redirect('/projects/trash');
    }

    public function countAll()
    {
        try {
            $db = getDBConnection();
            $stmt = $db->query("SELECT COUNT(*) FROM projects WHERE status = 1");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Handle file upload
     * @param array $file File data from $_FILES
     * @param string $uploadDir Upload directory
     * @return string|false Filename or false on failure
     */
    private function handleFileUpload($file, $uploadDir)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('project_', true) . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }
}
