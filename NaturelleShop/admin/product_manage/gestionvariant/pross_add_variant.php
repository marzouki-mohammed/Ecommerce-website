<?php
        session_start();
        include "../../../php/db_connect.php";



        // Vérification de la connexion à la base de données
        if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
        }

        // Vérification de la session pour les informations du produit
        if(!isset($_SESSION['idprosimplefunction']) || empty($_SESSION['idprosimplefunction'])){
        header("Location: ../selectionpro.php?error=error.");
        exit();
        }

// Fonction pour traiter les téléchargements d'images
function upload_image($file, $upload_dir) {
    $img_name = $file['name'];
    $tmp_name = $file['tmp_name'];
    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
    $img_ex_to_lc = strtolower($img_ex);
    $allowed_exs = ['jpg', 'jpeg', 'png'];

    // Vérifier l'extension de l'image
    if (in_array($img_ex_to_lc, $allowed_exs)) {
        $new_img_name = uniqid() . '.' . $img_ex_to_lc;
        $img_upload_path = $upload_dir . $new_img_name;
        move_uploaded_file($tmp_name, $img_upload_path);
        return $new_img_name;
    }
    return false;
}

// Vérification si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['title_add']) && !empty($_POST['title_add'])
    && isset($_POST['sku_add']) && !empty($_POST['sku_add'])
    && isset($_POST['quantity_add']) && !empty($_POST['quantity_add'])
    && isset($_POST['active_add'])) {

    $idpro = intval($_SESSION['idprosimplefunction']);
    $title = $_POST['title_add'];
    $quantity = intval($_POST['quantity_add']);
    $sku = $_POST['sku_add'];
    $active = $_POST['active_add'] ?? 0;

    // Vérification si la variante existe déjà
    $sqltest = "SELECT * FROM variant_options WHERE title = ?";
    $stmtset = $conn->prepare($sqltest);
    $stmtset->execute([$title]);

    if ($stmtset->rowCount() > 0) {
        header("Location: add_variant.php?error=La variante existe déjà.");
        exit();
    }

    // Insertion de la variante
    $sql = "INSERT INTO variant_options (title, product_id, quantity, sku, active) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$title, $idpro, $quantity, $sku, $active]);

    if ($result) {
        $id_variant = $conn->lastInsertId(); // Récupérer l'ID de la variante insérée
        $upload_dir = "../../../images/products/";
        
        // Liste des champs de fichiers à traiter
        $file_fields = ['image_files1_add', 'image_files2_add', 'image_files3_add', 'image_files4_add'];
        foreach ($file_fields as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $uploaded_img = upload_image($_FILES[$field], $upload_dir);
                
                if ($uploaded_img) {
                    // Insertion des images dans la table `gallery`
                    $sql_image = "INSERT INTO gallery (product_variant_id, image, placeholder) VALUES (?, ?, ?)";
                    $stmt_image = $conn->prepare($sql_image);
                    $stmt_image->execute([$id_variant, $uploaded_img, $title]);
                } else {
                    header("Location: add_variant.php?error=You can't upload files of this type");
                    exit();
                }
            }
        }

        // Redirection en cas de succès
        header("Location: add_variant.php?success=Variante ajoutée avec succès.");
        exit();
    } else {
        header("Location: add_variant.php?error=Erreur lors de l'insertion de la variante.");
        exit();
    }
} else {
    header("Location: add_variant.php?error=Formulaire non valide.");
    exit();
}
?>
