<?php
    session_start();
    include "../../../php/db_connect.php";

    // Vérification si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['select_id_reduction']) && !empty($_POST['select_id_reduction']) ) {
        
            // Récupération de l'ID du coupon sélectionné
            $coupon_id = intval($_POST['select_id_reduction']);
            
            $_SESSION['id_reduction']=$coupon_id;
            header("Location: selection_pro.php");
            exit;
        
    } else {
        header("Location: appliquer_reduction.php?error=Error");
        exit;
    }
?>
