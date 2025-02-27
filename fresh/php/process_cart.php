<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!empty($input['product_name']) && !empty($input['price'])) {
        // Create unique cart item identifier
        $cartItemKey = $input['product_id'] . '_' . 
                       $input['size_type'] . '_' . 
                       $input['size_value'] . '_' . 
                       $input['country_id'] . '_' . 
                       $input['weight_value'];

        // Check if item already exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            $itemKey = $item['product_id'] . '_' . 
                       $item['size_type'] . '_' . 
                       $item['size_value'] . '_' . 
                       $item['country_id'] . '_' . 
                       $item['weight_value'];

            if ($itemKey === $cartItemKey) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }

        // If not found, add new item
        if (!$found) {
            $_SESSION['cart'][] = [
                'product_id' => $input['product_id'],
                'name' => $input['product_name'],
                'size_type' => $input['size_type'],
                'size_text' => $input['size_text'],
                'country_text' => $input['country_text'],
                'weight_text' => $input['weight_text'],
                'quantity' => 1,
                'price' => floatval(str_replace(' ', '', $input['price']))
            ];
        }

        echo json_encode([
            'success' => true, 
            'cart_count' => count($_SESSION['cart'])
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid cart data']);
    }
    exit;
}
?>