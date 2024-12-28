<?php
session_start();
include "../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $code = htmlspecialchars($_POST['code_filde']);
    $discount_amount = floatval($_POST['discount_amount_filde']);
    $valid_from = $_POST['valid_from_filde'];
    $valid_until = $_POST['valid_until_filde'];

    // Validation simple des champs
    if (empty($code) || empty($discount_amount) || empty($valid_from) || empty($valid_until)) {
        header("Location: ajouter_reduction.php?error=Please fill all fields.");
        exit();
    }

    try {
        // Préparer la requête SQL pour insérer le coupon
        $sql = "INSERT INTO coupons (code, discount_amount, valid_from, valid_until, created_at) 
                VALUES (:code, :discount_amount, :valid_from, :valid_until, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':discount_amount', $discount_amount);
        $stmt->bindParam(':valid_from', $valid_from);
        $stmt->bindParam(':valid_until', $valid_until);

        // Exécuter la requête
        if ($stmt->execute()) {
            header("Location: ajouter_reduction.php?success=Coupon added successfully.");
        } else {
            header("Location: ajouter_reduction.php?error=Failed to add coupon.");
        }
    } catch (PDOException $e) {
        // Gérer les erreurs
        header("Location: ajouter_reduction.php?error=" . $e->getMessage());
    }
} else {
    header("Location: ajouter_reduction.php");
    exit();
}
?>
