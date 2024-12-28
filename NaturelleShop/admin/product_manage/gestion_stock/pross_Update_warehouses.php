<?php 
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérification de la session pour les informations du produit
if (!isset($_SESSION['idprosimplefunction']) || empty($_SESSION['idprosimplefunction'])) {
    header("Location: ../selectionpro.php?error=no_product_selected");
    exit();
}

$idprovar = intval($_SESSION['idprosimplefunction']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['warehouses_delete_ids']) && !empty($_POST['warehouses_delete_ids'])) {
    $idwer = intval($_POST['warehouses_delete_ids']);

    // Vérification de l'existence du produit
    $sqldata = "SELECT stock_quantity FROM products WHERE id = ?";
    $stmdata = $conn->prepare($sqldata);
    $stmdata->execute([$idprovar]);
    $data = $stmdata->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        header("Location: Update_warehouses.php?error=product_not_found");
        exit();
    }

    $qunti = $data['stock_quantity'];

    // Vérification si le produit existe dans cet entrepôt
    $sqltest = "SELECT * FROM warehouse_inventory WHERE warehouse_id = ? AND product_id = ?";
    $stmt_test = $conn->prepare($sqltest);
    $stmt_test->execute([$idwer, $idprovar]);

    $resule = $stmt_test->fetch();

    if ($resule) {
        // Le produit existe déjà dans cet entrepôt, donc suppression
        $sql = "DELETE FROM warehouse_inventory WHERE warehouse_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute([$idwer, $idprovar]);

        if (!$res) {
            header("Location: Update_warehouses.php?error=deletion_failed");
            exit();
        }
    } else {
        // Le produit n'existe pas dans cet entrepôt, insertion dans warehouse_inventory
        $sql = "INSERT INTO warehouse_inventory (warehouse_id, product_id, quantity) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute([$idwer, $idprovar, $qunti]);

        if (!$res) {
            header("Location: Update_warehouses.php?error=insertion_failed");
            exit();
        }
    }

    header("Location: Update_warehouses.php?success=operation_successful");
    exit();
} else {
    header("Location: Update_warehouses.php?error=invalid_request");
    exit();
}
?>
