<?php
/**
 * Test đường dẫn qua index.php
 */

echo "=== Testing Index.php Path ===\n\n";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Simulate the exact request
try {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_GET['page'] = 'contact';
    $_GET['action'] = 'submit';
    $_POST['name'] = 'Test User';
    $_POST['email'] = 'test@test.com';
    $_POST['phone'] = '123456789';
    $_POST['message'] = 'Test message with enough length here';
    
    echo "Step 1: Starting output buffer capture...\n";
    ob_start();
    
    echo "Step 2: Including index.php...\n";
    include __DIR__ . '/index.php';
    
    echo "Step 3: Getting output...\n";
    $output = ob_get_clean();
    
    echo "\n=== OUTPUT LENGTH: " . strlen($output) . " ===\n";
    echo "First 500 chars:\n" . substr($output, 0, 500) . "\n\n";
    
    // Check JSON
    $json = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ VALID JSON!\n";
        echo "Response: " . print_r($json, true) . "\n";
    } else {
        echo "❌ NOT JSON: " . json_last_error_msg() . "\n";
        echo "Full output:\n" . $output . "\n";
    }
    
} catch (Throwable $e) {
    // Clean any output buffer
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    echo "\n❌ ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== END ===\n";
