<?php
session_start();
// Inclure le fichier de connexion à la base de données
include "./db_connect.php";

// Vérifier si la connexion à la base de données est établie
if (!isset($conn)) {
    echo "La connexion à la base de données n'est pas établie.";
    exit;
}

// Vérifier si l'ID est passé en paramètre dans l'URL
if (isset($_GET['idC']) && !empty($_GET['idC'])) {

    $pro_idC = intval($_GET['idC']);

    $sql = "SELECT id FROM components  WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute([$pro_idC]);

    if ($stmt->rowCount() > 0) {
        // Récupérer les détails du composant
        $produitC = $stmt->fetch();

        // Stocker l'ID du composant dans la session
        $_SESSION['id_pCompoder'] = $pro_idC;
        $_SESSION['id_p'] = 'if';     
        $_SESSION['id_plien']='id_p';
    

        // Rediriger vers la page du produit
        header("Location: ../pages/productpage.php");
        exit;
    } else {
        // Si l'ID ne correspond à aucun produit ou composant, rediriger vers la page d'accueil
        header("Location: ../../index.php");
        exit;
    }


}else{
    // Requête non valide ou champ de recherche vide
    header("Location: ../../index.php");
    exit;

}
?>