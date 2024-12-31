<?php 
session_start();

include "./db_connect.php";

// Vérifiez la connexion à la base de données
if (!isset($conn)) {
    echo "La connexion à la base de données n'est pas établie.";
    exit;
}
// Vérifiez la méthode de requête et la présence de l'entrée de recherche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_input']) && !empty($_POST['search_input'])) {
    // Nettoyage et sécurisation de la requête utilisateur
    $query = trim($_POST['search_input']);
    $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
   
    // Vérifiez la longueur de la requête
    if (strlen($query) >= 3) {
        // Recherche dans la table des catégories
        $sql_cat = "SELECT id FROM categories WHERE name LIKE ? ";

        $stm_cat = $conn->prepare($sql_cat);
        $stm_cat->execute(['%' . $query . '%']);
        $data_cat = $stm_cat->fetchAll(PDO::FETCH_ASSOC);

        // Recherche dans la table des produits
        $sql_pro = "SELECT id FROM products WHERE product_name LIKE ? AND active = TRUE";

        $stm_pro = $conn->prepare($sql_pro);
        $stm_pro->execute(['%' . $query . '%']);
        $data_pro = $stm_pro->fetchAll(PDO::FETCH_ASSOC);

        // Vérifiez s'il y a des résultats
        if (count($data_cat) > 0 || count($data_pro) > 0) {
            // Stocker les résultats dans les sessions
            $_SESSION['id_cat'] = $data_cat;
            $_SESSION['id_pro'] = $data_pro;
            
            // Rediriger vers la page de résultats
            header("Location: ../pages/page_Serch.php");
            exit;
        } else {
            // Aucun résultat trouvé
            $em = "Aucun produit n'existe.";
            header("Location: ../pages/page_Serch.php?error=" . urlencode($em));
            exit;
        }
    } else {
        // Longueur de la requête trop courte
        $em = "La requête doit comporter au moins 3 caractères.";
        header("Location: ../pages/page_Serch.php?error=" . urlencode($em));
        exit;
    }
} else {
    // Requête non valide ou champ de recherche vide    
    header("Location: ../../index.php");
    exit;
}
?>
