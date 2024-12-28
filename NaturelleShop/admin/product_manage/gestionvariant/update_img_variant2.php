<?php 
     session_start();
     include "../../../php/db_connect.php";
     


    // Vérification de la connexion à la base de données
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    // Vérification de la session pour les informations du produit
    if(!isset($_SESSION['idvar_updateimage']) || empty($_SESSION['idvar_updateimage'])){
        header("Location: update_img_variant.php?error=error.");
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
        <?php if(isset($_GET['success'])){ ?>
                    <div class="alert alert-success" role="alert" >
                        <?php echo $_GET['success']; ?>
                    </div>
        <?php } ?>

        <form action="pross_update_img_variant2.php" method="post" enctype="multipart/form-data" class="form">
                    <!-- Upload images with unique IDs -->
                    <div class="field" style="border: none;">
                         <input type="file" id="image_files1_add1" name="image_files1_add1" accept=".jpg, .jpeg, .png" >
                         <label for="image_files1_add1">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
                    </div>

                    <div class="field" style="border: none;">
                         <input type="file" id="image_files2_add2" name="image_files2_add2" accept=".jpg, .jpeg, .png" >
                         <label for="image_files2_add2">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
                    </div>
                    <div class="field" style="border: none;">
                         <input type="file" id="image_files3_add3" name="image_files3_add3" accept=".jpg, .jpeg, .png" >
                         <label for="image_files3_add3">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
                    </div>

                    <div class="field" style="border: none;">
                         <input type="file" id="image_files4_add4" name="image_files4_add4" accept=".jpg, .jpeg, .png" >
                         <label for="image_files4_add4">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
                    </div>

                    <button type="submit" id="submit_btn_add2" class="submit">Ajouter l'image </button>
                    
          </form>



    </section>
    <!-- Ionicon link -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    

       
</body>
</html>