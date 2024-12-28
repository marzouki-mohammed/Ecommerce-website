<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérification de la session pour les informations du produit
if (!isset($_SESSION['id_proComposer']) || empty($_SESSION['id_proComposer'])) {
    header("Location: add_pro_Composer_etap3.php?error=La session est vide.");
    exit();
}

$id_comp = intval($_SESSION['id_proComposer']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $i = 1;
    // Boucle pour parcourir les produits soumis via le formulaire
    while (isset($_POST["pro_content_id$i"]) && !empty($_POST["pro_content_id$i"]) && isset($_POST["quantity_update_pro$i"])) {
        $id_pro = intval($_POST["pro_content_id$i"]);
        $qenty = intval($_POST["quantity_update_pro$i"]);

        // Insertion dans la table product_composer
        $sql = "INSERT INTO product_composer (product_id, component_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$id_pro, $id_comp, $qenty]);

        if (!$result) {
            // Redirection en cas d'erreur
            header("Location: add_pro_Composer_etap3.php?error=Erreur lors de l'attribution des produits");
            exit();
        }

        // Incrémentation de $i pour passer au produit suivant
        $i++;
    }

    // Redirection en cas de succès
    header("Location: vidersession.php?success=Produits attribués avec succès");
    exit();

} else {
    // Redirection si la méthode n'est pas POST
    header("Location: add_pro_Composer_etap3.php?error=La forme est invalide.");
    exit();
}
?>
