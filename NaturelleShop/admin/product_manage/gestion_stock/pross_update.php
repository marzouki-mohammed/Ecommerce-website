<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    header("Location: add_stock.php?error=Erreur de connexion à la base de données.");
    exit();
}

// Vérification de la session pour l'ID du produit
if (!isset($_SESSION['idprosimplefunction']) || empty($_SESSION['idprosimplefunction'])) {
    header("Location: add_stock.php?error=Erreur lors de la sélection du produit.");
    exit();
}

$idpro = intval($_SESSION['idprosimplefunction']);

// Récupération de la quantité du produit principal
$quantity_update = isset($_POST['quantity_update']) ? intval($_POST['quantity_update']) : 0;

// Vérification de la quantité du produit principal
if ($quantity_update < 0) {
    header("Location: add_stock.php?error=Quantité de produit invalide.");
    exit();
}

if($quantity_update > 0){
    // Mise à jour de la quantité du produit principal
    $sql_update_product_active  = "UPDATE products SET active = true WHERE id = ?";
}else{
    // Mise à jour de la quantité du produit principal
    $sql_update_product_active  = "UPDATE products SET active = false WHERE id = ?";
}
$stmt_update_product_active  = $conn->prepare($sql_update_product_active);
$update_product_active = $stmt_update_product_active->execute([ $idpro]);

if (!$update_product_active) {
    header("Location: add_stock.php?error=Échec de la mise à jour du produit.");
    exit();
}
// Mise à jour de la quantité du produit principal
$sql_update_product = "UPDATE products SET stock_quantity  = ? WHERE id = ?";
$stmt_update_product = $conn->prepare($sql_update_product);
$update_product = $stmt_update_product->execute([$quantity_update, $idpro]);

if (!$update_product) {
    header("Location: add_stock.php?error=Échec de la mise à jour du produit.");
    exit();
}


// Mise à jour des variantes de produit
$cuont = intval($_POST['cuont']); // Nombre total de variantes
for ($i = 1; $i < $cuont; $i++) {
    if (isset($_POST["quantity_update_variant$i"])) {
        $quantity_variant = intval($_POST["quantity_update_variant$i"]);
        $variant_id = intval($_POST["variant_id$i"]);

        // Vérification de la quantité de la variante
        if ($quantity_variant < 0) {
            header("Location: add_stock.php?error=Quantité de variante $i invalide.");
            exit();
        }

        if($quantity_update > 0){
            // Mise à jour de la quantité du produit principal
            $sql_update_product_active_var  = "UPDATE variant_options  SET active  = true WHERE id = ?";
        }else{
            // Mise à jour de la quantité du produit principal
            $sql_update_product_active_var  = "UPDATE variant_options  SET active  = false WHERE id = ?";
        }
        $stmt_update_product_active_var = $conn->prepare($sql_update_product_active_var);
        $update_product_active_var = $stmt_update_product_active_var->execute([$variant_id]);
        
        if (!$update_product_active_var) {
            header("Location: add_stock.php?error=Échec de la mise à jour du produit.");
            exit();
        }



        // Mise à jour de la quantité de la variante
        $sql_update_variant = "UPDATE variant_options  SET quantity = ? WHERE id = ?";
        $stmt_update_variant = $conn->prepare($sql_update_variant);
        $update_variant = $stmt_update_variant->execute([$quantity_variant, $variant_id]);

        if (!$update_variant) {
            header("Location: add_stock.php?error=Échec de la mise à jour de la variante $i.");
            exit();
        }
    }
}

$sql_test1="SELECT *FROM products WHERE id=?";
$stn_test1=$conn->prepare($sql_test1);
$stn_test1->execute([$idpro]);
$result_test1=$stn_test1->fetch();
if(!$result_test1){
    // Redirection avec un message de succès
    header("Location: add_stock.php?success=Stock mis à jour avec succès.");
    exit();
}

$sql_test = "SELECT * FROM porso_statuses WHERE product_id = ? AND quantity_obli_var > 0";
$stm_test=$conn->prepare($sql_test);
$stm_test->execute([$idpro]);
$result_test=$stm_test->fetchAll(PDO::FETCH_ASSOC);
if($result_test && $result_test1['active']==true){
    // Redirection avec un message de succès
    header("Location: gestion_orders.php");
    exit();
}else{
    // Redirection avec un message de succès
    header("Location: add_stock.php?success=Stock mis à jour avec succès.");
    exit();

}


?>
