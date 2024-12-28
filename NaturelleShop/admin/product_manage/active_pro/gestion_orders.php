<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    header("Location: add_stock.php?error=Erreur de connexion à la base de données.");
    exit();
}

if(!isset($_SESSION['stock_id']) || empty($_SESSION['stock_id'])){
    header("Location: pross_Update_stock.php?error=Veuillez entrer une product valide.");
    exit;
}

$idpro = intval($_SESSION['stock_id']);

$sql_orders = "
  SELECT DISTINCT o.*
  FROM orders o
  JOIN order_items oi ON o.id = oi.order_id
  JOIN porso_statuses ps ON o.id = ps.orders_id AND oi.id = ps.order_items_id
  WHERE oi.component_id = :product_comp_id
  AND ps.quantity_obli_comp > 0
";

        
$stm_orders = $conn->prepare($sql_orders);
$stm_orders->execute(['product_comp_id' => $idpro]);
$result_orders = $stm_orders->fetchAll(PDO::FETCH_ASSOC);
if(!$result_orders){
    header("Location: pross_Update_stock.php");
    exit();
}





function convertirDateEnEcriture($date) {
    $mois = array(
        1 => 'janvier', 
        2 => 'février', 
        3 => 'mars', 
        4 => 'avril', 
        5 => 'mai', 
        6 => 'juin', 
        7 => 'juillet', 
        8 => 'août', 
        9 => 'septembre', 
        10 => 'octobre', 
        11 => 'novembre', 
        12 => 'décembre'
    );
    
    $dateObj = new DateTime($date);
    $jour = $dateObj->format('j');
    $moisNum = (int)$dateObj->format('n');
    $annee = $dateObj->format('Y');
  
    $jourEcrit = ($jour == 1) ? '1er' : $jour;
  
    $dateEcriture = $jourEcrit . ' ' . $mois[$moisNum] . ' ' . $annee;
  
    return $dateEcriture;
   }
