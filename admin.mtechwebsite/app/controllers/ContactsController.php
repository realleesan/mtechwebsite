<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/ContactsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ContactsController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new ContactsModel();
    }

    public function index()
    {
        $page         = max(1, (int)($_GET['page'] ?? 1));
        $perPage      = 20;
        $offset       = ($page - 1) * $perPage;
        $search       = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status_filter'] ?? '';

        $contacts     = $this->model->getAll($perPage, $offset, $search ?: null, $statusFilter !== '' ? $statusFilter : null);
        $total        = $this->model->count($statusFilter !== '' ? (int)$statusFilter : null, $search ?: null);
        $totalPages   = ceil($total / $perPage);
        $unreadCount  = $this->model->count(0);
        $trashedCount = $this->model->countTrashed();

        $this->view('contacts/index', [
            'title'        => 'Quản lý Liên hệ - Admin MTech',
            'page'         => 'contacts',
            'contacts'     => $contacts,
            'total'        => $total,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'search'       => $search,
            'statusFilter' => $statusFilter,
            'unreadCount'  => $unreadCount,
            'trashedCount' => $trashedCount,
            'admin'        => AuthMiddleware::getAdmin(),
        ]);
    }

    public function show($id)
    {
        $contact = $this->model->getById($id);
        if (!$contact) {
            $_SESSION['error'] = 'Không tìm thấy liên hệ';
            $this->redirect('/contacts');
            return;
        }
        // Đánh dấu đã đọc nếu chưa đọc
        if ((int)($contact['status'] ?? 0) === 0) {
            $this->model->updateStatus($id, 1);
            $contact['status'] = 1;
        }

        $this->view('contacts/view', [
            'title'   => 'Chi tiết liên hệ - Admin MTech',
            'page'    => 'contacts',
            'contact' => $contact,
            'admin'   => AuthMiddleware::getAdmin(),
        ]);
    }

    public function edit($id)
    {
        $contact = $this->model->getById($id);
        if (!$contact) {
            $_SESSION['error'] = 'Không tìm thấy liên hệ';
            $this->redirect('/contacts');
            return;
        }
        // Đánh dấu đã đọc nếu chưa đọc
        if ((int)($contact['status'] ?? 0) === 0) {
            $this->model->updateStatus($id, 1);
            $contact['status'] = 1;
        }

        $this->view('contacts/edit', [
            'title'   => 'Chỉnh sửa liên hệ - Admin MTech',
            'page'    => 'contacts',
            'contact' => $contact,
            'admin'   => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        $contact = $this->model->getById($id);
        if (!$contact) {
            $_SESSION['error'] = 'Không tìm thấy liên hệ';
            $this->redirect('/contacts');
            return;
        }

        $adminReply = trim($_POST['admin_reply'] ?? '');
        $status     = (int)($_POST['status'] ?? $contact['status']);

        // Nếu có nội dung phản hồi → tự động chuyển status = 2 (đã phản hồi)
        if (!empty($adminReply)) {
            $status = 2;
        }

        $this->model->update($id, $status, $adminReply ?: null);

        // Gửi email phản hồi nếu có nội dung mới và khác nội dung cũ
        if (!empty($adminReply) && $adminReply !== ($contact['admin_reply'] ?? '')) {
            try {
                require_once __DIR__ . '/../services/EmailNotificationService.php';
                $emailService = new EmailNotificationService();
                $result = $emailService->sendContactReply($contact, $adminReply);
                if ($result['success']) {
                    $_SESSION['success'] = 'Đã lưu phản hồi và gửi email đến ' . $contact['email'];
                } else {
                    $_SESSION['success'] = 'Đã lưu phản hồi nhưng không gửi được email: ' . $result['message'];
                }
            } catch (\Exception $e) {
                error_log('ContactsController::update() - Email error: ' . $e->getMessage());
                $_SESSION['success'] = 'Đã lưu phản hồi nhưng không gửi được email.';
            }
        } else {
            $_SESSION['success'] = 'Đã cập nhật liên hệ thành công';
        }

        $this->redirect('/contacts/view/' . $id);
    }

    public function delete($id)
    {
        $this->model->softDelete($id);
        $_SESSION['success'] = 'Đã chuyển liên hệ vào thùng rác';
        $this->redirect('/contacts');
    }

    public function trash()
    {
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 20;
        $offset     = ($page - 1) * $perPage;
        $contacts   = $this->model->getTrashed($perPage, $offset);
        $total      = $this->model->countTrashed();
        $totalPages = ceil($total / $perPage);

        $this->view('contacts/trash', [
            'title'       => 'Thùng rác - Liên hệ - Admin MTech',
            'page'        => 'contacts',
            'contacts'    => $contacts,
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'admin'       => AuthMiddleware::getAdmin(),
        ]);
    }

    public function restore($id)
    {
        $this->model->restore($id);
        $_SESSION['success'] = 'Đã khôi phục liên hệ';
        $this->redirect('/contacts/trash');
    }

    public function hardDelete($id)
    {
        $this->model->hardDelete($id);
        $_SESSION['success'] = 'Đã xóa vĩnh viễn liên hệ';
        $this->redirect('/contacts/trash');
    }
}
