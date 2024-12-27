<?php 
    session_start();
   
   if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_rating']) && !empty($_POST['id_rating'])){
        // Connexion à la base de données
        include "../../php/db_connect.php";
        if (!isset($conn)) {
            echo "Database connection is not set.";
            exit;
        }
        $idRating = intval($_POST['id_rating']); 
        // Requête SQL pour incrémenter le champ riagi
        $sql = "UPDATE reviews SET riagi = riagi + 1 WHERE id = :id";
        
        // Préparer la requête
        $stmt = $conn->prepare($sql);
        
        // Exécuter la requête avec l'ID de la review
        $stmt->execute(['id' => $idRating]);
        // Si l'ID de la review n'est pas défini, rediriger vers une page d'erreur
        header("Location: ../productpage.php"); // Remplacez par la page de votre choix
        exit;


   }else{
    // Si l'ID de la review n'est pas défini, rediriger vers une page d'erreur
    header("Location: ../productpage.php"); // Remplacez par la page de votre choix
    exit;
   }

?>
