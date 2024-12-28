<?php 
     session_start();
     include "../../../php/db_connect.php";



     // Vérification de la connexion à la base de données
     if (!isset($conn)) {
     echo "Database connection is not set.";
     exit;
     }

     if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['provariant_active_ids']) && !empty($_POST['provariant_active_ids'])
    ) {
        $ids_activer=$_POST['provariant_active_ids'];
        foreach($ids_activer as $id){
            // Fetch existing image information from the database
            $sql_fetch = "SELECT  active  FROM variant_options  WHERE  id = ?";
            $stmt_fetch = $conn->prepare($sql_fetch);
            $stmt_fetch->execute([$id]);
            $active = $stmt_fetch->fetch();
            if($active){
                if($active['active'] == 0){
                    $sql = "UPDATE variant_options
                            SET active = 1
                            WHERE id =? ";
                    
                }else{
                    $sql = "UPDATE variant_options
                            SET active = 0
                            WHERE id =? ";

                }
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id]);
            }  
            
        }
        
        header("Location: desactiver_variant.php");
        exit();
   }else{
    header("Location: desactiver_variant.php?error=Formulaire non valide.");
    exit();
   }

    
?>