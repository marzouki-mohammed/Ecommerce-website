<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ifdelet_cart'])) {
    // Récupérer la valeur transmise et la décrémenter de 1
    $indexToDelete = (int)$_POST['ifdelet_cart'];

    // Vérifier si l'index existe dans le tableau $_SESSION['cart']
    if (isset($_SESSION['cart'][$indexToDelete])) {
        // Supprimer l'élément du panier à cet index
        unset($_SESSION['cart'][$indexToDelete]);

        // Réindexer le tableau pour ne pas laisser de trous dans les clés
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    
    // Rediriger vers la dernière page visitée
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: ../../../index.php'); // Redirection par défaut si HTTP_REFERER n'est pas défini
    }
    exit;
}
?>
