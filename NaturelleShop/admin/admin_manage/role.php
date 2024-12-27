<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <!-- Montserrat Font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }

    .content {
      flex: 1;
      display: flex;
      flex-direction: column;
      height: 100%;
      width: 100%;
    }

    .menu-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #70c489;
      color: #fff;
      padding: 0.5rem 1rem;
      cursor: pointer;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      font-size: 16px;
      position: relative;
    }

    .menu-bar .material-icons-outlined {
      margin-right: 10px;
    }

    .menu-items {
      display: none;
      flex-direction: column;
      background-color: #fff;
      padding: 1rem;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .menu-items a {
      display: block;
      padding: 0.5rem 1rem;
      text-decoration: none;
      color: #333;
      transition: background-color 0.3s ease;
    }

    .menu-items a:hover {
      background-color: #e8e8e8;
    }

    .frame {
      flex: 1;
      width: 100%;
      border: none;
      background-color: #fff;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
      .menu-bar {
        font-size: 14px;
      }
      .menu-items {
        padding: 0.5rem;
      }
      .menu-items a {
        padding: 0.5rem;
        font-size: 14px;
      }
    }

    @media (max-width: 320px) {
      .menu-bar {
        font-size: 12px;
      }
      .menu-items a {
        padding: 0.5rem;
        font-size: 12px;
      }
    }
  </style>
</head>
<body>
  <div class="content">
    <div class="menu-bar" id="menu-toggle">
      <span class="material-icons-outlined">menu</span> Menu
    </div>
    <div class="menu-items" id="menu-items">
      <a href="addrole.php" target="rame">
        <span class="material-icons-outlined">add</span> Add Role
      </a>
      <a href="deleteRole.php" target="rame">
        <span class="material-icons-outlined">remove</span> Delete Role
      </a>
      <a href="Attributes.php" target="rame">
        <span class="material-icons-outlined">sync_alt</span> Attributes Role
      </a>
      <a href="supp_role_admin.php" target="rame">
        <span class="material-icons-outlined">person_remove</span> Supp Role Admin
      </a>
    </div>
    <iframe src="addrole.php" name="rame" width="100%" height="100%" class="frame" style="margin: 0;padding: 0;border:none; overflow:hidden;" scrolling="no"></iframe>
  </div>

  <script>
    document.getElementById('menu-toggle').addEventListener('click', function() {
      const menuItems = document.getElementById('menu-items');
      if (menuItems.style.display === 'none' || menuItems.style.display === '') {
        menuItems.style.display = 'flex';
      } else {
        menuItems.style.display = 'none';
      }
    });
  </script>
</body>
</html>
