<?php
/**
 * Email Service Debug Test
 * Truy cập: http://localhost/mtechwebsite/test_email_service.php
 */

header('Content-Type: text/plain; charset=utf-8');
echo "=== Email Service Debug ===\n\n";

try {
    echo "Step 1: Loading email.php...\n";
    require_once __DIR__ . '/core/email.php';
    echo "✅ email.php loaded\n\n";
    
    echo "Step 2: Checking email config...\n";
    $config = getEmailConfig();
    echo "Config keys: " . implode(', ', array_keys($config)) . "\n";
    echo "SMTP Host: " . ($config['smtp_host'] ?? 'NOT SET') . "\n";
    echo "SMTP Port: " . ($config['smtp_port'] ?? 'NOT SET') . "\n";
    echo "SMTP Username: " . ($config['smtp_username'] ?? 'NOT SET') . "\n";
    echo "SMTP Password: " . (!empty($config['smtp_password']) ? 'SET (' . strlen($config['smtp_password']) . ' chars)' : 'NOT SET') . "\n";
    echo "From Email: " . ($config['from_email'] ?? 'NOT SET') . "\n";
    echo "From Name: " . ($config['from_name'] ?? 'NOT SET') . "\n";
    echo "Support Email: " . ($config['support_email'] ?? 'NOT SET') . "\n";
    echo "✅ Config loaded\n\n";
    
    echo "Step 3: Checking PHPMailer...\n";
    $phpmailerPath = __DIR__ . '/assets/vendor/phpmailer/src/PHPMailer.php';
    echo "PHPMailer path: $phpmailerPath\n";
    echo "File exists: " . (file_exists($phpmailerPath) ? 'YES' : 'NO') . "\n";
    
    if (file_exists($phpmailerPath)) {
        require_once __DIR__ . '/assets/vendor/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/assets/vendor/phpmailer/src/SMTP.php';
        require_once __DIR__ . '/assets/vendor/phpmailer/src/Exception.php';
        echo "✅ PHPMailer files loaded\n\n";
        
        echo "Step 4: Checking PHPMailer class...\n";
        $classExists = class_exists('PHPMailer\PHPMailer\PHPMailer');
        echo "PHPMailer class exists: " . ($classExists ? 'YES' : 'NO') . "\n";
        
        if ($classExists) {
            echo "✅ PHPMailer class available\n\n";
            
            echo "Step 5: Loading EmailNotificationService...\n";
            require_once __DIR__ . '/app/services/EmailNotificationService.php';
            echo "✅ EmailNotificationService.php loaded\n\n";
            
            echo "Step 6: Creating EmailNotificationService instance...\n";
            $service = new EmailNotificationService();
            echo "✅ Service created\n\n";
            
            echo "Step 7: Checking isConfigured...\n";
            $isConfigured = $service->isConfigured();
            echo "isConfigured: " . ($isConfigured ? 'true' : 'false') . "\n\n";
            
            echo "=== ALL TESTS PASSED ===";
        } else {
            echo "❌ PHPMailer class not found\n";
        }
    } else {
        echo "❌ PHPMailer not found at: $phpmailerPath\n";
        echo "Please check if PHPMailer files are in assets/vendor/phpmailer/src/\n";
    }
    
} catch (Throwable $e) {
    echo "\n❌ ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
