<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Traitement de la mise à jour du statut des produits
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_id_Update_status']) && !empty($_POST['select_id_Update_status'])) {
    // Filtrer et valider les IDs des produits à mettre à jour
    $ids_to_update_status = array_map('intval', $_POST['select_id_Update_status']);
    
    foreach ($ids_to_update_status as $id) {
        // Vérifier l'état actuel du produit
        $sql_data = "SELECT active FROM products WHERE id = ?";
        $stm_data = $conn->prepare($sql_data);
        $stm_data->execute([$id]);
        $data = $stm_data->fetch();

        if ($data) {
            // Inverser le statut du produit
            $new_status = !$data['active'];
            $sql_update = "UPDATE products SET active = ? WHERE id = ?";
            $stm_update = $conn->prepare($sql_update);
            $stm_update->execute([$new_status, $id]);
        }
    }
    
    // Rediriger après la mise à jour
    header("Location: Update_status.php");
    exit();   
} else {
    // Rediriger avec un message d'erreur si la requête POST est invalide
    header("Location: Update_status.php?error=Erreur: Action non reconnue.");
    exit();
}
?>
