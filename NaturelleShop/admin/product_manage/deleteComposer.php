
<?php
session_start();
include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Delete</title>
    <link rel="stylesheet" href="assets/css/styleselecte.css">
</head>
<body>
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
            <form method="post" action="prossdeleteComposer.php">
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
                            <th>status <span class="icon-arrow">&UpArrow;</span></th>

                            
                            <th>Date created <span class="icon-arrow">&UpArrow;</span></th>                                                       
                            <th>Date updated<span class="icon-arrow">&UpArrow;</span></th>   
                            <th>Select <span class="icon-arrow">&UpArrow;</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Requête SQL pour obtenir les données
                            $sql = "SELECT * FROM components ";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();

                            // Vérifier s'il y a des résultats et les afficher
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($rows) > 0) {
                                foreach ($rows as $row) {
                                    $id = $row["id"];
                                    echo "<tr>";
                                    echo "<td>" .$row["id"]. "</td>";
                                    echo "<td>" . htmlspecialchars($row["component_name"]) . "</td>";
                                    echo "<td>". htmlspecialchars($row["image"]) . "</td>";
                                     echo "<td>" . $row["price"]. "</td>";
                                    echo "<td>" .$row["compare_price"]. "</td>";
                                    echo "<td>" .$row["vente_price"]. "</td>";
                                    echo "<td>" .$row["stock_quantity"]. "</td>";
                                    if($row['is_active']){
                                        echo "<td><p class='status delivered'>enable</p></td>";
                                    }else{
                                        echo "<td><p class='status cancelled'>disable</p></td>";
                                    }
                                    
                                    echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                     echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                    
                                    echo "<td>";
                                    echo "<label class='container'>";
                                    echo "<input type='checkbox' name='procomposer_delete_ids[]' value='" . htmlspecialchars($id) . "'>";
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
                    <button type="submit" name="delete_produit" class="submit-button">Select</button>
                </div>
            </form>
        </section>
    </main>
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
