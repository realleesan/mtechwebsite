<?php
/**
 * DEBUG FILE - Xóa sau khi kiểm tra xong!
 * Upload file này lên hosting, truy cập: yourdomain.com/_debug_categories.php
 */
define('APP_INIT', true);
require_once __DIR__ . '/core/database.php';

echo '<pre>';
try {
    $pdo = getDBConnection();

    // 1. Kiểm tra bảng categories có tồn tại không
    $tables = $pdo->query("SHOW TABLES LIKE 'categories'")->fetchAll();
    echo "=== TABLE EXISTS: " . (count($tables) > 0 ? 'YES' : 'NO - CHƯA TẠO BẢNG!') . " ===\n\n";

    if (count($tables) > 0) {
        // 2. Xem cấu trúc bảng
        $cols = $pdo->query("SHOW COLUMNS FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        echo "=== COLUMNS ===\n";
        foreach ($cols as $c) {
            echo $c['Field'] . ' (' . $c['Type'] . ') default=' . $c['Default'] . "\n";
        }

        // 3. Xem dữ liệu
        $rows = $pdo->query("SELECT id, name, image, status, sort_order FROM categories ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
        echo "\n=== DATA (" . count($rows) . " rows) ===\n";
        foreach ($rows as $r) {
            echo "id={$r['id']} | {$r['name']} | image=" . ($r['image'] ?? 'NULL') . " | status={$r['status']}\n";
        }

        if (count($rows) === 0) {
            echo "\n>>> BẢNG RỖNG - Cần chạy migration 001_create_categories_table.sql\n";
        }
    }
} catch (Exception $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}
echo '</pre>';
