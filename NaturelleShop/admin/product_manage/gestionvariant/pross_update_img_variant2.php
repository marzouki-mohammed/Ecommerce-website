<?php
        session_start();
        include "../../../php/db_connect.php";



        // Vérification de la connexion à la base de données
        if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
        }

        // Vérification de la session pour les informations du produit
    if(!isset($_SESSION['idvar_updateimage']) || empty($_SESSION['idvar_updateimage'])){
        header("Location: update_img_variant.php?error=error.");
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_variant = intval($_SESSION['idvar_updateimage']);
    

    // Vérification si la variante existe déjà
    $sqltest = "SELECT title FROM variant_options WHERE id = ?";
    $stmtset = $conn->prepare($sqltest);
    $stmtset->execute([$id_variant]);
    $result=$stmtset->fetch();
   

    if ($result) {
        $title=$result['title'];
        $upload_dir = "../../../images/products/";
        
        // Liste des champs de fichiers à traiter
        $file_fields = ['image_files1_add1', 'image_files2_add2', 'image_files3_add3', 'image_files4_add4'];
        foreach ($file_fields as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $uploaded_img = upload_image($_FILES[$field], $upload_dir);
                
                if ($uploaded_img) {
                    // Insertion des images dans la table `gallery`
                    $sql_image = "INSERT INTO gallery (product_variant_id, image, placeholder) VALUES (?, ?, ?)";
                    $stmt_image = $conn->prepare($sql_image);
                    $stmt_image->execute([$id_variant, $uploaded_img, $title]);
                } else {
                    header("Location: update_img_variant2.php?error=You can't upload files of this type");
                    exit();
                }
            }
        }

        // Redirection en cas de succès
        header("Location: update_img_variant2.php?success=Variante ajoutée avec succès.");
        exit();
    } else {
        header("Location: update_img_variant2.php?error=Erreur lors de l'insertion de la variante.");
        exit();
    }
} else {
    header("Location: update_img_variant2.php?error=Formulaire non valide.");
    exit();
}
?>
