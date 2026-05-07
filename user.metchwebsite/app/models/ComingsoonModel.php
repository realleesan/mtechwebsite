<?php
/**
 * ComingsoonModel.php
 * 
 * Model quản lý cấu hình Coming Soon / Maintenance Mode
 * - Kiểm tra trạng thái active/inactive
 - Lấy thông tin countdown target date
 - Lưu email subscriber
 */

require_once __DIR__ . '/../../core/database.php';

class ComingsoonModel
{
    private $pdo;
    
    public function __construct()
    {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Lấy toàn bộ cấu hình coming soon
     * @return array|null
     */
    public function getSettings()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM comingsoon_settings WHERE id = 1 LIMIT 1");
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('ComingsoonModel::getSettings Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Kiểm tra xem Coming Soon có đang active không
     * @return bool
     */
    public function isComingSoonActive()
    {
        try {
            $stmt = $this->pdo->query("SELECT is_active FROM comingsoon_settings WHERE id = 1 LIMIT 1");
            $result = $stmt->fetch();
            return $result && (int)$result['is_active'] === 1;
        } catch (PDOException $e) {
            error_log('ComingsoonModel::isComingSoonActive Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy ngày target cho countdown
     * @return string|null
     */
    public function getTargetDate()
    {
        try {
            $stmt = $this->pdo->query("SELECT target_date FROM comingsoon_settings WHERE id = 1 LIMIT 1");
            $result = $stmt->fetch();
            return $result ? $result['target_date'] : null;
        } catch (PDOException $e) {
            error_log('ComingsoonModel::getTargetDate Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy target date dạng timestamp (milliseconds) cho JavaScript
     * @return int|null
     */
    public function getTargetTimestamp()
    {
        $targetDate = $this->getTargetDate();
        if ($targetDate) {
            return strtotime($targetDate) * 1000;
        }
        return null;
    }
    
    /**
     * Lưu email subscriber
     * @param string $email
     * @return array ['success' => bool, 'message' => string]
     */
    public function saveSubscriber($email)
    {
        try {
            $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Email không hợp lệ.'];
            }
            
            $stmt = $this->pdo->prepare("INSERT INTO comingsoon_subscribers (email) VALUES (:email)");
            $stmt->execute([':email' => $email]);
            
            return ['success' => true, 'message' => 'Cảm ơn bạn đã đăng ký! Chúng tôi sẽ thông báo khi website ra mắt.'];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['success' => false, 'message' => 'Email này đã được đăng ký trước đó.'];
            }
            error_log('ComingsoonModel::saveSubscriber Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'];
        }
    }
    
    /**
     * Bật/tắt Coming Soon mode
     * @param bool $active
     * @return bool
     */
    public function setActive($active)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE comingsoon_settings SET is_active = :active WHERE id = 1");
            return $stmt->execute([':active' => $active ? 1 : 0]);
        } catch (PDOException $e) {
            error_log('ComingsoonModel::setActive Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật target date
     * @param string $date Format: Y-m-d H:i:s
     * @return bool
     */
    public function setTargetDate($date)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE comingsoon_settings SET target_date = :date WHERE id = 1");
            return $stmt->execute([':date' => $date]);
        } catch (PDOException $e) {
            error_log('ComingsoonModel::setTargetDate Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy danh sách tất cả subscribers
     * @return array
     */
    public function getAllSubscribers()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM comingsoon_subscribers ORDER BY subscribed_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('ComingsoonModel::getAllSubscribers Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Đếm số lượng subscribers
     * @return int
     */
    public function countSubscribers()
    {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM comingsoon_subscribers");
            $result = $stmt->fetch();
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log('ComingsoonModel::countSubscribers Error: ' . $e->getMessage());
            return 0;
        }
    }
}
