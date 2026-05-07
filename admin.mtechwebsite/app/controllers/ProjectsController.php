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

        if (!empty($search)) {
            $projects = $this->model->search($search, $perPage);
            $total    = count($projects);
        } else {
            $projects = $this->model->getAll($perPage, $offset);
            $total    = $this->model->count();
        }
        $totalPages = ceil($total / $perPage);

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
        $this->view('projects/create', [
            'title' => 'Thêm dự án - Admin MTech',
            'page'  => 'projects',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/projects/create');
    }

    public function edit($id)
    {
        $project = $this->model->getById($id);
        if (!$project) {
            $_SESSION['error'] = 'Không tìm thấy dự án';
            $this->redirect('/projects');
            return;
        }
        $this->view('projects/edit', [
            'title'   => 'Chỉnh sửa dự án - Admin MTech',
            'page'    => 'projects',
            'project' => $project,
            'admin'   => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/projects/edit/' . $id);
    }

    public function delete($id)
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/projects');
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
}
