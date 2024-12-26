// main dashboard

<?php 
    session_start();

    include "../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    $sql = "SELECT COUNT(*) AS total_links FROM products   ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_linkspro = $result['total_links'];

    $sql = "SELECT COUNT(*) AS total_links FROM categories   ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();

    $total_linkscategorie = $result['total_links'];

    $sql = "SELECT COUNT(*) AS total_links FROM users    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();

    $total_linkusers = $result['total_links'];

    $sql = "SELECT COUNT(*) AS total_links FROM orders     ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();

    $total_linkorders = $result['total_links'];

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>NaturelleShop ADMINS</title>
  

    <!--
      - favicon
    -->
    <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">
    

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/styles.css">
  </head>
  <body>
    <div class="grid-container">

      <!-- Header -->
      <header class="header">
        <div>
            <a href="../../index.php" >
                    <svg width="145"  height="60" xmlns="http://www.w3.org/2000/svg">
                        
                        <circle cx="25" cy="30" r="20" stroke="black" stroke-width="3" fill="lightgreen" />
                        <text x="25" y="37" font-size="20" font-family="Arial" text-anchor="middle" fill="white">N</text>
                        
                        
                        <text x="50" y="37" font-size="15" font-family="Arial" fill="green">Naturelle</text>
                        <text x="110" y="37" font-size="15" font-family="Arial" fill="darkgreen">Shop</text>
                    </svg>          
            </a>
        </div>

        <div id="btn_menu" class="menu-icon" onclick="openSidebar()">
          <span class="material-icons-outlined">menu</span>
        </div>
        
        
      </header>
      <!-- End Header -->

      <!-- Sidebar -->
      <aside id="sidebar">
        <div class="sidebar-title">
          <div class="sidebar-brand">
            <span class="material-icons-outlined">shopping_cart</span> STORE
          </div>
          <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
        </div>

        <ul class="sidebar-list">
          <li class="sidebar-list-item">
            <a href="dashboard.php" >
              <span class="material-icons-outlined">dashboard</span> Dashboard
            </a>
          </li>

          <li class="sidebar-list-item">
            <a  href="manage_admin.php">
            <span class="material-icons-outlined">groups</span> admin
            </a>
          </li>


          <li class="sidebar-list-item">
            <a href="manage_products.php">
              <span class="material-icons-outlined">inventory_2</span> Products
            </a>
          </li>



          <li class="sidebar-list-item">
            <a href="manage_categories.php" >
              <span class="material-icons-outlined">category</span> Categories
            </a>
          </li>



          <li class="sidebar-list-item">
            <a href="manage_users.php" >
              <span class="material-icons-outlined">groups</span> Customers
            </a>
          </li>
          <li class="sidebar-list-item">
            <a href="orders/manage_orders.php" >
              <span class="material-icons-outlined">local_mall</span> Orders
            </a>
          </li>

        </ul>
        
      </aside>
      <!-- End Sidebar -->

      <!-- Main -->
      <main class="main-container" id="main">
        <div class="main-title">
          <h2 style="color:#141414;">DASHBOARD</h2>
        </div>

        <div class="main-cards">

          <div class="card">
            <div class="card-inner">
              <h3>PRODUCTS</h3>
              <span class="material-icons-outlined">inventory_2</span>
            </div>
            <h1><?php echo $total_linkspro ;?></h1>
          </div>

          <div class="card">
            <div class="card-inner">
              <h3>CATEGORIES</h3>
              <span class="material-icons-outlined">category</span>
            </div>
            <h1><?php echo $total_linkscategorie ;?></h1>
          </div>

          <div class="card">
            <div class="card-inner">
              <h3>CUSTOMERS</h3>
              <span class="material-icons-outlined">groups</span>
            </div>
            <h1><?php echo  $total_linkusers ;?></h1>
          </div>

          <div class="card">
            <div class="card-inner">
              <h3>ORDERS</h3>
              <span class="material-icons-outlined">shopping_cart</span>
            </div>
            <h1><?php echo $total_linkorders;?></h1>
          </div>

        </div>
        <!--
        <div class="charts">

          <div class="charts-card">
            <h2 class="chart-title">Top 5 Products</h2>
            <div id="bar-chart"></div>
          </div>

          <div class="charts-card">
            <h2 class="chart-title">Purchase and Sales Orders</h2>
            <div id="area-chart"></div>
          </div>
        </div>
        -->
        
      </main>
      <!-- End Main -->

    </div>

    <!-- Scripts -->
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <script src="assets/js/script.js"></script>
    <!-- Custom JS -->
    
  
  </body>
</html>