?>
<!DOCTYPE html>
<html lang="fr">
<head> 
    <style>
        td img {
            width: 36px;
            height: 36px;
            margin-right: .5rem;
            border-radius: 50%;
        
            vertical-align: middle;
        }
        .status.pending {
            background-color: #ffffff;
            color: #1e2022;
        }
        .status.processing {
            background-color: #ebc474;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Delete</title>
    <link rel="stylesheet" href="../assets/css/styleselecte.css">
</head>
<body>
    <main class="table">
        <section class="table__header">
            <div class="input-group">
                <input type="search" placeholder="Search...">
                <img src="../../images/search.png" alt="">
            </div>
            <div class="export__file">
                <label for="export-file" class="export__file-btn" title="Export File"></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Export As &nbsp; &#10140;</label>
                    <label for="export-file" id="toJSON">JSON <img src="../../images/json.png" alt=""></label>
                    <label for="export-file" id="toCSV">CSV <img src="../../images/csv.png" alt=""></label>
                    <label for="export-file" id="toEXCEL">EXCEL <img src="../../images/excel.png" alt=""></label>
                </div>
            </div>
        </section>
        
        <section class="table__body">
            <form method="post" action="pross_gestion_orders.php">
                <table class="tble1">
                    <thead>
                        <tr>

                            <th> Id Orders <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Components <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Components <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Buy.Components <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Remaining.Components <span class="icon-arrow">&UpArrow;</span></th>
                            <th> the order date <span class="icon-arrow">&UpArrow;</span></th>

                            <th> Option Product <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Product <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Buy <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Remaining <span class="icon-arrow">&UpArrow;</span></th>
                             
                            <th> Status</th>
                            <th> Select</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       
                                $table_orders=[];
                                $total=1;

                                foreach ($result_orders as $row) {
                                    // Requête SQL pour obtenir les données
                                    $sql = "SELECT *  FROM order_statuses  WHERE orders_id=?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$row['id']]);
                                    $rows = $stmt->fetch();

                                    if(!$rows ){
                                        header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné1");
                                        exit();
                                    }

                                    $sql_status="SELECT *FROM statuses WHERE id=?";
                                    $stm_status=$conn->prepare($sql_status);
                                    $stm_status->execute([$rows['status_id']]);
                                    $result_satus=$stm_status->fetch();

                                    if(!$result_satus){
                                        header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné2");
                                        exit();
                                    }

                                    if($result_satus['status_name']=='Pending' || $result_satus['status_name']=='Processing'){
                                        $table_orders[]=$row;
                                        $total++;                                        
                                    }
                                        
                                }


                                $sql_components="SELECT *FROM components WHERE id=?";
                                $stm_components=$conn->prepare($sql_components);
                                $stm_components->execute([$idpro]);
                                $result_components=$stm_components->fetch();
                                if(!$result_components){
                                    header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné3");
                                    exit();
                                }




                                foreach($table_orders as $id){
                                    $sql_order_items="SELECT *FROM order_items WHERE order_id=? and component_id=? and product_id is null and product_variant_id is null ";
                                    $stm_order_items=$conn->prepare($sql_order_items);
                                    $stm_order_items->execute([$id['id'] , $idpro]);
                                    $result_order_items=$stm_order_items->fetchAll(PDO::FETCH_ASSOC);
                                    if(!$result_order_items){
                                        header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné4");
                                        exit();
                                    }
                                    $quenty_buy=0;
                                    $quanty_Remaining=0;

                                    $table_buy_pro=[];
                                    $table_buy_pro_valid=[];
                                    $table_Remaining_pro=[];
                                    $table_Remaining_pro_valid=[];
                                    
                                    

                                    
                                    foreach($result_order_items as $order_items){

                                        $quenty_buy+=$order_items['quantity'];
                                        

                                        $sql_orders_items_compo="SELECT *FROM orders_items_compo  WHERE orders_items_id=?";
                                        $stm_orders_items_compo=$conn->prepare($sql_orders_items_compo);
                                        $stm_orders_items_compo->execute([$order_items['id']]);
                                        $result_orders_items_compo=$stm_orders_items_compo->fetchAll(PDO::FETCH_ASSOC);

                                        if(!$result_orders_items_compo){
                                            header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné5");
                                            exit();
                                        }



                                        foreach($result_orders_items_compo as $orders_items_compo){

                                            $table_buy_pro[]=[
                                                                'id_pro'=> $orders_items_compo['product_id'],
                                                                'id_var'=> $orders_items_compo['product_variant_id'],
                                                                'quanty'=> $orders_items_compo['quantity']
                                            ];
                                        }

                                        

                                        $sql_porso_statuses="SELECT *
                                                                FROM porso_statuses 
                                                                WHERE orders_id=? 
                                                                and order_items_id=? 
                                                                and component_id=? 
                                                                and quantity_obli_comp>0
                                                                and product_id is null
                                                                and product_variant_id is null"; 
                                                                
                                        $stm_porso_statuses=$conn->prepare($sql_porso_statuses);
                                        $stm_porso_statuses->execute([
                                                                           $id['id'],
                                                                           $order_items['id'],
                                                                           $idpro
                                                                        ]);
                                        $resulet_porso_statuses=$stm_porso_statuses->fetch();
                                        if($resulet_porso_statuses){
                                                $quanty_Remaining+=$resulet_porso_statuses['quantity_obli_comp'];

                                                $sql_porso_statuses_comp="SELECT *FROM porso_statuses_comp WHERE porso_statuses_id=?";
                                                $stm_porso_statuses_comp=$conn->prepare($sql_porso_statuses_comp);
                                                $stm_porso_statuses_comp->execute([$resulet_porso_statuses['id']]);
                                                $result_porso_statuses_comp=$stm_porso_statuses_comp->fetchAll(PDO::FETCH_ASSOC);
                                                if($result_porso_statuses_comp){
                                                    foreach($result_porso_statuses_comp as $Remaining){
                                                        $table_Remaining_pro[]=[
                                                                                'id_pro'=>$Remaining['product_id'],
                                                                                'id_var'=>$Remaining['product_variant_id'],
                                                                                'quanty'=>$Remaining['quantity_obli_var']
                                                        ];
                                                    }
                                                }
                                                
                                        }

                                            



                                           



                                        
                                        
                                        
                                    }








                                    foreach ($table_buy_pro as $pro_buy) {
                                        if (empty($table_buy_pro_valid)) {
                                            $table_buy_pro_valid[] = $pro_buy;

                                        } else {
                                            $exist = false;
                                            foreach ($table_buy_pro_valid as &$var) {
                                                if ( $pro_buy['id_pro'] == $var['id_pro'] && $pro_buy['id_var'] == $var['id_var']) {
                                                    $var['quanty'] += $pro_buy['quanty'];
                                                    $exist = true;
                                                    break;
                                                }
                                            }
                                            if (!$exist) {
                                                $table_buy_pro_valid[] = $pro_buy;
                                            }
                                        }
                                    }



                                    foreach ($table_Remaining_pro as $pro_Remaining) {
                                        if (empty($table_Remaining_pro_valid)) {
                                            $table_Remaining_pro_valid[] = $pro_Remaining;

                                        } else {
                                            $exist = false;
                                            foreach ($table_Remaining_pro_valid as &$var) {
                                                if ( $pro_Remaining['id_pro'] == $var['id_pro'] && $pro_Remaining['id_var'] == $var['id_var']) {
                                                    $var['quanty'] += $pro_Remaining['quanty'];
                                                    $exist = true;
                                                    break;
                                                }
                                            }
                                            if (!$exist) {
                                                $table_Remaining_pro_valid[] = $pro_Remaining;
                                            }
                                        }
                                    }





                                    



                                    $sql_status = "SELECT *  FROM order_statuses  WHERE orders_id=?";
                                    $stmt_status = $conn->prepare($sql_status);
                                    $stmt_status->execute([$id['id']]);
                                    $rows_status = $stmt_status->fetch();
                                    if(!$rows_status){
                                        header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné7");
                                        exit();
                                    }
                                    $sql_status_orders="SELECT *FROM statuses WHERE id=?";
                                    $stm_status_orders=$conn->prepare($sql_status_orders);
                                    $stm_status_orders->execute([$rows_status['status_id']]);
                                    $result_satus_orders=$stm_status_orders->fetch();

                                    if(!$result_satus_orders){
                                        header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné8");
                                        exit();
                                    }



                                    $id_product=$table_buy_pro_valid[0]['id_pro'];
                                    $id_prodcut_var=$table_buy_pro_valid[0]['id_var'];
                                    $quanty_buy_pro=$table_buy_pro_valid[0]['quanty'];
                                    $quanty_Remaining_pro=0;
                                    foreach($table_Remaining_pro_valid as $po){
                                        if($po['id_pro'] == $id_product && $po['id_var'] == $id_prodcut_var ){
                                            $quanty_Remaining_pro=$po['quanty'];
                                            break;
                                        }
                                    }
                                    $TEST="SELECT * FROM variant_options  WHERE  id=?";
                                    $stmt_TEST=$conn->prepare($TEST);
                                    $stmt_TEST->execute([$id_prodcut_var]);
                                    $resutlT=$stmt_TEST->fetch();
                                    if(!$resutlT){
                                        header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné9");
                                        exit();
                                    }
                                   
                                    
                                    $sql_product_composer="SELECT *FROM product_composer WHERE  product_id =? and component_id =?";
                                    $stm_product_composer=$conn->prepare($sql_product_composer);
                                    $stm_product_composer->execute([$id_product , $idpro]);
                                    $result_product_composer=$stm_product_composer->fetch();
                                    if(!$result_product_composer){
                                        header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné10");
                                        exit();
                                    }
                                    $sql_img_pro="SELECT *FROM gallery WHERE product_variant_id=? LIMIT 1";
                                    $stm_ima_pro=$conn->prepare($sql_img_pro);
                                    $stm_ima_pro->execute([$resutlT['id']]);
                                    $resul_img_pro=$stm_ima_pro->fetch();
                                                               



                                    echo '<tr>';
                                         echo "<td rowspan='".$total."'>".$id['id']."</td>";

                                         echo "<td rowspan='".$total."'> <img src='../../../images/products/" . htmlspecialchars($result_components['image']) . "' alt='" . htmlspecialchars($result_components['component_name']) . "'>" . htmlspecialchars($result_components['component_name']) . "</td>";

                                         echo "<td rowspan='".$total."'>".$result_components['stock_quantity']."</td>";
                                         echo "<td rowspan='".$total."'>".$quenty_buy."</td>";
                                         echo "<td rowspan='".$total."'>".$quanty_Remaining."</td>";
                                         $date=convertirDateEnEcriture($result_components['created_at']);
                                         echo "<td rowspan='".$total."'>".$date."</td>";

                                        if($resul_img_pro){
                                            echo "<td> <img src='../../../images/products/" . htmlspecialchars($resul_img_pro['image']) . "' alt='" . htmlspecialchars($resutlT['title']) . "'>" . htmlspecialchars($resutlT['title']) . "</td>";
                                            
                                        }else{
                                            echo "<td>". htmlspecialchars($resutlT['title'])."</td>";
                                           
                                        } 

                                        echo "<td>".$resutlT['quantity']."</td>";
                                        echo "<td>".$quanty_buy_pro."</td>";
                                        echo "<td>".$quanty_Remaining_pro."</td>";
                                        if($result_satus_orders['status_name']=='Pending'){
                                            echo "<td rowspan='".$total."'><p class='status pending'><a href='../../orders/detaille_orders.php?id_orders=" . $id['id'] . "' target='_blank'>Pending</a></p></td>";
                                        }elseif($result_satus_orders['status_name']=='Processing'){
                                            echo "<td rowspan='".$total."'><p class='status processing'><a href='../../orders/detaille_orders.php?id_orders=" . $id['id'] . "' target='_blank'>Processing</a></p></td>";
                                        }
                                         echo "<td rowspan='".$total."'>";
                                         echo "<label class='container'>";
                                         echo "<input type='checkbox' name='orders_ids_comp[]' value='" . htmlspecialchars($id['id']) . "'>";
                                         echo "<div class='checkmark'></div>";
                                         echo "</label>";
                                         echo "</td>";

                                    echo '</tr>';


   
                                    
                                    for($j=1 ; $j<count($table_buy_pro_valid) ; $j++){

                                        $id_product=$table_buy_pro_valid[$j]['id_pro'];
                                        $id_prodcut_var=$table_buy_pro_valid[$j]['id_var'];
                                        $quanty_buy_pro=$table_buy_pro_valid[$j]['quanty'];
                                        $quanty_Remaining_pro=0;
                                        foreach($table_Remaining_pro_valid as $po){
                                            if($po['id_pro'] == $id_product && $po['id_var'] == $id_prodcut_var ){
                                                $quanty_Remaining_pro=$po['quanty'];
                                                break;
                                            }
                                        }


                                        $TEST="SELECT * FROM variant_options WHERE active=true AND id=?";
                                        $stmt_TEST=$conn->prepare($TEST);
                                        $stmt_TEST->execute([$id_prodcut_var]);
                                        $resutlT=$stmt_TEST->fetch();
                                        if(!$resutlT){
                                            header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné11");
                                            exit();
                                        }
                                        $sql_product_composer="SELECT *FROM product_composer WHERE  product_id =? and component_id =?";
                                        $stm_product_composer=$conn->prepare($sql_product_composer);
                                        $stm_product_composer->execute([$id_product , $idpro]);
                                        $result_product_composer=$stm_product_composer->fetch();
                                        if(!$result_product_composer){
                                            header("Location: pross_Update_stock.php?error=une erreur lancer au cours de la recuperation du donné12");
                                            exit();
                                        }
                                        $sql_img_pro="SELECT *FROM gallery WHERE product_variant_id=? LIMIT 1";
                                        $stm_ima_pro=$conn->prepare($sql_img_pro);
                                        $stm_ima_pro->execute([$resutlT['id']]);
                                        $resul_img_pro=$stm_ima_pro->fetch();


                                        echo "<tr>";
                                            if($resul_img_pro){
                                                echo "<td> <img src='../../../images/products/" . htmlspecialchars($resul_img_pro['image']) . "' alt='" . htmlspecialchars($resutlT['title']) . "'>" . htmlspecialchars($resutlT['title']) . "</td>";
                                                
                                            }else{
                                                echo "<td>". htmlspecialchars($resutlT['title'])."</td>";
                                            
                                            } 
                                            echo "<td>".$resutlT['quantity']."</td>";
                                            echo "<td>".$quanty_buy_pro."</td>";
                                            echo "<td>".$quanty_Remaining_pro."</td>";

                                         
                                        echo "</tr>";
                                    }




                                    
                                    
                                   

                                }



                                     
                                    
                                
                            
                        ?>
                    </tbody>
                </table>
                <div class="submit-button-container">
                    <button type="submit" name="orders_comp" class="submit-button">Select</button>
                </div>
            </form>
        </section>
    </main>
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>