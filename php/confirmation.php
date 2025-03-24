<?php
session_start();
include('connection.php');

// Vérifier si l'ID de commande est présent
if (!isset($_GET['order_id'])) {
    header('Location: reference.php');
    exit;
}

$order_id = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT);

// Récupérer les détails de la commande
$sql = "SELECT o.*, oi.* 
        FROM orders o 
        LEFT JOIN order_items oi ON o.order_id = oi.order_id 
        WHERE o.order_id = ?";
$stmt = $bd->prepare($sql);
$stmt->execute([$order_id]);
$order_details = $stmt->fetchAll();

if (empty($order_details)) {
    header('Location: reference.php');
    exit;
}

$order = $order_details[0];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="sty.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .success-message {
            text-align: center;
            color: #28a745;
            margin-bottom: 30px;
        }
        .order-details {
            margin-bottom: 30px;
        }
        .shipping-details {
            margin-bottom: 30px;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-message">
            <i class="fas fa-check-circle fa-3x"></i>
            <h1>Commande confirmée !</h1>
            <p>Votre commande #<?php echo $order_id; ?> a été traitée avec succès.</p>
        </div>

        <div class="order-details">
            <h2>Détails de la commande</h2>
            <p><strong>Date :</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            <p><strong>Montant total :</strong> <?php echo number_format($order['total_amount'], 2); ?> Fcfa</p>
            
            <h3>Articles commandés</h3>
            <table class="order-items">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_details as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'], 2); ?> Fcfa</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="shipping-details">
            <h2>Adresse de livraison</h2>
            <p><?php echo htmlspecialchars($order['user_name']); ?></p>
            <p><?php echo htmlspecialchars($order['address']); ?></p>
            <p><?php echo htmlspecialchars($order['postal_code']) . ' ' . htmlspecialchars($order['city']); ?></p>
        </div>

        <div class="actions">
            <a href="reference.php" class="btn">Retour à la boutique</a>
            <a href="javascript:window.print()" class="btn">Imprimer la confirmation</a>
        </div>
    </div>
</body>
</html>