<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/CategoriesModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CategoriesController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new CategoriesModel();
    }

    public function index()
    {
        $categories = $this->model->getAllCategories();
        $this->view('categories/index', [
            'title'      => 'Quản lý Dịch vụ - Admin MTech',
            'page'       => 'categories',
            'categories' => $categories,
            'admin'      => AuthMiddleware::getAdmin(),
        ]);
    }

    public function create()
    {
        $this->view('categories/create', [
            'title' => 'Thêm dịch vụ - Admin MTech',
            'page'  => 'categories',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/categories/create');
    }

    public function edit($id)
    {
        $category = $this->model->getCategoryById($id);
        if (!$category) {
            $_SESSION['error'] = 'Không tìm thấy dịch vụ';
            $this->redirect('/categories');
            return;
        }
        $this->view('categories/edit', [
            'title'    => 'Chỉnh sửa dịch vụ - Admin MTech',
            'page'     => 'categories',
            'category' => $category,
            'admin'    => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/categories/edit/' . $id);
    }

    public function delete($id)
    {
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/categories');
    }
}
