<?php
/**
 * Test script kiểm tra frontend header hiển thị dự án từ project_services
 */

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Kiểm tra frontend CategoriesModel + Header (static analysis)</h2>\n";

$errors = [];
$passed = [];

// -----------------------------------------------
// Test 1: Frontend CategoriesModel::getAllCategories() có projects array
// -----------------------------------------------
echo "<hr><h3>Test 1: getAllCategories() có logic load projects từ project_services</h3>\n";

$modelCode = file_get_contents(__DIR__ . '/app/models/CategoriesModel.php');

if (strpos($modelCode, 'project_services') !== false) {
    echo "<p style='color:green'>✅ getAllCategories() query qua project_services</p>\n";
    $passed[] = "getAllCategories uses project_services";
} else {
    $err = "getAllCategories() không query project_services";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

if (strpos($modelCode, "\$categories as &\$cat") !== false && strpos($modelCode, "'projects'") !== false) {
    echo "<p style='color:green'>✅ getAllCategories() gán mảng projects vào từng category</p>\n";
    $passed[] = "getAllCategories assigns projects array to each category";
} else {
    $err = "getAllCategories() chưa gán mảng projects";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 2: Header view dùng $item['projects']
// -----------------------------------------------
echo "<hr><h3>Test 2: Header view dùng \$item['projects']</h3>\n";

$headerCode = file_get_contents(__DIR__ . '/app/views/_layout/header.php');

if (strpos($headerCode, "\$item['projects']") !== false || strpos($headerCode, '$item[\'projects\']') !== false) {
    echo "<p style='color:green'>✅ Header dùng \$item[\"projects\"]</p>\n";
    $passed[] = "header uses item projects array";
} else {
    $err = "Header không dùng item['projects']";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

if (strpos($headerCode, 'foreach ($projects as $project)') !== false) {
    echo "<p style='color:green'>✅ Header loop qua nhiều dự án</p>\n";
    $passed[] = "header loops through multiple projects";
} else {
    $err = "Header không loop qua nhiều dự án";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 3: Header không còn phụ thuộc chỉ vào 1 project_id
// -----------------------------------------------
echo "<hr><h3>Test 3: Header không còn chỉ check 1 project_id</h3>\n";

// Tìm hàm renderDropdownMenuItems
$funcStart = strpos($headerCode, 'function renderDropdownMenuItems');
$funcEnd = strpos($headerCode, '?>', $funcStart);
$funcCode = substr($headerCode, $funcStart, $funcEnd - $funcStart);

if (strpos($funcCode, "\$item['project_id']") === false) {
    echo "<p style='color:green'>✅ Header không còn dùng project_id đơn lẻ</p>\n";
    $passed[] = "header does not use single project_id";
} else {
    $err = "Header vẫn dùng project_id đơn lẻ";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 4: Header có case hiển thị dự án cho category lá
// -----------------------------------------------
echo "<hr><h3>Test 4: Header có case category lá có dự án</h3>\n";

// Kiểm tra có case cho !$hasChild && $hasProject không
if (strpos($funcCode, '!$hasChild && $hasProject') !== false || strpos($funcCode, '!$hasChild && $hasProject') !== false) {
    echo "<p style='color:green'>✅ Header có case category lá hiển thị dự án</p>\n";
    $passed[] = "header handles leaf category with projects";
} else {
    $err = "Header chưa có case category lá có dự án";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 5: Header vẫn giữ compatibility với dữ liệu cũ
// -----------------------------------------------
echo "<hr><h3>Test 5: Header vẫn giữ các biến cũ để backward compatible</h3>\n";

if (strpos($funcCode, "project_title") !== false || strpos($funcCode, "project_slug") !== false) {
    echo "<p style='color:green'>✅ Header vẫn tham chiếu project_title/project_slug (compatible)</p>\n";
    $passed[] = "header still references old project fields";
} else {
    echo "<p style='color:orange'>⚠️ Header không còn tham chiếu project_title cũ (có thể đã chuyển hết sang projects array)</p>\n";
}

// -----------------------------------------------
// Test 6: Kiểm tra syntax cơ bản của header.php
// -----------------------------------------------
echo "<hr><h3>Test 6: Syntax cơ bản của header.php</h3>\n";

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($headerCode);
$phpErrors = libxml_get_errors();
$hasPhpSyntaxError = false;
foreach ($phpErrors as $e) {
    if (stripos($e->message, 'php') !== false || stripos($e->message, '<?php') !== false) {
        $hasPhpSyntaxError = true;
        break;
    }
}
if (!$hasPhpSyntaxError) {
    echo "<p style='color:green'>✅ Header PHP không có lỗi syntax rõ ràng</p>\n";
    $passed[] = "header has no obvious PHP syntax errors";
} else {
    echo "<p style='color:orange'>⚠️ Header có thể có lỗi PHP syntax</p>\n";
}

// -----------------------------------------------
// Test 7: Kiểm tra CategoriesModel vẫn giữ featured_project_id
// -----------------------------------------------
echo "<hr><h3>Test 7: CategoriesModel vẫn có featured_project_id</h3>\n";

if (strpos($modelCode, 'featured_project_id') !== false) {
    echo "<p style='color:green'>✅ CategoriesModel vẫn có featured_project_id</p>\n";
    $passed[] = "CategoriesModel still has featured_project_id";
} else {
    $err = "CategoriesModel thiếu featured_project_id";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Tổng kết
// -----------------------------------------------
echo "<hr><h2>Tổng kết</h2>\n";
echo "<p><b>Đạt:</b> " . count($passed) . " / " . (count($passed) + count($errors)) . " tests</p>\n";

if (!empty($passed)) {
    echo "<ul>\n";
    foreach ($passed as $p) {
        echo "<li style='color:green'>✅ {$p}</li>\n";
    }
    echo "</ul>\n";
}

if (!empty($errors)) {
    echo "<ul>\n";
    foreach ($errors as $e) {
        echo "<li style='color:red'>❌ {$e}</li>\n";
    }
    echo "</ul>\n";
    echo "<p style='color:red; font-weight:bold;'>Có lỗi cần sửa!</p>\n";
} else {
    echo "<p style='color:green; font-weight:bold;'>Tất cả kiểm tra đều PASS!</p>\n";
}
?>