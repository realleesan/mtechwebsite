<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>Test Home Slider - Admin MTech</h2>";
echo "<style>body{font-family:Arial;max-width:900px;margin:30px auto;padding:0 20px;} .ok{color:green;font-weight:bold;} .err{color:red;font-weight:bold;} .info{color:#333;}</style>";

// Step 1: Check directory
echo "<hr><h3>1. Kiểm tra thư mục upload</h3>";
$uploadDir = __DIR__ . '/assets/uploads/home-sliders/';
if (is_dir($uploadDir)) {
    echo "<p class='ok'>✓ Thư mục tồn tại: $uploadDir</p>";
    if (is_writable($uploadDir)) {
        echo "<p class='ok'>✓ Có quyền ghi</p>";
    } else {
        echo "<p class='err'>✗ Không có quyền ghi - chmod 755 hoặc 775 thư mục này</p>";
    }
} else {
    echo "<p class='err'>✗ Thư mục chưa tồn tại</p>";
    if (mkdir($uploadDir, 0755, true)) {
        echo "<p class='ok'>✓ Đã tạo thư mục thành công</p>";
    } else {
        echo "<p class='err'>✗ Không thể tạo thư mục</p>";
    }
}

// Step 2: Database connection
echo "<hr><h3>2. Kiểm tra kết nối database</h3>";
try {
    $host = 'sql213.infinityfree.com';
    $dbname = 'if0_41698410_mtech';
    $username = 'if0_41698410';
    $password = 'idGskHPdbH';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "<p class='ok'>✓ Kết nối database thành công</p>";
} catch (PDOException $e) {
    echo "<p class='err'>✗ Lỗi kết nối: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Step 3: Check table exists
echo "<hr><h3>3. Kiểm tra bảng home_sliders</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'home_sliders'");
    $exists = $stmt->fetch();
    if ($exists) {
        echo "<p class='ok'>✓ Bảng home_sliders đã tồn tại</p>";

        // Show table structure
        $stmt = $pdo->query("DESCRIBE home_sliders");
        $columns = $stmt->fetchAll();
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;margin-top:10px;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($columns as $col) {
            echo "<tr><td>" . htmlspecialchars($col['Field']) . "</td><td>" . htmlspecialchars($col['Type']) . "</td><td>" . htmlspecialchars($col['Null']) . "</td><td>" . htmlspecialchars($col['Key']) . "</td></tr>";
        }
        echo "</table>";

        // Count rows
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM home_sliders");
        $count = $stmt->fetch();
        echo "<p class='info'>Số slide hiện tại: <strong>" . $count['total'] . "</strong></p>";
    } else {
        echo "<p class='err'>✗ Bảng home_sliders CHƯA tồn tại</p>";
        echo "<p class='info'>Hãy chạy SQL sau để tạo bảng:</p>";
        echo "<pre style='background:#f5f5f5;padding:15px;border-radius:5px;overflow-x:auto;'>";
        echo "CREATE TABLE IF NOT EXISTS `home_sliders` (\n";
        echo "  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
        echo "  `sort_order` INT(11) NOT NULL DEFAULT 0,\n";
        echo "  `status` TINYINT(1) NOT NULL DEFAULT 1,\n";
        echo "  `image_1` VARCHAR(500) NOT NULL DEFAULT '',\n";
        echo "  `image_2` VARCHAR(500) NOT NULL DEFAULT '',\n";
        echo "  `image_3` VARCHAR(500) NOT NULL DEFAULT '',\n";
        echo "  `deleted_at` DATETIME DEFAULT NULL,\n";
        echo "  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
        echo "  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
        echo "  PRIMARY KEY (`id`),\n";
        echo "  INDEX `idx_status_order` (`status`, `sort_order`, `id`)\n";
        echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        echo "</pre>";
    }
} catch (PDOException $e) {
    echo "<p class='err'>✗ Lỗi khi kiểm tra bảng: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Step 4: Test model methods
echo "<hr><h3>4. Test các hàm Model</h3>";
try {
    require_once __DIR__ . '/app/models/HomeSliderModel.php';
    $model = new HomeSliderModel($pdo);

    echo "<p class='info'>Đang test getActiveSlides()...</p>";
    $slides = $model->getActiveSlides();
    echo "<p class='info'>Kết quả: " . (is_array($slides) ? "Array (" . count($slides) . " items)" : gettype($slides)) . "</p>";

    if (!empty($slides)) {
        echo "<p class='ok'>✓ Dữ liệu mẫu:</p><ul>";
        foreach ($slides as $s) {
            echo "<li>ID: {$s['id']}, sort: {$s['sort_order']}, status: {$s['status']}</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p class='err'>✗ Lỗi Model: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p class='info'>Trace: " . htmlspecialchars($e->getTraceAsString()) . "</p>";
}

// Step 5: Test upload dir path
echo "<hr><h3>5. Kiểm tra path upload trong Model</h3>";
$uploadDirModel = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/assets/uploads/home-sliders/')), '/');
echo "<p class='info'>Path thư mục upload thực tế: <code>" . htmlspecialchars($uploadDirModel) . "</code></p>";

// Step 6: PHP info (chỉ hiện config quan trọng)
echo "<hr><h3>6. PHP Config</h3>";
echo "<p class='info'>PHP Version: " . phpversion() . "</p>";
echo "<p class='info'>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p class='info'>post_max_size: " . ini_get('post_max_size') . "</p>";
echo "<p class='info'>file_uploads: " . (ini_get('file_uploads') ? 'On' : 'Off') . "</p>";
echo "<p class='info'>max_file_uploads: " . ini_get('max_file_uploads') . "</p>";

echo "<hr><p style='color:#666;'>Test completed at " . date('Y-m-d H:i:s') . "</p>";