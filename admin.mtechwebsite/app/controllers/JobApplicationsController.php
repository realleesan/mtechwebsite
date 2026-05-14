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

    // ----------------------------------------------------------------
    // GET /job-applications
    // ----------------------------------------------------------------
    public function index()
    {
        $pageNum      = max(1, (int)($_GET['page'] ?? 1));
        $perPage      = 15;
        $offset       = ($pageNum - 1) * $perPage;
        $statusFilter = $_GET['status_filter'] ?? '';

        $applications = $this->model->getAllApplications(
            $statusFilter ?: 'all',
            $perPage,
            $offset
        );
        $total        = $this->model->countAll($statusFilter ?: null);
        $totalPages   = max(1, (int)ceil($total / $perPage));
        $pendingCount = $this->model->countByStatus('pending');

        $this->view('job-applications/index', [
            'title'        => 'Quản lý Đơn ứng tuyển - Admin MTech',
            'page'         => 'job-applications',
            'applications' => $applications,
            'total'        => $total,
            'pageNum'      => $pageNum,
            'totalPages'   => $totalPages,
            'statusFilter' => $statusFilter,
            'pendingCount' => $pendingCount,
            'admin'        => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------------------------------
    // GET /job-applications/view/{id}
    // ----------------------------------------------------------------
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

    // ----------------------------------------------------------------
    // GET /job-applications/edit/{id}
    // ----------------------------------------------------------------
    public function edit($id)
    {
        $app = $this->model->getApplicationById($id);
        if (!$app) {
            $_SESSION['error'] = 'Không tìm thấy đơn ứng tuyển';
            $this->redirect('/job-applications');
            return;
        }

        $this->view('job-applications/edit', [
            'title'       => 'Cập nhật trạng thái đơn - Admin MTech',
            'page'        => 'job-applications',
            'application' => $app,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------------------------------
    // POST /job-applications/update/{id}
    // ----------------------------------------------------------------
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/job-applications');
            return;
        }

        $app = $this->model->getApplicationById($id);
        if (!$app) {
            $_SESSION['error'] = 'Không tìm thấy đơn ứng tuyển';
            $this->redirect('/job-applications');
            return;
        }

        $status        = $_POST['status']         ?? 'pending';
        $adminNote     = trim($_POST['admin_note']     ?? '');
        $employerReply = trim($_POST['employer_reply'] ?? '');

        $allowed = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowed)) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ';
            $this->redirect('/job-applications/edit/' . $id);
            return;
        }

        $oldStatus = $app['status'] ?? 'pending';

        // Lưu vào DB
        $this->model->updateStatusAndNote($id, $status, $adminNote ?: null, $employerReply ?: null);

        // Gửi email khi chuyển sang approved hoặc rejected
        if (in_array($status, ['approved', 'rejected']) && $status !== $oldStatus) {
            try {
                require_once __DIR__ . '/../services/EmailNotificationService.php';
                $emailService = new EmailNotificationService();
                $result = $emailService->sendJobApplicationStatusEmail($app, $status, $employerReply ?: null);
                if ($result['success']) {
                    $_SESSION['success'] = 'Đã cập nhật trạng thái và gửi email thông báo đến ứng viên';
                } else {
                    $_SESSION['success'] = 'Đã cập nhật trạng thái nhưng không gửi được email: ' . $result['message'];
                }
            } catch (\Exception $e) {
                error_log('JobApplicationsController::update() - Email error: ' . $e->getMessage());
                $_SESSION['success'] = 'Đã cập nhật trạng thái nhưng không gửi được email.';
            }
        } else {
            $_SESSION['success'] = 'Đã cập nhật trạng thái đơn ứng tuyển';
        }

        // Redirect về trang index
        $this->redirect('/job-applications');
    }

    // ----------------------------------------------------------------
    // GET /job-applications/download-cv/{id}
    // ----------------------------------------------------------------
    public function downloadCv($id)
    {
        $app = $this->model->getApplicationById($id);
        if (!$app) {
            $_SESSION['error'] = 'Không tìm thấy đơn ứng tuyển';
            $this->redirect('/job-applications');
            return;
        }

        // Ưu tiên cv_url (absolute URL) — được thêm từ migration 027
        // Fallback sang cv_file nếu cv_url chưa có
        $cvUrl = !empty($app['cv_url']) ? $app['cv_url'] : null;

        // Nếu cv_url chưa có, thử build từ cv_file
        if (!$cvUrl && !empty($app['cv_file'])) {
            $cvFile = $app['cv_file'];
            if (filter_var($cvFile, FILTER_VALIDATE_URL)) {
                // cv_file đã là absolute URL (dữ liệu trung gian)
                $cvUrl = $cvFile;
            } else {
                // cv_file là relative path → build URL
                $cvUrl = 'https://truongvinalogistics.com.vn/' . ltrim($cvFile, '/');
            }
        }

        if (!$cvUrl) {
            $_SESSION['error'] = 'Không tìm thấy file CV';
            $this->redirect('/job-applications/view/' . $id);
            return;
        }

        $fileName = 'CV_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $app['full_name'] ?? 'ung_vien') . '.pdf';

        // Redirect thẳng sang URL public — đơn giản, không cần proxy
        // Trình duyệt sẽ tải file trực tiếp từ user site
        header('Location: ' . $cvUrl);
        exit;
    }

    // ----------------------------------------------------------------
    // POST /job-applications/delete/{id}  → soft delete
    // ----------------------------------------------------------------
    public function delete($id)
    {
        $this->model->softDelete($id);
        $_SESSION['success'] = 'Đã chuyển đơn vào thùng rác';
        $this->redirect('/job-applications');
    }

    // ----------------------------------------------------------------
    // GET /job-applications/trash
    // ----------------------------------------------------------------
    public function trash()
    {
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 20;
        $offset     = ($page - 1) * $perPage;
        $apps       = $this->model->getTrashed($perPage, $offset);
        $total      = $this->model->countTrashed();
        $totalPages = (int)ceil($total / $perPage);

        $this->view('job-applications/trash', [
            'title'        => 'Thùng rác - Đơn ứng tuyển - Admin MTech',
            'page'         => 'job-applications',
            'applications' => $apps,
            'total'        => $total,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'admin'        => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------------------------------
    // POST /job-applications/restore/{id}
    // ----------------------------------------------------------------
    public function restore($id)
    {
        $this->model->restore($id);
        $_SESSION['success'] = 'Đã khôi phục đơn ứng tuyển';
        $this->redirect('/job-applications/trash');
    }

    // ----------------------------------------------------------------
    // POST /job-applications/hard-delete/{id}
    // ----------------------------------------------------------------
    public function hardDelete($id)
    {
        $this->model->hardDelete($id);
        $_SESSION['success'] = 'Đã xóa vĩnh viễn đơn ứng tuyển';
        $this->redirect('/job-applications/trash');
    }
}
