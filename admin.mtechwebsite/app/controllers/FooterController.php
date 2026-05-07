<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/FooterModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class FooterController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new FooterModel();
    }

    public function edit()
    {
        $footer = $this->model->getFooterData();
        $this->view('footer/edit', [
            'title'  => 'Quản lý Footer - Admin MTech',
            'page'   => 'footer',
            'footer' => $footer,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/footer');
            return;
        }
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/footer');
    }
}
