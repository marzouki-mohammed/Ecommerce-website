<?php 
session_start();
include "../../php/db_connect.php";
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}
//ADMIN
$sql = "SELECT COUNT(*) AS total_links FROM admin ";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$total_linksADMIN = $result['total_links'];

//ROLE
$sql = "SELECT COUNT(*) AS total_links FROM role  ";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$total_linksROLE = $result['total_links'];





$sql = "SELECT a.full_name AS admin_name, COUNT(ar.role_id) AS role_count
    FROM admin a
    LEFT JOIN admin_role ar ON a.id = ar.admin_id
    GROUP BY a.full_name";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convertir les données en format JSON pour les utiliser dans JavaScript
$data_json = json_encode($data);

?>


<!DOCTYPE html>
<html lang="en">
<head>

    <style>
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #ffffff;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .charts {
            display: flex;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }
        .charts-card {
            background-color: #fffff0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px;
           
        }
        .chart-container {
            width: 100%;
            height: 100%;
        }


        .has-scrollbar { padding-bottom: 5px; }
  
  .has-scrollbar::-webkit-scrollbar {
    width: 5px; /* for vertical scroll */
    height: 12px; /* for horizontal scroll */
  }
  
  .has-scrollbar::-webkit-scrollbar-thumb {
    background: transparent;
    border: 3px solid var(--white);
    -webkit-border-radius: 20px;
            border-radius: 20px;
  }
  
  .has-scrollbar:hover::-webkit-scrollbar-thumb { background: hsl(0, 0%, 90%); }
  
  .has-scrollbar::-webkit-scrollbar-thumb:hover { background: hsl(0, 0%, 80%); }
  
        .main-container {
          display:grid;
          grid-template-columns: 30% 70%;
          height: 100vh;
          overflow-y: scroll;
          overflow-x: hidden;
          gap: 20px;     
        }
        .main-title {
          display: flex;
          justify-content: space-between;
          margin: 25px;
        }
        .main-cards {
         display: grid;
         grid-template-columns: 1fr 1fr 1fr 1fr;
         gap: 20px;
         margin: 20px 0;
        }
        .card {
         display: flex;
         flex-direction: column;
         justify-content: space-around;
         margin:25px ;
         padding: 10px;
         border-radius: 5px;
        }

  
  .card:nth-child(2) {
    background-color: #ff6d00;
  }
  .card:nth-child(3) {
    background-color: #2962ff;
  }
  .card-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .card-inner > .material-icons-outlined {
    font-size: 45px;
  }
  @media screen and (max-width: 800px){
        .main-container {
          display:flex;
          flex-direction: column;
          gap: 20px;     
        }

  }

 
        
        
     
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop ADMINS</title>
  

    <!--
      - favicon
    -->
    <link rel="shortcut icon" href="../../images/icons/icons.png" type="image/x-icon">
    
    
    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    
</head>
<body>

<div class="main-container has-scrollbar" id="main">
    <div>
          <div class="main-title">
             <h2>DASHBOARD ADMIN ROOT</h2>
          </div>
          <div class="card">
            <div class="card-inner">
              <h3>ROLE</h3>
              <span class="material-icons-outlined">category</span>
            </div>
            <h1><?php echo  $total_linksROLE ?></h1>
          </div>

          <div class="card">
            <div class="card-inner">
              <h3>ADMINS</h3>
              <span class="material-icons-outlined">groups</span>
            </div>
            <h1><?php echo  $total_linksADMIN ?></h1>
          </div>
    </div>
    <div class="charts">
          <div class="charts-card">
              <div id="adminRoleChart" class="chart-container"></div>
          </div>
    </div> 
</div> 

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Les données PHP converties en JSON
        const data = <?php echo $data_json; ?>;

        // Préparer les données pour le graphique en barres
        const options = {
            series: [{
                name: 'Roles Managed',
                data: data.map(item => item.role_count)
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: true
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: data.map(item => item.admin_name),
                title: {
                    text: 'Administrators'
                }
            },
            yaxis: {
                title: {
                    text: 'Number of Roles'
                }
            }
        };

        // Initialiser et rendre le graphique
        const chart = new ApexCharts(document.querySelector("#adminRoleChart"), options);
        chart.render();
    </script>
   
</body>
</html>