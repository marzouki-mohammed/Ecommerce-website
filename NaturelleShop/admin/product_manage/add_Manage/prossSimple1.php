
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

// Vérification si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['supplier_name']) && !empty($_POST['supplier_name'])
    && isset($_POST['contact_name']) && !empty($_POST['contact_name'])
    && isset($_POST['email']) && !empty($_POST['email'])
    && isset($_POST['phone']) && !empty($_POST['phone'])
    && isset($_POST['address']) && !empty($_POST['address'])
    && isset($_POST['city']) && !empty($_POST['city'])
    && isset($_POST['country']) && !empty($_POST['country'])
) {
    // Assainir les champs du formulaire
    $supplier_name = htmlspecialchars($_POST['supplier_name']);
    $contact_name = htmlspecialchars($_POST['contact_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $country = htmlspecialchars($_POST['country']);

    // Si le bouton "Ajouter le fournisseur" est soumis
    
        // Vérification si le fournisseur existe déjà
        $sqltest = "SELECT * FROM supplier WHERE email = ?";
        $stmtste = $conn->prepare($sqltest);
        $stmtste->execute([$email]);

        if ($stmtste->rowCount() > 0) {
            header("Location: ../add.php?error=Le fournisseur existe déjà.");
            exit();
        }

        // Préparer la requête pour insérer les informations du fournisseur
        $sql = "INSERT INTO supplier (supplier_name, contact_name, email, phone, address, city, country)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$supplier_name, $contact_name, $email, $phone, $address, $city, $country]);

        // Vérification si l'insertion du fournisseur a réussi
        if ($result) {
            $lastInsertIdsupplier = $conn->lastInsertId(); // Récupérer l'ID du dernier fournisseur inséré

            // Récupérer les informations du produit depuis la session
            $title = $_SESSION['title_filde'];
            $description = $_SESSION['desc_filde'];
            $price = $_SESSION['price_filde'];
            $quantity = $_SESSION['quentiter_filde'];
            $actine_pro=$_SESSION['active_pro'];

            // Préparer la requête pour insérer le produit
            $sql = "INSERT INTO products (product_name, description, price, vente_price, stock_quantity, supplier_id ,active )
                    VALUES (?, ?, ?, ?, ?, ?,?)";

            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$title, $description, $price, $price, $quantity, $lastInsertIdsupplier,$actine_pro]);

            // Vérifier si l'insertion du produit a réussi
            if ($result) {
                $_SESSION['id_proSimple']=$conn->lastInsertId();
                header("Location: add_pro_etap2.php");
                exit();
            } else {
                header("Location: ../add.php?error=Erreur lors de l'ajout du produit.");
                exit();
            }
        }    
}else{
    
        header("Location: add_pro_etap1.php?error=error");
        exit();    
}
?>
