
<?php 
    session_start();

    include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    $sql = "SELECT COUNT(*) AS total_links FROM products   ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_linkspro = $result['total_links'];

    $sql2 = "SELECT COUNT(*) AS total_linkssub FROM components    ";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

    $total_linksprocom = $result2['total_linkssub'];

?>


<!DOCTYPE html>
<html lang="en">
<head>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     
    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    
</head>
<body>
    <div class="containerrr" id="cont">
           <div class="data">
                 <h3>DASHBOARD Products</h3>
                 <div class="card_content">
                    <div class="card">
                        <div class="card-inner">
                            <h4>Products</h4>
                        <span class="material-icons-outlined">inventory_2</span>
                        </div>
                        <h4><?php echo  $total_linkspro ?></h4>
                    </div>
                    <div class="card">
                        <div class="card-inner">
                            <h4>Products Composer</h4>
                        <span class="material-icons-outlined">inventory_2</span>
                        </div>
                        <h4><?php echo  $total_linksprocom ?></h4>
                    </div>
                 </div>
            </div>
            <div class="tab">
            
                <main class="table">
                                    <section class="table__header">
                                        
                                        <div class="input-group">
                                            <input type="search" placeholder="Search...">
                                            <img src="../images/search.png" alt="">
                                        </div>
                                        <div class="export__file">
                                            <label for="export-file" class="export__file-btn" title="Export File"></label>
                                            <input type="checkbox" id="export-file">
                                            <div class="export__file-options">
                                                <label>Export As &nbsp; &#10140;</label>
                                                <label for="export-file" id="toJSON">JSON <img src="../images/json.png" alt=""></label>
                                                <label for="export-file" id="toCSV">CSV <img src="../images/csv.png" alt=""></label>
                                                <label for="export-file" id="toEXCEL">EXCEL <img src="../images/excel.png" alt=""></label>
                                            </div>
                                        </div>
                                    </section>
                                    <section class="table__body">
                                            <table class="tble1">
                                                <thead>
                                                    <tr>
                                                        <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>name <span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>image<span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>price<span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>Compare price<span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>Vente price <span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>stock_quantity <span class="icon-arrow">&UpArrow;</span></th>               
                                                        <th>supplier Id  <span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>status <span class="icon-arrow">&UpArrow;</span></th>
                                                        
                                                        <th>Date created <span class="icon-arrow">&UpArrow;</span></th>                                                       
                                                        <th>Date updated<span class="icon-arrow">&UpArrow;</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                                    <?php


                                                                    // Requête SQL pour obtenir les données
                                                                    $sql1 = "SELECT *  FROM products  ";
                                                                    $stmt1 = $conn->prepare($sql1);
                                                                    $stmt1->execute();

                                                                    // Vérifier s'il y a des résultats et les afficher
                                                                    $rows1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                                                                    if (count($rows1) > 0) {
                                                                        foreach ($rows1 as $row) {
                                                                            echo "<tr>";
                                                                            echo "<td>" .$row["id"]. "</td>";
                                                                            echo "<td>" . htmlspecialchars($row["product_name"]) . "</td>";
                                                                            echo "<td></td>";
                                                                            echo "<td>" . $row["price"]. "</td>";
                                                                            echo "<td>" .$row["compare_price"]. "</td>";
                                                                            echo "<td>" .$row["vente_price"]. "</td>";
                                                                            echo "<td>" .$row["stock_quantity"]. "</td>";
                                                                            echo "<td>" .$row["supplier_id"]. "</td>";
                                                                            if($row['active']){
                                                                                echo "<td><p class='status delivered'>enable</p></td>";
                                                                            }else{
                                                                                echo "<td><p class='status cancelled'>disable</p></td>";
                                                                            }
                                                                            echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                                                            echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                                                            echo "</tr>";
                                                
                                                                        }
                                                                           
                                                                    }
                                                                    // Requête SQL pour obtenir les données
                                                                    $sql2 = "SELECT *  FROM components   ";
                                                                    $stmt2 = $conn->prepare($sql2);
                                                                    $stmt2->execute();
                                                                    // Vérifier s'il y a des résultats et les afficher
                                                                    $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                                                                    if (count($rows2) > 0) {
                                                                        foreach ($rows2 as $row) {
                                                                            echo "<tr>";
                                                                            echo "<td>" .$row["id"]. "</td>";
                                                                            echo "<td>" . htmlspecialchars($row["component_name"]) . "</td>";
                                                                            echo "<td>". htmlspecialchars($row["image"]) . "</td>";
                                                                            echo "<td>" . $row["price"]. "</td>";
                                                                            echo "<td>" .$row["compare_price"]. "</td>";
                                                                            echo "<td>" .$row["vente_price"]. "</td>";
                                                                            echo "<td>" .$row["stock_quantity"]. "</td>";
                                                                            echo "<td></td>";
                                                                            if($row['is_active']){
                                                                                echo "<td><p class='status delivered'>enable</p></td>";
                                                                            }else{
                                                                                echo "<td><p class='status cancelled'>disable</p></td>";
                                                                            }
                                                                            echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                                                            echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                                                            echo "</tr>";
                                                
                                                                        }
                                                                           
                                                                    }

                                                                    if(!$rows1 && !$rows2){
                                                                       echo "<tr><td colspan='9'>No data available</td></tr>";

                                                                    }
                                                                        
                                                                    
                                                                    ?>
                                                    
                                                </tbody>
                                            </table>
                                    </section>
                </main>
            </div>
            </div>

    <!-- Scripts -->
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
      <!-- SheetJS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

  
<script src="assets/js/script.js"></script>
</body>
</html>