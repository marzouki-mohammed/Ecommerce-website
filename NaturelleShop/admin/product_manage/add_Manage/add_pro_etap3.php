<?php 
     session_start();
     include "../../../php/db_connect.php";
     


// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérification de la session pour les informations du produit
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
        <?php if(isset($_GET['success'])){ ?>
                    <div class="alert alert-success" role="alert" >
                        <?php echo $_GET['success']; ?>
                    </div>
        <?php } ?>
        <form action="prossSimple3.php" method="post" enctype="multipart/form-data" class="form">
                    <div class="input-box">
                         <label for="title">Titre de la variante</label>
                         <input type="text" name="title" id="title" placeholder="Entrez le titre de la variante" required />
                    </div>
                                   
                    <div class="input-box">
                         <label for="quantity">Quantité</label>
                         <input type="number" name="quantity" id="quantity" placeholder="Entrez la quantité" min="0" required />
                    </div>

                    <div class="input-box">
                         <label for="sku">SKU</label>
                         <input type="text" name="sku" id="sku" placeholder="Entrez le SKU" required />
                    </div>

                    <div class="input-box">
                         <label for="active">Actif</label>
                         <select name="active" id="active">
                              <option value="1">Oui</option>
                              <option value="0">Non</option>
                         </select>
                    </div>

                    <!-- Upload images with unique IDs -->
                    <div class="field" style="border: none;">
                         <input type="file" id="image1" name="image_files1" accept=".jpg, .jpeg, .png" >
                         <label for="image1">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
                    </div>
                    <div class="field" style="border: none;">
                         <input type="file" id="image2" name="image_files2" accept=".jpg, .jpeg, .png" >
                         <label for="image2">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
                    </div>
                    <div class="field" style="border: none;">
                         <input type="file" id="image3" name="image_files3" accept=".jpg, .jpeg, .png" >
                         <label for="image3">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
                    </div>
                    <div class="field" style="border: none;">
                         <input type="file" id="image4" name="image_files4" accept=".jpg, .jpeg, .png" >
                         <label for="image4">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
                    </div>

                    <button type="submit" id="submit_btn" class="submit">Ajouter la variante</button>
                    <button id="submit_btn2" class="submit"><a href="add_pro_etap4.php">Next</a></button>
          </form>



    </section>
    <!-- Ionicon link -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    

       
</body>
</html>