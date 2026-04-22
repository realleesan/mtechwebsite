<?php
/**
 * Categories Page - Detailed Diagnostic Test
 * Chạy file này trên server để xác định chính xác lỗi
 */

// 1. BẬT HIỂN THỊ LỖI TỐI ĐA
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// 2. HEADER
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html>
<html>
<head>
    <title>Categories - Detailed Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1 { color: #333; border-bottom: 2px solid #ff6b35; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .pass { color: #28a745; font-weight: bold; }
        .fail { color: #dc3545; font-weight: bold; }
        .warn { color: #ffc107; font-weight: bold; }
        .info { background: #f8f9fa; padding: 10px; border-left: 4px solid #007bff; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #007bff; color: white; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
<h1>🔍 Categories Page - Detailed Diagnostic</h1>
<p>Thời gian: ' . date('Y-m-d H:i:s') . '</p>
';

$errors = [];
$warnings = [];

// ============================================
// SECTION 1: PHP ENVIRONMENT
// ============================================
echo '<div class="section">';
echo '<h2>1. PHP Environment</h2>';

$phpVersion = phpversion();
echo '<p>PHP Version: <strong>' . $phpVersion . '</strong></p>';

$requiredExtensions = ['pdo', 'pdo_mysql', 'mysqli'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo '<p class="pass">✓ Extension ' . $ext . ' loaded</p>';
    } else {
        echo '<p class="fail">✗ Extension ' . $ext . ' NOT loaded</p>';
        $errors[] = 'Extension ' . $ext . ' missing';
    }
}
echo '</div>';

// ============================================
// SECTION 2: FILE CHECKS
// ============================================
echo '<div class="section">';
echo '<h2>2. File Existence Check</h2>';

$files = [
    '.env' => '.env',
    'core/database.php' => 'core/database.php',
    'app/models/CategoriesModel.php' => 'app/models/CategoriesModel.php',
    'app/views/categories/categories.php' => 'app/views/categories/categories.php',
    'assets/css/categories.css' => 'assets/css/categories.css',
    'assets/js/categories.js' => 'assets/js/categories.js',
    'app/views/_layout/master.php' => 'app/views/_layout/master.php',
    'index.php' => 'index.php',
];

foreach ($files as $file => $desc) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $size = filesize($path);
        echo '<p class="pass">✓ ' . $desc . ' exists (' . $size . ' bytes)</p>';
    } else {
        echo '<p class="fail">✗ ' . $desc . ' NOT FOUND!</p>';
        $errors[] = 'Missing file: ' . $desc;
    }
}
echo '</div>';

// ============================================
// SECTION 3: .ENV CONFIGURATION
// ============================================
echo '<div class="section">';
echo '<h2>3. Environment (.env) Configuration</h2>';

$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo '<p class="pass">✓ .env file exists</p>';
    
    // Parse .env
    $envContent = file_get_contents($envPath);
    preg_match('/DB_HOST=(.+)/', $envContent, $dbHost);
    preg_match('/DB_NAME=(.+)/', $envContent, $dbName);
    preg_match('/DB_USER=(.+)/', $envContent, $dbUser);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $dbPass);
    
    echo '<div class="info">';
    echo '<strong>DB Config:</strong><br>';
    echo 'DB_HOST: ' . (isset($dbHost[1]) ? trim($dbHost[1]) : '<span class="fail">NOT SET</span>') . '<br>';
    echo 'DB_NAME: ' . (isset($dbName[1]) ? trim($dbName[1]) : '<span class="fail">NOT SET</span>') . '<br>';
    echo 'DB_USER: ' . (isset($dbUser[1]) ? trim($dbUser[1]) : '<span class="fail">NOT SET</span>') . '<br>';
    echo 'DB_PASSWORD: ' . (isset($dbPass[1]) && trim($dbPass[1]) ? '******' : '<span class="fail">NOT SET</span>');
    echo '</div>';
    
    if (!isset($dbHost[1]) || !isset($dbName[1]) || !isset($dbUser[1])) {
        $errors[] = 'Missing DB configuration in .env';
    }
} else {
    echo '<p class="fail">✗ .env file NOT FOUND!</p>';
    $errors[] = 'Missing .env file';
}
echo '</div>';

