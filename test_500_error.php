<?php
/**
 * Test để bắt lỗi 500 chi tiết
 */

// Hiển thị tất cả lỗi
echo "=== Testing 500 Error Debug ===\n\n";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Test trực tiếp form submission
try {
    // Set up environment
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_GET['page'] = 'contact';
    $_GET['action'] = 'submit';
    $_POST['name'] = 'Test User';
    $_POST['email'] = 'test@test.com';
    $_POST['phone'] = '123456789';
    $_POST['message'] = 'Test message with enough length here';
    
    echo "Step 1: Testing email.php...\n";
    require_once __DIR__ . '/core/email.php';
    echo "✅ email.php loaded\n";
    
    echo "\nStep 2: Testing PHPMailer...\n";
    $phpmailerPath = __DIR__ . '/assets/vendor/phpmailer/src/';
    if (file_exists($phpmailerPath . 'PHPMailer.php')) {
        require_once $phpmailerPath . 'PHPMailer.php';
        require_once $phpmailerPath . 'SMTP.php';
        require_once $phpmailerPath . 'Exception.php';
        echo "✅ PHPMailer loaded\n";
    } else {
        echo "❌ PHPMailer not found at: " . $phpmailerPath . "\n";
    }
    
    echo "\nStep 3: Testing ContactsModel...\n";
    require_once __DIR__ . '/app/models/ContactsModel.php';
    echo "✅ ContactsModel loaded\n";
    
    echo "\nStep 4: Testing EmailNotificationService...\n";
    require_once __DIR__ . '/app/services/EmailNotificationService.php';
    echo "✅ EmailNotificationService loaded\n";
    
    echo "\nStep 5: Creating service instance...\n";
    $emailService = new EmailNotificationService();
    echo "✅ Service instance created\n";
    
    echo "\nStep 6: Checking isConfigured...\n";
    $configured = $emailService->isConfigured();
    echo "isConfigured: " . ($configured ? 'true' : 'false') . "\n";
    
    echo "\nStep 7: Testing actual contact form processing...\n";
    
    // Include contact page để test
    ob_start();
    include __DIR__ . '/app/views/contact/contact.php';
    $output = ob_get_clean();
    
    echo "Output captured: " . strlen($output) . " chars\n";
    echo "First 200 chars:\n";
    echo substr($output, 0, 200) . "\n\n";
    
    // Check if valid JSON
    $json = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON response!\n";
        echo "Response: " . print_r($json, true) . "\n";
    } else {
        echo "❌ Not valid JSON: " . json_last_error_msg() . "\n";
        echo "Full output:\n" . $output . "\n";
    }
    
} catch (Throwable $e) {
    echo "\n❌ ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== END TEST ===\n";
