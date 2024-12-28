<?php 
 session_start();
 // Vérification de la session pour les informations du produit
if (
    !isset($_SESSION['title_filde']) || empty($_SESSION['title_filde']) ||
    !isset($_SESSION['desc_filde']) || empty($_SESSION['desc_filde']) ||
    !isset($_SESSION['price_filde']) || empty($_SESSION['price_filde']) ||
    !isset($_SESSION['quentiter_filde']) || 
    !isset($_SESSION['active_pro']) 

) {
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
        <form  action="prossComposer1.php" method="post" enctype="multipart/form-data" class="form">

            <div class="field" style="border: none;">
                    <input type="file" id="imagecomposer" name="image_files_composer" accept=".jpg, .jpeg, .png" >
                    <label for="imagecomposer">Choisir des photos <ion-icon name="camera-outline"></ion-icon></label>
            </div>
            <button type="submit" id="submit_btn" class="submit">Ajouter une image</button>
           
        </form>


    </section>
    <!-- Ionicon link -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
       
</body>
</html>