<?php
/**
 * AccessLogsModel.php
 * 
 * Model xử lý dữ liệu bảng `access_logs` cho admin dashboard
 * Chịu trách nhiệm đọc thống kê truy cập người dùng
 */

class AccessLogsModel
{
    /** @var PDO */
    private $db;
    
    /** @var string Tên bảng */
    private $table = 'access_logs';

    /**
     * Constructor - Khởi tạo kết nối database
     */
    public function __construct($database = null)
    {
        if ($database) {
            $this->db = $database;
        } else {
            require_once __DIR__ . '/../../core/database.php';
            $this->db = getDBConnection();
        }
    }

    /**
     * Lấy thống kê truy cập hôm nay của người dùng
     * @return array Mảng chứa số lượt truy cập hôm nay
     */
    public function getTodayStats()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as visits 
                 FROM {$this->table} 
                 WHERE DATE(created_at) = CURDATE() AND domain = 'user'"
            );
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['visits' => 0];
        } catch (PDOException $e) {
            error_log('AccessLogsModel::getTodayStats() - ' . $e->getMessage());
            return ['visits' => 0];
        }
    }

    /**
     * Lấy thống kê truy cập tháng này của người dùng
     * @return array Mảng chứa số lượt truy cập tháng này
     */
    public function getMonthStats()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as visits 
                 FROM {$this->table} 
                 WHERE MONTH(created_at) = MONTH(CURDATE()) 
                   AND YEAR(created_at) = YEAR(CURDATE()) 
                   AND domain = 'user'"
            );
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['visits' => 0];
        } catch (PDOException $e) {
            error_log('AccessLogsModel::getMonthStats() - ' . $e->getMessage());
            return ['visits' => 0];
        }
    }

    /**
     * Lấy tổng số lượt truy cập của người dùng
     * @return array Mảng chứa tổng lượt truy cập
     */
    public function getTotalVisits()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as total 
                 FROM {$this->table} 
                 WHERE domain = 'user'"
            );
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['total' => 0];
        } catch (PDOException $e) {
            error_log('AccessLogsModel::getTotalVisits() - ' . $e->getMessage());
            return ['total' => 0];
        }
    }

    /**
     * Ghi log truy cập admin
     * @param int $adminId ID của admin
     * @param string $pageUrl URL trang truy cập
     * @return bool Kết quả insert
     */
    public function logVisit($adminId, $pageUrl)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (admin_id, page_url, ip_address, user_agent, domain, created_at) 
                 VALUES (?, ?, ?, ?, 'admin', NOW())"
            );
            
            return $stmt->execute([
                $adminId,
                $pageUrl,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (PDOException $e) {
            error_log('AccessLogsModel::logVisit() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy dữ liệu truy cập theo khoảng thời gian cho biểu đồ
     * @param string $period Khoảng thời gian: 7days, month, year, all
     * @return array Mảng chứa labels và data cho biểu đồ
     */
    public function getChartData($period = '7days')
    {
        try {
            switch ($period) {
                case '7days':
                    return $this->getLast7DaysData();
                case 'month':
                    return $this->getMonthData();
                case 'year':
                    return $this->getYearData();
                case 'all':
                    return $this->getAllData();
                default:
                    return $this->getLast7DaysData();
            }
        } catch (PDOException $e) {
            error_log('AccessLogsModel::getChartData() - ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Lấy dữ liệu 7 ngày gần nhất
     */
    public function getLast7DaysData()
    {
        $sql = "
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as visits
            FROM {$this->table}
            WHERE domain = 'user' 
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Điền vào các ngày thiếu dữ liệu
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateFormatted = date('d/m', strtotime($date));
            
            $visits = 0;
            foreach ($results as $row) {
                if ($row['date'] === $date) {
                    $visits = (int) $row['visits'];
                    break;
                }
            }
            
            $labels[] = $dateFormatted;
            $data[] = $visits;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Lấy dữ liệu 30 ngày gần nhất (hiển thị cách 3 ngày)
     */
    public function getMonthData()
    {
        $sql = "
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as visits
            FROM {$this->table}
            WHERE domain = 'user' 
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i -= 3) { // Lấy cách 3 ngày
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateFormatted = date('d/m', strtotime($date));
            
            $visits = 0;
            foreach ($results as $row) {
                if ($row['date'] === $date) {
                    $visits = (int) $row['visits'];
                    break;
                }
            }
            
            $labels[] = $dateFormatted;
            $data[] = $visits;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Lấy dữ liệu 12 tháng gần nhất
     */
    public function getYearData()
    {
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as visits
            FROM {$this->table}
            WHERE domain = 'user' 
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthFormatted = date('M', strtotime($month));
            
            $visits = 0;
            foreach ($results as $row) {
                if ($row['month'] === $month) {
                    $visits = (int) $row['visits'];
                    break;
                }
            }
            
            $labels[] = $monthFormatted;
            $data[] = $visits;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Lấy dữ liệu tất cả các năm
     */
    private function getAllData()
    {
        $sql = "
            SELECT 
                YEAR(created_at) as year,
                COUNT(*) as visits
            FROM {$this->table}
            WHERE domain = 'user'
            GROUP BY YEAR(created_at)
            ORDER BY year ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [];
        $labels = [];
        
        foreach ($results as $row) {
            $labels[] = $row['year'];
            $data[] = (int) $row['visits'];
        }
        
        return ['labels' => $labels, 'data' => $data];
    }
}
