<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/HeaderModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class HeaderController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new HeaderModel();
    }

    public function edit()
    {
        $header = $this->model->getSettingsWithFallback();
        $this->view('header/edit', [
            'title'  => 'Quản lý Header - Admin MTech',
            'page'   => 'header',
            'header' => $header,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/header');
            return;
        }
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/header');
    }
}
