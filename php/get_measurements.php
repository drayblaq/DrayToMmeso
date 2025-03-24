<?php
include('connection.php');

$product_id = $_GET['product_id'];
$table = $_GET['table'];
$value_field = $_GET['value_field'];
$id_field = $_GET['id_field'];

// Validate table name to prevent SQL injection
if (!in_array($table, ['measurement_cm', 'measurement_g'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid table']);
    exit;
}

// Modified SQL query to include size_name for cm measurements and prevent duplicates
if ($table === 'measurement_cm') {
    $sql = "SELECT DISTINCT m.$id_field as id, m.$value_field as value, m.size_name 
            FROM $table m
            JOIN product_variation pv ON pv.{$id_field} = m.{$id_field}
            WHERE pv.product_id = ?
            GROUP BY m.$id_field, m.$value_field, m.size_name
            ORDER BY m.$value_field";
} else {
    $sql = "SELECT DISTINCT m.$id_field as id, m.$value_field as value, m.g_range_end 
            FROM $table m
            JOIN product_variation pv ON pv.{$id_field} = m.{$id_field}
            WHERE pv.product_id = ?
            GROUP BY m.$id_field, m.$value_field, m.g_range_end
            ORDER BY m.$value_field";
}

try {
    $stmt = $bd->prepare($sql);
    $stmt->execute([$product_id]);
    $measurements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($measurements);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
}
?>