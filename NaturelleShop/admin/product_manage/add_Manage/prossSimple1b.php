<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérification de la session pour les informations du produit
if (
    !isset($_SESSION['title_filde']) || empty($_SESSION['title_filde']) ||
    !isset($_SESSION['desc_filde']) || empty($_SESSION['desc_filde']) ||
    !isset($_SESSION['price_filde']) || empty($_SESSION['price_filde']) ||
    !isset($_SESSION['quentiter_filde']) || 
    !isset($_SESSION['active_pro']) 
) {
    header("Location: ../add.php?error=La session est vide.");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_id_supplier']) && !empty($_POST['select_id_supplier'])){
           $Idsupplier = intval($_POST['select_id_supplier']); // Récupérer l'ID du dernier fournisseur inséré

            // Récupérer les informations du produit depuis la session
            $title = $_SESSION['title_filde'];
            $description = $_SESSION['desc_filde'];
            $price = $_SESSION['price_filde'];
            $quantity = $_SESSION['quentiter_filde'];
            $active_pro = $_SESSION['active_pro'];


            // Préparer la requête pour insérer le produit
            $sql = "INSERT INTO products (product_name, description, price, vente_price, stock_quantity, supplier_id,active )
                    VALUES (?, ?, ?, ?, ?, ?,?)";

            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$title, $description, $price, $price, $quantity, $Idsupplier,$active_pro]);

            // Vérifier si l'insertion du produit a réussi
            if ($result) {
                $_SESSION['id_proSimple']=$conn->lastInsertId();
                header("Location: add_pro_etap2.php");
                exit();
            } else {
                header("Location: ../add.php?error=Erreur lors de l'ajout du produit.");
                exit();
            }

}else{
    header("Location: add_pro_etap1b.php?error=error.");
    exit();
}
?>