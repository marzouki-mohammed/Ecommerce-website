<?php
session_start();
include "../../php/db_connect.php";

// Vérifier la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Requête SQL pour obtenir la distribution des catégories avec le nombre de produits
$sql = "SELECT c.name AS category_name, COUNT(pc.product_id) AS product_count
        FROM categories c
        LEFT JOIN product_categories pc ON c.id = pc.category_id
        GROUP BY c.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convertir les données en format JSON pour les utiliser dans JavaScript
$data_json = json_encode($data);

// Requête SQL pour obtenir la catégorie la plus populaire
$sqlPopular = "SELECT c.name AS category_name, COUNT(pc.product_id) AS product_count
               FROM categories c
               LEFT JOIN product_categories pc ON c.id = pc.category_id
               GROUP BY c.id
               ORDER BY product_count DESC
               LIMIT 1";
$stmtPopular = $conn->prepare($sqlPopular);
$stmtPopular->execute();
$popular = $stmtPopular->fetch(PDO::FETCH_ASSOC);

$popular_json = json_encode($popular);
?>
<?php
// Votre code PHP reste le même.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop ADMINS</title>
  

  <!--
    - favicon
  -->
  <link rel="shortcut icon" href="../../images/icons/icons.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            color: #495057;
            margin: 0;
            padding: 20px;
        }
        .charts {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .charts-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            height: 400px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto; /* Center the card */
            border: 1px solid #dee2e6;
            overflow: hidden;
        }
        .chart-title {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            padding: 10px;
            background-color: #343a40;
            color: #ffffff;
            text-align: center;
            font-size: 1.2em;
            z-index: 1;
        }
        #stackedBarChart {
            width: 100%;
            height: 100%;
        }
        #donutChart {
            width: 80%; /* Ajuster la largeur en fonction de vos besoins */
            height: 80%; /* Ajuster la hauteur en fonction de vos besoins */
            display: block;
            position: absolute; /* Positionnement absolu par rapport au parent */
            top:30%; /* Décalage du haut de 10% */
            left: 20%; /* Décalage du côté gauche de 10% */
        }
                @media screen and (max-width: 800px) {
            .charts {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="charts">
        <div class="charts-card">
            <div class="chart-title">Distribution des Produits par Catégorie</div>
            <div id="stackedBarChart" class="chart-container"></div>
        </div>
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Les données PHP converties en JSON
        const data = <?php echo $data_json; ?>;
        const popular = <?php echo $popular_json; ?>;

        // Préparer les données pour le graphique en colonnes empilées
        const stackedBarOptions = {
            series: [{
                name: 'Nombre de Produits',
                data: data.map(item => item.product_count)
            }],
            chart: {
                type: 'bar',
                height: '100%',
                stacked: true
            },
            plotOptions: {
                bar: {
                    horizontal: false
                }
            },
            dataLabels: {
                enabled: false
            },
            
            yaxis: {
                title: {
                    text: 'Nombre de Produits'
                }
            }
        };

        // Initialiser et rendre le graphique en colonnes empilées
        const stackedBarChart = new ApexCharts(document.querySelector("#stackedBarChart"), stackedBarOptions);
        stackedBarChart.render();

    </script>
</body>
</html>
