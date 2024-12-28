<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit();
}

// Vérification de la session pour les informations du produit
if (!isset($_SESSION['id_proSimple']) || empty($_SESSION['id_proSimple'])) {
    header("Location: ../add.php?error=La session est vide.");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_id_warehouses']) && !empty($_POST['select_id_warehouses'])){
            $idprovar = intval($_SESSION['id_proSimple']);
            $warehouse_id=intval($_POST['select_id_warehouses']);

            // Récupérer la quantité de stock du produit
            $ss = "SELECT stock_quantity FROM products WHERE id = ?";
            $stm = $conn->prepare($ss);
            $stm->execute([$idprovar]);
            $resu = $stm->fetch();

            if ($resu && isset($resu['stock_quantity'])) {
                $stock_quantity = intval($resu['stock_quantity']);
            } else {
                header("Location: add_pro_etap4.php?error=Erreur: Quantité de stock introuvable.");
                exit();
            }
           $Idsupplier = intval($_POST['select_id_warehouses']); // Récupérer l'ID du dernier fournisseur inséré
            
            // Insertion de la quantité de stock dans la table 'warehouse_inventory'
            $sql_stock = "INSERT INTO warehouse_inventory (warehouse_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt_stock = $conn->prepare($sql_stock);
            $result=$stmt_stock->execute([$warehouse_id, $idprovar, $stock_quantity]);

           
            // Vérifier si l'insertion du produit a réussi
            if ($result) {
                 // Redirection en cas de succès
                header("Location: vidersession.php?success=Stock ajouté avec succès.");
                exit();
            } else {
                header("Location: add_pro_etap4b.php?error=Erreur lors la selection");
                exit();
            }

}else{
    header("Location: add_pro_etap4b.php?error=error.");
    exit();
}
?>