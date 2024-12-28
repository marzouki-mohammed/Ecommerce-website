<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérification de l'ID du produit dans la session
if (!isset($_SESSION['idprosimplefunction']) || empty($_SESSION['idprosimplefunction'])) {
    header("Location: ../selectionpro.php?error=Erreur: Aucune sélection de produit.");
    exit();
}

$idpro = intval($_SESSION['idprosimplefunction']);

// Traitement de la suppression des catégories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_categories'])) {
    if (isset($_POST['select_idcat_delete']) && is_array($_POST['select_idcat_delete']) && count($_POST['select_idcat_delete']) > 0) {
        // Filtrer et valider les IDs des catégories à supprimer
        $ids_to_delete = array_map('intval', $_POST['select_idcat_delete']);
        
        // Préparer la requête de suppression
        $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
        $sql_delete = "DELETE FROM product_categories WHERE product_id = ? AND category_id IN ($placeholders)";
        $stmt_delete = $conn->prepare($sql_delete);
        
        // Fusionner l'ID du produit avec les IDs des catégories
        $params = array_merge([$idpro], $ids_to_delete);
        
        try {
            $stmt_delete->execute($params);
            header("Location: attributproduct2.php?success=Catégories supprimées avec succès.");
            exit();
        } catch (PDOException $e) {
            header("Location: attributproduct2.php?error=Erreur lors de la suppression des catégories.");
            exit();
        }
    } else {
        header("Location: attributproduct2.php?error=Erreur: Aucune catégorie sélectionnée pour suppression.");
        exit();
    }
}

// Traitement de l'attribution des nouvelles catégories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_categories'])) {
    if (isset($_POST['select_idcat_attribuer']) && is_array($_POST['select_idcat_attribuer']) && count($_POST['select_idcat_attribuer']) > 0) {
        // Filtrer et valider les IDs des catégories à attribuer
        $ids_to_assign = array_map('intval', $_POST['select_idcat_attribuer']);
        
        // Préparer la requête d'insertion
        $sql_insert = "INSERT INTO product_categories (product_id, category_id) VALUES ";
        $values = [];
        $params = [];
        foreach ($ids_to_assign as $cat_id) {
            $values[] = "(?, ?)";
            $params[] = $idpro;
            $params[] = $cat_id;
        }
        $sql_insert .= implode(',', $values);
        
        try {
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->execute($params);
            header("Location: attributproduct2.php?success=Catégories attribuées avec succès.");
            exit();
        } catch (PDOException $e) {
            // Gestion des erreurs, par exemple, duplication des entrées
            header("Location: attributproduct2.php?error=Erreur lors de l'attribution des catégories.");
            exit();
        }
    } else {
        header("Location: attributproduct2.php?error=Erreur: Aucune catégorie sélectionnée pour attribution.");
        exit();
    }
}

// Si aucune action n'est détectée, rediriger
header("Location: attributproduct2.php?error=Erreur: Action non reconnue.");
exit();
?>
