<?php
/**
 * Test file mô phỏng đúng flow của index.php -> master.php với page=blogs
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session để tránh lỗi
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (Exception $e) {
    // Ignore
}

echo "<h1>🔍 Test Full Blog Page Flow</h1><hr>";

// Bước 1: Load models và lấy dữ liệu (giống index.php)
echo "<h2>Step 1: Load Models & Data</h2>";
try {
    require_once __DIR__ . '/core/database.php';
    require_once __DIR__ . '/app/models/BlogsModel.php';
    
    $blogsModel = new BlogsModel();
    
    $filterCatId  = isset($_GET['cat']) ? (int) $_GET['cat'] : 0;
    $filterTag    = isset($_GET['tag']) ? trim($_GET['tag']) : '';
    $searchQuery  = isset($_GET['search']) ? trim(urldecode($_GET['search'])) : '';
    $currentPage  = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1;
    $perPage      = 5;

    $blogsResult    = $blogsModel->getBlogs($currentPage, $perPage, $filterCatId, $filterTag, $searchQuery);
    $blogs          = $blogsResult['blogs'];
    $totalBlogs     = $blogsResult['total'];
    $blogCategories = $blogsModel->getAllBlogCategories();
    $recentBlogs    = $blogsModel->getRecentBlogs(4);
    $allTags        = $blogsModel->getAllTags();
    
    echo "✅ Data loaded: " . count($blogs) . " blogs, " . count($blogCategories) . " categories, " . count($allTags) . " tags<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    exit;
}

// Bước 2: Set variables cho master.php
echo "<h2>Step 2: Set Layout Variables</h2>";
$page           = 'blogs';
$title          = 'Blog - MTECHJSC';
$content        = 'app/views/blogs/blogs.php';
$showPageHeader = true;
$pageTitle      = 'Blog';
$showCTA        = false;
$showBreadcrumb = true;

// Set breadcrumb
require_once __DIR__ . '/app/views/_layout/breadcrumb.php';
$breadcrumbs    = get_breadcrumbs('blogs');

echo "✅ Variables set<br>";
echo "- page: {$page}<br>";
echo "- title: {$title}<br>";
echo "- content: {$content}<br>";
echo "- showPageHeader: " . ($showPageHeader ? 'true' : 'false') . "<br>";
echo "- showCTA: " . ($showCTA ? 'true' : 'false') . "<br>";

// Bước 3: Kiểm tra các file layout có tồn tại
echo "<h2>Step 3: Check Layout Files</h2>";
$layoutFiles = [
    'master.php' => __DIR__ . '/app/views/_layout/master.php',
    'header.php' => __DIR__ . '/app/views/_layout/header.php',
    'footer.php' => __DIR__ . '/app/views/_layout/footer.php',
    'pageheader.php' => __DIR__ . '/app/views/_layout/pageheader.php',
];

foreach ($layoutFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✅ {$name} exists<br>";
    } else {
        echo "❌ {$name} NOT FOUND at: {$path}<br>";
    }
}

// Bước 4: Thử render master.php với output buffering
echo "<h2>Step 4: Render Master Layout</h2>";
echo "<p><strong>Rendering master.php...</strong></p>";

// Capture output
ob_start();
try {
    include __DIR__ . '/app/views/_layout/master.php';
    $output = ob_get_clean();
    
    echo "✅ Master layout rendered successfully! (" . strlen($output) . " bytes)<br>";
    
    // Kiểm tra các thành phần trong output
    $checks = [
        'header' => ['header', 'navbar', 'nav'],
        'sidebar' => ['blog_sidebar_area', 'widget_categories', 'recent_inner'],
        'footer' => ['footer', 'copyright'],
        'content' => ['blog_area', 'lt_blog_item'],
    ];
    
    echo "<h3>Content Check:</h3>";
    foreach ($checks as $component => $keywords) {
        $found = false;
        foreach ($keywords as $keyword) {
            if (stripos($output, $keyword) !== false) {
                $found = true;
                break;
            }
        }
        if ($found) {
            echo "✅ {$component} found<br>";
        } else {
            echo "❌ {$component} NOT found!<br>";
        }
    }
    
    // Hiển thị phần đầu và cuối của HTML output để debug
    echo "<h3>HTML Output Preview (first 2000 chars):</h3>";
    echo "<pre style='background:#f5f5f5;padding:10px;overflow:auto;max-height:200px;'>";
    echo htmlspecialchars(substr($output, 0, 2000));
    echo "</pre>";
    
    echo "<h3>HTML Output Preview (last 1000 chars):</h3>";
    echo "<pre style='background:#f5f5f5;padding:10px;overflow:auto;max-height:200px;'>";
    echo htmlspecialchars(substr($output, -1000));
    echo "</pre>";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Error rendering master: " . $e->getMessage() . "<br>";
    echo "Stack trace:<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    ob_end_clean();
    echo "❌ Fatal Error: " . $e->getMessage() . "<br>";
    echo "In file: " . $e->getFile() . " line " . $e->getLine() . "<br>";
    echo "Stack trace:<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><h2>✅ Test Completed!</h2>";
