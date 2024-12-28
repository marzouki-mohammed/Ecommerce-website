<?php
session_start();
include "../../php/db_connect.php";

if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_id_pro']) && !empty($_POST['select_id_pro'])) {
    $proat_id = intval($_POST['select_id_pro']);
    
    // Requête pour vérifier l'existence du produit
    $ss = "SELECT id FROM products WHERE id = ?";
    $stm = $conn->prepare($ss);
    $stm->execute([$proat_id]);
    $resu = $stm->fetch();

    if ($resu) {
        $_SESSION['idprosimplefunction'] = $proat_id;
        header("Location: listeatribute.php");
        exit();
    } else {
        header("Location: selectionpro.php?error=Erreur: Produit introuvable.");
        exit();
    }
} else {
    header("Location: selectionpro.php?error=Erreur: Aucune sélection effectuée.");
    exit();
}
?>
