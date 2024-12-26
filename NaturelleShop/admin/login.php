// login page 


<!DOCTYPE html>
<html lang="en">
<head>
<style>
  body {
  font-family: Arial, sans-serif;
  background:#bbfdae;
 
    
}

.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.cintent {
  text-align: center;
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h3, h1 {
  margin: 0;
}

h1 span {
  color: #bbfdae;
}

p {
  margin: 10px 0;
}

a {
  display: block;
  margin-top: 20px;
  padding: 5px 10px;
  background-color:#141414;
  color:white;
  text-decoration: none;
  border-radius: 5px;
}

a:hover {
  background-color: #bbfdae;
  color: #141414;
}
  </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop ADMINS</title>
  

    <!--
      - favicon
    -->
    <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">
    
</head>
<body>
<?php 
    session_start();
    if (isset($_SESSION['full_name'])) {
  ?>
    <div class="container">
        <div class="cintent">
            <h3>Hi ,<span>admin</span></h3>
            <h1>welcome <span><?=$_SESSION['full_name']?></span></h1>
            <p>this is an admin page </p>
            <a href="dashboard.php">login</a>
            <a href="../../index.php">logout</a>
        </div>
    </div>
    <?php 
    } else {
      header("Location: ../../index.php");
      exit;
    } 
  ?>
</body>
</html>



 
