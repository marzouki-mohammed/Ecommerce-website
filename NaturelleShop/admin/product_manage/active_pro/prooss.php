<?php 
     session_start();
     include "../../../php/db_connect.php";
     


    // Vérification de la connexion à la base de données
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }
   

// Vérification que les données sont envoyées via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST'  ) {

    // Vérification de la session pour les informations du produit
    if(isset($_POST['ids_components']) && !empty($_POST['ids_components'])){
        $id_pro=intval($_POST['ids_components']);
        $_SESSION['stock_id']=$id_pro;

        header("Location: pross_Update_stock.php");
        exit();
        
    }else{

        header("Location: Update_stock.php?error=error.");
        exit();
    }


}else{
    header("Location: Update_stock.php?error=error.");
    exit();
}
?>