<?php
/**
 * test_filter.php
 * File kiểm tra hoạt động của bộ lọc dự án và danh mục trên hosting
 */

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/core/database.php';
require_once __DIR__ . '/app/models/ProjectsModel.php';
require_once __DIR__ . '/app/models/CategoriesModel.php';

echo "<h2>=== KIỂM TRA BỘ LỌC DỰ ÁN & LĨNH VỰC ===</h2>";

try {
    $db = getDBConnection();
    $projectsModel = new ProjectsModel($db);
    $categoriesModel = new CategoriesModel($db);

    echo "<h3>1. Kiểm tra danh mục (Categories & Tree)</h3>";
    $allCategories = $categoriesModel->getAllCategories();
    echo "<p>Tổng số categories active: <strong>" . count($allCategories) . "</strong></p>";

    $categoryTree = $categoriesModel->buildTree($allCategories);
    echo "<p>Số danh mục cấp 1 (Root Categories): <strong>" . count($categoryTree) . "</strong></p>";

    echo "<ul>";
    foreach ($categoryTree as $cat) {
        $childrenCount = count($cat['children'] ?? []);
        echo "<li><strong>" . htmlspecialchars($cat['name']) . "</strong> (ID: {$cat['id']}, Slug: {$cat['slug']}, Số con: {$childrenCount})";
        if (!empty($cat['children'])) {
            echo "<ul>";
            foreach ($cat['children'] as $child) {
                echo "<li>" . htmlspecialchars($child['name']) . " (ID: {$child['id']}, Slug: {$child['slug']})</li>";
            }
            echo "</ul>";
        }
        echo "</li>";
    }
    echo "</ul>";

    echo "<h3>2. Kiểm tra đếm dự án theo lĩnh vực (project_services)</h3>";
    $counts = $projectsModel->getCategoryProjectCounts();
    echo "<table border='1' cellpadding='6' style='border-collapse: collapse;'>";
    echo "<tr><th>Category ID</th><th>Tên Category</th><th>Số dự án gán</th></tr>";
    foreach ($allCategories as $cat) {
        $cnt = $counts[(int)$cat['id']] ?? 0;
        echo "<tr><td>{$cat['id']}</td><td>" . htmlspecialchars($cat['name']) . "</td><td><strong>{$cnt}</strong></td></tr>";
    }
    echo "</table>";

    echo "<h3>3. Kiểm tra bảng trung gian project_services</h3>";
    $psRows = $db->query("SELECT * FROM project_services LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Mẫu 10 bản ghi trong project_services:</p><pre>";
    print_r($psRows);
    echo "</pre>";

    echo "<h3>4. Kiểm tra lọc dự án</h3>";
    $totalAll = $projectsModel->countFilteredProjects([], 1);
    echo "<p>Tổng số dự án active: <strong>{$totalAll}</strong></p>";

    $sampleProjects = $projectsModel->getFilteredProjects([], 3, 0, 1);
    echo "<p>Lấy mẫu 3 dự án đầu tiên:</p>";
    echo "<ul>";
    foreach ($sampleProjects as $p) {
        echo "<li><strong>" . htmlspecialchars($p['title']) . "</strong> (ID: {$p['id']}, Slug: {$p['slug']})</li>";
    }
    echo "</ul>";

    // Test filter with first available category that has projects
    $testCatId = 0;
    foreach ($counts as $catId => $cnt) {
        if ($cnt > 0) {
            $testCatId = $catId;
            break;
        }
    }
    if ($testCatId > 0) {
        $countTest = $projectsModel->countFilteredProjects([$testCatId], 1);
        $testProjects = $projectsModel->getFilteredProjects([$testCatId], 5, 0, 1);
        echo "<p style='color:blue;font-weight:bold;'>Thử nghiệm lọc theo Category ID {$testCatId}: tìm thấy {$countTest} dự án.</p>";
        echo "<ul>";
        foreach ($testProjects as $p) {
            echo "<li>" . htmlspecialchars($p['title']) . " (ID: {$p['id']})</li>";
        }
        echo "</ul>";
    }

    echo "<p style='color:green;font-weight:bold;'>✅ Tất cả các phương thức Model đã hoạt động tốt!</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
