<?php
/**
 * DashboardController - Trang chủ Admin Panel
 * Hiển thị thống kê tổng quan
 */

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/ContactsModel.php';
require_once __DIR__ . '/../models/JobApplicationModel.php';
require_once __DIR__ . '/../models/BlogsModel.php';
require_once __DIR__ . '/../models/ProjectsModel.php';
require_once __DIR__ . '/../models/AccessLogsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class DashboardController extends BaseController
{
    private $contactsModel;
    private $jobAppModel;
    private $blogsModel;
    private $projectsModel;
    private $accessLogsModel;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->contactsModel = new ContactsModel();
        $this->jobAppModel   = new JobApplicationModel();
        $this->blogsModel    = new BlogsModel();
        $this->projectsModel = new ProjectsModel();
        $this->accessLogsModel = new AccessLogsModel();
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

        // Lấy thống kê truy cập người dùng
        $accessStats = [
            'today' => $this->accessLogsModel->getTodayStats(),
            'month'  => $this->accessLogsModel->getMonthStats(),
            'total'  => $this->accessLogsModel->getTotalVisits()
        ];

        // Lấy 5 liên hệ mới nhất
        $recentContacts = $this->contactsModel->getAll(5, 0);

        // Lấy 5 đơn ứng tuyển mới nhất
        $recentJobApps = $this->jobAppModel->getAllApplications('all', 5);

        // Lấy 5 tin tức mới nhất
        $recentBlogs = $this->blogsModel->getRecentBlogs(5);

        // Lấy 5 dự án mới nhất
        $recentProjects = $this->projectsModel->getAll(5, 0, 1);

        // Lấy thống kê truy cập người dùng
        $accessStats = [
            'today' => $this->accessLogsModel->getTodayStats(),
            'month'  => $this->accessLogsModel->getMonthStats(),
            'total'  => $this->accessLogsModel->getTotalVisits()
        ];

        $this->view('dashboard/index', [
            'title'          => 'Dashboard - Admin MTech',
            'page'           => 'dashboard',
            'stats'          => $stats,
            'recentContacts' => $recentContacts,
            'recentJobApps'  => $recentJobApps,
            'recentBlogs'    => $recentBlogs,
            'recentProjects' => $recentProjects,
            'access_stats'   => $accessStats,
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
