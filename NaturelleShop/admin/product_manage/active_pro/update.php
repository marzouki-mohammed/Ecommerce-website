<?php
session_start();
include "../../../php/db_connect.php"; // Assurez-vous que cette ligne pointe vers votre fichier de connexion à la base de données

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}
if(!isset($_SESSION['stock_id']) || empty($_SESSION['stock_id'])){
    header("Location: pross_Update_stock.php?error=Veuillez entrer une product valide.");
    exit;
}
// Vérification que les données sont envoyées via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et filtrer les données du formulaire
    $new_quantity_update = intval($_POST['quantity_update']);
    $product_id = intval($_SESSION['stock_id']);
    $sql_test_pro_comp="SELECT *FROM components WHERE id=?";
    $stm_test_pro_comp=$conn->prepare($sql_test_pro_comp);
    $stm_test_pro_comp->execute([$product_id]);
    $result_test_pro_comp=$stm_test_pro_comp->fetch();

    if(!$result_test_pro_comp){
         // Si les données ne sont pas valides, rediriger avec une erreur
        header("Location: pross_Update_stock.php?error=ereur dans la selection du produit");
        exit;
    }

    $quantity_update=$new_quantity_update-$result_test_pro_comp['stock_quantity'];
    // Vérifier si la quantité est bien envoyée
    if ($new_quantity_update >= 0 && isset($product_id)) {
        $activ_comp;
        
        if($new_quantity_update>0){
            $activ_comp=true;
        }else{
            $activ_comp=false;
        }
        // Préparer la requête SQL pour mettre à jour la quantité de stock
        $sql_update = "UPDATE components SET stock_quantity = ?, is_active=? ,  updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        // Exécuter la requête
        if ($stmt->execute([$new_quantity_update , $activ_comp , $product_id])) {
            $sql_product="SELECT *FROM product_composer WHERE component_id=?";
            $stm_product=$conn->prepare($sql_product);
            $stm_product->execute([$product_id]);
            $result_product=$stm_product->fetchAll(PDO::FETCH_ASSOC);
            if(!$result_product){
             // Gestion des erreurs
             header("Location: pross_Update_stock.php?error=Erreur lors de la recuperation des donnee.");
             exit;
            }

            foreach($result_product as $product){
                $sql_pro="SELECT *FROM products WHERE id=?";
                $stm_pro=$conn->prepare($sql_pro);
                $stm_pro->execute([$product['product_id']]);
                $result_pro=$stm_pro->fetch();
                if(!$result_pro){
                    header("Location: pross_Update_stock.php?error=Erreur lors de la recuperation des donnee.");
                    exit;
                }
                $quanty_pro_cpm=$product['quantity'];
                $sql_pro_var="SELECT *FROM variant_options WHERE product_id=?";
                $stm_pro_var=$conn->prepare($sql_pro_var);
                $stm_pro_var->execute([$result_pro['id']]);
                $result_pro_var=$stm_pro_var->fetchAll(PDO::FETCH_ASSOC);
                if(!$result_pro_var){
                    header("Location: pross_Update_stock.php?error=Erreur lors de la recuperation des donnee.");
                    exit;
                }
                $new_quanty_pro=0;  
                foreach($result_pro_var as $var){                    
                    $new_quanty_var=($quanty_pro_cpm*$quantity_update)+$var['quantity'];
                    $active_var;
                    if($new_quanty_var>0){
                        $active=true;
                    }else{
                        $active=false;
                        $new_quanty_var=0;
                    }
                    $updat_var="UPDATE variant_options
                                SET quantity = ?,
                                    active=?
                                WHERE id = ?";
                    $stm_updat_var=$conn->prepare($updat_var);
                    $stm_updat_var->execute([$new_quanty_var , $active , $var['id']]);
                    $new_quanty_pro+=$new_quanty_var;
                }
                $active_pro;
                $delet=true;
                if($new_quanty_pro>0){
                    $active_pro=true;
                    $delet=false;
                }else{
                    $active_pro=false;
                }
                $sql_updat_pro="UPDATE products 
                                SET stock_quantity  = ? ,
                                    active = ? ,
                                    updated_at = CURRENT_TIMESTAMP
                                WHERE id = ?";
                $stm_updat_pro=$conn->prepare( $sql_updat_pro);
                $stm_updat_pro->execute([ $new_quanty_pro , $active_pro , $result_pro['id'] ]);
                if($delet){
                        $sql_delete_warehouse="DELETE FROM warehouse_inventory WHERE product_id  = ?";
                        $stm_delete_warehouse=$conn->prepare($sql_delete_warehouse);
                        $stm_delete_warehouse->execute([$result_pro['id']]);
                    
                }else{
                        $sql_updat_warehouse="UPDATE warehouse_inventory  SET quantity=? WHERE product_id = ?";
                        $stm_updat_warehouse=$conn->prepare($sql_updat_warehouse);
                        $stm_updat_warehouse->execute([$new_quanty_pro , $result_pro['id']]);
                }           
            }
            $sql_test = "SELECT * FROM porso_statuses WHERE component_id = ? AND (quantity_obli_comp > 0) ";
            $stm_test=$conn->prepare($sql_test);
            $stm_test->execute([$product_id]);
            $result_test=$stm_test->fetchAll(PDO::FETCH_ASSOC);
            if($result_test && $activ_comp==true){
                // Redirection après succès
                header("Location: gestion_orders.php");
                exit;
            }                    
            // Redirection après succès
            header("Location: pross_Update_stock.php?success=Quantité mise à jour avec succès");
            exit;
        }else{
             // Gestion des erreurs
             header("Location: pross_Update_stock.php?error=Erreur lors de la mise à jour du stock.");
             exit;
        }
    }else{
          // Si les données ne sont pas valides, rediriger avec une erreur
          header("Location: pross_Update_stock.php?error=Veuillez entrer une quantité valide.");
          exit;
    }
}else{
    // Si la méthode n'est pas POST, rediriger vers la page de mise à jour
    header("Location: pross_Update_stock.php");
    exit;
}

?>
