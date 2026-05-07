<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/JobApplicationModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class JobApplicationsController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new JobApplicationModel();
    }

    public function index()
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $applications = $this->model->getAllApplications('all', $perPage, $offset);
        $stats        = $this->model->getStatistics();
        $total        = $stats['total'] ?? 0;
        $totalPages   = ceil($total / $perPage);

        $this->view('job-applications/index', [
            'title'        => 'Quản lý Đơn ứng tuyển - Admin MTech',
            'page'         => 'job-applications',
            'applications' => $applications,
            'total'        => $total,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'admin'        => AuthMiddleware::getAdmin(),
        ]);
    }

    public function show($id)
    {
        $app = $this->model->getApplicationById($id);
        if (!$app) {
            $_SESSION['error'] = 'Không tìm thấy đơn ứng tuyển';
            $this->redirect('/job-applications');
            return;
        }

        $this->view('job-applications/view', [
            'title'       => 'Chi tiết đơn ứng tuyển - Admin MTech',
            'page'        => 'job-applications',
            'application' => $app,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/job-applications');
            return;
        }

        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ';
            $this->redirect('/job-applications/view/' . $id);
            return;
        }

        $this->model->updateStatus($id, $status);
        $_SESSION['success'] = 'Đã cập nhật trạng thái';
        $this->redirect('/job-applications/view/' . $id);
    }
}
