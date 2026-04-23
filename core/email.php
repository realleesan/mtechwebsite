<?php
/**
 * Email Configuration
 * Cấu hình email cho EmailNotificationService
 * Đọc thông tin từ file .env để bảo mật
 */

/**
 * Đọc file .env và parse thành mảng
 * @param string $path Đường dẫn đến file .env
 * @return array Mảng chứa các biến môi trường
 */
function parseEnvFile($path = __DIR__ . '/../.env')
{
    $env = [];
    
    if (!file_exists($path)) {
        error_log('Email Config: .env file not found at ' . $path);
        return $env;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Bỏ qua comment
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Loại bỏ quote nếu có
            if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            }
            
            $env[$key] = $value;
        }
    }
    
    return $env;
}

/**
 * Lấy cấu hình email
 * @return array Cấu hình email
 */
function getEmailConfig()
{
    $env = parseEnvFile();
    
    return [
        // SMTP Configuration
        'smtp_host' => $env['SMTP_HOST'] ?? 'smtp.gmail.com',
        'smtp_port' => intval($env['SMTP_PORT'] ?? 587),
        'smtp_username' => $env['SMTP_USERNAME'] ?? '',
        'smtp_password' => $env['SMTP_PASSWORD'] ?? '',
        'smtp_secure' => 'tls', // PHPMailer sẽ chuyển đổi sang constant
        'smtp_auth' => true,
        'use_smtp' => true,
        
        // Sender Information
        'from_email' => $env['MAIL_FROM_EMAIL'] ?? 'noreply@mtech.com',
        'from_name' => $env['MAIL_FROM_NAME'] ?? 'MTech',
        'support_email' => $env['SMTP_USERNAME'] ?? 'support@mtech.com',
        
        // Email Settings
        'charset' => 'UTF-8',
        'timeout' => 30,
        
        // Template Settings
        'template_path' => __DIR__ . '/../app/views/emails/',
        
        // Development Settings
        'debug_mode' => false,
        'log_emails' => true,
    ];
}

/**
 * Kiểm tra cấu hình email có hợp lệ không
 * @return bool
 */
function isEmailConfigValid()
{
    $config = getEmailConfig();
    
    return !empty($config['smtp_username']) && 
           !empty($config['smtp_password']) &&
           !empty($config['smtp_host']);
}
