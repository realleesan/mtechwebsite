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
}
