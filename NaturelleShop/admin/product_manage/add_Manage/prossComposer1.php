<?php
session_start();
include "../../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Vérification de la session pour les informations du produit
if (
    !isset($_SESSION['title_filde']) || empty($_SESSION['title_filde']) ||
    !isset($_SESSION['desc_filde']) || empty($_SESSION['desc_filde']) ||
    !isset($_SESSION['price_filde']) || empty($_SESSION['price_filde']) ||
    !isset($_SESSION['quentiter_filde']) || 
    !isset($_SESSION['active_pro']) 
) {
    header("Location: ../add.php?error=La session est vide.");
    exit();
}
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si un fichier a été soumis
    if (isset($_FILES['image_files_composer']) && $_FILES['image_files_composer']['error'] === UPLOAD_ERR_OK) {
        
        $fileTmpPath = $_FILES['image_files_composer']['tmp_name'];
        $fileName = $_FILES['image_files_composer']['name'];
        $fileSize = $_FILES['image_files_composer']['size'];
        $fileType = $_FILES['image_files_composer']['type'];
        $fileNameCmps = explode('.', $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Définir les extensions de fichiers autorisées
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        // Vérifier l'extension du fichier
        if (in_array($fileExtension, $allowedExtensions)) {
            // Définir le répertoire de téléchargement
            $uploadDir = '../../../images/products/';
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadFileDir = $uploadDir . $newFileName;

            // Déplacer le fichier téléchargé vers le répertoire de destination
            if (move_uploaded_file($fileTmpPath, $uploadFileDir)) {
                // Récupérer les informations du produit depuis la session
                $title = $_SESSION['title_filde'];
                $description = $_SESSION['desc_filde'];
                $price = $_SESSION['price_filde'];
                $quantity = $_SESSION['quentiter_filde'];
                $activ_pro = $_SESSION['active_pro'];

                // Enregistrer les informations du fichier dans la base de données si nécessaire
                 // Préparer et exécuter l'insertion dans la base de données
                $sql = "INSERT INTO components (component_name, description, image, price, vente_price, stock_quantity,is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?,?, NOW(), NOW())";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$title, $description, $newFileName, $price, $price, $quantity,$activ_pro ]);

                if ($result) {
                    $_SESSION['id_proComposer']=$conn->lastInsertId();
                    // Redirection en cas de succès
                    header("Location: add_pro_Composer_etap2.php?success=Composant ajouté avec succès.");
                    exit();
                } else {
                    // En cas d'erreur d'insertion
                    header("Location: add_pro_Composer_etap1.php?error=Erreur lors de l'insertion.");
                    exit();
                }

                
            } else {
                // En cas d'erreur lors du déplacement du fichier
                header("Location: add_pro_Composer_etap1.php?error=Erreur lors du téléchargement de l'image.");
                exit();
            }
        } else {
            // Si l'extension du fichier n'est pas autorisée
            header("Location: add_pro_Composer_etap1.php?error=Extension de fichier non autorisée.");
            exit();
        }
    } else {
        // Si aucun fichier n'a été soumis ou en cas d'erreur
        header("Location: add_pro_Composer_etap1.php?error=Aucun fichier soumis ou erreur lors du téléchargement.");
        exit();
    }
} else {
    // Si le formulaire n'a pas été soumis correctement
    header("Location: add_pro_Composer_etap1.php?error=Formulaire non soumis correctement.");
    exit();
}
?>
