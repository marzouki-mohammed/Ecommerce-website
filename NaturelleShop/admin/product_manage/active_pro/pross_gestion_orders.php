<?php 
 session_start();
 include "../../../php/db_connect.php";
 
 // Vérification de la connexion à la base de données
 if (!isset($conn)) {
     header("Location: add_stock.php?error=Erreur de connexion à la base de données.");
     exit();
 }
 
 if(!isset($_SESSION['stock_id']) || empty($_SESSION['stock_id'])){
     header("Location: pross_Update_stock.php?error=Veuillez entrer une product valide 1.");
     exit;
 }


 $id_product=intval($_SESSION['stock_id']);



 // Vérification que les données sont envoyées via POST
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orders_ids_comp']) && !empty($_POST['orders_ids_comp'])){
    $orders_ids=$_POST['orders_ids_comp'];
    foreach($orders_ids as $orders){
        $sql_orders_item="SELECT *FROM order_items WHERE order_id=? AND component_id=? AND product_id IS NULL AND product_variant_id IS NULL";
        $stm_orders_item=$conn->prepare($sql_orders_item);
        $stm_orders_item->execute([intval($orders) , $id_product ]);
        $result_orders_item=$stm_orders_item->fetchAll(PDO::FETCH_ASSOC); 
        if($result_orders_item){
            foreach($result_orders_item as $orders_item){
                $sql_porso_statuses="SELECT *FROM porso_statuses 
                                      WHERE orders_id=? 
                                      and  order_items_id=?
                                      and  component_id =?
                                      and  quantity_obli_comp>0
                                      and  product_id is null
                                      and  product_variant_id is null
                                      ";
                $stm_porso_statuses=$conn->prepare($sql_porso_statuses);
                $stm_porso_statuses->execute([
                                               intval($orders),
                                               $orders_item['id'],
                                               $id_product
                                             ]);
                $result_porso_statuses=$stm_porso_statuses->fetch();
                
                
                
                if($result_porso_statuses){ 
                    $quanty_Remaining_comp=$result_porso_statuses['quantity_obli_comp'];

                    $sql_product="SELECT *FROM components WHERE id=?";
                    $stm_product=$conn->prepare($sql_product);
                    $stm_product->execute([$id_product]);
                    $result_product=$stm_product->fetch();
                   
                    if(!$result_product){
                       header("Location: pross_Update_stock.php?error=Veuillez entrer une product valide.2");
                       exit;
                    }
                   
                    $qunaty_comp=$result_product['stock_quantity'];



                    $comp_active=true;
                    $new_quanty_comp=0;
                    $new_quanty_Remaining_comp=0;
                    $quanty_delete=0;

                    if($qunaty_comp > $quanty_Remaining_comp){
                        $new_quanty_comp=$qunaty_comp-$quanty_Remaining_comp;
                        $quanty_delete=$quanty_Remaining_comp;
                    }elseif($qunaty_comp < $quanty_Remaining_comp){
                        $comp_active=false;
                        $new_quanty_Remaining_comp=$quanty_Remaining_comp-$qunaty_comp;
                        $quanty_delete=$qunaty_comp;
                    }else{
                        $comp_active=false;
                        $quanty_delete=$qunaty_comp;
                    }


                    
                    

                    $sql_product_composer="SELECT *FROM product_composer  WHERE component_id=?";
                    $stm_product_composer=$conn->prepare($sql_product_composer);
                    $stm_product_composer->execute([$id_product]);
                    $result_product_composer=$stm_product_composer->fetchAll(PDO::FETCH_ASSOC);
                    if($result_product_composer){
                         // Update components table
                         $sql_update_comp = "UPDATE components 
                         SET stock_quantity = ?, is_active = ? 
                         WHERE id = ?";
                        $stm_updat_comp = $conn->prepare($sql_update_comp);
                        $stm_updat_comp->execute([$new_quanty_comp, $comp_active, $id_product]);

                        // Update porso_statuses table
                        $sql_update_porso_statuses = "UPDATE porso_statuses 
                                SET quantity_obli_comp = ? 
                                WHERE id = ?";
                        $stm_update_porso_statuses = $conn->prepare($sql_update_porso_statuses);
                        $stm_update_porso_statuses->execute([$new_quanty_Remaining_comp, $result_porso_statuses['id']]);


                        foreach($result_product_composer as $product){
                            $sql_porso_statuses_comp="SELECT *FROM orders_items_compo  WHERE orders_items_id=? and product_id=?";
                            $stm_porso_statuses_comp=$conn->prepare($sql_porso_statuses_comp);
                            $stm_porso_statuses_comp->execute([$orders_item['id'] , $product['product_id']]);
                            $resulte_porso_statuses_comp=$stm_porso_statuses_comp->fetch();
                            if(!$resulte_porso_statuses_comp){
                                header("Location: pross_Update_stock.php?error=Veuillez entrer une product valide.3");
                                exit;
                            }

                            $quanty_var_Remaining=$quanty_delete*$product['quantity'];

                                 $sql_pro="SELECT *FROM products WHERE id=?";
                                 $stm_pro=$conn->prepare($sql_pro);
                                 $stm_pro->execute([$product['product_id']]);
                                 $result_pro=$stm_pro->fetch();
                                 if(!$result_pro){
                                     header("Location: pross_Update_stock.php?error=ereur dans la recuperation des donnee1");
                                     exit;
                                 }
                                 $quanty_pro=$result_pro['stock_quantity'];

                                 $sql_var="SELECT *FROM variant_options WHERE  id=?";
                                 $stm_var=$conn->prepare($sql_var);
                                 $stm_var->execute([$resulte_porso_statuses_comp['product_variant_id']]);
                                 $result_var=$stm_var->fetch();
                                 if(!$result_var){
                                     header("Location: pross_Update_stock.php?error=ereur dans la recuperation des donnee2");
                                     exit;
                                 }
                                 $quanty_var=$result_var['quantity'];
                            

                            $active_var=true;
                            $active_pro=true;
                            $delet=true;
 
                            $new_quanty_pro=0;
                            $new_quanty_var=0;
                            $new_quanty_Remaining_var=0;
 
                            if($quanty_var > $quanty_var_Remaining){
 
                                     $new_quanty_var=$quanty_var-$quanty_var_Remaining;
                                     $new_quanty_pro=$quanty_pro-$quanty_var_Remaining;
                                     $delet=false;
 
                            }elseif($quanty_var < $quanty_var_Remaining){
                                     $active_var=false;
                                     $new_quanty_Remaining_var=$quanty_var_Remaining-$quanty_var;
                                     $new_quanty_pro=$quanty_pro-$quanty_var;
                                     if($new_quanty_pro > 0){
                                         $delet=false;
                                     }else{
                                         $active_pro=false;
                                     }
                            }else{
                                     $active_var=false;
                                     $new_quanty_pro=$quanty_pro-$quanty_var_Remaining;
                                     if($new_quanty_pro > 0){
                                         $delet=false;
                                     }else{
                                         $active_pro=false;
                                     }
                            }
                                 // Updating variant options
                                $sql_update_var = "UPDATE variant_options SET active = ?, quantity = ? WHERE id = ?";
                                $stm_update_var = $conn->prepare($sql_update_var);
                                $stm_update_var->execute([$active_var, $new_quanty_var, $result_var['id']]);

                                // Conditional check for deletion or update in warehouse_inventory
                                if ($delet) {
                                    // Delete from warehouse_inventory
                                    $sql_delete_warehouse = "DELETE FROM warehouse_inventory WHERE product_id = ?";
                                    $stm_delete_warehouse = $conn->prepare($sql_delete_warehouse);
                                    $stm_delete_warehouse->execute([$result_pro['id']]);
                                } else {
                                    // Update warehouse_inventory
                                    $sql_update_warehouse = "UPDATE warehouse_inventory SET quantity = ? WHERE product_id = ?";
                                    $stm_update_warehouse = $conn->prepare($sql_update_warehouse);
                                    $stm_update_warehouse->execute([$new_quanty_pro, $result_pro['id']]);
                                }

                                // Updating the products table
                                $sql_update_pro = "UPDATE products SET active = ?, stock_quantity = ? WHERE id = ?";
                                $stm_update_pro = $conn->prepare($sql_update_pro);
                                $stm_update_pro->execute([$active_pro, $new_quanty_pro, $result_pro['id']]);


                                // Updating the porso_statuses_comp table
                                $sql_update_porso_statuses_comp = "UPDATE porso_statuses_comp 
                                                                SET quantity_obli_var = ? 
                                                                WHERE porso_statuses_id = ? 
                                                                AND product_id = ? 
                                                                AND product_variant_id = ?";
                                $stm_update_porso_statuses_comp = $conn->prepare($sql_update_porso_statuses_comp);
                                $stm_update_porso_statuses_comp->execute([
                                    $new_quanty_Remaining_var, 
                                    $result_porso_statuses['id'], 
                                    $result_pro['id'], 
                                    $result_var['id']
                                ]);                              

                        }


                        


                   }







                   
                 



                }
                



            }
        }





        
        //statuses du order
           // Récupérer le total des enregistrements
           $sql_porss_total = "SELECT COUNT(*) AS total FROM porso_statuses WHERE orders_id = ?";
           $stm_porss_total = $conn->prepare($sql_porss_total);
           $stm_porss_total->execute([intval($orders)]);
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
                                        AND quantity_obli_var =0 ";
                $stm_porss_Completed = $conn->prepare($sql_porss_Completed);
                $stm_porss_Completed->execute([intval($orders)]);
                $result_porss_Completed = $stm_porss_Completed->fetch();
                if ($result_porss_Completed) {
                    $Completed = $result_porss_Completed['Completed'];
                }

                  // Requête pour compter les 'Pending'
                $sql_porss_Pending = "SELECT COUNT(*) AS Pending
                                        FROM porso_statuses 
                                        WHERE orders_id = ?
                                        AND (quantity_obli_comp > 0 || quantity_obli_var > 0 )";
                $stm_porss_Pending = $conn->prepare($sql_porss_Pending);
                $stm_porss_Pending->execute([intval($orders)]);
                $result_porss_Pending = $stm_porss_Pending->fetch();

                if ($result_porss_Pending) {
                $Pending = $result_porss_Pending['Pending'];
                }

                 // Calcul des pourcentages
                if ($total > 0) {
                    $Pending_percentage = ($Pending / $total) * 100;
                    $Completed_percentage = ($Completed / $total) * 100;

                } 

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
                $sql_insert_order_status = "UPDATE order_statuses 
                                            SET status_id = ?, description = ? 
                                            WHERE orders_id = ?";

                $stmt_insert = $conn->prepare($sql_insert_order_status);
                $stmt_insert->execute([$status_id, $description, intval($orders)]);







           }


            
        //fin
        



    }
    

    header("Location: Update_stock.php");
    exit;
    
 }else{
    header("Location: gestion_orders.php");
    exit;

 }









?>

