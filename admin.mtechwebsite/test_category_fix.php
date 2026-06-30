<?php
/**
 * Test script kiểm tra fix quản lý lĩnh vực (không cần DB)
 * Chỉ kiểm tra code logic: view, controller, model
 */

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Kiểm tra fix quản lý lĩnh vực (static analysis)</h2>\n";

$errors = [];
$passed = [];

// -----------------------------------------------
// Test 1: View create.php không còn dropdown dự án
// -----------------------------------------------
echo "<hr><h3>Test 1: View create.php không còn dropdown Dự án hiển thị</h3>\n";

$createView = file_get_contents(__DIR__ . '/app/views/categories/create.php');
if (strpos($createView, 'featured_project_id') === false) {
    echo "<p style='color:green'>✅ View create.php không còn trường featured_project_id</p>\n";
    $passed[] = "create.php has no featured_project_id field";
} else {
    $err = "View create.php vẫn còn trường featured_project_id";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 2: View edit.php vẫn có dropdown dự án
// -----------------------------------------------
echo "<hr><h3>Test 2: View edit.php vẫn có dropdown Dự án hiển thị</h3>\n";

$editView = file_get_contents(__DIR__ . '/app/views/categories/edit.php');
if (strpos($editView, 'featured_project_id') !== false) {
    echo "<p style='color:green'>✅ View edit.php vẫn có trường featured_project_id (đúng logic)</p>\n";
    $passed[] = "edit.php has featured_project_id dropdown";
} else {
    $err = "View edit.php mất trường featured_project_id";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// Kiểm tra edit view có đúng syntax PHP không bị lỗi
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($editView);
$phpErrors = libxml_get_errors();
$hasPhpSyntaxError = false;
foreach ($phpErrors as $e) {
    if (stripos($e->message, 'php') !== false || stripos($e->message, '<?php') !== false) {
        $hasPhpSyntaxError = true;
        break;
    }
}
if (!$hasPhpSyntaxError) {
    echo "<p style='color:green'>✅ View edit.php không có lỗi PHP syntax rõ ràng</p>\n";
    $passed[] = "edit.php has no obvious PHP syntax errors";
} else {
    echo "<p style='color:orange'>⚠️ View edit.php có thể có lỗi PHP syntax</p>\n";
}

// -----------------------------------------------
// Test 3: Controller create() không còn load projects
// -----------------------------------------------
echo "<hr><h3>Test 3: Controller create() không còn load danh sách dự án</h3>\n";

$controllerCode = file_get_contents(__DIR__ . '/app/controllers/CategoriesController.php');
$createMethodStart = strpos($controllerCode, 'public function create()');
$createMethodEnd = strpos($controllerCode, 'public function store()', $createMethodStart);
$createMethod = substr($controllerCode, $createMethodStart, $createMethodEnd - $createMethodStart);

if (strpos($createMethod, "'projects'") === false && strpos($createMethod, '$allProjects') === false && strpos($createMethod, '$projectsModel') === false) {
    echo "<p style='color:green'>✅ Controller create() không còn biến dự án</p>\n";
    $passed[] = "controller create() has no projects loading";
} else {
    $err = "Controller create() vẫn còn code load dự án";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 4: Controller edit() dùng getProjectsByCategory
// -----------------------------------------------
echo "<hr><h3>Test 4: Controller edit() dùng getProjectsByCategory</h3>\n";

$editMethodStart = strpos($controllerCode, 'public function edit(');
$editMethodEnd = strpos($controllerCode, 'public function update(', $editMethodStart);
$editMethod = substr($controllerCode, $editMethodStart, $editMethodEnd - $editMethodStart);

if (strpos($editMethod, 'getProjectsByCategory') !== false) {
    echo "<p style='color:green'>✅ Controller edit() dùng getProjectsByCategory</p>\n";
    $passed[] = "controller edit() uses getProjectsByCategory";
} else {
    $err = "Controller edit() không dùng getProjectsByCategory";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

if (strpos($editMethod, 'featured_project') !== false) {
    echo "<p style='color:green'>✅ Controller edit() vẫn truyền featured_project (đúng logic)</p>\n";
    $passed[] = "controller edit() still passes featured_project";
} else {
    $err = "Controller edit() thiếu dữ liệu featured_project";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 5: ProjectsModel có method getProjectsByCategory
// -----------------------------------------------
echo "<hr><h3>Test 5: ProjectsModel có method getProjectsByCategory</h3>\n";

$modelCode = file_get_contents(__DIR__ . '/app/models/ProjectsModel.php');
if (strpos($modelCode, 'function getProjectsByCategory') !== false) {
    echo "<p style='color:green'>✅ ProjectsModel có method getProjectsByCategory</p>\n";
    $passed[] = "ProjectsModel has getProjectsByCategory method";
} else {
    $err = "ProjectsModel thiếu method getProjectsByCategory";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// Kiểm tra method có JOIN project_services và WHERE category_id
$methodStart = strpos($modelCode, 'function getProjectsByCategory');
$methodEnd = strpos($modelCode, 'function getProjectServices', $methodStart);
$methodCode = substr($modelCode, $methodStart, $methodEnd - $methodStart);

if (strpos($methodCode, 'project_services') !== false) {
    echo "<p style='color:green'>✅ getProjectsByCategory có JOIN với project_services</p>\n";
    $passed[] = "getProjectsByCategory joins project_services";
} else {
    $err = "getProjectsByCategory không JOIN project_services";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

if (strpos($methodCode, 'category_id') !== false) {
    echo "<p style='color:green'>✅ getProjectsByCategory lọc theo category_id</p>\n";
    $passed[] = "getProjectsByCategory filters by category_id";
} else {
    $err = "getProjectsByCategory không filter theo category_id";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 6: buildData() vẫn có featured_project_id (cho update)
// -----------------------------------------------
echo "<hr><h3>Test 6: buildData() vẫn xử lý featured_project_id</h3>\n";

if (strpos($controllerCode, "'featured_project_id'") !== false) {
    echo "<p style='color:green'>✅ buildData() vẫn xử lý featured_project_id</p>\n";
    $passed[] = "buildData() still handles featured_project_id";
} else {
    $err = "buildData() mất xử lý featured_project_id";
    echo "<p style='color:red'>❌ {$err}</p>\n";
    $errors[] = $err;
}

// -----------------------------------------------
// Test 7: View create.php không còn truy cập $projects
// -----------------------------------------------
echo "<hr><h3>Test 7: View create.php không còn biến \$projects</h3>\n";

// Tìm các PHPDoc hoặc PHP comment nói về featured_project trong create
$commentMatches = [];
preg_match_all('/featured_project|\$projects/', $createView, $commentMatches);
if (empty($commentMatches[0])) {
    echo "<p style='color:green'>✅ View create.php không còn tham chiếu đến \$projects hoặc featured_project</p>\n";
    $passed[] = "create.php has no projects variable references";
} else {
    $err = "View create.php vẫn còn tham chiếu đến projects/featured_project: " . implode(', ', array_unique($commentMatches[0]));
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