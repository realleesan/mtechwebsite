<?php
/**
 * DEBUG — Xóa file này sau khi kiểm tra xong!
 * Upload lên hosting rồi truy cập: yourdomain.com/_debug.php
 */
define('APP_INIT', true);
echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red} .warn{color:orange}</style>';
echo '<h2>Debug Report</h2>';

// 1. PHP version
echo '<p>PHP: ' . phpversion() . '</p>';

// 2. Kiểm tra DB
require_once __DIR__ . '/core/database.php';
echo '<h3>Database</h3>';
try {
    $pdo = getDBConnection();
    echo '<p class="ok">✓ DB connected</p>';

    // Kiểm tra bảng categories
    $tables = $pdo->query("SHOW TABLES LIKE 'categories'")->fetchAll();
    if (count($tables) === 0) {
        echo '<p class="err">✗ Bảng categories CHƯA TỒN TẠI — cần chạy 001_create_categories_table.sql</p>';
    } else {
        echo '<p class="ok">✓ Bảng categories tồn tại</p>';
        $rows = $pdo->query("SELECT id, name, image, status FROM categories ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
        echo '<p>Số rows: ' . count($rows) . '</p>';
        if (count($rows) === 0) {
            echo '<p class="err">✗ Bảng RỖNG — cần chạy INSERT trong 001_create_categories_table.sql</p>';
        } else {
            echo '<table border="1" cellpadding="5"><tr><th>id</th><th>name</th><th>image</th><th>status</th></tr>';
            foreach ($rows as $r) {
                $imgStatus = empty($r['image']) ? '<span class="err">NULL</span>' : '<span class="ok">' . htmlspecialchars($r['image']) . '</span>';
                echo "<tr><td>{$r['id']}</td><td>{$r['name']}</td><td>{$imgStatus}</td><td>{$r['status']}</td></tr>";
            }
            echo '</table>';
        }
    }
} catch (Exception $e) {
    echo '<p class="err">✗ DB Error: ' . $e->getMessage() . '</p>';
}

// 3. Kiểm tra file CSS có tồn tại không
echo '<h3>Files</h3>';
$files = [
    'assets/css/categories.css',
    'app/views/categories/categories.php',
    'app/models/CategoriesModel.php',
];
foreach ($files as $f) {
    if (file_exists($f)) {
        echo '<p class="ok">✓ ' . $f . ' (' . number_format(filesize($f)) . ' bytes, modified: ' . date('Y-m-d H:i:s', filemtime($f)) . ')</p>';
    } else {
        echo '<p class="err">✗ MISSING: ' . $f . '</p>';
    }
}
