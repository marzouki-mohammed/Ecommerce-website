<?php 
    session_start();
    include "../../../php/db_connect.php";
    
    // Vérification de la connexion à la base de données
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }
    
    // Vérification de la session pour les informations du produit
    if (!isset($_SESSION['id_proComposer']) || empty($_SESSION['id_proComposer'])) {
        header("Location: ../add.php?error=La session est vide.");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pro_ids = $_POST['pro_ids']; // Récupération des IDs des catégories
        
        // Vérifiez si pro_ids est défini et si c'est un tableau, sinon, le convertir en tableau
        if (!empty($pro_ids)) {
            if (!is_array($pro_ids)) {
                // Si un seul produit est sélectionné, $_POST['pro_ids'] peut être une chaîne, il faut donc le convertir en tableau
                $pro_ids = array($pro_ids);
            }
            
            $_SESSION['id_pro_content'] = $pro_ids;
            
            /*
            foreach ($pro_ids as $id) {
                // Préparer la requête pour insérer le produit dans chaque catégorie
                $sql = "INSERT INTO product_composer (product_id, component_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$id, $idprodSimple]);

                if (!$result) {
                    // Redirection en cas d'erreur
                    header("Location: add_pro_Composer_etap2.php?error=Erreur lors de l'attribution des produits");
                    exit();
                }
            }
            */
    
            // Redirection après insertion réussie
            header("Location: add_pro_Composer_etap3.php");
            exit();
        } else {
            // Redirection si aucune catégorie n'a été sélectionnée
            header("Location: add_pro_Composer_etap2.php?error=Aucune produit sélectionnée.");
            exit();
        }
    } else {
        // Redirection si le formulaire n'a pas été soumis par POST
        header("Location: add_pro_Composer_etap2.php?error=Formulaire non valide.");
        exit();
    }
?>