// ============================================
// SECTION 4: DATABASE CONNECTION
// ============================================
echo '<div class="section">';
echo '<h2>4. Database Connection Test</h2>';

try {
    require_once __DIR__ . '/core/database.php';
    
    // Test getDBConnection
    $db = getDBConnection();
    echo '<p class="pass">✓ Database connection successful</p>';
    
    // Test PDO attributes
    $serverVersion = $db->getAttribute(PDO::ATTR_SERVER_VERSION);
    echo '<p class="info">MySQL Server Version: ' . $serverVersion . '</p>';
    
} catch (Exception $e) {
    echo '<p class="fail">✗ Database connection FAILED</p>';
    echo '<pre class="fail">Error: ' . $e->getMessage() . '</pre>';
    $errors[] = 'DB Connection: ' . $e->getMessage();
} catch (Error $e) {
    echo '<p class="fail">✗ Fatal Error loading database.php</p>';
    echo '<pre class="fail">' . $e->getMessage() . '</pre>';
    $errors[] = 'DB File Error: ' . $e->getMessage();
}
echo '</div>';

// ============================================
// SECTION 5: TABLE CHECK
// ============================================
echo '<div class="section">';
echo '<h2>5. Database Table Check</h2>';

if (isset($db) && $db instanceof PDO) {
    try {
        // Check if categories table exists
        $stmt = $db->query("SHOW TABLES LIKE 'categories'");
        $tableExists = $stmt->rowCount() > 0;
        
        if ($tableExists) {
            echo '<p class="pass">✓ Table "categories" exists</p>';
            
            // Get table structure
            $stmt = $db->query("DESCRIBE categories");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<h3>Table Structure:</h3>';
            echo '<table>';
            echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
            foreach ($columns as $col) {
                echo '<tr>';
                echo '<td>' . $col['Field'] . '</td>';
                echo '<td>' . $col['Type'] . '</td>';
                echo '<td>' . $col['Null'] . '</td>';
                echo '<td>' . $col['Key'] . '</td>';
                echo '<td>' . ($col['Default'] ?? 'NULL') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
            // Check data count
            $stmt = $db->query("SELECT COUNT(*) FROM categories WHERE status = 1");
            $count = $stmt->fetchColumn();
            echo '<p>Active categories: <strong>' . $count . '</strong></p>';
            
            // Show sample data
            $stmt = $db->query("SELECT id, name, slug, image, status FROM categories WHERE status = 1 LIMIT 3");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($data) {
                echo '<h3>Sample Data (first 3 rows):</h3>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Name</th><th>Slug</th><th>Image</th><th>Status</th></tr>';
                foreach ($data as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['slug'] . '</td>';
                    echo '<td>' . ($row['image'] ? substr($row['image'], 0, 30) . '...' : 'NULL') . '</td>';
                    echo '<td>' . $row['status'] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            
        } else {
            echo '<p class="fail">✗ Table "categories" does NOT exist!</p>';
            echo '<div class="info">Run SQL: database/migrations/001_create_categories_table.sql</div>';
            $errors[] = 'categories table not found';
        }
    } catch (PDOException $e) {
        echo '<p class="fail">✗ Database query error</p>';
        echo '<pre class="fail">' . $e->getMessage() . '</pre>';
        $errors[] = 'DB Query: ' . $e->getMessage();
    }
} else {
    echo '<p class="warn">⚠ Database not connected - skipping table check</p>';
}
echo '</div>';

// ============================================
// SECTION 6: CategoriesModel
// ============================================
echo '<div class="section">';
echo '<h2>6. CategoriesModel Test</h2>';

try {
    require_once __DIR__ . '/app/models/CategoriesModel.php';
    echo '<p class="pass">✓ CategoriesModel.php loaded successfully</p>';
    
    if (isset($db) && $db instanceof PDO) {
        $model = new CategoriesModel();
        echo '<p class="pass">✓ CategoriesModel instantiated</p>';
        
        // Test getAllCategories
        $categories = $model->getAllCategories();
        echo '<p class="pass">✓ getAllCategories() returned ' . count($categories) . ' categories</p>';
        
        if (count($categories) > 0) {
            echo '<h3>Categories Data:</h3>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Slug</th><th>Image</th></tr>';
            foreach ($categories as $cat) {
                echo '<tr>';
                echo '<td>' . ($cat['id'] ?? 'N/A') . '</td>';
                echo '<td>' . ($cat['name'] ?? 'N/A') . '</td>';
                echo '<td>' . ($cat['slug'] ?? 'N/A') . '</td>';
                echo '<td>' . ($cat['image'] ? 'Yes' : 'No') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            $warnings[] = 'No categories found in database';
        }
    } else {
        echo '<p class="warn">⚠ Cannot test model without DB connection</p>';
    }
} catch (Exception $e) {
    echo '<p class="fail">✗ CategoriesModel error</p>';
    echo '<pre class="fail">' . $e->getMessage() . '</pre>';
    $errors[] = 'CategoriesModel: ' . $e->getMessage();
} catch (Error $e) {
    echo '<p class="fail">✗ Fatal error loading CategoriesModel</p>';
    echo '<pre class="fail">' . $e->getMessage() . '</pre>';
    echo '<p>File path: ' . __DIR__ . '/app/models/CategoriesModel.php</p>';
    $errors[] = 'CategoriesModel Load: ' . $e->getMessage();
}
echo '</div>';

// ============================================
// SECTION 7: RENDER VIEW
// ============================================
echo '<div class="section">';
echo '<h2>7. View Rendering Test</h2>';

$viewFile = __DIR__ . '/app/views/categories/categories.php';
if (file_exists($viewFile)) {
    echo '<p class="pass">✓ View file exists: app/views/categories/categories.php</p>';
    
    // Check if view is readable
    if (is_readable($viewFile)) {
        echo '<p class="pass">✓ View file is readable</p>';
    } else {
        echo '<p class="fail">✗ View file is NOT readable!</p>';
        $errors[] = 'View file not readable';
    }
    
    // Check for syntax errors
    ob_start();
    $syntaxCheck = shell_exec('php -l ' . escapeshellarg($viewFile) . ' 2>&1');
    ob_end_clean();
    
    if (strpos($syntaxCheck, 'No syntax errors') !== false) {
        echo '<p class="pass">✓ No PHP syntax errors in view</p>';
    } else {
        echo '<p class="fail">✗ Syntax errors found in view!</p>';
        echo '<pre class="fail">' . $syntaxCheck . '</pre>';
        $errors[] = 'View syntax error';
    }
    
} else {
    echo '<p class="fail">✗ View file NOT FOUND!</p>';
    $errors[] = 'Missing view file';
}
echo '</div>';

// ============================================
// SECTION 8: ACTUAL RENDER TEST
// ============================================
echo '<div class="section">';
echo '<h2>8. Actual Page Render Test</h2>';

try {
    // Capture output of actual page
    ob_start();
    
    // Set up required variables
    $_GET['page'] = 'categories';
    $page = 'categories';
    $title = 'Our Services - Test';
    $content = 'app/views/categories/categories.php';
    $showPageHeader = false;
    $showCTA = false;
    $showBreadcrumb = false;
    
    // Try to include the view directly
    if (file_exists(__DIR__ . '/app/views/categories/categories.php')) {
        include __DIR__ . '/app/views/categories/categories.php';
    }
    
    $output = ob_get_clean();
    
    if (strlen($output) > 100) {
        echo '<p class="pass">✓ Page rendered successfully!</p>';
        echo '<p>Output length: ' . strlen($output) . ' characters</p>';
        echo '<details><summary>Click to view rendered HTML (first 1000 chars)</summary>';
        echo '<pre>' . htmlspecialchars(substr($output, 0, 1000)) . '...</pre>';
        echo '</details>';
    } else {
        echo '<p class="fail">✗ Page rendered empty or too short!</p>';
        echo '<p>Output length: ' . strlen($output) . ' characters</p>';
        $warnings[] = 'View rendered empty content';
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo '<p class="fail">✗ Error rendering page</p>';
    echo '<pre class="fail">' . $e->getMessage() . '</pre>';
    $errors[] = 'Render: ' . $e->getMessage();
} catch (Error $e) {
    ob_end_clean();
    echo '<p class="fail">✗ Fatal error rendering page</p>';
    echo '<pre class="fail">' . $e->getMessage() . '</pre>';
    $errors[] = 'Render Fatal: ' . $e->getMessage();
}
echo '</div>';

// ============================================
// SECTION 9: URL & SERVER INFO
// ============================================
echo '<div class="section">';
echo '<h2>9. Server & URL Information</h2>';

echo '<div class="info">';
echo '<strong>Server Info:</strong><br>';
echo 'REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'N/A') . '<br>';
echo 'SCRIPT_NAME: ' . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . '<br>';
echo 'PHP_SELF: ' . ($_SERVER['PHP_SELF'] ?? 'N/A') . '<br>';
echo 'DOCUMENT_ROOT: ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . '<br>';
echo 'HTTP_HOST: ' . ($_SERVER['HTTP_HOST'] ?? 'N/A') . '<br>';
echo '</div>';

// Check if categories.css accessible
$cssUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/assets/css/categories.css';
echo '<p>CSS URL: <a href="' . $cssUrl . '" target="_blank">' . $cssUrl . '</a></p>';
echo '<p class="info">Click link above - if 404, file chưa upload!</p>';
echo '</div>';

// ============================================
// SUMMARY
// ============================================
echo '<div class="section">';
echo '<h2>📋 SUMMARY</h2>';

if (count($errors) === 0) {
    echo '<h3 class="pass">✓ ALL TESTS PASSED!</h3>';
    echo '<p>Categories page should work. If still blank, clear browser cache or check:</p>';
    echo '<ul>';
    echo '<li>Cloudflare cache (Purge Cache)</li>';
    echo '<li>CDN cache</li>';
    echo '<li>Browser cache (Ctrl+F5)</li>';
    echo '</ul>';
} else {
    echo '<h3 class="fail">✗ FOUND ' . count($errors) . ' ERROR(S):</h3>';
    echo '<ol>';
    foreach ($errors as $err) {
        echo '<li class="fail">' . htmlspecialchars($err) . '</li>';
    }
    echo '</ol>';
    
    // Solutions
    echo '<h3>🔧 SUGGESTED FIXES:</h3>';
    
    foreach ($errors as $err) {
        if (strpos($err, 'Missing file') !== false) {
            echo '<div class="info"><strong>Missing File:</strong> Upload file via FTP/cPanel File Manager</div>';
        }
        if (strpos($err, '.env') !== false) {
            echo '<div class="info"><strong>.env Missing:</strong> Upload .env file with DB config to document root</div>';
        }
        if (strpos($err, 'DB Connection') !== false) {
            echo '<div class="info"><strong>DB Connection Failed:</strong> Kiểm tra .env config hoặc database server</div>';
        }
        if (strpos($err, 'categories table') !== false) {
            echo '<div class="info"><strong>Missing Table:</strong> Import database/migrations/001_create_categories_table.sql via PHPMyAdmin</div>';
        }
    }
}

if (count($warnings) > 0) {
    echo '<h3 class="warn">⚠ WARNINGS:</h3>';
    echo '<ol>';
    foreach ($warnings as $warn) {
        echo '<li class="warn">' . htmlspecialchars($warn) . '</li>';
    }
    echo '</ol>';
}

echo '</div>';

echo '</body></html>';
