<?php 
  session_start();

  include "../../php/db_connect.php";
 
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }
    if(!isset($_SESSION['id_cat_delete']) || empty($_SESSION['id_cat_delete'])){
        header("Location: delete.php?error=Category ID is not set.");
        exit;
    }
    $idcatedelete = $_SESSION['id_cat_delete'] ;
         // Récupérer les informations de la catégorie à afficher
         $sql_cat = "SELECT * FROM categories WHERE id = :id_cat_delet";
         $stmt_cat = $conn->prepare($sql_cat);
         $stmt_cat->execute(['id_cat_delet' => $idcatedelete]);
         $categorie = $stmt_cat->fetch(PDO::FETCH_ASSOC);
 
         if (!$categorie) {
             header("Location: delete.php?error=Category not found.");
             exit;
         }


        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Get the IDs of the selected checkboxes
            $delete_ids = $_POST['delete_ids'] ?? [];
            if (!empty($delete_ids)) {
                // Convert the IDs to a comma-separated string
                $ids = implode(",", array_map('intval', $delete_ids));
                         
                // SQL query to delete the selected rows              
                $sql = "
                        DELETE FROM product_categories  WHERE product_id IN ($ids) and category_id=?
                    ";
                $stmt = $conn->prepare($sql);
                
                if($stmt->execute([$idcatedelete])){

                    // Récupérer les produits de la catégorie
                    $sql = "SELECT product_id FROM product_categories WHERE category_id = :id_cat_delet";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['id_cat_delet' => $idcatedelete]);
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($products)) {
                        header("Location: manage_pro_attribit.php");
                        exit;
                    }else{
                        header("Location: manage_categorie.php");
                        exit;
                    }
                } else {
                    header("Location: manage_pro.php");
                    exit;
                }



            }else{
                // Récupérer les produits de la catégorie
                $sql = "SELECT product_id FROM product_categories WHERE category_id = :id_cat_delet";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['id_cat_delet' => $idcatedelete]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($products)) {
                    header("Location: manage_pro_attribit.php");
                    exit;
                }else{
                    header("Location: manage_categorie.php");
                    exit;
                }
            }


        } else{
            header("Location: delet_pro.php");
            exit;
        }
 
                 
                     
                  
     
 
?>