<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
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
    <link rel="stylesheet" href="../assets/css/styleselecte.css">
</head>
<body>
    <main class="table">
        <section class="table__header">
            <div class="input-group">
                <input type="search" placeholder="Search...">
                <img src="../../images/search.png" alt="Search icon">
            </div>
            <div class="export__file">
                <label for="export-file" class="export__file-btn" title="Export File"></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Export As &nbsp; &#10140;</label>
                    <label for="export-file" id="toJSON">JSON <img src="../../images/json.png" alt="JSON icon"></label>
                    <label for="export-file" id="toCSV">CSV <img src="../../images/csv.png" alt="CSV icon"></label>
                    <label for="export-file" id="toEXCEL">EXCEL <img src="../../images/excel.png" alt="EXCEL icon"></label>
                </div>
            </div>
        </section>

        

        <section class="table__body">
            <form method="post" action="pross_Update_status_comp.php">
                <table class="tble1">
                    <thead>
                        <tr>
                            <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Nom <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix comparé <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix de vente <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Quantité en stock <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Status <span class="icon-arrow">&UpArrow;</span></th>

                            <th>Date de création <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Date de mise à jour <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Sélectionner <span class="icon-arrow">&UpArrow;</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Requête SQL pour obtenir les produits
                            $sql = "SELECT * FROM components ";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($rows) > 0) {
                                foreach ($rows as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["component_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["price"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["compare_price"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["vente_price"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["stock_quantity"]) . "</td>";
                                    if($row['is_active']){
                                        echo "<td><p class='status delivered'>enable</p></td>";
                                    }else{
                                        echo "<td><p class='status cancelled'>disable</p></td>";
                                    }
                                    echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                    echo "<td>";
                                    echo "<label class='container'>";
                                    echo "<input type='checkbox' name='select_id_composer_Update_status[]' value='" . htmlspecialchars($row["id"]) . "' aria-label='Supprimer catégorie " . htmlspecialchars($row["component_name"]) . "'>";
                                    echo "<div class='checkmark'></div>";
                                    echo "</label>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10'>Aucun produit disponible</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='10'>Erreur lors de la récupération des produits</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="submit-button-container">
                    <button type="submit" name="Update_status_composer" class="submit-button">Sélectionner</button>
                </div>
            </form>
        </section>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
