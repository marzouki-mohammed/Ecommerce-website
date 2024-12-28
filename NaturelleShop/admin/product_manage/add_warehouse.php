<?php
session_start();
include "../../php/db_connect.php";

// Vérification de la connexion à la base de données
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" 
&& isset($_POST['name']) && !empty($_POST['name'])
&& isset($_POST['location']) && !empty($_POST['location'])

) {
    // Récupérer les valeurs du formulaire
    $name = htmlspecialchars($_POST['name']);
    $location = htmlspecialchars($_POST['location']);

    // Requête SQL pour insérer une nouvelle entrée dans la table warehouses
    $sql = "INSERT INTO warehouses (name, location) VALUES (?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // Exécuter la requête avec les valeurs du formulaire
    if ($stmt->execute([$name, $location])) {
        header("Location: Add_Warehouses.php?success=Warehouse added successfully.");
        exit();

    } else {
        header("Location: Add_Warehouses.php?error=Error adding warehouse.");
        exit();

    }
}else{
    header("Location: Add_Warehouses.php");
    exit();
}
?>
