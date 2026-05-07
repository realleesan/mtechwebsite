<?php
/**
 * Admin Panel Entry Point
 * Khởi động ứng dụng admin và xử lý routing
 */

// Bắt đầu session
session_start();

// Thiết lập error reporting cho development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Thiết lập timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Load các file cần thiết
require_once __DIR__ . '/core/router.php';

try {
    // Khởi tạo và chạy router
    $router = new AdminRouter();
    $router->dispatch();
} catch (Exception $e) {
    // Log lỗi và hiển thị trang lỗi
    error_log('Admin Panel Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Lỗi hệ thống</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Có lỗi xảy ra</h1>
    <p>Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p>
</body>
</html>';
}