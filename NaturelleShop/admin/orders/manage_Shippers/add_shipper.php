<?php

include "../../../php/db_connect.php";

  if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
  }
if($_SERVER['REQUEST_METHOD'] === 'POST' 
&& isset($_POST['shipper_name']) && !empty($_POST['shipper_name'])
&& isset($_POST['email']) && !empty($_POST['email'])
&& isset($_POST['phone']) && !empty($_POST['phone'])
&& isset($_POST['adress']) && !empty($_POST['adress'])
&& isset($_POST['country']) && !empty($_POST['country'])
&& isset($_POST['city']) && !empty($_POST['city'])
){
    // Récupération des données du formulaire
    $shipper_name = $_POST['shipper_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $adress = $_POST['adress'];
    $country = $_POST['country'];
    $city = $_POST['city'];

    // Requête d'insertion
    $sql = 'INSERT INTO shippers (shipper_name, email, phone, adress, country, city) 
            VALUES (:shipper_name, :email, :phone, :adress, :country, :city)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':shipper_name' => $shipper_name,
        ':email' => $email,
        ':phone' => $phone,
        ':adress' => $adress,
        ':country' => $country,
        ':city' => $city
    ]);

    // Redirection ou message de succès
    header('Location: ajouter.php'); // Redirection après l'ajout
    exit;
}else{
    // Redirection ou message de succès
    header('Location: ajouter.php?error=invalid form'); // Redirection après l'ajout
    exit;
}

?>
