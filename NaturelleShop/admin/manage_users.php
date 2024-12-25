<?php 
session_start();
$existe = '';
$message = '';

if (isset($_SESSION['user_id']) && isset($_SESSION['admin_username'])) {
  $message = 'success';
  $admin_id = $_SESSION['user_id'];
  $admin_name=$_SESSION['admin_username'];
  include "../php/db_connect.php";

  if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
  }
  if($admin_name==='root'){
    $existe = 'existe';

  }else{

  $sql = "SELECT role.id, role.name
          FROM role
          INNER JOIN admin_role ON role.id = admin_role.role_id
          WHERE admin_role.admin_id = :admin_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute(['admin_id' => $admin_id]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($rows) > 0) {
    foreach ($rows as $row) {
      if ($row["name"] === 'gestion des user'  ) {
        $existe = 'existe';
        break;
      }
    }
  }
}
  
} else {
  $message = 'erreur';
}
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
<?php if ($existe != ''): ?>
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
      <div class="menu-icon" onclick="openSidebar()">
        <span class="material-icons-outlined">menu</span>
      </div>
     
    </header>
    <!-- End Header -->

    <!-- Sidebar -->
    <aside id="sidebar">
      <div class="sidebar-title">
        <div class="sidebar-brand">
          <span class="material-icons-outlined">manage_accounts</span> users
        </div>
        <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
      </div>

      <ul class="sidebar-list">
       <li class="sidebar-list-item">
          <a href="manage_users.php">
            <span class="material-icons-outlined">dashboard</span> Dashboard users
          </a>
        </li>
        
        
        <li class="sidebar-list-item">
            <a href="users_manage/geaphe.php" >
              <span class="material-icons-outlined">poll</span> Reports
            </a>
        </li>
        <li class="sidebar-list-item" >
            <a href="dashboard.php">
              <span class="material-icons-outlined">keyboard_return</span> Back
            </a>
      </li>
      </ul>
      
    </aside>
    <!-- End Sidebar -->

    <!-- Main -->
    <main class="main-container has-scrollbar" id="main">
      <iframe src="users_manage/dashboardusers.php" name="description" width="100%" height="100%" class="iframe" style="margin: 0;padding: 0;border:none"></iframe>
    </main>
    <!-- End Main -->

  </div>
<?php else: ?>
  <p>inaccessible</p>
  <p><?php echo $message; ?></p>
<?php endif; ?>
<!-- Scripts -->
<!-- ApexCharts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/script.js"></script>

 
</body>
</html>
