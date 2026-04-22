<?php
/**
 * Test script for categories page
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Categories Debug Test</h1>";

// Test 1: Check database connection
echo "<h2>1. Database Connection</h2>";
try {
    require_once __DIR__ . '/core/database.php';
    $db = getDBConnection();
    echo "<p style='color:green'>✓ Database connected successfully</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Check if categories table exists
echo "<h2>2. Categories Table</h2>";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'categories'");
    $tableExists = $stmt->rowCount() > 0;
    if ($tableExists) {
        echo "<p style='color:green'>✓ Table 'categories' exists</p>";
    } else {
        echo "<p style='color:red'>✗ Table 'categories' does NOT exist!</p>";
        echo "<p>Run this SQL to create table:</p>";
        echo "<pre>source database/migrations/001_create_categories_table.sql</pre>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test 3: Check data in categories table
echo "<h2>3. Categories Data</h2>";
try {
    $stmt = $db->query("SELECT * FROM categories WHERE status = 1");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Found " . count($data) . " active categories</p>";
    if (count($data) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Slug</th><th>Image</th></tr>";
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>" . ($row['id'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['name'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['slug'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['image'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange'>⚠ No data in categories table!</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test 4: Check CategoriesModel
echo "<h2>4. CategoriesModel</h2>";
try {
    require_once __DIR__ . '/app/models/CategoriesModel.php';
    $model = new CategoriesModel();
    $categories = $model->getAllCategories();
    echo "<p style='color:green'>✓ CategoriesModel works</p>";
    echo "<p>Model returned " . count($categories) . " categories</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ CategoriesModel error: " . $e->getMessage() . "</p>";
}

// Test 5: Check view file
echo "<h2>5. View File</h2>";
$viewFile = __DIR__ . '/app/views/categories/categories.php';
if (file_exists($viewFile)) {
    echo "<p style='color:green'>✓ View file exists: app/views/categories/categories.php</p>";
} else {
    echo "<p style='color:red'>✗ View file NOT found!</p>";
}

echo "<hr><h2>Solutions</h2>";
echo "<p>If table doesn't exist:</p>";
echo "<ol>";
echo "<li>Import: <code>database/migrations/001_create_categories_table.sql</code></li>";
echo "<li>Or run: mysql -u [user] -p [database] < database/migrations/001_create_categories_table.sql</li>";
echo "</ol>";
