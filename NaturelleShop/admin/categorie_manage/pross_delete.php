<?php 
 session_start();
 include "../../php/db_connect.php";
 
 if (!isset($conn)) {
     echo "Database connection is not set.";
     exit;
 }
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name_filed_delete']) ) {
     $id =intval($_POST['name_filed_delete']);
    
     if (!empty($id)) {
         $_SESSION['id_cat_delete'] = $id;
         $sql_test_pro="SELECT *FROM product_categories  WHERE category_id =?";
         $stm_test_pro=$conn->prepare($sql_test_pro);
         $stm_test_pro->execute([$id]);
         $result_test_pro=$stm_test_pro->fetchAll(PDO::FETCH_ASSOC);
         if($result_test_pro){
            header("Location: delet_pro.php");
            exit;
         }else{
            $sql_test_cat="SELECT *FROM categories WHERE parent_id  =?";
            $stm_test_cat=$conn->prepare($sql_test_cat);
            $stm_test_cat->execute([$id]);
            $result_test_cat=$stm_test_cat->fetchAll(PDO::FETCH_ASSOC);
            if($result_test_cat){
                header("Location: manage_categorie.php");
                exit;
            }else{
                // Suppression de la galerie associée et de la catégorie
                $sql_delete = "
                            DELETE FROM gallery WHERE categorie_id = :id_cat_delete;
                            DELETE FROM categories WHERE id = :id_cat_delete;
                            ";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->execute(['id_cat_delete' => $id]);

                header("Location: delete.php");
                exit;
            }
         }    
        
     } else {
         header("Location: delete.php?error=Veuillez sélectionner une catégorie.");
         exit;
     }
 }
?>