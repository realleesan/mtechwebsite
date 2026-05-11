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
     * API endpoint lấy dữ liệu biểu đồ thống kê truy cập
     * GET /api/access-stats?period=7days|month|year|all
     */
    public function getAccessStats()
    {
        // Set header for JSON response
        header('Content-Type: application/json');
        
        // Get period from query parameter
        $period = $_GET['period'] ?? '7days';
        
        // Validate period
        $validPeriods = ['7days', 'month', 'year', 'all'];
        if (!in_array($period, $validPeriods)) {
            $period = '7days';
        }
        
        try {
            // Get chart data
            $chartData = $this->accessLogsModel->getChartData($period);
            
            // Get current stats for the period
            $stats = $this->getPeriodStats($period);
            
            // Return JSON response
            echo json_encode([
                'success' => true,
                'labels' => $chartData['labels'],
                'data' => $chartData['data'],
                'stats' => $stats,
                'period' => $period
            ]);
            
        } catch (Exception $e) {
            // Return error response
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load data',
                'labels' => [],
                'data' => [],
                'stats' => ['today' => 0, 'month' => 0, 'total' => 0]
            ]);
        }
        exit;
    }

    /**
     * Lấy thống kê theo khoảng thời gian
     */
    private function getPeriodStats($period)
    {
        switch ($period) {
            case '7days':
                return [
                    'today' => $this->accessLogsModel->getTodayStats()['visits'] ?? 0,
                    'month' => $this->accessLogsModel->getMonthStats()['visits'] ?? 0,
                    'total' => $this->accessLogsModel->getTotalVisits()['total'] ?? 0
                ];
            case 'month':
                $monthData = $this->accessLogsModel->getMonthData();
                $totalMonth = array_sum($monthData['data']);
                return [
                    'today' => $this->accessLogsModel->getTodayStats()['visits'] ?? 0,
                    'month' => $totalMonth,
                    'total' => $this->accessLogsModel->getTotalVisits()['total'] ?? 0
                ];
            case 'year':
                $yearData = $this->accessLogsModel->getYearData();
                $totalYear = array_sum($yearData['data']);
                return [
                    'today' => $this->accessLogsModel->getTodayStats()['visits'] ?? 0,
                    'month' => $this->accessLogsModel->getMonthStats()['visits'] ?? 0,
                    'total' => $totalYear
                ];
            case 'all':
                return [
                    'today' => $this->accessLogsModel->getTodayStats()['visits'] ?? 0,
                    'month' => $this->accessLogsModel->getMonthStats()['visits'] ?? 0,
                    'total' => $this->accessLogsModel->getTotalVisits()['total'] ?? 0
                ];
            default:
                return [
                    'today' => 0,
                    'month' => 0,
                    'total' => 0
                ];
        }
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
