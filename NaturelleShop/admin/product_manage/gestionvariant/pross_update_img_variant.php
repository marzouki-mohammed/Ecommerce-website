<?php 
     session_start();
     include "../../../php/db_connect.php";



     // Vérification de la connexion à la base de données
     if (!isset($conn)) {
     echo "Database connection is not set.";
     exit;
     }

     if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['update_id_image']) && !empty($_POST['update_id_image'])
    ) {
        $ids_updat= intval($_POST['update_id_image']) ;
        
        $_SESSION['idvar_updateimage']=$ids_updat;
        header("Location: update_img_variant2.php");
        exit();
   }else{
    header("Location: update_img_variant.php?error=Formulaire non valide.");
    exit();
   }

    
?>