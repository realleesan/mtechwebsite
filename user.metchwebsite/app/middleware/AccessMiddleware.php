<?php
/**
 * AccessMiddleware.php
 * 
 * Middleware để track truy cập người dùng vào website
 * Tạo visitor ID duy nhất và ghi log mỗi khi truy cập
 */

class AccessMiddleware
{
    /**
     * Track truy cập người dùng
     * Ghi log vào database và quản lý session tracking
     */
    public static function trackVisit()
    {
        // Bắt session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Tạo visitor ID duy nhất cho mỗi phiên truy cập
        if (!isset($_SESSION['visitor_id'])) {
            $_SESSION['visitor_id'] = self::generateVisitorId();
            $_SESSION['last_activity'] = time();
            $_SESSION['visit_tracked_today'] = date('Y-m-d');
            
            // Ghi log truy cập đầu tiên
            self::logVisit($_SESSION['visitor_id']);
            
            return true; // Lượt truy cập mới
        }

        // Kiểm tra timeout (30 phút = 1800 giây)
        $sessionTimeout = 1800;
        $lastActivity = $_SESSION['last_activity'] ?? 0;
        
        if (time() - $lastActivity > $sessionTimeout) {
            // Session cũ nhưng quá timeout -> lượt truy cập mới
            $_SESSION['last_activity'] = time();
            $_SESSION['visit_tracked_today'] = date('Y-m-d');
            
            // Ghi log truy cập mới
            self::logVisit($_SESSION['visitor_id']);
            
            return true; // Lượt truy cập mới
        }

        // Cùng session, chưa timeout -> không đếm lượt mới
        $_SESSION['last_activity'] = time();
        return false; // Không phải lượt mới
    }

    /**
     * Tạo visitor ID duy nhất
     * Sử dụng IP, User Agent, và timestamp để tạo fingerprint
     */
    private static function generateVisitorId()
    {
        $visitorData = [
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            date('Y-m-d')
        ];
        
        return 'visitor_' . md5(implode('|', $visitorData)) . '_' . time();
    }

    /**
     * Ghi log truy cập vào database
     * @param string $visitorId ID của visitor
     */
    private static function logVisit($visitorId)
    {
        try {
            require_once __DIR__ . '/../models/AccessLogsModel.php';
            $accessModel = new AccessLogsModel();
            $accessModel->logVisit($visitorId, $_SERVER['REQUEST_URI'] ?? '/');
        } catch (Exception $e) {
            error_log('AccessMiddleware::logVisit() - ' . $e->getMessage());
        }
    }
}
