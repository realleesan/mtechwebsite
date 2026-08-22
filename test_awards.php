<?php
// Test script for AwardsController
chdir('D:\\Xampp\\htdocs\\mtechwebsite');

// Load database
require_once __DIR__ . '/user.metchwebsite/core/database.php';

try {
    $db = getDBConnection();
    echo "Database connected successfully\n";
    
    // Test AwardsModel query
    $stmt = $db->prepare("SELECT id, name, certificate, image FROM awards WHERE status = 1 AND deleted_at IS NULL ORDER BY sort_order ASC, id ASC");
    $stmt->execute();
    $awards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Awards count: " . count($awards) . "\n";
    
    // Test CapacityFieldsModel query
    $stmt = $db->prepare("SELECT id, sort_order, name FROM capacity_fields WHERE status = 1 AND deleted_at IS NULL ORDER BY sort_order ASC, id ASC");
    $stmt->execute();
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Capacity fields count: " . count($fields) . "\n";
    
    foreach ($fields as $field) {
        $items = $db->prepare("SELECT name, rank, sort_order FROM capacity_field_items WHERE field_id = ? AND status = 1 AND deleted_at IS NULL ORDER BY sort_order ASC, id ASC");
        $items->execute([$field['id']]);
        $field['items'] = $items->fetchAll(PDO::FETCH_ASSOC);
        echo "Field: {$field['name']} - Items: " . count($field['items']) . "\n";
    }
    
    echo "All queries executed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
