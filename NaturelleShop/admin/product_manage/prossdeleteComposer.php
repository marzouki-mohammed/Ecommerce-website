<?php
session_start();
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procomposer_delete_ids'])) {
    include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    // Récupérer les IDs des cases cochées
    $delete_ids = $_POST['procomposer_delete_ids'] ?? [];

    if (!empty($delete_ids)) {
        
        foreach( $delete_ids as $id){
            $sql="SELECT * FROM components WHERE id=?";
            $stm=$conn->prepare($sql);
            $stm->execute([intval($id)]);
            $result=$stm->fetch();
            if(!$result){
                header("Location: deleteComposer.php");
                exit;
            }
            $oldImagePath = "../../images/products/".$result['image']."";
            $newImagePath = "../../images/produits_data/".$result['image'].""; 
            if (file_exists($oldImagePath)) {
                rename($oldImagePath, $newImagePath);
            }
        }
        
        
        
            
            deleteFromTable($conn, 'product_Composer_coupons', 'components_id', $delete_ids);
            deleteFromTable($conn, 'product_composer', 'component_id', $delete_ids);
            deleteFromTable($conn, 'reviews', 'components_id', $delete_ids);
            deleteFromTable($conn, 'components', 'id', $delete_ids);

           

            // Redirection après suppression
            header("Location: deleteComposer.php");
            exit;
        
    } else {
        // Rediriger si aucun ID n'a été sélectionné
        header("Location: deleteComposer.php");
        exit;
    }
} else {
    header("Location: deleteComposer.php");
    exit;
}

// Fonction générique pour supprimer des enregistrements d'une table
function deleteFromTable($conn, $table, $column, $ids) {
    $placeholders = implode(",", $ids);
    $sql = "DELETE FROM $table WHERE $column IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
}


