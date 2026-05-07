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
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $contacts   = $this->model->getAll($perPage, $offset);
        $total      = $this->model->count();
        $totalPages = ceil($total / $perPage);

        $this->view('contacts/index', [
            'title'       => 'Quản lý Liên hệ - Admin MTech',
            'page'        => 'contacts',
            'contacts'    => $contacts,
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'admin'       => AuthMiddleware::getAdmin(),
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
        // Đánh dấu đã đọc
        $this->model->updateStatus($id, 1);

        $this->view('contacts/view', [
            'title'   => 'Chi tiết liên hệ - Admin MTech',
            'page'    => 'contacts',
            'contact' => $contact,
            'admin'   => AuthMiddleware::getAdmin(),
        ]);
    }

    public function delete($id)
    {
        $this->model->delete($id);
        $_SESSION['success'] = 'Đã xóa liên hệ';
        $this->redirect('/contacts');
    }
}
