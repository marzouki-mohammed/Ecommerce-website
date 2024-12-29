<?php
  session_start();
  include "../../php/db_connect.php";

  if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
  }
  if(!isset($_GET['id_orders']) || empty($_GET['id_orders'])){
    header("Location: manage_orders.php");
    exit;
  }

  $id_ord=intval($_GET['id_orders']);

  $sql_orders="SELECT *FROM orders WHERE id=?";
  $stm_orders=$conn->prepare($sql_orders);
  $stm_orders->execute([$id_ord]);
  $result_ord=$stm_orders->fetch();
  if(!$result_ord){
    header("Location: manage_orders.php");
    exit;
  }

  $sql_address="SELECT *FROM user_address  WHERE id=?";
  $stm_address=$conn->prepare($sql_address);
  $stm_address->execute([$result_ord['address_id']]);
  $result_adre=$stm_address->fetch();
  if(!$result_adre){
    header("Location: manage_orders.php");
    exit;
  }

  $sql_user="SELECT * FROM users WHERE id=?";
  $stm_user=$conn->prepare($sql_user);
  $stm_user->execute([$result_ord['user_id']]);
  $result_user=$stm_user->fetch();
  if(!$result_user){
    header("Location: manage_orders.php");
    exit;
  }



  $sql_status="SELECT * FROM order_statuses WHERE orders_id=?";
  $stm_status=$conn->prepare($sql_status);
  $stm_status->execute([intval($result_ord['id'])]);
  $result_status=$stm_status->fetch();
  if(!$result_status){
    header("Location: manage_orders.php");
    exit;
  }

   $sql_ordes_status="SELECT * FROM statuses WHERE id=?";
   $stm_orders_status=$conn->prepare($sql_ordes_status);
   $stm_orders_status->execute([intval($result_status['status_id'])]);
   $res=$stm_orders_status->fetch();                           
   if(!$res){
    header("Location: manage_orders.php");
    exit;
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
   $date=convertirDateEnEcriture($result_ord['created_at']);
   $prix=$result_ord['price'];
  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop ADMINS</title>
    <link rel="shortcut icon" href="../../images/icons/icons.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/styles.css">
    

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="globale">
        <header class="header">
            <div>
                    <a href="../../../index.php" >
                            <svg width="145"  height="60" xmlns="http://www.w3.org/2000/svg">
                                
                                <circle cx="25" cy="30" r="20" stroke="black" stroke-width="3" fill="lightgreen" />
                                <text x="25" y="37" font-size="20" font-family="Arial" text-anchor="middle" fill="white">N</text>
                                
                                
                                <text x="50" y="37" font-size="15" font-family="Arial" fill="green">Naturelle</text>
                                <text x="110" y="37" font-size="15" font-family="Arial" fill="darkgreen">Shop</text>
                            </svg>          
                    </a>
                </div>
              <div class="menu-icon">
                <a href="manage_orders.php"><i class="fas fa-arrow-left"></i></a>
              </div>
             
        </header>
        <div class="global2">
            <div class="content1">
                
                    <div class="card">
                        <div class="title">Purchase Reciept</div>
                        <div class="info">
                            <div class="row">
                                <div class="col-7">
                                    <span id="heading">Date</span><br>
                                    <span id="details"><?php echo $date ;?></span>
                                </div>
                                <div class="col-5 pull-right">
                                    <span id="heading">Order No.</span><br>
                                    <span id="details"><?php echo $id_ord ;?></span>
                                </div>
                            </div>      
                        </div>      
                        
                        <div class="total">
                            <div class="row">
                                <div class="col-9"></div>
                                <div class="col-3"><big>&pound;<?php echo $prix ;?></big></div>
                            </div>
                        </div>
                        <div class="tracking">
                            <div class="title">Tracking Order</div>
                        </div>
                        <div class="progress-track">
                            <ul id="progressbar">
                                <?php 
                                  if($res['id']==1){
                                    echo   '<li class="step0 active" id="step1">Pending</li>
                                            <li class="step0" id="step2">Processing</li>
                                            <li class="step0" id="step3">Shipped</li>
                                            
                                            <li class="step0" id="step4">Delivered</li>
                                            <li class="step0" id="step5">Canceled</li>';

                                  }elseif($res['id']==2){
                                    echo   '<li class="step0 active" id="step1">Pending</li>
                                            <li class="step0 active" id="step2">Processing</li>
                                            <li class="step0" id="step3">Shipped</li>
                                            
                                            <li class="step0" id="step4">Delivered</li>
                                            <li class="step0" id="step5">Canceled</li>';
                                  }elseif($res['id']==3){
                                    echo   '<li class="step0 active" id="step1">Pending</li>
                                            <li class="step0 active" id="step2">Processing</li>
                                            <li class="step0 active" id="step3">Shipped</li>
                                            
                                            <li class="step0" id="step4">Delivered</li>
                                            <li class="step0" id="step5">Canceled</li>';
                                  }elseif($res['id']==4){
                                    echo   '<li class="step0 active" id="step1">Pending</li>
                                            <li class="step0 active" id="step2">Processing</li>
                                            <li class="step0 active" id="step3">Shipped</li>
                                            
                                            <li class="step0 active" id="step4">Delivered</li>
                                            <li class="step0" id="step5">Canceled</li>';
                                  }else{
                                    echo   '<li class="step0 active" id="step1">Pending</li>
                                            <li class="step0 active" id="step2">Processing</li>
                                            <li class="step0 active" id="step3">Shipped</li>
                                            
                                            <li class="step0 active" id="step4">Delivered</li>
                                            <li class="step0 active" id="step5">Canceled</li>';
                                  }
                                ?>
                            </ul>
                        </div>
                        
            
                        
                    </div>
                
                
                

                        <div class="card2">
                            
                            <div class="card-header">
                                <h4 class="card-header-title">Customer</h4>
                            </div> 
                            <div class="top_port">     
                                    <div class="top-container">
                                            <?php 
                                              if($result_user['sex'] == 'Male'){
                                                echo '<img src="assets/img/person_M.png" class="img-fluid profile-image" width="70">';
                                              }else{
                                                echo '<img src="assets/img/person_F.png" class="img-fluid profile-image" width="70">';
                                              }
                                            ?>
                                            
                                            
                                                <h5 class="name"><?php echo $result_user['name'];?></h5>
                                            <!-- <p class="mail">clark@zmail.com</p> -->
                                            
                                    </div>
                                    
                            
                            
                                    <div class="middle-container d-flex justify-content-between align-items-center mt-3 p-2">
                                            <div class="dollar-div px-3">
                                                
                                                <div class="round-div"><i class="fa fa-dollar dollar"></i></div>
                            
                                            </div>
                                            <div class="d-flex flex-column text-right mr-2">
                                                <span class="current-balance">total orders</span>
                                                <?php 
                                                    $sql_price="SELECT SUM(price) AS total_price
                                                                FROM orders
                                                                WHERE user_id = ?";
                                                    $stm_price=$conn->prepare($sql_price);
                                                    $stm_price->execute([intval($result_user['id'])]);
                                                    $result_price=$stm_price->fetch();
                                                    if($result_price){
                                                        echo "<span class='amount'><span class='dollar-sign'>$</span>".$result_price['total_price']."</span>";
                                                    }else{
                                                        echo '<span class="amount"><span class="dollar-sign">$</span>00</span>';
                                                    }
                                                ?>
                                                
                                            </div>
                            
                                    </div>
                            </div>   
                    
                            <div class="recent-border mt-4">
                                <span class="recent-orders">Customer info</span>
                            </div>
                            <div class="wishlist-border">
                                <i class="fa fa-phone"></i>
                                <span class="mail"><?php echo $result_adre['phone_number']; ?></span>
                            </div>
                            <div class="wishlist-border">
                                <i class="fa fa-envelope"></i>
                                <span class="mail"><?php echo $result_adre['contact_email'];?></span>
                            </div>
                            <div class="wishlist-border">
                                <i class="fa fa-map-marker"></i>
                                <span class="mail"><?php echo $result_adre['address_line1'];?></span>
                            </div>
                            

                            
                        </div>
                        
                    
                
            </div>
        
            <div class="content_product">
                
                <div class="table">
                    <main class="table" id="customers_table">
                        <section class="table__header">
                            <h6>P.simple</h6>
                            <div class="input-group">
                                <input type="search" placeholder="Search Data...">
                                <img src="assets/img/search.png" alt="">
                            </div>
                            <div class="export__file">
                                <label for="export-file" class="export__file-btn" title="Export File"></label>
                                <input type="checkbox" id="export-file">
                                <div class="export__file-options">
                                    <label>Export As &nbsp; &#10140;</label>
                                    <label for="export-file" id="toJSON">JSON <img src="assets/img/json.png" alt=""></label>
                                    <label for="export-file" id="toCSV">CSV <img src="assets/img/csv.png" alt=""></label>
                                    <label for="export-file" id="toEXCEL">EXCEL <img src="assets/img/excel.png" alt=""></label>
                                </div>
                            </div>
                        </section>
                        <section class="table__body">
                            <table class="myTable">
                                <thead>
                                    <tr>
                                        <th> Id Product<span class="icon-arrow">&UpArrow;</span></th>
                                        <th> Product <span class="icon-arrow">&UpArrow;</span></th>
                                        <th> Option  <span class="icon-arrow">&UpArrow;</span></th>
                                        <th> Q.Buy <span class="icon-arrow">&UpArrow;</span></th>
                                        <th> Q.Remaining <span class="icon-arrow">&UpArrow;</span></th>
                                        <th> Status <span class="icon-arrow">&UpArrow;</span></th>
                                        <th> Price <span class="icon-arrow">&UpArrow;</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql_pro="SELECT *
                                                FROM order_items
                                                WHERE order_id = ?
                                                AND component_id IS NULL";
                                        $stm_pro=$conn->prepare($sql_pro);
                                        $stm_pro->execute([$id_ord]);
                                        $resu_pro=$stm_pro->fetchAll(PDO::FETCH_ASSOC);
                                        if($resu_pro){
                                            foreach($resu_pro as $pro){
                                                echo '<tr>';
                                                echo "<td>".$pro['product_id']."</td>";
                                                $sql_pros="SELECT *FROM products WHERE id=?";
                                                $stm_pros=$conn->prepare($sql_pros);
                                                $stm_pros->execute([$pro['product_id']]);
                                                $res_pros=$stm_pros->fetch();
                                                if($res_pros){
                                                    $sql_pro_var="SELECT *FROM variant_options WHERE id=? AND product_id=?";
                                                    $stm_pro_var=$conn->prepare($sql_pro_var);
                                                    $stm_pro_var->execute([$pro['product_variant_id'] , $res_pros['id']]);
                                                    $resu_pro_var=$stm_pro_var->fetch();
                                                    if($resu_pro_var){
                                                        $sql_img_pro="SELECT *FROM gallery WHERE product_variant_id=? LIMIT 1";
                                                        $stm_ima_pro=$conn->prepare($sql_img_pro);
                                                        $stm_ima_pro->execute([$resu_pro_var['id']]);
                                                        $resul_img_pro=$stm_ima_pro->fetch();
                                                        if($resul_img_pro){
                                                            echo "<td> <img src='../../images/products/" . htmlspecialchars($resul_img_pro['image']) . "' alt='" . htmlspecialchars($res_pros['product_name']) . "'>" . htmlspecialchars($res_pros['product_name']) . "</td>";
                                                            echo "<td>".$resu_pro_var['title']."</td>";
                                                        }else{
                                                            echo "<td>".$res_pros['product_name']."</td>";
                                                            echo "<td>".$resu_pro_var['title']."</td>";
                                                        }                                                       
                                                    }else{
                                                      echo "<td>".$res_pros['product_name']."</td>";
                                                      echo "<td></td>";
                                                    }

                                                }else{
                                                    echo "<td></td>";
                                                    echo "<td></td>";
                                                }
                                                echo "<td>".$pro['quantity']."</td>";
                                                $sql_quanty="SELECT * FROM porso_statuses WHERE orders_id=? AND order_items_id=? AND component_id is null  AND product_id =? AND product_variant_id=?";
                                                $stm_quanty=$conn->prepare($sql_quanty);
                                                $stm_quanty->execute([$id_ord ,$pro['id'], $pro['product_id'] , $pro['product_variant_id'] ]);
                                                $resul_quanty=$stm_quanty->fetch();
                                                if($resul_quanty){
                                                    
                                                    echo "<td>".$resul_quanty['quantity_obli_var']."</td>";
                                                    if($resul_quanty['quantity_obli_var']<=0){
                                                        echo '<td>
                                                                <p class="status delivered">valid</p>
                                                             </td>';
                                                    }else{
                                                        echo '<td>
                                                                <p class="status cancelled">not valid</p>
                                                             </td>';
                                                    }


                                                }else{
                                                    echo "<td></td>"; 
                                                    echo "<td></td>";                                                   
                                                }
                                                echo "<td>".$pro['price']." $</td>";
                                                echo '</tr>';


                                                
                                            }
                                        }else{
                                            echo "<tr><td colspan='11'>No data available</td></tr>";
                                        }
                                    ?>                                   
                                </tbody>
                            </table>
                        </section>
                    </main>
                </div>
            </div>
            <?php 
                $sql_pro2="SELECT *
                            FROM order_items
                            WHERE order_id = ?
                            AND product_id  IS NULL AND product_variant_id  IS NULL";
                $stm_pro2=$conn->prepare($sql_pro2);
                $stm_pro2->execute([$id_ord]);
                $resu_pro2=$stm_pro2->fetchAll(PDO::FETCH_ASSOC);
                if($resu_pro2){
            ?>
            <div class="content_product">
                
                <div class="table">
                    <main class="table" id="customers_table">
                        <section class="table__header">
                            <h6>P.compound</h6>
                        </section>
                        <section class="table__body">
                            <table>
                                <thead>
                                    <tr>
                                        <th> Id P.compound</th>
                                        <th> P.compound</th>
                                        <th> Q.Buy.compound</th>
                                        <th> Q.Remaining.compound </th>
                                        <th> Price </th>

                                        <th> Id Product</th>
                                        <th> Product </th>
                                        <th> Option </th>
                                        <th> Q.Buy </th>
                                        <th> Q.Remaining </th>
                                        <th> Status</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                            foreach($resu_pro2 as $pro){
                                                

                                                $sql_pros2="SELECT *FROM components  WHERE id=?";
                                                $stm_pros2=$conn->prepare($sql_pros2);
                                                $stm_pros2->execute([$pro['component_id']]);
                                                $res_pros2=$stm_pros2->fetch();
                                                if($res_pros2){
                                                    echo '<tr>';
                                                    echo "<td>".$pro['component_id']."</td>";
                                                    echo "<td> <img src='../../images/products/" . htmlspecialchars($res_pros2['image']) . "' alt='" . htmlspecialchars($res_pros2['component_name']) . "'>" . htmlspecialchars($res_pros2['component_name']) . "</td>";
                                                    echo "<td>".$pro['quantity']."</td>";
                                                    $sql_quanty_compo="SELECT * FROM porso_statuses WHERE orders_id=? AND order_items_id=? AND component_id=?";
                                                    $stm_quanty_comp=$conn->prepare($sql_quanty_compo);
                                                    $stm_quanty_comp->execute([$id_ord ,$pro['id'], $pro['component_id']]);
                                                    $resul_quanty_comp=$stm_quanty_comp->fetch();
                                                    if($resul_quanty_comp){
                                                        
                                                    echo "<td>".$resul_quanty_comp['quantity_obli_comp']."</td>";
                                                    echo "<td>".$pro['price']." $</td>";
                                                    echo  "<td></td>";
                                                    echo  "<td></td>";
                                                    echo  "<td></td>";
                                                    echo  "<td></td>";
                                                    echo  "<td></td>";                                    

                                                    if($resul_quanty_comp['quantity_obli_comp']<=0){
                                                        echo '<td>
                                                                <p class="status delivered">valid</p>
                                                              </td>';
                                                    }else{
                                                        echo '<td>
                                                                <p class="status cancelled">not valid</p>
                                                             </td>';
                                                    }
                                                    echo '</tr>';

                                                    $sql_orders_item="SELECT *FROM orders_items_compo WHERE orders_items_id=?";
                                                    $stm_orders_item=$conn->prepare($sql_orders_item);
                                                    $stm_orders_item->execute([$pro['id']]);
                                                    $result_orders_item=$stm_orders_item->fetchAll(PDO::FETCH_ASSOC);
                                                    if($result_orders_item){
                                                        foreach($result_orders_item as $item){                    
                                                            $sql_pro_item="SELECT *FROM products WHERE id=?";
                                                            $stm_pro_item=$conn->prepare($sql_pro_item);
                                                            $stm_pro_item->execute([$item['product_id']]);
                                                            $resulte_pro_item=$stm_pro_item->fetch();
                                                            if($resulte_pro_item){
                                                                echo "<tr>";
                                                                    echo  "<td></td>";
                                                                    echo  "<td></td>";
                                                                    echo  "<td></td>";
                                                                    echo  "<td></td>";
                                                                    echo  "<td></td>";
                                                                  echo "<td>".$resulte_pro_item['id']."</td>";
                                                                  $sql_pro_var_item="SELECT *FROM variant_options WHERE id=? AND product_id=?";
                                                                  $stm_pro_var_item=$conn->prepare($sql_pro_var_item);
                                                                  $stm_pro_var_item->execute([$item['product_variant_id'],$resulte_pro_item['id']]);
                                                                  $resu_pro_var_item=$stm_pro_var_item->fetch();
                                                                  if($resu_pro_var_item){
                                                                    $sql_img_pro_item="SELECT *FROM gallery WHERE product_variant_id=? LIMIT 1";
                                                                    $stm_ima_pro_item=$conn->prepare($sql_img_pro_item);
                                                                    $stm_ima_pro_item->execute([$resu_pro_var_item['id']]);
                                                                    $resul_img_pro_item=$stm_ima_pro_item->fetch();
                                                                    if($resul_img_pro_item){
                                                                        echo "<td> <img src='../../images/products/" . htmlspecialchars($resul_img_pro_item['image']) . "' alt='" . htmlspecialchars($resulte_pro_item['product_name']) . "'>" . htmlspecialchars($resulte_pro_item['product_name']) . "</td>";
                                                                        echo "<td>".$resu_pro_var_item['title']."</td>";

                                                                    }else{
                                                                        echo "<td>".$resulte_pro_item['product_name']."</td>";
                                                                        echo "<td>".$resu_pro_var_item['title']."</td>";

                                                                    }
                                                                    
                                                                  }else{
                                                                    echo "<td>".$resulte_pro_item['product_name']."</td>";
                                                                    echo "<td></td>";
                                                                  }
                                                                  echo "<td>".$item['quantity']."</td>";




                                                                  
                                                                  $sql_Remaining="SELECT * FROM porso_statuses WHERE orders_id=? AND order_items_id=? AND component_id=? AND product_id is null AND product_variant_id is null";
                                                                  $stm_Remaining=$conn->prepare($sql_Remaining);
                                                                  $stm_Remaining->execute([$id_ord , $pro['id'] , $pro['component_id'] ]);
                                                                  $result_Remaining=$stm_Remaining->fetch();
                                                                  if($result_Remaining){
                                                                    $sql_porso_statuses_comp="SELECT *FROM porso_statuses_comp WHERE porso_statuses_id=? AND product_id=? AND product_variant_id=?";
                                                                    $stm_porso_statuses_comp=$conn->prepare($sql_porso_statuses_comp);
                                                                    $stm_porso_statuses_comp->execute([$result_Remaining['id'] , $item['product_id'] , $item['product_variant_id'] ]);
                                                                    $result_porso_statuses_comp=$stm_porso_statuses_comp->fetch();
                                                                    if($result_porso_statuses_comp){
                                                                        echo "<td>".$result_porso_statuses_comp['quantity_obli_var']."</td>";
                                                                    }else{
                                                                        echo "<td></td>";
                                                                    }
                                                                   
                                                                    if($result_porso_statuses_comp['quantity_obli_var']<=0){
                                                                        echo '<td>
                                                                                <p class="status delivered">valid</p>
                                                                             </td>';
                                                                    }else{
                                                                        echo '<td>
                                                                                <p class="status cancelled">not valid</p>
                                                                             </td>';
                                                                    }
                                                                    
                                                                }else{
                                                                    echo "<td></td>";
                                                                    echo "<td></td>";

                                                                }
                                                                echo "</tr>";
                                                            }
                                                            
                                                        }
                                                    }

                                                  }                                              
                                                }else{
                                                    echo "<tr><td colspan='11'>No data available</td></tr>";
                                                                                                      
                                                }
                                            }                                       
                                    ?>                                   
                                </tbody>
                            </table>
                        </section>
                    </main>
                </div>
            </div>
            <?php }?>

        </div>
    
    </div>

    
  
    <script>
                // Sélectionner toutes les tables avec la classe 'myTable'
                const tables = document.querySelectorAll('.myTable');
                const search = document.querySelector('.input-group input');
                tables.forEach((table, index) => {
            const sheetName = `Sheet${index + 1}`;
            console.log(sheetName); // Affiche: "Sheet1", "Sheet2", etc.
            });

                // Fonction de recherche dans les tables
                function searchTable() {
                    tables.forEach(table => {
                        const rows = table.querySelectorAll('tbody tr');
                        const searchValue = search.value.toLowerCase();

                        rows.forEach((row, i) => {
                            const cells = row.querySelectorAll('td');
                            const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
                            const isVisible = rowText.includes(searchValue);
                            row.classList.toggle('hide', !isVisible);
                            row.style.setProperty('--delay', i / 25 + 's');
                        });

                        // Alterner la couleur de fond des lignes visibles
                        table.querySelectorAll('tbody tr:not(.hide)').forEach((visible_row, i) => {
                            visible_row.style.backgroundColor = (i % 2 === 0) ? 'transparent' : '#0000001b';
                        });
                    });
                }

                // Ajouter l'événement de recherche
                search.addEventListener('input', searchTable);





                // Fonction de tri
            function sortTable(column, sort_asc, tbody) {
                const rowsArray = Array.from(tbody.querySelectorAll('tr')); // Convertir NodeList en tableau

                rowsArray.sort((a, b) => {
                    // Récupérer le texte des cellules à comparer
                    let first_row = a.querySelectorAll('td')[column].textContent.trim();
                    let second_row = b.querySelectorAll('td')[column].textContent.trim();

                    // Vérifier si les valeurs sont numériques
                    let first_number = parseFloat(first_row.replace(/,/g, '')); // Retirer les virgules des nombres
                    let second_number = parseFloat(second_row.replace(/,/g, ''));

                    // Comparer en fonction du type (nombre ou texte)
                    if (!isNaN(first_number) && !isNaN(second_number)) {
                        return sort_asc ? first_number - second_number : second_number - first_number;
                    } else {
                        return sort_asc ? first_row.localeCompare(second_row) : second_row.localeCompare(first_row);
                    }
                });

                // Réinsérer les lignes triées dans le tableau
                rowsArray.forEach(row => tbody.appendChild(row));
            }

            // 2. Sorting | Ordering data of HTML table

            tables.forEach(table => {
                const table_headings = table.querySelectorAll('thead th');
                const tbody = table.querySelector('tbody'); // Utiliser querySelector pour obtenir un seul tbody

                table_headings.forEach((head, i) => {
                    let sort_asc = true;  // Variable pour contrôler l'ordre de tri
                    head.onclick = () => {
                        // Supprimer la classe 'active' de tous les en-têtes
                        table_headings.forEach(h => h.classList.remove('active', 'asc', 'desc'));
                        // Ajouter la classe 'active' à l'en-tête cliqué
                        head.classList.add('active');
                        head.classList.toggle('asc', sort_asc);
                        head.classList.toggle('desc', !sort_asc);

                        // Trier les lignes du tableau
                        sortTable(i, sort_asc, tbody);

                        // Inverser l'ordre de tri pour le prochain clic
                        sort_asc = !sort_asc;
                    };
                });
            });



            // Fonction pour télécharger un fichier
            function downloadFile(content, type, filename) {
                const blob = new Blob([content], { type: `application/${type}` });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                a.click();
                URL.revokeObjectURL(url);
            }

            // Fonction pour convertir la table HTML en JSON
            function toJSON() {
                tables.forEach(table => {
                    let tableData = [];
                    const tHeadings = table.querySelectorAll('thead th');
                    const tRows = table.querySelectorAll('tbody tr');

                    // Collecte des en-têtes de colonnes
                    let tHead = [];
                    tHeadings.forEach(tHeading => {
                        let actualHead = tHeading.textContent.trim().split(' ');
                        tHead.push(actualHead.splice(0, actualHead.length - 1).join(' ').toLowerCase());
                    });

                    // Collecte des données des lignes
                    tRows.forEach(row => {
                        const rowObject = {};
                        const tCells = row.querySelectorAll('td');

                        tCells.forEach((tCell, cellIndex) => {
                            rowObject[tHead[cellIndex]] = tCell.textContent.trim();
                        });
                        tableData.push(rowObject);
                    });

                    const json = JSON.stringify(tableData, null, 4);
                    downloadFile(json, 'json', 'table_data.json');
                });
            }

            // Ajouter l'événement de clic au bouton JSON
            const jsonBtn = document.querySelector('#toJSON');
            jsonBtn.addEventListener('click', toJSON);



            // Fonction pour convertir la table HTML en CSV
            function toCSV() {
                tables.forEach(table => {
                    const tHeads = table.querySelectorAll('thead th');
                    const tbodyRows = table.querySelectorAll('tbody tr');
                    
                    // Collecte des en-têtes de colonnes
                    const headings = [...tHeads].map(head => head.textContent.trim()).join(',') + '\n';
                    
                    // Collecte des données des lignes
                    const tableData = [...tbodyRows].map(row => {
                        const cells = row.querySelectorAll('td');
                        return [...cells].map(cell => cell.textContent.trim()).join(',');
                    }).join('\n');
                    
                    const csv = headings + tableData;
                    downloadFile(csv, 'csv', 'table_data.csv');
                });
            }

            // Ajouter l'événement de clic au bouton CSV
            const csvBtn = document.querySelector('#toCSV');
            csvBtn.addEventListener('click', toCSV);


            function toExcel() {
                const wb = XLSX.utils.book_new(); // Crée un nouveau workbook

                tables.forEach((table, index) => {
                    const ws = XLSX.utils.table_to_sheet(table); // Convertit chaque table en feuille
                    const sheetName = `Sheet${index + 1}`; // Nom unique pour chaque feuille
                    XLSX.utils.book_append_sheet(wb, ws, sheetName); // Ajoute la feuille au workbook
                });

                XLSX.writeFile(wb, 'table_data.xlsx'); // Télécharge le fichier Excel
            }

            // Ajouter l'événement de clic au bouton EXCEL
            const excelBtn = document.querySelector('#toEXCEL');
            excelBtn.addEventListener('click', toExcel);


            document.querySelector('form[action="prossAttrubu2.php"]').addEventListener('submit', function(e) {
                const confirmDelete = confirm("Êtes-vous sûr de vouloir supprimer les catégories sélectionnées ?");
                if (!confirmDelete) {
                    e.preventDefault(); // Annuler la soumission du formulaire si l'utilisateur ne confirme pas
                }
            });




</script>
                                        

</body>
</html>