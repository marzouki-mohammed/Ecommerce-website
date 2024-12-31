<?php
session_start();
// Inclure le fichier de connexion à la base de données
include "./db_connect.php";

// Vérifier si la connexion à la base de données est établie
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}


    // Vérifier si l'ID de la catégorie est passé en paramètre dans l'URL
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        // Récupérer l'ID et le sécuriser
        $category_id = intval($_GET['id']);

        // Préparer la requête pour récupérer les informations de la catégorie
        $sql = "SELECT id FROM categories  WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        // Vérifier si la catégorie existe
        if ($stmt->rowCount() > 0) {
            // Récupérer les détails de la catégorie
            $category = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Vous pouvez également récupérer et afficher les produits de cette catégorie
            $sql_products = "SELECT product_id  FROM product_categories  WHERE category_id  = :catid";
            $stmt_products = $conn->prepare($sql_products);
            $stmt_products->bindParam(':catid', $category_id, PDO::PARAM_INT);
            $stmt_products->execute();
            if ($stmt_products->rowCount() > 0) {

            $id=0;
            $_SESSION['id_cat']=$category ;
            $_SESSION['id_pro'] = $id;

            
            // Rediriger vers la page de résultats
            header("Location: ../pages/page_Serch.php");
            exit;
            }else{
                // Requête non valide ou champ de recherche vide    
                header("Location: ../../index.php");
                exit;

            }


            
        } else {
            // Requête non valide ou champ de recherche vide    
            header("Location: ../../index.php");
            exit;
        }
    } else {
        // Requête non valide ou champ de recherche vide    
        header("Location: ../../index.php");
        exit;
    }

?>
