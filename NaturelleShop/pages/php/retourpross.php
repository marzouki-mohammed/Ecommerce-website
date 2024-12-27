<?php 
     session_start();
     if(isset($_GET['idrot']) && !empty($_GET['idrot'])){
        $idcom=intval($_GET['idrot']);
        $_SESSION['id_p']='id_p';
        $_SESSION['id_plien']='id_p';
        $_SESSION['id_pCompoder']=$idcom;

     }
     echo $idcom;
     /*
     header("Location: ../productpage.php"); // Remplacez par la page de votre choix
     exit;
     */

     


?>