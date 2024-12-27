<?php
// Supprimer la variable de session spécifique
unset($_SESSION['id_pCompoder']);
unset($_SESSION['id_p']);

session_start();

// Connexion à la base de données
include "../../php/db_connect.php";
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérifier si l'ID est passé en paramètre dans l'URL
if (isset($_GET['idlien']) && !empty($_GET['idlien'])) {
    // Récupérer l'ID et le sécuriser
    $pro_id = intval($_GET['idlien']);

    // Préparer la requête pour récupérer les informations du produit
    $sql = "SELECT id FROM products WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $pro_id, PDO::PARAM_INT);
    $stmt->execute();

    // Vérifier si le produit existe
    if ($stmt->rowCount() > 0) {
        // Récupérer les détails du produit
        $produit = $stmt->fetch();

        // Stocker l'ID du produit dans la session
        $_SESSION['id_plien'] = $pro_id;
        $_SESSION['id_p']='id_p';
        $id_procom=$_SESSION['id_pCompoder'];
        $_SESSION['id_pCompoder']='id_p';


        header("Location: ../productpage.php?id_roteur=$id_procom");
         exit;

    } else {
        // Si l'ID ne correspond à aucun produit, rediriger vers la page d'accueil
        header("Location: ../../index.php");
        exit;
    }
} else {
    // Requête non valide ou champ de recherche vide, rediriger vers la page d'accueil
    header("Location: ../../index.php");
    exit;
}
?>
