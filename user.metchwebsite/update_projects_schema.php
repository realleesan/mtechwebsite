<?php
/**
 * update_projects_schema.php
 * File PHP tự động cập nhật cấu trúc database bảng projects trực tiếp trên hosting qua trình duyệt
 */

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/core/database.php';

echo "<h2>=== CẬP NHẬT CẤU TRÚC DATABASE BẢNG PROJECTS ===</h2>";

try {
    $db = getDBConnection();

    // Danh sách các cột cần thêm
    $columns = [
        'capacity' => "VARCHAR(255) NULL DEFAULT '' COMMENT '2. Công suất'",
        'total_investment' => "VARCHAR(255) NULL DEFAULT '' COMMENT '5. Tổng mức đầu tư'",
        'construction_year' => "VARCHAR(255) NULL DEFAULT '' COMMENT '6. Năm xây dựng / hoàn thành'",
        'bidding_form' => "VARCHAR(255) NULL DEFAULT '' COMMENT '7. Hình thức gói thầu (EP/EPC)'",
        'equipment_contractor' => "VARCHAR(255) NULL DEFAULT '' COMMENT '8. Nhà thầu cung cấp thiết bị'",
        'design_consultant' => "VARCHAR(255) NULL DEFAULT '' COMMENT '9. Đơn vị tư vấn thiết kế xây dựng'",
        'supervision_consultant' => "VARCHAR(255) NULL DEFAULT '' COMMENT '10. Đơn vị tư vấn giám sát'",
        'gallery' => "TEXT NULL COMMENT 'Thư viện ảnh JSON array'",
    ];

    echo "<h3>Kiểm tra và thêm các cột vào bảng `projects`:</h3><ul>";

    foreach ($columns as $columnName => $columnDef) {
        // Kiểm tra xem cột đã tồn tại chưa
        $stmt = $db->query("SHOW COLUMNS FROM `projects` LIKE '{$columnName}'");
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            echo "<li style='color:#555;'>ℹ️ Cột <code>{$columnName}</code> đã tồn tại.</li>";
        } else {
            $alterSql = "ALTER TABLE `projects` ADD COLUMN `{$columnName}` {$columnDef}";
            $db->exec($alterSql);
            echo "<li style='color:green;'>✅ Đã thêm mới cột: <strong>{$columnName}</strong></li>";
        }
    }

    echo "</ul>";
    echo "<h3 style='color:green;'>🎉 Bảng `projects` đã được cập nhật đầy đủ 10 trường thông số kỹ thuật và thư viện ảnh!</h3>";

} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
