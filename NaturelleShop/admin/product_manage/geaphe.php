<?php
session_start();
include "../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Requête SQL pour obtenir les produits les plus achetés ce mois
$sql_top_month = "
    SELECT p.product_name, SUM(oi.quantity) AS total_quantity
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON p.id = oi.product_variant_id
    WHERE MONTH(o.created_at) = MONTH(CURRENT_DATE())
    AND YEAR(o.created_at) = YEAR(CURRENT_DATE())
    GROUP BY p.product_name
    ORDER BY total_quantity DESC
    LIMIT 5";
$stmt_top_month = $conn->prepare($sql_top_month);
$stmt_top_month->execute();
$data_top_month = $stmt_top_month->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour obtenir les 5 produits les plus achetés dans le magasin
$sql_top_store = "
    SELECT p.product_name, SUM(oi.quantity) AS total_quantity
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON p.id = oi.product_variant_id
    GROUP BY p.product_name
    ORDER BY total_quantity DESC
    LIMIT 5";
$stmt_top_store = $conn->prepare($sql_top_store);
$stmt_top_store->execute();
$data_top_store = $stmt_top_store->fetchAll(PDO::FETCH_ASSOC);

// Convertir les données en format JSON pour les utiliser dans JavaScript
$data_top_month_json = json_encode($data_top_month);
$data_top_store_json = json_encode($data_top_store);
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
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 0;
            align-items: center;
            justify-content: space-between;
            
        }
        .charts {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            padding: 20px;
        }
        .charts-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            height: 400px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px;
            border: 1px solid #ddd;
            overflow: hidden;
        }
        .chart-title {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #ffffff;
            text-align: center;
            font-size: 1.2em;
            z-index: 1;
        }
        .chart-container {
            width: 100%;
            height: 100%;
        }
        @media  (max-width: 800px) {
            .charts {
                flex-direction: column;

            }
        }
    </style>
</head>
<body>
    <div class="charts">
        <div class="charts-card">
            <div class="chart-title">Top Products Purchased This Month</div>
            <div id="topMonthChart" class="chart-container"></div>
        </div>
        <div class="charts-card">
            <div class="chart-title">Top 5 Products Purchased in the Store</div>
            <div id="topStoreChart" class="chart-container"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Les données PHP converties en JSON pour les graphiques
        const dataTopMonth = <?php echo $data_top_month_json; ?>;
        const dataTopStore = <?php echo $data_top_store_json; ?>;

        // Préparer les données pour le graphique des produits les plus achetés ce mois
        const topMonthOptions = {
            series: [{
                name: 'Quantité',
                data: dataTopMonth.map(item => item.total_quantity)
            }],
            chart: {
                type: 'bar',
                height: '100%'
            },
            xaxis: {
                categories: dataTopMonth.map(item => item.product_name),
                title: {
                    text: 'Produit'
                }
            },
            yaxis: {
                title: {
                    text: 'Quantité achetée'
                }
            }
        };

        // Préparer les données pour le graphique des 5 produits les plus achetés
        const topStoreOptions = {
            series: [{
                name: 'Quantité',
                data: dataTopStore.map(item => item.total_quantity)
            }],
            chart: {
                type: 'bar',
                height: '100%'
            },
            xaxis: {
                categories: dataTopStore.map(item => item.product_name),
                title: {
                    text: 'Produit'
                }
            },
            yaxis: {
                title: {
                    text: 'Quantité achetée'
                }
            }
        };

        // Rendre les graphiques
        const topMonthChart = new ApexCharts(document.querySelector("#topMonthChart"), topMonthOptions);
        topMonthChart.render();

        const topStoreChart = new ApexCharts(document.querySelector("#topStoreChart"), topStoreOptions);
        topStoreChart.render();
    </script>
</body>
</html>
