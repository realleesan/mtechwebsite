<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/AwardsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AwardsController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new AwardsModel();
    }

    public function index()
    {
        $awards = $this->model->getAll();
        $this->view('awards/index', [
            'title'  => 'Quản lý Giải thưởng - Admin MTech',
            'page'   => 'awards',
            'awards' => $awards,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    public function create()
    {
        $this->view('awards/create', [
            'title' => 'Thêm giải thưởng - Admin MTech',
            'page'  => 'awards',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/awards/create');
    }

    public function edit($id)
    {
        $award = $this->model->getById($id);
        if (!$award) {
            $_SESSION['error'] = 'Không tìm thấy giải thưởng';
            $this->redirect('/awards');
            return;
        }
        $this->view('awards/edit', [
            'title' => 'Chỉnh sửa giải thưởng - Admin MTech',
            'page'  => 'awards',
            'award' => $award,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/awards/edit/' . $id);
    }

    public function delete($id)
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/awards');
    }
}
