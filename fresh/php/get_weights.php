<?php
include('connection.php');

$product_id = filter_input(INPUT_GET, 'product_id', FILTER_SANITIZE_NUMBER_INT);
$weight_type = filter_input(INPUT_GET, 'weight_type', FILTER_SANITIZE_STRING);

if ($weight_type === 'grams') {
    $sql = "SELECT gram_id AS id, gram_value AS value FROM grams";
} else {
    $sql = "SELECT kg_id AS id, kg_value AS value FROM kgs";
}

$stmt = $bd->prepare($sql);
$stmt->execute();
$weights = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($weights);
?>