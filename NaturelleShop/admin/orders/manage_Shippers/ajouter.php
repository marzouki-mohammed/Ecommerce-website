<?php 
 session_start();
 
 
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Form</title>
    

   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../product_manage/assets/css/styleform.css">
</head>
<body>


    <section class="container">
        <header class="title">ADD</header>
        <?php if(isset($_GET['error'])){ ?>
                    <div class="alert alert-danger" role="alert" >
                        <?php echo $_GET['error']; ?>
                    </div>
        <?php } ?>
        <form id="add-shipper" action="add_shipper.php" method="post" enctype="multipart/form-data" class="form">
            <div class="input-box">
                <label for="shipper_name">Nom de l'expéditeur</label>
                <input type="text" name="shipper_name" id="shipper_name" placeholder="Entrez le nom de l'expéditeur" required />
            </div>

            <div class="input-box">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Entrez l'email" required />
            </div>

            <div class="input-box">
                <label for="phone">Téléphone</label>
                <input type="tel" name="phone" id="phone" placeholder="Entrez le numéro de téléphone" required />
            </div>

            <div class="input-box">
                <label for="adress">Adresse</label>
                <input type="text" name="adress" id="adress"  placeholder="neighborhood, city, country"  required />
            </div>

            <div class="input-box">
                <label for="country">Pays</label>
                <input type="text" name="country" id="country" value="Maroc" />
            </div>

            <div class="input-box">
                <label for="city">Ville</label>
                <input type="text" name="city" id="city" placeholder="Entrez la ville" required />
            </div>

            <button type="submit" id="submit_btn" class="submit">Ajouter</button>
        </form>


    </section>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       
</body>
</html>
