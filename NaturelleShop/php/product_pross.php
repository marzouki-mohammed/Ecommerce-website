<?php
session_start();
// Inclure le fichier de connexion à la base de données
include "./db_connect.php";

// Vérifier si la connexion à la base de données est établie
if (!isset($conn)) {
    echo "La connexion à la base de données n'est pas établie.";
    exit;
}


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_product_simple']) && !empty($_POST['id_product_simple'])){
    // Récupérer l'ID et le sécuriser
    $pro_id=intval($_POST['id_product_simple']);
    // Préparer la requête pour récupérer les informations du produit
    $sql = "SELECT * FROM products WHERE id =?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$pro_id]);
    // Vérifier si le produit existe
    if ($stmt->rowCount() > 0) {
        // Récupérer les détails du produit
        $produit = $stmt->fetch();

        // Stocker l'ID du produit dans la session
        $_SESSION['id_p'] = $pro_id ;
       
        $_SESSION['id_plien']='id_p';
        $_SESSION['id_pCompoder']='id_p';

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
