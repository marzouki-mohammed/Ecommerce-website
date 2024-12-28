<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérification de la session pour les informations du produit
if (!isset($_SESSION['id_proSimple']) || empty($_SESSION['id_proSimple'])) {
    header("Location: ../add.php?error=La session est vide.");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_ids = $_POST['categorie_ids'] ?? ''; // Récupération des IDs des catégories
    if (!empty($cat_ids)) {
        $idprodSimple = intval($_SESSION['id_proSimple']); // ID du produit

        foreach ($cat_ids as $id) {
            // Préparer la requête pour insérer le produit dans chaque catégorie
            $sql = "INSERT INTO product_categories (category_id, product_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$id, $idprodSimple]);

            if (!$result) {
                // Redirection en cas d'erreur
                header("Location: add_pro_etap2.php?error=Erreur lors de l'attribution des catégories");
                exit();
            }
        }

        // Redirection après insertion réussie
        header("Location: add_pro_etap3.php");
        exit();
    } else {
        // Redirection si aucune catégorie n'a été sélectionnée
        header("Location: add_pro_etap2.php?error=Aucune catégorie sélectionnée.");
        exit();
    }
} else {
    // Redirection si le formulaire n'a pas été soumis par POST
    header("Location: add_pro_etap2.php?error=Formulaire non valide.");
    exit();
}
?>
