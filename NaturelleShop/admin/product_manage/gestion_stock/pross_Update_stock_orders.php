<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    header("Location: add_stock.php?error=Erreur de connexion à la base de données.");
    exit();
}

// Vérification de la session pour l'ID du produit
if (!isset($_SESSION['idprosimplefunction']) || empty($_SESSION['idprosimplefunction'])) {
    header("Location: add_stock.php?error=Erreur lors de la sélection du produit.");
    exit();
}

$idpro = intval($_SESSION['idprosimplefunction']);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['orders_ids']) && !empty($_POST['orders_ids'])) {
    $ids_orders=$_POST['orders_ids'];
    foreach($ids_orders as $id ){
        $sql_orders_item="SELECT *FROM order_items WHERE order_id=? AND component_id IS NULL AND product_id=?";
        $stm_orders_item=$conn->prepare( $sql_orders_item);
        $stm_orders_item->execute([intval($id) , $idpro]);
        $result_orders_item=$stm_orders_item->fetchAll(PDO::FETCH_ASSOC);
        if($result_orders_item){
            foreach($result_orders_item as $item){
                $test_var_orders_item="SELECT * FROM variant_options WHERE id = ? AND product_id =? AND active=true AND quantity>0";
                $stm_test_var_orders_item=$conn->prepare($test_var_orders_item);
                $stm_test_var_orders_item->execute([$item['product_variant_id'] , $idpro]);
                $result_test_var_orders_item=$stm_test_var_orders_item->fetchAll(PDO::FETCH_ASSOC);
                if($result_test_var_orders_item){
                    //gestion du stock
                    foreach($result_test_var_orders_item as $var){
                        $sql_quanty = "SELECT * FROM porso_statuses 
                                        WHERE orders_id = ? 
                                        AND order_items_id = ? 
                                        AND component_id is null 
                                        AND product_id = ? 
                                        AND product_variant_id = ? 
                                        AND quantity_obli_var > 0";
                        $stm_quanty=$conn->prepare( $sql_quanty);
                        $stm_quanty->execute([$id , $item['id'] , $idpro , $var['id'] ]);
                        $result_quanty=$stm_quanty->fetch();
                        if($result_quanty){
                            $sql_pro="SELECT *FROM products WHERE id=?";
                            $stm_pro=$conn->prepare($sql_pro);
                            $stm_pro->execute([$idpro]);
                            $result_pro=$stm_pro->fetch();
                            

                            $qunty=$result_quanty['quantity_obli_var'] ;
                            $quanty_var=$var['quantity'];
                            $quanty_pro=$result_pro['stock_quantity'];
    
                            $stockvar=0;
                            $active_var=true;
                            $stock_oblig_var=0;
                            $stock_pro=0;
                            $active_pro=true;
                            $delete=true;
                            if( $quanty_var < $qunty){
    
                                $active_var=false;
                                $stock_oblig_var=$qunty-$quanty_var;
                                $stock_pro=$quanty_pro-$quanty_var;
    
                                if($stock_pro>0){
                                    $delete=false;
                                }else{
                                    $active_pro=false;
                                }
    
                            }elseif($quanty_var > $qunty){
    
                                    $stockvar=$quanty_var-$qunty;
                                    $stock_pro=$quanty_pro-$qunty;
    
                                    $delete=false;
                            }else{
                                    $active_var=false;
                                    $stock_pro=$quanty_pro-$qunty;
                                    if($stock_pro>0){
                                        $delete=false;
                                    }else{
                                        $active_pro=false;
                                    }
                            }
                            $sql_update_var="UPDATE variant_options SET active = ?, quantity = ? WHERE id = ?";
                            $stm_update_var=$conn->prepare($sql_update_var);
                            $stm_update_var->execute([$active_var,$stockvar,$var['id']]);
                            if($delete){
                                $sql_delete_warehouse="DELETE FROM warehouse_inventory WHERE product_id  = ?";
                                $stm_delete_warehouse=$conn->prepare($sql_delete_warehouse);
                                $stm_delete_warehouse->execute([$idpro]);
            
                            }else{
                                $sql_updat_warehouse="UPDATE warehouse_inventory  SET quantity=? WHERE product_id = ?";
                                $stm_updat_warehouse=$conn->prepare($sql_updat_warehouse);
                                $stm_updat_warehouse->execute([$stock_pro , $idpro]);
                            }
                            $sql_update_pro="UPDATE products  SET active = ?, stock_quantity = ? WHERE id = ?";
                            $stm_update_pro=$conn->prepare($sql_update_pro);
                            $stm_update_pro->execute([$active_pro,$stock_pro,$idpro]);
                            
    
                            
                            
    
                            $sql_update_porso_status="UPDATE porso_statuses   SET quantity_obli_var  = ? WHERE id = ?";
                            $stm_update_porso_status=$conn->prepare($sql_update_porso_status);
                            $stm_update_porso_status->execute([ $stock_oblig_var ,$result_quanty['id']]);
                            
    
    
                        }

                    }
                    
                    
                    

                }
            }
        }

        //statuses du order
            // Récupérer le total des enregistrements
            $sql_porss_total = "SELECT COUNT(*) AS total FROM porso_statuses WHERE orders_id = ?";
            $stm_porss_total = $conn->prepare($sql_porss_total);
            $stm_porss_total->execute([$id]);
            $result_porss_total = $stm_porss_total->fetch();
            if ($result_porss_total) {
                $total = $result_porss_total['total'];
                $Pending = 0;
                $Completed = 0;
                 // Requête pour compter les 'Completed'
                 $sql_porss_Completed = "SELECT COUNT(*) AS Completed
                                        FROM porso_statuses 
                                        WHERE orders_id = ?
                                        AND quantity_obli_comp = 0 
                                        AND quantity_obli_var = 0";
                $stm_porss_Completed = $conn->prepare($sql_porss_Completed);
                $stm_porss_Completed->execute([$id]);
                $result_porss_Completed = $stm_porss_Completed->fetch();
                if ($result_porss_Completed) {
                    $Completed = $result_porss_Completed['Completed'];
                }
                // Requête pour compter les 'Pending'
                $sql_porss_Pending = "SELECT COUNT(*) AS Pending
                                    FROM porso_statuses 
                                    WHERE orders_id = ?
                                    AND (quantity_obli_comp > 0 OR quantity_obli_var > 0)";
                $stm_porss_Pending = $conn->prepare($sql_porss_Pending);
                $stm_porss_Pending->execute([$id]);
                $result_porss_Pending = $stm_porss_Pending->fetch();
                if ($result_porss_Pending) {
                    $Pending = $result_porss_Pending['Pending'];
                }
                // Calcul des pourcentages
                if ($total > 0) {
                    $Pending_percentage = ($Pending / $total) * 100;
                    $Completed_percentage = ($Completed / $total) * 100;

                } 
                // En fonction des pourcentages, vous pouvez attribuer un statut à l'ordre
                if ($Pending_percentage == 100) {
                    // Statut : "Pending"
                    $status_id = 1; // Remplacez par l'ID correspondant à "Pending" dans votre table `statuses`
                    $description = "Commande en attente";
                } elseif ($Completed_percentage == 100) {
                    // Statut : "Completed"
                    $status_id = 3; // Remplacez par l'ID correspondant à "Completed"
                    $description = "Commande terminée tout les produits existe";
                } else {
                    // Statut : "En cours" si ni 100% Pending ni 100% Completed
                    $status_id = 2; // Remplacez par l'ID correspondant à "In Progress"
                    $description = "Commande en cours";
                }
                // Insertion dans la table `order_statuses`
                $sql_updat_order_status = "UPDATE order_statuses    SET status_id   = ? , description=?  WHERE orders_id  = ?";
                $stmt_update = $conn->prepare($sql_updat_order_status);
                $stmt_update->execute([$status_id, $description , $id]);






            }
        //fin
    }
    
      
    header("Location: gestion_orders.php");
    exit(); 
}else{
    header("Location: add_stock.php?error=Erreur lors de la sélection du orders.");
    exit(); 
}
?>