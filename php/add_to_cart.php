<?php
// add_to_cart.php
include('connection.php');

function getProductPrice($productId, $cmSize, $gSize, $countryId, $weightValue) {
    global $bd;
    
    // Debug logging
    error_log("Getting price for: Product ID: $productId, CM: $cmSize, G: $gSize, Country: $countryId, Weight: $weightValue");
    
    // Determine which size field to use
    $sizeField = $cmSize !== null ? 'cm_id' : 'g_id';
    $sizeValue = $cmSize !== null ? $cmSize : $gSize;
    
    $sql = "SELECT pv.price 
            FROM product_variation pv
            WHERE pv.product_id = :product_id
            AND pv.$sizeField = :size_value
            AND pv.country_of_origin = :country_id
            AND pv.kg_id = :weight_value";
            
    try {
        $stmt = $bd->prepare($sql);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':size_value', $sizeValue, PDO::PARAM_INT);
        $stmt->bindParam(':country_id', $countryId, PDO::PARAM_INT);
        $stmt->bindParam(':weight_value', $weightValue, PDO::PARAM_INT);
        
        // Debug logging
        error_log("Executing SQL: $sql");
        error_log("Parameters: " . json_encode([
            'product_id' => $productId,
            'size_value' => $sizeValue,
            'country_id' => $countryId,
            'weight_value' => $weightValue
        ]));
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            error_log("Price found: " . $result['price']);
            return ['success' => true, 'price' => $result['price']];
        } else {
            error_log("No price found for the selected combination");
            return ['success' => false, 'error' => 'Aucun prix trouvé pour cette combinaison'];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Erreur de base de données'];
    }
}

// Handle the AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and decode JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Debug logging
    error_log("Received input: " . json_encode($input));
    
    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'Données invalides']);
        exit;
    }
    
    $productId = filter_var($input['product_id'], FILTER_SANITIZE_NUMBER_INT);
    $cmSize = isset($input['cm_size']) ? filter_var($input['cm_size'], FILTER_SANITIZE_NUMBER_INT) : null;
    $gSize = isset($input['g_size']) ? filter_var($input['g_size'], FILTER_SANITIZE_NUMBER_INT) : null;
    $countryId = filter_var($input['country'], FILTER_SANITIZE_NUMBER_INT);
    $weightValue = filter_var($input['weight_value'], FILTER_SANITIZE_NUMBER_INT);
    
    // Validate required fields
    if (!$productId || (!$cmSize && !$gSize) || !$countryId || !$weightValue) {
        echo json_encode([
            'success' => false, 
            'error' => 'Champs requis manquants',
            'debug' => [
                'product_id' => $productId,
                'cm_size' => $cmSize,
                'g_size' => $gSize,
                'country' => $countryId,
                'weight_value' => $weightValue
            ]
        ]);
        exit;
    }
    
    $result = getProductPrice($productId, $cmSize, $gSize, $countryId, $weightValue);
    
    header('Content-Type: application/json');
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'price' => number_format($result['price'], 0, ',', ' ') . ' FCFA'
        ]);
    } else {
        echo json_encode($result);
    }
}
?>