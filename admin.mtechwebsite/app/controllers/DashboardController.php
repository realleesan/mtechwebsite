<?php
/**
 * DashboardController - Trang chủ Admin Panel
 * Hiển thị thống kê tổng quan
 */

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/ContactsModel.php';
require_once __DIR__ . '/../models/JobApplicationModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class DashboardController extends BaseController
{
    private $contactsModel;
    private $jobAppModel;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->contactsModel = new ContactsModel();
        $this->jobAppModel   = new JobApplicationModel();
    }

    public function index()
    {
        $jobStats = $this->jobAppModel->getStatistics();

        // Lấy thống kê tổng quan
        $stats = [
            'total_blogs'    => $this->countTable('blogs',    'status = 1'),
            'total_projects' => $this->countTable('projects', 'status = 1'),
            'total_contacts' => $this->contactsModel->count(),
            'new_contacts'   => $this->contactsModel->countUnread(),
            'total_jobs'     => $jobStats['total']   ?? 0,
            'new_jobs'       => $jobStats['pending']  ?? 0,
        ];

        // Lấy 5 liên hệ mới nhất
        $recentContacts = $this->contactsModel->getAll(5, 0);

        // Lấy 5 đơn ứng tuyển mới nhất
        $recentJobApps = $this->jobAppModel->getAllApplications('all', 5);

        $this->view('dashboard/index', [
            'title'          => 'Dashboard - Admin MTech',
            'page'           => 'dashboard',
            'stats'          => $stats,
            'recentContacts' => $recentContacts,
            'recentJobApps'  => $recentJobApps,
            'admin'          => AuthMiddleware::getAdmin(),
        ]);
    }

    /**
     * Đếm số bản ghi trong bảng với điều kiện tùy chọn
     */
    private function countTable($table, $where = '')
    {
        try {
            $db  = getDBConnection();
            $sql = "SELECT COUNT(*) FROM `{$table}`" . ($where ? " WHERE {$where}" : '');
            return (int) $db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
