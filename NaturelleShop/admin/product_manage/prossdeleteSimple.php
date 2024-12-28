<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pro_delete_ids'])) {
    include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    // Récupérer les IDs des cases cochées
    $delete_ids = $_POST['pro_delete_ids'] ?? [];

    if (!empty($delete_ids)) {
        
            // Supprimer les entrées des différentes tables
            
            deleteFromTable($conn, 'product_categories', 'product_id', $delete_ids);
            deleteFromTable($conn, 'warehouse_inventory', 'product_id', $delete_ids);
            deleteFromTable($conn, 'product_coupons', 'product_id', $delete_ids);
            deleteFromTable($conn, 'reviews', 'product_id', $delete_ids);
            deleteProductComponents($conn, $delete_ids);
            deleteProductVariants($conn, $delete_ids);


            deleteFromTable($conn, 'products', 'id', $delete_ids);

            // Redirection après suppression
            header("Location: deletesimple.php");
            exit;
        
    } else {
        // Rediriger si aucun ID n'a été sélectionné
        header("Location: deletesimple.php");
        exit;
    }
} else {
    header("Location: deletesimple.php");
    exit;
}

// Fonction générique pour supprimer des enregistrements d'une table
function deleteFromTable($conn, $table, $column, $ids) {
    $placeholders = implode(",", $ids);
    $sql = "DELETE FROM $table WHERE $column IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
}

// Fonction pour supprimer les variantes de produit et les images associées
function deleteProductVariants($conn, $product_ids) {
    foreach ($product_ids as $id) {
        $sql = "SELECT id FROM variant_options WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $variant_options = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($variant_options) {
            foreach ($variant_options as $var) {
                $sql_img="SELECT * FROM gallery  WHERE product_variant_id =?";
                $stm_img=$conn->prepare($sql_img);
                $stm_img->execute([intval($var['id'])]);
                $result=$stm_img->fetchAll(PDO::FETCH_ASSOC);
                if($result){
                    foreach($result as $img){
                        $oldImagePath = "../../images/products/".$img['image']."";
                        $newImagePath = "../../images/produits_data/".$img['image'].""; 
                        if (file_exists($oldImagePath)) {
                            rename($oldImagePath, $newImagePath);
                        }
                    }
                }              
                deleteFromTableById($conn, 'gallery', 'product_variant_id', $var['id']);
            }
            deleteFromTableById($conn, 'variant_options', 'product_id',$id);

        }
    }
}

// Fonction pour supprimer les composants de produit et les liaisons associées
function deleteProductComponents($conn, $product_ids) {
    foreach ($product_ids as $id) {
        $sql = "SELECT component_id FROM product_composer WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $components = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($components) {
            foreach ($components as $comp) {
                $sql="SELECT * FROM components WHERE id=?";
                $stm=$conn->prepare($sql);
                $stm->execute([intval($comp['component_id'])]);
                $result=$stm->fetch();
                $oldImagePath = "../../images/products/".$result['image']."";
                $newImagePath = "../../images/produits_data/".$result['image'].""; 
                if (file_exists($oldImagePath)) {
                    rename($oldImagePath, $newImagePath);
                }
                deleteFromTableById($conn, 'components', 'id', $comp['component_id']);
            }
            deleteFromTableById($conn, 'product_composer', 'product_id', $id);

        }
    }
}

// Fonction pour supprimer une entrée spécifique dans une table par ID
function deleteFromTableById($conn, $table, $column, $id) {
    $sql = "DELETE FROM $table WHERE $column = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
}
