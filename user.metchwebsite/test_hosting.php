<?php
/**
 * Diagnostic & Testing Tool for Hosting Environment
 * MTech Website
 *
 * Mở file này trên trình duyệt (ví dụ: http://mtechjsc.com/test_hosting.php)
 * để kiểm tra nguyên nhân gây lỗi trên Hosting.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>MTech Website - Diagnosing Hosting Environment</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4f6f9; color: #333; padding: 20px; line-height: 1.5; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #0056b3; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; margin-top: 0; }
        h2 { color: #495057; margin-top: 25px; font-size: 1.2rem; border-left: 4px solid #0056b3; padding-left: 10px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 13px; color: white; }
        .badge-ok { background-color: #28a745; }
        .badge-err { background-color: #dc3545; }
        .badge-warn { background-color: #ffc107; color: #212529; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #dee2e6; text-align: left; }
        th { background-color: #f8f9fa; }
        pre { background: #272822; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Công Cụ Chẩn Đoán Lỗi Hosting - MTech</h1>

    <h2>1. Môi Trường PHP & Cấu Hình Máy Chủ</h2>
    <table>
        <tr><th>Thông số</th><th>Giá trị</th><th>Trạng thái</th></tr>
        <tr>
            <td>Phiên bản PHP</td>
            <td><?php echo PHP_VERSION; ?></td>
            <td><span class="badge badge-ok">OK</span></td>
        </tr>
        <tr>
            <td>Chế độ bảo mật open_basedir</td>
            <td><?php echo ini_get('open_basedir') ?: 'Không giới hạn'; ?></td>
            <td>
                <?php if (ini_get('open_basedir')): ?>
                    <span class="badge badge-warn">Có hạn chế (open_basedir)</span>
                <?php else: ?>
                    <span class="badge badge-ok">Bình thường</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Hỗ trợ GD Extension</td>
            <td><?php echo extension_loaded('gd') ? 'Có' : 'Không'; ?></td>
            <td>
                <?php if (extension_loaded('gd')): ?>
                    <span class="badge badge-ok">OK</span>
                <?php else: ?>
                    <span class="badge badge-err">Cần bật GD</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Hỗ trợ xử lý WebP (imagewebp)</td>
            <td><?php echo function_exists('imagewebp') ? 'Có' : 'Không'; ?></td>
            <td>
                <?php if (function_exists('imagewebp')): ?>
                    <span class="badge badge-ok">Hỗ trợ WebP</span>
                <?php else: ?>
                    <span class="badge badge-warn">Chưa hỗ trợ WebP (Trình duyệt sẽ tự động nhận JPG/PNG gốc)</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <h2>2. Kiểm Tra Kết Nối Cơ Sở Dữ Liệu (Database)</h2>
    <?php
    $dbConnected = false;
    $dbError = '';
    try {
        require_once __DIR__ . '/core/database.php';
        $db = getDBConnection();
        if ($db) {
            $dbConnected = true;
        }
    } catch (Throwable $e) {
        $dbError = $e->getMessage();
    }
    ?>
    <?php if ($dbConnected): ?>
        <p><span class="badge badge-ok">Thành công</span> Kết nối CSDL thành công!</p>
    <?php else: ?>
        <p><span class="badge badge-err">Lỗi</span> Không thể kết nối Database!</p>
        <pre><?php echo htmlspecialchars($dbError); ?></pre>
    <?php endif; ?>

    <h2>3. Kiểm Tra Đọc / Ghi Thư Mục Cache</h2>
    <?php
    $cacheDir = __DIR__ . '/cache';
    $cacheWritable = false;
    $cacheErr = '';
    try {
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $testFile = $cacheDir . '/test_write.txt';
        if (@file_put_contents($testFile, 'test') !== false) {
            $cacheWritable = true;
            @unlink($testFile);
        } else {
            $cacheErr = 'Không thể ghi file vào ' . $cacheDir;
        }
    } catch (Throwable $e) {
        $cacheErr = $e->getMessage();
    }
    ?>
    <?php if ($cacheWritable): ?>
        <p><span class="badge badge-ok">OK</span> Thư mục cache hoạt động bình thường (<?php echo htmlspecialchars($cacheDir); ?>)</p>
    <?php else: ?>
        <p><span class="badge badge-warn">Cảnh báo</span> Thư mục cache không ghi được (Hệ thống sẽ tự động chuyển sang đọc CSDL trực tiếp mà không gây lỗi trang).</p>
        <?php if ($cacheErr): ?><pre><?php echo htmlspecialchars($cacheErr); ?></pre><?php endif; ?>
    <?php endif; ?>

    <h2>4. Kiểm Tra Nạp Thử Layout & Model Chức Năng</h2>
    <?php
    $errors = [];
    
    // Test Helpers
    try {
        require_once __DIR__ . '/core/helpers.php';
        echo "<p>✅ Nạp core/helpers.php: <strong>OK</strong></p>";
    } catch (Throwable $e) {
        $errors['helpers.php'] = $e->getMessage();
    }

    // Test ImageHelper
    try {
        require_once __DIR__ . '/core/ImageHelper.php';
        $testUrl = ImageHelper::getUrl('assets/images/logo_mtech.png');
        echo "<p>✅ Nạp ImageHelper (Test URL: <code>$testUrl</code>): <strong>OK</strong></p>";
    } catch (Throwable $e) {
        $errors['ImageHelper.php'] = $e->getMessage();
    }

    // Test HeaderModel
    try {
        require_once __DIR__ . '/app/models/HeaderModel.php';
        $hm = new HeaderModel();
        $hs = $hm->getSettingsWithFallback();
        echo "<p>✅ Nạp HeaderModel & Query DB: <strong>OK</strong></p>";
    } catch (Throwable $e) {
        $errors['HeaderModel.php'] = $e->getMessage();
    }

    // Test CategoriesModel
    try {
        require_once __DIR__ . '/app/models/CategoriesModel.php';
        $cm = new CategoriesModel();
        $cats = $cm->getAllCategories();
        echo "<p>✅ Nạp CategoriesModel (" . count($cats) . " danh mục): <strong>OK</strong></p>";
    } catch (Throwable $e) {
        $errors['CategoriesModel.php'] = $e->getMessage();
    }

    // Test ProjectsModel
    try {
        require_once __DIR__ . '/app/models/ProjectsModel.php';
        $pm = new ProjectsModel();
        $projs = $pm->getMenuProjects(5);
        echo "<p>✅ Nạp ProjectsModel (" . count($projs) . " dự án menu): <strong>OK</strong></p>";
    } catch (Throwable $e) {
        $errors['ProjectsModel.php'] = $e->getMessage();
    }

    // Test FooterModel
    try {
        require_once __DIR__ . '/app/models/FooterModel.php';
        $fm = new FooterModel();
        $fs = $fm->getSettings();
        echo "<p>✅ Nạp FooterModel: <strong>OK</strong></p>";
    } catch (Throwable $e) {
        $errors['FooterModel.php'] = $e->getMessage();
    }

    // Test Header Layout Rendering
    try {
        ob_start();
        include __DIR__ . '/app/views/_layout/header.php';
        $headerHtml = ob_get_clean();
        echo "<p>✅ Test Render header.php (" . strlen($headerHtml) . " bytes HTML): <strong>OK</strong></p>";
    } catch (Throwable $e) {
        if (ob_get_level() > 0) ob_end_clean();
        $errors['header.php layout'] = $e->getMessage() . "\n" . $e->getTraceAsString();
    }
    ?>

    <?php if (!empty($errors)): ?>
        <h2>❌ Chi Tiết Các Lỗi Phát Hiện Được:</h2>
        <?php foreach ($errors as $file => $msg): ?>
            <p><strong>Lỗi tại <?php echo $file; ?>:</strong></p>
            <pre><?php echo htmlspecialchars($msg); ?></pre>
        <?php endforeach; ?>
    <?php else: ?>
        <h2>🎉 Tóm Tắt: Không phát hiện lỗi nghiêm trọng nào trong hệ thống!</h2>
    <?php endif; ?>
</div>
</body>
</html>
