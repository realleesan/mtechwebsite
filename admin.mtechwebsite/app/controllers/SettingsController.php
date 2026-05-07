<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class SettingsController extends BaseController
{
    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    public function index()
    {
        $this->view('settings/index', [
            'title' => 'Cài đặt - Admin MTech',
            'page'  => 'settings',
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings');
            return;
        }
        $_SESSION['error'] = 'Chức năng đang phát triển';
        $this->redirect('/settings');
    }
}
