<?php
session_start();
include "../../../php/db_connect.php";

// Vérifier si le coupon est défini dans la session
if (!isset($_SESSION['id_reduction']) || empty($_SESSION['id_reduction'])) {
    header("Location: appliquer_reduction.php?error=Erreur dans la récupération du coupon ID");
    exit;
}

// Convertir l'ID du coupon en entier pour éviter les injections SQL
$idrud = intval($_SESSION['id_reduction']);

// Vérifier si la méthode de requête est POST et si un produit a été sélectionné
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['select_id_prosimple_reduction']) && !empty($_POST['select_id_prosimple_reduction'])) {
    $idpro = $_POST['select_id_prosimple_reduction'];

    // Parcourir les ID des produits sélectionnés
    foreach ($idpro as $id) {
        // Vérifier si le produit a déjà ce coupon appliqué
        $test = "SELECT * FROM product_coupons WHERE product_id = ? AND coupon_id = ?";
        $stmest = $conn->prepare($test);
        $stmest->execute([intval($id), $idrud]);
        $resul = $stmest->fetch(PDO::FETCH_ASSOC);

        if ($resul) {
            // Si le produit a déjà ce coupon, supprimer l'entrée
            $sql_delete = "DELETE FROM product_coupons WHERE product_id = ? AND coupon_id = ?";
            $stm_delete = $conn->prepare($sql_delete);
            $result_delete = $stm_delete->execute([intval($id), $idrud]);
            if (!$result_delete) {
                header("Location: selection_pro.php?error=Erreur dans la suppression");
                exit;
            }
        } else {
            // Insérer le produit et le coupon dans la table product_coupons
            $sql = "INSERT INTO product_coupons (product_id, coupon_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([intval($id), $idrud]);

            if (!$result) {
                header("Location: selection_pro.php?error=Erreur dans l'insertion");
                exit;
            }
        }

        // Récupérer les données du produit
        $datapro = "SELECT * FROM products WHERE id = ?";
        $stmdata = $conn->prepare($datapro);
        $stmdata->execute([intval($id)]);
        $data = $stmdata->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            header("Location: selection_pro.php?error=Erreur dans la récupération des données du produit");
            exit;
        }

        // Vérifier si des réductions sont applicables au produit
        $sqlappliquer = "SELECT c.*
                         FROM coupons c
                         INNER JOIN product_coupons pc ON c.id = pc.coupon_id
                         WHERE pc.product_id = ?
                         AND NOW() BETWEEN c.valid_from AND c.valid_until";
        $stmappliquer = $conn->prepare($sqlappliquer);
        $stmappliquer->execute([intval($id)]);
        $resulteappliquer = $stmappliquer->fetchAll(PDO::FETCH_ASSOC);

        if ($resulteappliquer) {
            $reduction = 0;
            foreach ($resulteappliquer as $app) {
                $reduction += $app['discount_amount'];
            }

            // Si la réduction dépasse 90%, afficher une erreur
            if ($reduction > 90) {
                header("Location: selection_pro.php?error=La réduction appliquée dépasse la limite logique pour le produit ID=$id");
                exit;
            }

            

            // Calculer le nouveau prix de vente en fonction de la réduction
            $price = $data['price'] - (($data['price'] * $reduction) / 100);

            // Mettre à jour le prix de vente et le prix de comparaison du produit
            $sred = "UPDATE products
                     SET vente_price = ?, 
                         compare_price = ?,
                         updated_at = CURRENT_TIMESTAMP
                     WHERE id = ?";
            $stmred = $conn->prepare($sred);
            $result_update = $stmred->execute([$price, $reduction, intval($id)]);

            if (!$result_update) {
                header("Location: selection_pro.php?error=La réduction n'a pas pu être appliquée");
                exit;
            }
        }else{
            // Réinitialiser le prix de vente au prix original et le prix de comparaison à 0 (sans réduction)
            $sred = "UPDATE products
            SET vente_price = ?, 
                compare_price = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";
            $stmred = $conn->prepare($sred);
            $result_update = $stmred->execute([$data['price'],0, intval($id)]);

            if (!$result_update) {
            header("Location: selection_pro.php?error=La réduction n'a pas pu être réinitialisée");
            exit;
            }
        }
    }

    // Rediriger avec un message de succès
    header("Location: selection_pro.php?success=Ajouté avec succès.");
    exit;
} else {
    // Rediriger avec un message d'erreur si le formulaire n'est pas valide
    header("Location: selection_pro.php?error=Erreur dans la validation du formulaire.");
    exit;
}
?>
