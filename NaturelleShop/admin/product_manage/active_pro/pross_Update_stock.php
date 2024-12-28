<?php 
     session_start();
     include "../../../php/db_connect.php";
     


    // Vérification de la connexion à la base de données
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }
   
    
    // Vérification de la session pour les informations du produit
    if(!isset($_SESSION['stock_id']) || empty($_SESSION['stock_id'])){
        header("Location: Update_stock.php?error=error.");
        exit();
    }

    $idpro=intval($_SESSION['stock_id']);
    

    $sql_stock_produit="SELECT * FROM components  WHERE id=?";
    $stm_stock_produit=$conn->prepare($sql_stock_produit);
    $stm_stock_produit->execute([$idpro]);
    $result_stock_produit=$stm_stock_produit->fetch(PDO::FETCH_ASSOC);

    if(!$result_stock_produit){
        header("Location: Update_stock.php?error=error.");
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
        <header class="title"><?php echo "Product :".$result_stock_produit['component_name'] ;?></header>
        
        <form action="update.php" method="post" enctype="multipart/form-data" class="form">                                  
            <div class="input-box">
                <label for="quantity_update">Stock Quantité du Produit</label>
                <input type="number" name="quantity_update" id="quantity_update" placeholder="Entrez la quantité" min="0" value="<?php echo htmlspecialchars($result_stock_produit['stock_quantity']); ?>" required />
            </div>

            <button type="submit" id="submit_btn_update_stock" class="submit">Update</button>
        </form>



    </section>
    <!-- Ionicon link -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    

       
</body>
</html>