<?php
header('Content-Type: text/plain');
echo "Test started\n";

// Check for syntax errors first
$file = __DIR__ . '/app/services/EmailNotificationService.php';
echo "Checking: $file\n";
echo "File exists: " . (file_exists($file) ? 'YES' : 'NO') . "\n";

// Try to get syntax error
$output = [];
$return = 0;
exec('php -l ' . escapeshellarg($file), $output, $return);
echo "\nSyntax check:\n";
echo implode("\n", $output) . "\n";
echo "Return code: $return\n";

if ($return === 0) {
    echo "\nNo syntax errors found. Trying to load...\n";
    
    // Try with error handler
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        echo "ERROR [$errno]: $errstr in $errfile:$errline\n";
        return true;
    });
    
    set_exception_handler(function($e) {
        echo "EXCEPTION: " . get_class($e) . "\n";
        echo "Message: " . $e->getMessage() . "\n";
    });
    
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error) {
            echo "FATAL ERROR: " . $error['message'] . "\n";
            echo "File: " . $error['file'] . ":" . $error['line'] . "\n";
        }
    });
    
    require_once __DIR__ . '/core/email.php';
    echo "Loaded email.php\n";
    
    require_once __DIR__ . '/vendor/autoload.php';
    echo "Loaded autoload.php\n";
    
    echo "About to load EmailNotificationService...\n";
    require_once __DIR__ . '/app/services/EmailNotificationService.php';
    echo "Loaded EmailNotificationService.php\n";
    
    echo "About to create instance...\n";
    $service = new EmailNotificationService();
    echo "Instance created!\n";
}

echo "\nTest ended\n";
