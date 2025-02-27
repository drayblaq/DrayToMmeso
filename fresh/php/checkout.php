<?php
// config/database.php
class Database {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO('mysql:host=localhost;dbname=fishes_online;charset=utf8', 'root', '', 
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            } catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}

// models/Cart.php
class Cart {
    private $db;
    private $userId;
    private $cartId;

    public function __construct($userId) {
        $this->db = Database::getInstance();
        $this->userId = $userId;
        $this->initializeCart();
    }

    private function initializeCart() {
        // Get existing cart or create new one
        $stmt = $this->db->prepare("
            SELECT cart_id FROM cart 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([':user_id' => $this->userId]);
        
        if ($cart = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->cartId = $cart['cart_id'];
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO cart (user_id) VALUES (:user_id)
            ");
            $stmt->execute([':user_id' => $this->userId]);
            $this->cartId = $this->db->lastInsertId();
        }
    }

    public function addItem($variationId, $quantity = 1) {
        // Check if item already exists in cart
        $stmt = $this->db->prepare("
            SELECT cart_item_id, quantity 
            FROM cart_items 
            WHERE cart_id = :cart_id AND variation_id = :variation_id
        ");
        $stmt->execute([
            ':cart_id' => $this->cartId,
            ':variation_id' => $variationId
        ]);
        
        if ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Update existing item
            $stmt = $this->db->prepare("
                UPDATE cart_items 
                SET quantity = quantity + :quantity 
                WHERE cart_item_id = :cart_item_id
            ");
            return $stmt->execute([
                ':quantity' => $quantity,
                ':cart_item_id' => $item['cart_item_id']
            ]);
        } else {
            // Add new item
            $stmt = $this->db->prepare("
                INSERT INTO cart_items (cart_id, variation_id, quantity)
                VALUES (:cart_id, :variation_id, :quantity)
            ");
            return $stmt->execute([
                ':cart_id' => $this->cartId,
                ':variation_id' => $variationId,
                ':quantity' => $quantity
            ]);
        }
    }

    public function updateQuantity($cartItemId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        $stmt = $this->db->prepare("
            UPDATE cart_items 
            SET quantity = :quantity 
            WHERE cart_item_id = :cart_item_id 
            AND cart_id = :cart_id
        ");
        
        return $stmt->execute([
            ':quantity' => $quantity,
            ':cart_item_id' => $cartItemId,
            ':cart_id' => $this->cartId
        ]);
    }

    public function removeItem($cartItemId) {
        $stmt = $this->db->prepare("
            DELETE FROM cart_items 
            WHERE cart_item_id = :cart_item_id 
            AND cart_id = :cart_id
        ");
        
        return $stmt->execute([
            ':cart_item_id' => $cartItemId,
            ':cart_id' => $this->cartId
        ]);
    }

    public function getItems() {
        $stmt = $this->db->prepare("
            SELECT 
                ci.cart_item_id,
                ci.quantity,
                p.product_id,
                p.product_name,
                p.product_picture,
                pv.variation_id,
                pv.price,
                c.country_name,
                COALESCE(mc.cm_value, mg.g_value) as size_value,
                COALESCE(mc.size_name, 
                    CASE 
                        WHEN mg.g_range_end IS NOT NULL 
                        THEN CONCAT(mg.g_value, '-', mg.g_range_end)
                        ELSE CAST(mg.g_value AS CHAR)
                    END
                ) as size_display,
                CASE 
                    WHEN mc.cm_id IS NOT NULL THEN 'cm'
                    WHEN mg.g_id IS NOT NULL THEN 'g'
                END as size_unit,
                mk.kg_value as package_size,
                (pv.price * ci.quantity) as subtotal
            FROM cart_items ci
            JOIN product_variation pv ON ci.variation_id = pv.variation_id
            JOIN product p ON pv.product_id = p.product_id
            JOIN countries c ON pv.country_of_origin = c.country_id
            LEFT JOIN measurement_cm mc ON pv.cm_id = mc.cm_id
            LEFT JOIN measurement_g mg ON pv.g_id = mg.g_id
            JOIN measurement_kg mk ON pv.kg_id = mk.kg_id
            WHERE ci.cart_id = :cart_id
        ");
        
        $stmt->execute([':cart_id' => $this->cartId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotal() {
        $stmt = $this->db->prepare("
            SELECT SUM(pv.price * ci.quantity) as total
            FROM cart_items ci
            JOIN product_variation pv ON ci.variation_id = pv.variation_id
            WHERE ci.cart_id = :cart_id
        ");
        
        $stmt->execute([':cart_id' => $this->cartId]);
        return $stmt->fetchColumn() ?: 0;
    }

    public function clear() {
        $stmt = $this->db->prepare("
            DELETE FROM cart_items 
            WHERE cart_id = :cart_id
        ");
        
        return $stmt->execute([':cart_id' => $this->cartId]);
    }
}

// cart.php
session_start();
require_once 'connection.php';
require_once 'checkout.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Instantiate the Cart class
$cart = new Cart($_SESSION['user_id']);

// Handle AJAX requests
if (isset($_POST['action'])) {
    $response = ['success' => false];
    
    switch ($_POST['action']) {
        case 'add':
            $response['success'] = $cart->addItem($_POST['variation_id'], $_POST['quantity'] ?? 1);
            break;
        case 'update':
            $response['success'] = $cart->updateQuantity($_POST['cart_item_id'], $_POST['quantity']);
            break;
        case 'remove':
            $response['success'] = $cart->removeItem($_POST['cart_item_id']);
            break;
    }
    
    if ($response['success']) {
        $response['total'] = $cart->getTotal();
        $response['items'] = $cart->getItems();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get cart items and total for display
$cartItems = $cart->getItems();
$total = $cart->getTotal();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-8">Mon Panier</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600">Votre panier est vide</p>
                <a href="products.php" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">
                    Continuer les achats
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Produit</th>
                                    <th class="px-6 py-3 text-right">Spécifications</th>
                                    <th class="px-6 py-3 text-center">Quantité</th>
                                    <th class="px-6 py-3 text-right">Prix</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($cartItems as $item): ?>
                                    <tr data-cart-item-id="<?= htmlspecialchars($item['cart_item_id']) ?>">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <img src="<?= htmlspecialchars($item['product_picture']) ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                     class="w-16 h-16 object-cover rounded">
                                                <div class="ml-4">
                                                    <div class="font-medium text-gray-900">
                                                        <?= htmlspecialchars($item['product_name']) ?>
                                                    </div>
                                                    <div class="text-gray-500">
                                                        Origine: <?= htmlspecialchars($item['country_name']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-sm text-gray-900">
                                                Taille: <?= htmlspecialchars($item['size_display']) ?> <?= htmlspecialchars($item['size_unit']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Poids: <?= htmlspecialchars($item['package_size']) ?> kg
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center items-center">
                                                <button class="quantity-btn minus bg-gray-200 px-2 py-1 rounded-l">-</button>
                                                <input type="number" 
                                                       class="quantity-input w-16 text-center border-t border-b border-gray-200" 
                                                       value="<?= htmlspecialchars($item['quantity']) ?>"
                                                       min="1">
                                                <button class="quantity-btn plus bg-gray-200 px-2 py-1 rounded-r">+</button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <?= number_format($item['price'], 2) ?> €
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <?= number_format($item['subtotal'], 2) ?> €
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="remove-item text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold mb-4">Résumé de la commande</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span>Sous-total</span>
                                <span><?= number_format($total, 2) ?> €</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Livraison</span>
                                <span>Calculé à l'étape suivante</span>
                            </div>
                            <hr class="my-4">
                            <div class="flex justify-between font-semibold">
                                <span>Total</span>
                                <span><?= number_format($total, 2) ?> €</span>
                            </div>
                            <a href="checkout.php" 
                               class="block w-full bg-blue-500 text-white text-center px-4 py-2 rounded hover:bg-blue-600">
                                Procéder au paiement
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateCart = async (productId, action, quantity = null) => {
            try {
                const formData = new FormData();
                formData.append('action', action);
                formData.append('product_id', productId);
                if (quantity !== null) {
                    formData.append('quantity', quantity);
                }

                const response = await fetch('cart.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    // Refresh the page to show updated cart
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Une erreur est survenue');
            }
        };

        // Quantity buttons
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const productId = row.dataset.productId;
                const input = row.querySelector('.quantity-input');
                let quantity = parseInt(input.value);

                if (this.classList.contains('plus')) {
                    quantity++;
                } else if (this.classList.contains('minus')) {
                    quantity = Math.max(1, quantity - 1);
                }

                updateCart(productId, 'update', quantity);
            });
        });

        // Quantity input
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                const productId = row.dataset.productId;
                const quantity = Math.max(1, parseInt(this.value) || 1);
                
                updateCart(productId, 'update', quantity);
            });
        });

        // Remove buttons
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const productId = row.dataset.productId;
                
                if (confirm('Êtes-vous sûr de vouloir retirer cet article ?')) {
                    updateCart(productId, 'remove');
                }
            });
        });
    });
    </script>
</body>
</html>