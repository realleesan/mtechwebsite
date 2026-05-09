<?php
/**
 * AccessLogsModel.php
 * 
 * Model xử lý dữ liệu bảng `access_logs` cho tracking truy cập người dùng
 * Chịu trách nhiệm ghi log và lấy thống kê truy cập
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
     * Ghi log truy cập mới
     * @param string $visitorId ID duy nhất của visitor
     * @param string $pageUrl URL trang truy cập
     * @return bool Kết quả insert
     */
    public function logVisit($visitorId, $pageUrl)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}` (visitor_id, page_url, ip_address, user_agent, domain, created_at) 
                 VALUES (?, ?, ?, ?, 'user', NOW())"
            );
            
            return $stmt->execute([
                $visitorId,
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
     * Lấy thống kê truy cập hôm nay
     * @return array Mảng chứa số lượt truy cập hôm nay
     */
    public function getTodayStats()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as visits 
                 FROM `{$this->table}` 
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
     * Lấy thống kê truy cập tháng này
     * @return array Mảng chứa số lượt truy cập tháng này
     */
    public function getMonthStats()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as visits 
                 FROM `{$this->table}` 
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
     * Lấy tổng số lượt truy cập
     * @return array Mảng chứa tổng lượt truy cập
     */
    public function getTotalVisits()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as total 
                 FROM `{$this->table}` 
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
     * Lấy danh sách truy cập gần đây (cho dashboard chi tiết)
     * @param int $limit Số lượng bản ghi
     * @param int $offset Vị trí bắt đầu
     * @return array Danh sách truy cập
     */
    public function getRecentVisits($limit = 10, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT visitor_id, page_url, ip_address, created_at 
                 FROM `{$this->table}` 
                 WHERE domain = 'user' 
                 ORDER BY created_at DESC 
                 LIMIT ? OFFSET ?"
            );
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('AccessLogsModel::getRecentVisits() - ' . $e->getMessage());
            return [];
        }
    }
}
