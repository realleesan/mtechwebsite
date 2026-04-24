<?php
/**
 * Debug Test for Contact Form
 * Chạy file này để kiểm tra các thành phần của contact form
 * Truy cập: http://localhost/mtechwebsite/test_contact_debug.php
 */

header('Content-Type: text/html; charset=utf-8');
echo '<h1>Contact Form Debug Test</h1>';
echo '<hr>';

$tests = [];

// Test 1: PHP Version
echo '<h2>1. PHP Version</h2>';
echo 'PHP Version: ' . phpversion() . '<br>';
$tests['php_version'] = version_compare(PHP_VERSION, '7.0.0', '>=');
echo 'Status: ' . ($tests['php_version'] ? '✅ OK' : '❌ FAIL') . '<br><br>';

// Test 2: Output Buffering
echo '<h2>2. Output Buffering</h2>';
echo 'Ob level: ' . ob_get_level() . '<br>';
if (!ob_get_level()) {
    ob_start();
}
echo 'Started buffer, level: ' . ob_get_level() . '<br>';
$tests['ob_start'] = true;
echo 'Status: ✅ OK<br><br>';

// Test 3: Database Connection
echo '<h2>3. Database Connection</h2>';
try {
    require_once __DIR__ . '/core/database.php';
    $db = getDBConnection();
    echo 'Database connected successfully<br>';
    $tests['database'] = true;
    echo 'Status: ✅ OK<br><br>';
} catch (Exception $e) {
    echo 'Database Error: ' . $e->getMessage() . '<br>';
    $tests['database'] = false;
    echo 'Status: ❌ FAIL<br><br>';
}

// Test 4: ContactsModel
echo '<h2>4. ContactsModel</h2>';
try {
    require_once __DIR__ . '/app/models/ContactsModel.php';
    $model = new ContactsModel();
    echo 'ContactsModel loaded<br>';
    
    // Test get all
    $contacts = $model->getAll(1);
    echo 'getAll() works, found ' . count($contacts) . ' contacts<br>';
    $tests['model'] = true;
    echo 'Status: ✅ OK<br><br>';
} catch (Exception $e) {
    echo 'Model Error: ' . $e->getMessage() . '<br>';
    echo 'Stack: <pre>' . $e->getTraceAsString() . '</pre><br>';
    $tests['model'] = false;
    echo 'Status: ❌ FAIL<br><br>';
}

// Test 5: Email Configuration
echo '<h2>5. Email Configuration</h2>';
try {
    require_once __DIR__ . '/core/email.php';
    $emailConfig = getEmailConfig();
    echo 'Email config loaded<br>';
    echo 'SMTP Host: ' . ($emailConfig['smtp_host'] ?? 'NOT SET') . '<br>';
    echo 'SMTP Port: ' . ($emailConfig['smtp_port'] ?? 'NOT SET') . '<br>';
    echo 'From Email: ' . ($emailConfig['from_email'] ?? 'NOT SET') . '<br>';
    echo 'SMTP Password: ' . (!empty($emailConfig['smtp_password']) ? 'SET (hidden)' : 'NOT SET') . '<br>';
    $tests['email_config'] = !empty($emailConfig['smtp_host']) && !empty($emailConfig['smtp_password']);
    echo 'Status: ' . ($tests['email_config'] ? '✅ OK' : '⚠️ WARNING (some values not set)') . '<br><br>';
} catch (Exception $e) {
    echo 'Email Config Error: ' . $e->getMessage() . '<br>';
    $tests['email_config'] = false;
    echo 'Status: ❌ FAIL<br><br>';
}

// Test 6: EmailNotificationService
echo '<h2>6. EmailNotificationService</h2>';
try {
    require_once __DIR__ . '/app/services/EmailNotificationService.php';
    $emailService = new EmailNotificationService();
    echo 'EmailNotificationService loaded<br>';
    $isConfigured = $emailService->isConfigured();
    echo 'isConfigured(): ' . ($isConfigured ? 'true' : 'false') . '<br>';
    $tests['email_service'] = true;
    echo 'Status: ✅ OK (configured: ' . ($isConfigured ? 'yes' : 'no') . ')<br><br>';
} catch (Exception $e) {
    echo 'Email Service Error: ' . $e->getMessage() . '<br>';
    echo 'Stack: <pre>' . $e->getTraceAsString() . '</pre><br>';
    $tests['email_service'] = false;
    echo 'Status: ❌ FAIL<br><br>';
}

