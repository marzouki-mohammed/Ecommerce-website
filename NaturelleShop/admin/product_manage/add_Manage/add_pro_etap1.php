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
        <form  action="prossSimple1.php" method="post" enctype="multipart/form-data" class="form">
            <div class="input-box">
                <label for="supplier_name">Nom du fournisseur</label>
                <input type="text" name="supplier_name" id="supplier_name" placeholder="Entrez le nom du fournisseur"  />
            </div>

            <div class="input-box">
                <label for="contact_name">Nom du contact</label>
                <input type="text" name="contact_name" id="contact_name" placeholder="Entrez le nom du contact" />
            </div>

            <div class="input-box">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Entrez l'email"  />
            </div>
            
            <div class="input-box">
                <label for="phone">Téléphone</label>
                <input type="tel" name="phone" id="phone" placeholder="Entrez le numéro de téléphone" />
            </div>

            <div class="input-box">
                <label for="address">Adresse</label>
                <input type="text" name="address" id="address" placeholder="Entrez l'adresse" />
            </div>

            <div class="input-box">
                <label for="city">Ville</label>
                <input type="text" name="city" id="city" placeholder="Entrez la ville" />
            </div>

            <div class="input-box">
                <label for="country">Pays</label>
                <input type="text" name="country" id="country" placeholder="Entrez le pays" />
            </div>

            <button type="submit" id="submit_btn" class="submit">Ajouter le fournisseur</button>
            <button  id="submit_btn2" class="submit"><a href="add_pro_etap1b.php">Selectionner une fournisseur</a></button>
        </form>


    </section>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       
</body>
</html>

