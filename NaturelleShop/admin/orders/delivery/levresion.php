
<?php 
session_start();

include "../../../php/db_connect.php";
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}
//ADMIN
$sql = "SELECT COUNT(*) AS total_links FROM shippers   ";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$total_linksUsers = $result['total_links'];
?>


<!DOCTYPE html>
<html lang="en">
<head>

<style>
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
            font-size: 13px;
    }
    body {
         margin: 0;
         padding: 0;
         background-color: white;
         color: #4a4a4d;
         font-family: 'Montserrat', sans-serif;
    }
   
   
    .containerrr {
          display:flex;
          
          width: 100%;
          flex-direction: column;
          height: 100vh;
               
    }

    .data {
    display: grid;
    grid-template-columns: 50% 50%;
    align-items: center; /* Centre verticalement les éléments dans chaque cellule */
    justify-items: center; /* Centre horizontalement les éléments dans chaque cellule */
    width: 100%;
    height: 50px;
    padding-top: 0;
    margin-top: 0;
    margin-bottom: 10px;
    }
    .retour{
        width: 100px;
        display: flex;
        justify-content: center;
        text-align: center;
        font-size: 20px;
        text-decoration: none;
        color: black;
    }
    .retour span {
        margin-right: 5px;
        font-size: 28px;
        background: #5ef900;
        border-radius: 7px;
        padding-left: 5px;
        padding-right: 5px;
    }
   

    .tab{
        
        display: flex;
        width: 100%;
        height: 100vh;
        align-items: center;
        justify-items: center;
        

    }
    .table {
            width: 94vw;
            height: 80vh;
           margin-left: 25px;
           
            background-color: #D9E8D8;
            backdrop-filter: blur(7px);
            box-shadow: 0 .4rem .8rem #0005;
            border-radius: .8rem;
            overflow: hidden;
    }
    .table__header {
            width: 100%;
            height: 15%;
            background-color: rgba(255, 255, 255, 0.333);
            padding: .8rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
    }


    .table__header .input-group {
            width: 35%;
            height: 100%;
            background-color: #D9E8D8;
            padding: 0 .8rem;
            border-radius: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: .2s;
        }
        .table__header .input-group:hover {
            width: 45%;
            background-color: #fff8;
            box-shadow: 0 .1rem .4rem #0002;
        }
        .table__header .input-group img {
            width: 1.2rem;
            height: 1.2rem;
        }
        .table__header .input-group input {
            width: 100%;
            padding: .5rem .8rem .5rem .8rem;
            background-color: transparent;
            border: none;
            outline: none;
        }
        .table__body {
            width: 95%;
            max-height: calc(89% - 2.8rem);
            background-color: #fffb;
            margin: .8rem auto;
            border-radius: .6rem;
            overflow: auto;
            overflow: overlay;
        }
        .table__body::-webkit-scrollbar {
            width: 0.5rem;
            height: 0.5rem;
        }
        .table__body::-webkit-scrollbar-thumb {
            border-radius: .5rem;
            background-color: #0004;
            visibility: hidden;
        }
        .table__body:hover::-webkit-scrollbar-thumb {
            visibility: visible;
        }
        table {
            width: 100%;
        }
        table, th, td {
            border-collapse: collapse;
            padding: 1rem;
            text-align: left;
        }
        thead th {
            position: sticky;
            top: 0;
            left: 0;
            background-color: #a2db9e;
            cursor: pointer;
            text-transform: capitalize;
        }
        tbody tr:nth-child(even) {
            background-color: #0000000b;
        }
        tbody tr {
            --delay: .1s;
            transition: .5s ease-in-out var(--delay), background-color 0s;
        }
        tbody tr.hide {
            opacity: 0;
            transform: translateX(100%);
        }
        tbody tr:hover {
            background-color: #fff6 !important;
        }
        tbody tr td,
        tbody tr td p,
        tbody tr td img {
            transition: .2s ease-in-out;
        }
        tbody tr.hide td,
        tbody tr.hide td p {
            padding: 0;
            font: 0 / 0 sans-serif;
            transition: .2s ease-in-out .5s;
        }
        tbody tr.hide td {
            width: 0;
            height: 0;
            transition: .2s ease-in-out .5s;
        }
        @media (max-width: 1000px) {
            td:not(:first-of-type) {
                min-width: 12.1rem;
            }
        }
        thead th span.icon-arrow {
            display: inline-block;
            width: 1.3rem;
            height: 1.3rem;
            border-radius: 50%;
            border: 1.4px solid transparent;
            text-align: center;
            font-size: 1rem;
            margin-left: .5rem;
            transition: .2s ease-in-out;
        }
        thead th:hover span.icon-arrow {
            border: 1.4px solid #6c00bd;
        }
        thead th:hover {
            color: #6c00bd;
        }
        thead th.active span.icon-arrow {
            background-color: #6c00bd;
            color: #fff;
        }
        thead th.asc span.icon-arrow {
            transform: rotate(180deg);
        }
        thead th.active, tbody td.active {
            color: #6c00bd;
        }
        .export__file {
            position: relative;
        }
        .export__file .export__file-btn {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            background: #fff6 url(../../images/export.png) center / 80% no-repeat;
            border-radius: 50%;
            transition: .2s ease-in-out;
        }
        .export__file .export__file-btn:hover {
            background-color: #fff;
            transform: scale(1.15);
            cursor: pointer;
        }
        .export__file input {
            display: none;
        }
        .export__file .export__file-options {
            position: absolute;
            right: 0;
            width: 12rem;
            border-radius: .5rem;
            overflow: hidden;
            text-align: center;
            opacity: 0;
            transform: scale(.8);
            transform-origin: top right;
            box-shadow: 0 .2rem .5rem #0004;
            transition: .2s;
        }
        .export__file input:checked + .export__file-options {
            opacity: 1;
            transform: scale(1);
            z-index: 100;
        }
        .export__file .export__file-options label {
            display: block;
            width: 100%;
            padding: .6rem 0;
            background-color: #f2f2f2;
            display: flex;
            justify-content: space-around;
            align-items: center;
            transition: .2s ease-in-out;
        }
        .export__file .export__file-options label:first-of-type {
            padding: 1rem 0;
            background-color: #86e49d !important;
        }
        .export__file .export__file-options label:hover {
            transform: scale(1.05);
            background-color: #fff;
            cursor: pointer;
        }
        .export__file .export__file-options img {
            width: 2rem;
            height: auto;
        }
        .status {
            
            padding: .4rem 0;
            border-radius: 2rem;
            text-align: center;
        }
        .status.delivered {
            background-color: #86e49d;
            color: #006b21;
        }
        .status.cancelled {
            background-color: #d893a3;
            color: #b30021;
        }
        .status.pending {
            background-color: #ebc474;
        }
        .status.shipped {
            background-color: #6fcaea;
        }
        .container {
            display: block;
            height: 1.5em;
            width: 1.5em;
            cursor: pointer;
            position: relative;
        }
        .container input {
            position: absolute;
            transform: scale(0);
        }
        .container input:checked ~ .checkmark {
            transform: rotate(45deg);
            height: 2em;
            width: .7em;
            border-color: #32cd32;
            border-top-color: transparent;
            border-left-color: transparent;
            border-radius: 0;
        }
        .container .checkmark {
            display: block;
            width: inherit;
            height: inherit;
            border: 2px solid #32cd32;
            border-radius: 4px;
            transition: all .3s;
        }
        .submit-button-container {
            text-align: right;
            margin-top: 1rem;
        }
        .submit-button {
            padding: 0.5rem 1rem;
            background-color: #32cd32;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-button:hover {
            background-color: #28a745;
        }
     
  @media screen and (max-width:700px) {

    
    .table{
        width: 100%;
        margin-left: 0;
    }

    
  }
  #cont{
    overflow: hidden;
  }


  
        .container {
            display: block;
            height: 1.5em;
            width: 1.5em;
            cursor: pointer;
            position: relative;
        }
        .container input {
            position: absolute;
            transform: scale(0);
        }
        .container input:checked ~ .checkmark {
            transform: rotate(45deg);
            height: 2em;
            width: .7em;
            border-color: #32cd32;
            border-top-color: transparent;
            border-left-color: transparent;
            border-radius: 0;
        }
        .container .checkmark {
            display: block;
            width: inherit;
            height: inherit;
            border: 2px solid #32cd32;
            border-radius: 4px;
            transition: all .3s;
        }
        .submit-button-container {
            text-align: right;
            margin-top: 1rem;
        }
        .submit-button {
            padding: 0.5rem 1rem;
            background-color: #32cd32;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-button:hover {
            background-color: #28a745;
        }
        @media  (max-width:639px) {
            
            .table__header .input-group {
                width: 70%;
    
            }
            .table__header .input-group:hover {
                width: 80%;
    
            }
    
    
    
          }
   

