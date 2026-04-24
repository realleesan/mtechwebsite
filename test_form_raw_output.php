<?php
/**
 * Test để xem raw output từ form submission
 * File này sẽ show chính xác những gì server trả về
 */

echo "=== Testing Contact Form Raw Output ===\n\n";

// Test 1: Check for BOM or whitespace in key files
echo "=== CHECK 1: BOM/Whitespace in included files ===\n";
$files_to_check = [
    __DIR__ . '/core/email.php',
    __DIR__ . '/app/models/ContactsModel.php',
    __DIR__ . '/app/services/EmailNotificationService.php',
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $has_bom = (substr($content, 0, 3) === "\xEF\xBB\xBF");
        $leading_ws = preg_match('/^\s*<\?php/', $content);
        $trailing_ws = preg_match('/\?>\s*$/s', $content);
        echo "File: " . basename($file) . "\n";
        echo "  BOM: " . ($has_bom ? 'YES ❌' : 'No ✅') . "\n";
        echo "  Leading whitespace before <?php: " . ($leading_ws ? 'No ✅' : 'YES ❌') . "\n";
        echo "  Trailing whitespace after ?>: " . ($trailing_ws ? 'YES ⚠️' : 'No ✅') . "\n\n";
    } else {
        echo "File: " . basename($file) . " - NOT FOUND\n\n";
    }
}

// Test 2: Simulate the form submission and capture output
echo "\n=== CHECK 2: Simulating form submission ===\n\n";

// Save original output buffer level
$original_level = ob_get_level();

// Clean all output buffers
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Start fresh buffer
ob_start();

echo "Starting form submission test...\n";

// Simulate POST data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['action'] = 'submit';
$_POST['name'] = 'Test User';
$_POST['email'] = 'test@example.com';
$_POST['phone'] = '123456789';
$_POST['message'] = 'This is a test message with enough length';

try {
    include __DIR__ . '/app/views/contact/contact.php';
} catch (Throwable $e) {
    echo "Error caught: " . $e->getMessage() . "\n";
}

// Get the captured output
$output = ob_get_clean();

echo "\n=== RAW OUTPUT LENGTH ===\n";
echo strlen($output) . " characters\n";

echo "\n=== FIRST 500 CHARACTERS ===\n";
echo substr($output, 0, 500) . "\n";

echo "\n=== LAST 200 CHARACTERS ===\n";
echo substr($output, -200) . "\n";

echo "\n=== CHECKING IF VALID JSON ===\n";
$decoded = json_decode($output, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "✅ Valid JSON!\n";
    echo "Data: " . print_r($decoded, true) . "\n";
} else {
    echo "❌ Not valid JSON: " . json_last_error_msg() . "\n";
    
    // Check if HTML
    if (stripos($output, '<!DOCTYPE') !== false || stripos($output, '<html') !== false) {
        echo "⚠️ Output contains HTML!\n";
        
        // Try to find what PHP file generated the HTML
        if (preg_match('/<title>([^<]+)<\/title>/i', $output, $matches)) {
            echo "Page title found: " . $matches[1] . "\n";
        }
        
        // Check for error messages in HTML
        if (preg_match('/(Fatal error|Parse error|Warning|Notice)/i', $output, $matches)) {
            echo "PHP error found: " . $matches[0] . "\n";
        }
    }
}

echo "\n=== FULL OUTPUT (for debugging) ===\n";
echo "---START---\n";
echo $output;
echo "\n---END---\n";
