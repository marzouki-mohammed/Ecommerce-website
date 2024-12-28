<?php
session_start();
include "../../../php/db_connect.php";

if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['type_filde']) && !empty($_POST['type_filde'])
    && isset($_POST['title_filde']) && !empty($_POST['title_filde'])
    && isset($_POST['desc_filde']) && !empty($_POST['desc_filde'])
    && isset($_POST['price_filde']) && !empty($_POST['price_filde'])
    && isset($_POST['quentiter_filde']) 
    && isset($_POST['active_pro']) 
) {
    // Assainissement des données
    $type = htmlspecialchars($_POST['type_filde']);
    $title = htmlspecialchars($_POST['title_filde']);
    $description = htmlspecialchars($_POST['desc_filde']);
    $price = floatval($_POST['price_filde']);
    $quantity = intval($_POST['quentiter_filde']);
    $active_pro = intval($_POST['active_pro']);

    // Validation des champs
    if (empty($type) || empty($title) || empty($description) || $price <= 0 ) {
        header("Location: ../add.php?error=Veuillez remplir tous les champs correctement.");
        exit();
    }

    $sqltest = "
        SELECT * FROM products WHERE product_name = ?
        UNION
        SELECT * FROM components WHERE component_name = ?
    ";
    $stmtest = $conn->prepare($sqltest);
    $stmtest->execute([$title, $title]);

    if ($stmtest->rowCount() > 0) {
        header("Location: ../add.php?error=Le produit ou le composant existe déjà.");
        exit();
    }

    $_SESSION['title_filde'] = $title;
    $_SESSION['desc_filde'] = $description;
    $_SESSION['price_filde'] = $price;
    $_SESSION['quentiter_filde'] = $quantity;
    $_SESSION['active_pro'] = $active_pro;

    if ($type == 'simple') {
        header("Location: add_pro_etap1.php");
        exit;
    } elseif ($type == 'composer') {
        header("Location: add_pro_Composer_etap1.php");
        exit;
    } else {
        header("Location: ../add.php?error=Type de produit non reconnu.");
        exit;
    }
} else {
    header("Location: ../add.php?error=Error.");
    exit;
}
?>
