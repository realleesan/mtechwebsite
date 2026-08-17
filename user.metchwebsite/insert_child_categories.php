<?php
/**
 * insert_child_categories.php
 * File PHP hỗ trợ chạy script thêm lĩnh vực con trực tiếp trên hosting bằng trình duyệt
 */

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/core/database.php';
require_once __DIR__ . '/app/models/CategoriesModel.php';

echo "<h2>=== THÊM LĨNH VỰC CON VÀO LĨNH VỰC CHA ===</h2>";

try {
    $db = getDBConnection();

    // 1. Kiểm tra 6 lĩnh vực cha
    $parentCategories = [
        1 => ['name' => 'Lập quy hoạch xây dựng', 'slug' => 'lap-quy-hoach-xay-dung', 'image' => 'assets/images/services/service-1.jpg'],
        2 => ['name' => 'Lập dự án đầu tư và Thiết kế cơ sở', 'slug' => 'lap-du-an-dau-tu-va-thiet-ke-co-so', 'image' => 'assets/images/services/service-2.jpg'],
        3 => ['name' => 'Lập thiết kế xây dựng và Dự toán / Tổng dự toán', 'slug' => 'lap-thiet-ke-xay-dung-va-du-toan-tong-du-toan', 'image' => 'assets/images/services/service-3.jpg'],
        4 => ['name' => 'Giám sát thi công xây dựng và Lắp đặt thiết bị', 'slug' => 'giam-sat-thi-cong-xay-dung-va-lap-dat-thiet-bi', 'image' => 'assets/images/services/service-4.jpg'],
        5 => ['name' => 'Tư vấn kỹ thuật', 'slug' => 'tu-van-ky-thuat', 'image' => 'assets/images/services/service-5.jpg'],
        6 => ['name' => 'Tổng thầu tư vấn dự án đầu tư', 'slug' => 'tong-thau-tu-van-du-an-dau-tu', 'image' => 'assets/images/services/service-6.jpg'],
    ];

    echo "<h3>1. Kiểm tra trạng thái 6 lĩnh vực cha:</h3><ul>";
    $parentIds = [];

    foreach ($parentCategories as $sortOrder => $p) {
        $stmt = $db->prepare("SELECT id, name, image FROM categories WHERE (name LIKE ? OR slug LIKE ?) AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['%' . mb_substr($p['name'], 0, 15) . '%', '%' . mb_substr($p['slug'], 0, 15) . '%']);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $parentIds[$sortOrder] = $existing;
            echo "<li style='color:green;'>✅ Đã có cha: <strong>" . htmlspecialchars($existing['name']) . "</strong> (ID: {$existing['id']})</li>";
        } else {
            // Tạo mới nếu chưa có
            $ins = $db->prepare("INSERT INTO categories (parent_id, name, slug, image, status, sort_order) VALUES (NULL, ?, ?, ?, 1, ?)");
            $ins->execute([$p['name'], $p['slug'], $p['image'], $sortOrder]);
            $newId = (int)$db->lastInsertId();
            $parentIds[$sortOrder] = ['id' => $newId, 'name' => $p['name'], 'image' => $p['image']];
            echo "<li style='color:blue;'>➕ Đã thêm mới cha: <strong>{$p['name']}</strong> (ID: {$newId})</li>";
        }
    }
    echo "</ul>";

    // 2. Thêm các lĩnh vực con
    echo "<h3>2. Thêm các lĩnh vực con theo cấu trúc:</h3>";

    $childConfigs = [
        // Con của "Lập dự án đầu tư và Thiết kế cơ sở" (sortOrder = 2)
        2 => [
            ['name' => 'Các dự án luyện kim - năng lượng', 'slug' => 'cac-du-an-luyen-kim-nang-luong-lap-du-an-dau-tu', 'sort_order' => 1],
            ['name' => 'Các dự án nông nghiệp', 'slug' => 'cac-du-an-nong-nghiep-lap-du-an-dau-tu', 'sort_order' => 2],
            ['name' => 'Các dự án xi măng và vật liệu xây dựng', 'slug' => 'cac-du-an-xi-mang-va-vat-lieu-xay-dung-lap-du-an-dau-tu', 'sort_order' => 3],
        ],
        // Con của "Lập thiết kế xây dựng và Dự toán / Tổng dự toán" (sortOrder = 3)
        3 => [
            ['name' => 'Các dự án luyện kim - năng lượng', 'slug' => 'cac-du-an-luyen-kim-nang-luong-lap-thiet-ke-xay-dung', 'sort_order' => 1],
            ['name' => 'Các dự án nông nghiệp', 'slug' => 'cac-du-an-nong-nghiep-lap-thiet-ke-xay-dung', 'sort_order' => 2],
            ['name' => 'Các dự án xi măng và vật liệu xây dựng', 'slug' => 'cac-du-an-xi-mang-va-vat-lieu-xay-dung-lap-thiet-ke-xay-dung', 'sort_order' => 3],
        ]
    ];

    echo "<ul>";
    foreach ($childConfigs as $parentKey => $children) {
        $parentInfo = $parentIds[$parentKey] ?? null;
        if (!$parentInfo) continue;

        $pId = (int)$parentInfo['id'];
        $pImg = !empty($parentInfo['image']) ? $parentInfo['image'] : 'assets/images/services/service-1.jpg';

        echo "<li><strong>Lĩnh vực cha: " . htmlspecialchars($parentInfo['name']) . " (ID: {$pId})</strong><ul>";

        foreach ($children as $c) {
            // Kiểm tra xem con đã có chưa
            $chk = $db->prepare("SELECT id, name FROM categories WHERE parent_id = ? AND (name LIKE ? OR slug = ?) AND deleted_at IS NULL LIMIT 1");
            $chk->execute([$pId, '%' . $c['name'] . '%', $c['slug']]);
            $hasChild = $chk->fetch(PDO::FETCH_ASSOC);

            if ($hasChild) {
                echo "<li style='color:#555;'>ℹ️ Đã tồn tại con: <strong>" . htmlspecialchars($hasChild['name']) . "</strong> (ID: {$hasChild['id']})</li>";
            } else {
                $insChild = $db->prepare("INSERT INTO categories (parent_id, name, slug, image, status, sort_order) VALUES (?, ?, ?, ?, 1, ?)");
                $insChild->execute([$pId, $c['name'], $c['slug'], $pImg, $c['sort_order']]);
                $childId = (int)$db->lastInsertId();
                echo "<li style='color:green;'>✅ Đã thêm con: <strong>" . htmlspecialchars($c['name']) . "</strong> (ID: {$childId})</li>";
            }
        }

        echo "</ul></li>";
    }
    echo "</ul>";

    echo "<h3 style='color:green;'>🎉 Hoàn tất thành công! Toàn bộ 6 lĩnh vực cha và các lĩnh vực con đã được cập nhật đầy đủ.</h3>";

} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
