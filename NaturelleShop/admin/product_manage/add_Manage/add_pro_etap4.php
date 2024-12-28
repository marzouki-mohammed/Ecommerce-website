<?php
   session_start();
   include "../../../php/db_connect.php";
   
   // Vérification de la connexion à la base de données
   if (!isset($conn)) {
       echo "Database connection is not set.";
       exit;
   }
   if (!isset($_SESSION['id_proSimple']) || empty($_SESSION['id_proSimple'])) {
      header("Location: ../add.php?error=La session est vide.");
      exit();
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Form</title>
    

   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styleform.css">
</head>
<body>


    <section class="container">
        <header class="title">ADD</header>
        <?php if(isset($_GET['error'])){ ?>
                    <div class="alert alert-danger" role="alert" >
                        <?php echo $_GET['error']; ?>
                    </div>
        <?php } ?>
        <form action="prossWarehouse.php" method="post" enctype="multipart/form-data" class="form">
               <div class="input-box">
                  <label for="warehouse_name">Nom de l'entrepôt</label>
                  <input type="text" name="warehouse_name" id="warehouse_name" placeholder="Entrez le nom de l'entrepôt" required />
               </div>

               <div class="input-box">
                  <label for="location">Emplacement</label>
                  <input type="text" name="location" id="location" placeholder="Entrez l'emplacement" required />
               </div>
               <button type="submit" id="submit_btn" class="submit">Ajouter le stock</button>
               <button id="submit_btn2" class="submit"><a href="add_pro_etap4b.php">Selectionner une Warehouses </a></button>


         </form>



    </section>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       
</body>
</html>
