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

// Vérification si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_POST['warehouse_name']) && !empty($_POST['warehouse_name'])
    && isset($_POST['location']) && !empty($_POST['location'])
    ) {

    $idprovar = intval($_SESSION['id_proSimple']);

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

    // Récupérer les données du formulaire
    $warehouse_name = $_POST['warehouse_name'];
    $location = $_POST['location'];

    // Vérification si l'entrepôt existe déjà
    $sql_check = "SELECT * FROM warehouses WHERE name = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$warehouse_name]);

    if ($stmt_check->rowCount() > 0) {
        // Si l'entrepôt existe déjà
        header("Location: add_pro_etap4.php?error=L'entrepôt existe déjà.");
        exit();
    }

    // Insertion des données dans la table 'warehouses'
    $sql = "INSERT INTO warehouses (name, location, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$warehouse_name, $location]);

    if ($result) {
        // Récupérer l'ID de l'entrepôt ajouté
        $warehouse_id = $conn->lastInsertId();

        // Insertion de la quantité de stock dans la table 'warehouse_inventory'
        $sql_stock = "INSERT INTO warehouse_inventory (warehouse_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt_stock = $conn->prepare($sql_stock);
        $stmt_stock->execute([$warehouse_id, $idprovar, $stock_quantity]);

        // Redirection en cas de succès
        header("Location: vidersession.php?success=Stock ajouté avec succès.");
        exit();
    } else {
        header("Location: add_warehouse.php?error=Erreur lors de l'insertion de l'entrepôt.");
        exit();
    }

} else {
    // Si le formulaire est incomplet ou non valide
    header("Location: add_warehouse.php?error=Formulaire incomplet ou non valide.");
    exit();
}
?>
