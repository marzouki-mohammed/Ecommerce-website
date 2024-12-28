<?php
    session_start();
    include "../../../php/db_connect.php";

    // Vérification de la connexion à la base de données
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    // Récupération d'un éventuel message d'erreur
    $errorMsg = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <style>
        .alert {
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
    text-align: center;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Delete</title>
    <link rel="stylesheet" href="../assets/css/styleselecte.css">
</head>
<body>
    <div>
    <!-- Affichage du message d'erreur -->
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger">
            <p><?php echo $errorMsg; ?></p>
        </div>
    <?php endif; ?>
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
            <form method="post" action="pross_selection_reduction.php">
                <table class="tble1">
                    <thead>
                        <tr>
                        <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Code</th>
                        <th>Discount Amount <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Date de début <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Date de fin <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Date de création <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Sélectionner <span class="icon-arrow">&UpArrow;</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Requête SQL pour obtenir les produits
                            $sql = "SELECT * FROM coupons ";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($rows) > 0) {
                                foreach ($rows as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["code"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["discount_amount"]) . "%</td>";
                                    echo "<td>" . htmlspecialchars($row["valid_from"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["valid_until"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                    echo "<td>";
                                    echo "<label class='container'>";
                                    echo "<input type='radio' name='select_id_reduction' value='" . htmlspecialchars($row["id"]) . "'>";
                                    echo "<div class='checkmark'></div>";
                                    echo "</label>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10'>Aucun Coupons disponible</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='10'>Erreur lors de la récupération des Coupons</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="submit-button-container">
                    <button type="submit" name="selectcoupons" class="submit-button">Sélectionner</button>
                </div>
            </form>
        </section>
    </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
