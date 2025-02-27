<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reference.php');
    exit;
}

// Récupérer et nettoyer les données du formulaire
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
$postal_code = filter_input(INPUT_POST, 'postal_code', FILTER_SANITIZE_STRING);

// Calculer le total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

try {
    // Démarrer une transaction
    $bd->beginTransaction();

    // Créer la commande
    $sql = "INSERT INTO orders (user_name, email, address, city, postal_code, total_amount, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
    $stmt = $bd->prepare($sql);
    $stmt->execute([$name, $email, $address, $city, $postal_code, $total]);
    $order_id = $bd->lastInsertId();

    // Enregistrer les détails de la commande
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, size) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $bd->prepare($sql);

    foreach ($_SESSION['cart'] as $item) {
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $item['size']
        ]);

        // Mettre à jour le stock (si vous gérez le stock)
        $update_stock = "UPDATE product SET stock = stock - ? WHERE product_id = ?";
        $stmt_stock = $bd->prepare($update_stock);
        $stmt_stock->execute([$item['quantity'], $item['product_id']]);
    }

    // Simuler une vérification de paiement
    // Dans un environnement de production, vous intégreriez ici un système de paiement réel comme Stripe
    $payment_successful = true;

    if ($payment_successful) {
        // Mettre à jour le statut de la commande
        $sql = "UPDATE orders SET status = 'completed' WHERE order_id = ?";
        $stmt = $bd->prepare($sql);
        $stmt->execute([$order_id]);

        // Valider la transaction
        $bd->commit();

        // Envoyer un email de confirmation
        $to = $email;
        $subject = "Confirmation de votre commande #" . $order_id;
        $message = "Bonjour " . htmlspecialchars($name) . ",\n\n";
        $message .= "Nous avons bien reçu votre commande #" . $order_id . ".\n";
        $message .= "Montant total : " . number_format($total, 2) . " Fcfa\n\n";
        $message .= "Détails de la livraison :\n";
        $message .= $address . "\n";
        $message .= $postal_code . " " . $city . "\n\n";
        $message .= "Merci de votre confiance !\n";

        $headers = "From: noreply@votresite.com";

        mail($to, $subject, $message, $headers);

        // Vider le panier
        unset($_SESSION['cart']);

        // Rediriger vers la page de confirmation
        header("Location: confirmation.php?order_id=" . $order_id);
        exit;
    } else {
        throw new Exception("Le paiement a échoué");
    }
} catch (Exception $e) {
    // En cas d'erreur, annuler la transaction
    $bd->rollBack();
    
    // Rediriger vers la page de paiement avec un message d'erreur
    $_SESSION['error'] = "Une erreur est survenue lors du traitement de votre commande : " . $e->getMessage();
    header("Location: cart_page.php");
    exit;
}
?>