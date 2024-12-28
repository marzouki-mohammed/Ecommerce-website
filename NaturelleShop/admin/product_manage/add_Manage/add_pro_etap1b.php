<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérification de la session pour les informations du produit
if (
    !isset($_SESSION['title_filde']) || empty($_SESSION['title_filde']) ||
    !isset($_SESSION['desc_filde']) || empty($_SESSION['desc_filde']) ||
    !isset($_SESSION['price_filde']) || empty($_SESSION['price_filde']) ||
    !isset($_SESSION['quentiter_filde']) || 
    !isset($_SESSION['active_pro']) 
) {
    header("Location: ../add.php?error=La session est vide.");
    exit();
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
            <form method="post" action="prossSimple1b.php">
                <table class="tble1">
                    <thead>
                        <tr>
                            <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Supplier Name <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Contact Name <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Date Created <span class="icon-arrow">&UpArrow;</span></th> 
                            <th>Date Updated <span class="icon-arrow">&UpArrow;</span></th>      
                            <th>Select <span class="icon-arrow">&UpArrow;</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Requête SQL pour obtenir les données
                            $sql = "SELECT * FROM supplier";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();

                            // Vérifier s'il y a des résultats et les afficher
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($rows) > 0) {
                                foreach ($rows as $row) {
                                    $id = $row["id"];
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["id"]). "</td>";
                                    echo "<td>" . htmlspecialchars($row["supplier_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["contact_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["city"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["country"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                    echo "<td>";
                                    echo "<label class='container'>";
                                    echo "<input type='radio' name='select_id_supplier' value='" . htmlspecialchars($id) . "' aria-label='Select supplier " . htmlspecialchars($row["supplier_name"]) . "'>";
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
                    <button type="submit" name="selecte" class="submit-button">Select</button>
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