</style>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop ADMINS</title>
    
  

    <!--
      - favicon
    -->
    <link rel="shortcut icon" href="../../../images/icons/icons.png" type="image/x-icon">
     
    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    


    
</head>
<body>
    <div class="containerrr" id="cont">
           <div class="data">
                <a href="../manage_orders.php" class="retour">
                    <span class="material-icons-outlined">keyboard_return</span> Orders
                </a>
                    
            </div>
            <div class="tab">
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
                            <form method="post" action="pross_levresion.php">
                                <table class="tble1">
                                    <thead>
                                        <tr>
                                            <th>Orders Id <span class="icon-arrow">&UpArrow;</span></th>                                                                   
                                            <th>User ID<span class="icon-arrow">&UpArrow;</span></th>
                                            <th>Name <span class="icon-arrow">&UpArrow;</span></th>
                                            <th>Shippers ID <span class="icon-arrow">&UpArrow;</span></th>
                                            <th>Shippers Name<span class="icon-arrow">&UpArrow;</span></th>
                                            <th>Address<span class="icon-arrow">&UpArrow;</span></th>
                                            <th>Country</th>
                                            <th>City </th> 
                                            <th>Status</th> 
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            // Requête SQL pour obtenir les données
                                            $sql = "SELECT o.*
                                                    FROM orders o
                                                    INNER JOIN order_statuses os ON o.id = os.orders_id
                                                    INNER JOIN statuses s ON os.status_id = s.id
                                                    WHERE s.status_name = 'Shipped'";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->execute();

                                            // Vérifier s'il y a des résultats et les afficher
                                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            if (count($rows) > 0) {
                                                foreach ($rows as $row) {
                                                    $id = $row["id"];
                                                    echo "<tr>";
                                                    echo "<td>".$row['id']."</td>";
                                                    echo "<td>".$row['user_id']."</td>";
                                                    $sql_users="SELECT * FROM users WHERE id=?";
                                                    $stm_users=$conn->prepare($sql_users);
                                                    $stm_users->execute([intval($row['user_id'])]);
                                                    $resulte_users=$stm_users->fetch();
                                                    if($resulte_users){
                                                    echo "<td>".htmlspecialchars($resulte_users['name'])."</td>";                           
                                                    }else{
                                                    echo "<td></td>";
                                                    }
                                                    echo "<td>".$row['shipping_id']."</td>";
                                                    $sql_shipping="SELECT * FROM shippers WHERE id=?";
                                                    $stm_shipping=$conn->prepare($sql_shipping);
                                                    $stm_shipping->execute([intval($row['shipping_id'])]);
                                                    $result_shipping=$stm_shipping->fetch();
                                                    if($result_shipping){
                                                        echo "<td>".htmlspecialchars($result_shipping['shipper_name'])."</td>";   
                                                    }else{
                                                        echo "<td></td>";
                                                    }
                                                    $sql_address="SELECT * FROM user_address WHERE id=?";
                                                    $stm_assress=$conn->prepare($sql_address);
                                                    $stm_assress->execute([intval($row['address_id'])]);
                                                    $resulte_address=$stm_assress->fetch();
                                                    if($resulte_address){
                                                    echo "<td>".$resulte_address['address_line1']."</td>";
                                                    echo "<td>".$resulte_address['country']."</td>";
                                                    echo "<td>".$resulte_address['city']."</td>";                              
                                                    }else{
                                                    echo "<td></td>";
                                                    echo "<td></td>";
                                                    echo "<td></td>";
                                                    }
                                                    echo "<td><p class='status shipped'>Shipped</p></td>";
                                                    
                                                    echo "<td>";
                                                    echo "<label class='container'>";
                                                    echo "<input type='checkbox' name='orders_id_delivery[]' value='" . htmlspecialchars($id) . "'>";
                                                    echo "<div class='checkmark'></div>";
                                                    echo "</label>";
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='11'>No data available</td></tr>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "<tr><td colspan='11'>Error: </td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="submit-button-container">
                                    <button type="submit" name="levresion" class="submit-button">delivery</button>
                                </div>
                            </form>
                        </section>
                </main>
                
            </div>
            </div>

    <!-- Scripts -->
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
      <!-- SheetJS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
  <script src="../../product_manage/assets/js/script.js"></script>
  


 

</body>
</html>