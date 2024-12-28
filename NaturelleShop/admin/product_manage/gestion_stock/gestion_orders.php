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

$sql_orders = "
  SELECT DISTINCT o.*
  FROM orders o
  JOIN order_items oi ON o.id = oi.order_id
  JOIN porso_statuses ps ON o.id = ps.orders_id AND oi.id = ps.order_items_id
  WHERE oi.product_id = :product_id
  AND ps.quantity_obli_var > 0
";
        
$stm_orders = $conn->prepare($sql_orders);
$stm_orders->execute(['product_id' => $idpro]);
$result_orders = $stm_orders->fetchAll(PDO::FETCH_ASSOC);
if(!$result_orders){
    header("Location: add_stock.php");
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
            <form method="post" action="pross_Update_stock_orders.php">
                <table class="tble1">
                    <thead>
                        <tr>
                            <th> Id Orders<span class="icon-arrow">&UpArrow;</span></th>
                            <th> Option Product <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Product <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Buy <span class="icon-arrow">&UpArrow;</span></th>
                            <th> Q.Remaining <span class="icon-arrow">&UpArrow;</span></th>
                            <th> the order date <span class="icon-arrow">&UpArrow;</span></th> 
                            <th> Status</th>
                            <th> Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       
                            
                                foreach ($result_orders as $row) {
                                    // Requête SQL pour obtenir les données
                                    $sql = "SELECT * FROM order_statuses  WHERE orders_id=?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$row['id']]);
                                    $rows = $stmt->fetch();
                                    if( $rows ){
                                         $sql_status="SELECT *FROM statuses WHERE id=?";
                                         $stm_status=$conn->prepare($sql_status);
                                         $stm_status->execute([$rows['status_id']]);
                                         $result_satus=$stm_status->fetch();
                                         if($result_satus){
                                            if($result_satus['status_name']=='Pending'
                                            || $result_satus['status_name']=='Processing' ){
                                                    $TEST="SELECT * FROM variant_options WHERE product_id=? AND active=true";
                                                    $stmt=$conn->prepare($TEST);
                                                    $stmt->execute([$idpro]);
                                                    $resutlT=$stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    if($resutlT){
                                                        foreach($resutlT as $var){
                                                            $sql_test_pro_var="SELECT *FROM order_items WHERE order_id=? AND component_id IS NULL AND product_id=? AND product_variant_id=?";
                                                            $stm_test_pro_var=$conn->prepare($sql_test_pro_var);
                                                            $stm_test_pro_var->execute([$row['id'] , $idpro ,$var['id'] ]);
                                                            $result_test_pro_var=$stm_test_pro_var->fetchAll(PDO::FETCH_ASSOC);
                                                            
                                                            if($result_test_pro_var){
                                                                $id = $row["id"];
                                                                echo "<tr>";
                                                                echo "<td>" .$row["id"]. "</td>";
                                                                $sql_img_pro="SELECT *FROM gallery WHERE product_variant_id=? LIMIT 1";
                                                                $stm_ima_pro=$conn->prepare($sql_img_pro);
                                                                $stm_ima_pro->execute([$var['id']]);
                                                                $resul_img_pro=$stm_ima_pro->fetch();
                                                                if($resul_img_pro){
                                                                    echo "<td> <img src='../../../images/products/" . htmlspecialchars($resul_img_pro['image']) . "' alt='" . htmlspecialchars($var['title']) . "'>" . htmlspecialchars($var['title']) . "</td>";
                                                                    
                                                                }else{
                                                                    echo "<td>". htmlspecialchars($var['title'])."</td>";
                                                                   
                                                                } 
                                                                echo "<td>".$var['quantity']."</td>";
                                                                $Buy=0;
                                                                $Remaining=0;
                                                                foreach($result_test_pro_var as $item){
                                                                    $Buy+=$item['quantity'];
                                                                    $sql_pros="SELECT *FROM porso_statuses WHERE orders_id=? AND order_items_id=? AND component_id is null AND product_id=? AND product_variant_id=?";
                                                                    $stm_pros=$conn->prepare($sql_pros);
                                                                    $stm_pros->execute([$row['id'] , $item['id'] , $idpro , $var['id']]);
                                                                    $result_pross=$stm_pros->fetchAll(PDO::FETCH_ASSOC);
                                                                    if($result_pross){
                                                                        foreach($result_pross as $prodo){
                                                                            $Remaining+=$prodo['quantity_obli_var'];
                                                                        }
                                                                    }
                                                                    
                                                                }
                                                                echo "<td>".$Buy."</td>";
                                                                echo "<td>".$Remaining."</td>";
                                                                $date=convertirDateEnEcriture($row['created_at']);
                                                                echo "<td>".$date."</td>"; 
                                                                if($result_satus['status_name']=='Pending'){
                                                                    echo "<td><p class='status pending'><a href='../../orders/detaille_orders.php?id_orders=" . $row['id'] . "' target='_blank'>Pending</a></p></td>";
                                                                }elseif($result_satus['status_name']=='Processing'){
                                                                    echo "<td><p class='status processing'><a href='../../orders/detaille_orders.php?id_orders=" . $row['id'] . "' target='_blank'>Processing</a></p></td>";
                                                                }
                                                                echo "<td>";
                                                                echo "<label class='container'>";
                                                                echo "<input type='checkbox' name='orders_ids[]' value='" . htmlspecialchars($row['id']) . "'>";
                                                                echo "<div class='checkmark'></div>";
                                                                echo "</label>";
                                                                echo "</td>";
                                                                echo "</tr>";


                                                            }
                                                            

                                                        }
                                                    }
                                                    
                                                    

                                                  
          
                                                }
                                         }







                                    }
                                }
                                    
                                
                            
                        ?>
                    </tbody>
                </table>
                <div class="submit-button-container">
                    <button type="submit" name="orders" class="submit-button">Select</button>
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