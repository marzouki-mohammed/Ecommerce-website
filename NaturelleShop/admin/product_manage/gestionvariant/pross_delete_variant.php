<?php 
     session_start();
     include "../../../php/db_connect.php";



     // Vérification de la connexion à la base de données
     if (!isset($conn)) {
     echo "Database connection is not set.";
     exit;
     }

     if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['provariant_delete_ids']) && !empty($_POST['provariant_delete_ids'])
    ) {
        $ids_delete=$_POST['provariant_delete_ids'];
        foreach($ids_delete as $id){
            // Fetch existing image information from the database
            $sql_fetch = "SELECT  image FROM gallery WHERE product_variant_id  = ?";
            $stmt_fetch = $conn->prepare($sql_fetch);
            $stmt_fetch->execute([$id]);
            $images = $stmt_fetch->fetchAll(PDO::FETCH_ASSOC);

            foreach ($images as $image) {
                $image_name = $image['image'];
                $image_path = '../../../images/products/' . $image_name;

                // Delete the existing image file
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $sql = "DELETE FROM  gallery WHERE product_variant_id =?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
        }
        $ids=implode(",", $ids_delete);
        $sql = "DELETE FROM  variant_options  WHERE id IN ($ids)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        header("Location: delete_variant.php");
        exit();
   }else{
    header("Location: delete_variant.php?error=Formulaire non valide.");
    exit();
   }

    
?>