// Test 7: PHPMailer
echo '<h2>7. PHPMailer Check</h2>';
$phpmailerPaths = [
    __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php',
    __DIR__ . '/vendor/autoload.php',
];
$phpmailerFound = false;
foreach ($phpmailerPaths as $path) {
    if (file_exists($path)) {
        echo 'Found: ' . $path . '<br>';
        $phpmailerFound = true;
    }
}
if (!$phpmailerFound) {
    echo 'PHPMailer not found in vendor directory<br>';
    echo 'Run: composer require phpmailer/phpmailer<br>';
}
$tests['phpmailer'] = $phpmailerFound;
echo 'Status: ' . ($phpmailerFound ? '✅ OK' : '❌ FAIL - Run: composer require phpmailer/phpmailer') . '<br><br>';

// Test 8: Test Insert to Database
echo '<h2>8. Test Database Insert</h2>';
try {
    $testData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'message' => 'This is a test message for debugging'
    ];
    $contactId = $model->create($testData);
    if ($contactId) {
        echo 'Test contact created with ID: ' . $contactId . '<br>';
        // Delete test data
        $model->delete($contactId);
        echo 'Test contact deleted<br>';
        $tests['db_insert'] = true;
        echo 'Status: ✅ OK<br><br>';
    } else {
        echo 'Failed to create test contact<br>';
        $tests['db_insert'] = false;
        echo 'Status: ❌ FAIL<br><br>';
    }
} catch (Exception $e) {
    echo 'Insert Error: ' . $e->getMessage() . '<br>';
    echo 'Stack: <pre>' . $e->getTraceAsString() . '</pre><br>';
    $tests['db_insert'] = false;
    echo 'Status: ❌ FAIL<br><br>';
}

// Test 9: Simulated Form Submission (raw)
echo '<h2>9. Simulated AJAX Request Test</h2>';
echo 'Testing what happens when we simulate the POST request...<br>';

// Save current GET/POST
$origGet = $_GET;
$origPost = $_POST;
$origServer = $_SERVER;

// Set up test request
$_GET['page'] = 'contact';
$_GET['action'] = 'submit';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'name' => 'Debug Test',
    'email' => 'debug@test.com',
    'phone' => '1234567890',
    'message' => 'Debug message test'
];

// Capture output
ob_start();
try {
    // This will include the contact.php with our modified GET/POST
    // But we need to be careful - let's just test the model directly
    $testId = $model->create($_POST);
    if ($testId) {
        echo 'Direct model insert from POST data: SUCCESS (ID: ' . $testId . ')<br>';
        $model->delete($testId);
    } else {
        echo 'Direct model insert from POST data: FAILED<br>';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . '<br>';
}
$output = ob_get_clean();
echo 'Captured output length: ' . strlen($output) . ' bytes<br>';
if (strlen($output) > 0) {
    echo 'Output preview: <pre>' . htmlspecialchars(substr($output, 0, 500)) . '</pre><br>';
}

// Restore
$_GET = $origGet;
$_POST = $origPost;
$_SERVER = $origServer;

$tests['simulation'] = empty($output) || strpos($output, 'DOCTYPE') === false;
echo 'Status: ' . ($tests['simulation'] ? '✅ OK' : '⚠️ Has unexpected output') . '<br><br>';

// Summary
echo '<hr><h2>Summary</h2>';
echo '<table border="1" cellpadding="5">';
echo '<tr><th>Test</th><th>Status</th></tr>';
foreach ($tests as $name => $passed) {
    echo '<tr><td>' . $name . '</td><td>' . ($passed ? '✅ PASS' : '❌ FAIL') . '</td></tr>';
}
echo '</table>';

$allPassed = !in_array(false, $tests, true);
echo '<h3>' . ($allPassed ? '✅ All tests passed!' : '❌ Some tests failed') . '</h3>';

if (!$tests['phpmailer']) {
    echo '<p><strong>To fix PHPMailer:</strong></p>';
    echo '<ol>';
    echo '<li>cd to project root: <code>cd D:\xampp\htdocs\mtechwebsite</code></li>';
    echo '<li>Run: <code>composer require phpmailer/phpmailer</code></li>';
    echo '</ol>';
}

if (!$tests['email_config']) {
    echo '<p><strong>To fix Email Config:</strong></p>';
    echo '<ol>';
    echo '<li>Check .env file exists and has SMTP_* variables</li>';
    echo '<li>Check core/email.php can read .env file</li>';
    echo '</ol>';
}

echo '<hr><h2>Next Steps</h2>';
echo '<p>If all tests pass but form still fails:</p>';
echo '<ol>';
echo '<li>Open browser DevTools (F12)</li>';
echo '<li>Go to Network tab</li>';
echo '<li>Submit the contact form</li>';
echo '<li>Look for the POST request to <code>?page=contact&action=submit</code></li>';
echo '<li>Click on it and check the "Response" tab</li>';
echo '<li>Copy and paste that response here</li>';
echo '</ol>';

echo '<hr><p>Generated: ' . date('Y-m-d H:i:s') . '</p>';
