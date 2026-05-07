<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/ClientLogosModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ClientLogosController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new ClientLogosModel();
    }

    public function index()
    {
        $logos = $this->model->getAll();
        $this->view('client-logos/index', [
            'title' => 'Quản lý Logo đối tác - Admin MTech',
            'page'  => 'client-logos',
            'logos' => $logos,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function create()
    {
        $this->view('client-logos/create', [
            'title' => 'Thêm logo đối tác - Admin MTech',
            'page'  => 'client-logos',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/client-logos/create');
    }

    public function edit($id)
    {
        $logo = $this->model->getById($id);
        if (!$logo) {
            $_SESSION['error'] = 'Không tìm thấy logo';
            $this->redirect('/client-logos');
            return;
        }
        $this->view('client-logos/edit', [
            'title' => 'Chỉnh sửa logo - Admin MTech',
            'page'  => 'client-logos',
            'logo'  => $logo,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/client-logos/edit/' . $id);
    }

    public function delete($id)
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/client-logos');
    }
}
