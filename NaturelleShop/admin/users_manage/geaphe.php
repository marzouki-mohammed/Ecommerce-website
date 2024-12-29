<?php
session_start();
include "../../php/db_connect.php";

// Vérifier la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Requête SQL pour obtenir les données
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count
    FROM users
    GROUP BY month
    ORDER BY month";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convertir les données en format JSON pour les utiliser dans JavaScript
$data_json = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 0;
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
            <div class="chart-title">Bar Chart</div>
            <div id="barChart" class="chart-container"></div>
        </div>
        <div class="charts-card">
            <div class="chart-title">Area Chart</div>
            <div id="areaChart" class="chart-container"></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Les données PHP converties en JSON
        const data = <?php echo $data_json; ?>;

        // Préparer les données pour le graphique en barres
        const barOptions = {
            series: [{
                name: 'Users Registered',
                data: data.map(item => item.count)
            }],
            chart: {
                type: 'bar',
                height: '100%'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: data.map(item => item.month),
                title: {
                    text: 'Month'
                }
            },
            yaxis: {
                title: {
                    text: 'Number of Users'
                }
            }
        };

        // Initialiser et rendre le graphique en barres
        const barChart = new ApexCharts(document.querySelector("#barChart"), barOptions);
        barChart.render();

        // Préparer les données pour le graphique en aire
        const areaOptions = {
            series: [{
                name: 'Users Registered',
                data: data.map(item => item.count)
            }],
            chart: {
                type: 'area',
                height: '100%'
            },
            xaxis: {
                categories: data.map(item => item.month),
                title: {
                    text: 'Month'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            fill: {
                type: 'gradient'
            },
            yaxis: {
                title: {
                    text: 'Number of Users'
                }
            }
        };

        // Initialiser et rendre le graphique en aire
        const areaChart = new ApexCharts(document.querySelector("#areaChart"), areaOptions);
        areaChart.render();
    </script>
</body>
</html>
