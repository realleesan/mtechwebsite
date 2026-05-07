<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/TeamsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class TeamsController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new TeamsModel();
    }

    public function index()
    {
        $teams = $this->model->getAll();

        $this->view('teams/index', [
            'title' => 'Quản lý Đội ngũ - Admin MTech',
            'page'  => 'teams',
            'teams' => $teams,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function create()
    {
        $this->view('teams/create', [
            'title' => 'Thêm thành viên - Admin MTech',
            'page'  => 'teams',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teams');
            return;
        }

        $data = [
            'name'       => trim($_POST['name'] ?? ''),
            'position'   => trim($_POST['position'] ?? ''),
            'image'      => trim($_POST['image'] ?? ''),
            'bio'        => trim($_POST['bio'] ?? ''),
            'status'     => (int)($_POST['status'] ?? 1),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];

        if (empty($data['name']) || empty($data['position'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc';
            $this->redirect('/teams/create');
            return;
        }

        if ($this->model->create($data)) {
            $_SESSION['success'] = 'Đã thêm thành viên thành công';
            $this->redirect('/teams');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm thành viên';
            $this->redirect('/teams/create');
        }
    }

    public function edit($id)
    {
        $team = $this->model->getById($id);

        if (!$team) {
            $_SESSION['error'] = 'Không tìm thấy thành viên';
            $this->redirect('/teams');
            return;
        }

        $this->view('teams/edit', [
            'title' => 'Chỉnh sửa thành viên - Admin MTech',
            'page'  => 'teams',
            'team'  => $team,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teams');
            return;
        }

        $data = [
            'name'       => trim($_POST['name'] ?? ''),
            'position'   => trim($_POST['position'] ?? ''),
            'image'      => trim($_POST['image'] ?? ''),
            'bio'        => trim($_POST['bio'] ?? ''),
            'status'     => (int)($_POST['status'] ?? 1),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];

        if (empty($data['name']) || empty($data['position'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc';
            $this->redirect('/teams/edit/' . $id);
            return;
        }

        if ($this->model->update($id, $data)) {
            $_SESSION['success'] = 'Đã cập nhật thành viên thành công';
            $this->redirect('/teams');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật thành viên';
            $this->redirect('/teams/edit/' . $id);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teams');
            return;
        }

        if ($this->model->delete($id)) {
            $_SESSION['success'] = 'Đã xóa thành viên thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa thành viên';
        }
        
        $this->redirect('/teams');
    }
}
