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

    if (!isset($_SESSION['id_pro_content']) || empty($_SESSION['id_pro_content'])) {
        header("Location: add_pro_Composer_etap3.php?error=La session est vide.");
        exit();
    }

    $id_pro_content = $_SESSION['id_pro_content'];
    $idpro = intval($_SESSION['id_proComposer']);

    $sql_stock_produit = "SELECT * FROM components WHERE id=?";
    $stm_stock_produit = $conn->prepare($sql_stock_produit);
    $stm_stock_produit->execute([$idpro]);
    $result_stock_produit = $stm_stock_produit->fetch(PDO::FETCH_ASSOC);

    if (!$result_stock_produit) {
        header("Location: add_pro_Composer_etap3.php?error=error.");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styleform.css">
</head>
<body>
    <section class="container">
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php } ?>
        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php } ?>
        <header class="title"><?php echo "Product: " . htmlspecialchars($result_stock_produit['component_name']); ?></header>

        <form action="prossComposer3.php" method="post" enctype="multipart/form-data" class="form">
            <?php
            $i = 1;
            foreach ($id_pro_content as $row) {
                $sql_stock_variant = "SELECT * FROM products WHERE id=?";
                $stmt_stock_variant = $conn->prepare($sql_stock_variant);
                $stmt_stock_variant->execute([intval($row)]);
                $result_stock_variant = $stmt_stock_variant->fetchAll(PDO::FETCH_ASSOC);

                if (!$result_stock_variant) {
                    header("Location: add_pro_Composer_etap3.php?error=error.");
                    exit();
                }

                foreach ($result_stock_variant as $variant) {
                    echo "<header class='title'>Product " . $i . ": " . htmlspecialchars($variant['product_name']) . "</header>";
                    echo '<div class="input-box">';
                    echo "<input type='hidden' name='pro_content_id$i' value='" . htmlspecialchars($variant['id']) . "'>"; // Envoi de l'ID de la variante
                    echo "<label for='quantity_update_pro$i'>Stock Quantité du Variant " . $i . "</label>";
                    echo "<input type='number' name='quantity_update_pro$i' id='quantity_update_pro$i' placeholder='Entrez la quantité' min='0' required />";
                    echo '</div>';
                    $i++;
                }
            }
            ?>

            <button type="submit" id="submit_btn_update_stock_comp" class="submit">Add</button>
        </form>
    </section>
    <!-- Ionicon link -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
