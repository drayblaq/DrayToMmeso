<?php
session_start();

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        echo "<p>Product: {$item['name']}</p>";
        echo "<p>Size: {$item['size']} kg</p>";
        echo "<p>Price: {$item['price']}</p>";
        echo "<p>Quantity: {$item['quantity']}</p>";
        echo "<hr>";
    }
} else {
    echo "Your cart is empty.";
}
?>
