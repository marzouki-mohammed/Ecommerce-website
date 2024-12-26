<?php  
session_start();

// Inclure le fichier de connexion à la base de données
include "../php/db_connect.php";

// Vérifiez la connexion à la base de données
if (!isset($conn)) {
    echo "La connexion à la base de données n'est pas établie.";
    exit;
} 

// Check if the session cart is not set
if (!isset($_SESSION['cart'])) {
   echo 'pas cart';
}
$cart=$_SESSION['cart'];

$apiKey = 'YOUR_API_KEY';
        $url = "https://api.exchangerate-api.com/v4/latest/USD";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $usdToEur = $data['rates']['EUR'];
        $usdToMad = $data['rates']['MAD'];

$type='';
$taux=1;
$ty='&dollar;';
if(isset($_SESSION['type_Argant'])){
  $type=$_SESSION['type_Argant'];
}
if($type == 'usd'){
  $taux=1;
  $ty='&dollar;';
    
}elseif($type == 'eur'){
  $taux=$usdToEur;
  $ty='&euro;';

  
}elseif($type == 'mad'){
  $taux=$usdToMad;
  $ty='DH';
  
}else{
  $taux=1;
  $ty='&dollar;';
}




function getProductDiscount($conn, $productId) {
  // SQL pour récupérer la réduction la plus récente et valide pour un produit
  $sql = "
      SELECT compare_price 
      FROM products        
      WHERE id = :product_id     
  ";
  
  // Préparation de la requête
  $stmt = $conn->prepare($sql);
  
  // Exécution de la requête avec l'ID du produit passé en paramètre
  $stmt->execute(['product_id' => $productId]);
  
  // Récupération du résultat
  $discount = $stmt->fetch(PDO::FETCH_ASSOC);
  
  // Retourner les détails de la réduction (ou `null` s'il n'y a pas de réduction valide)
  return $discount;
}

function getProductComposerDiscount($conn, $componentsId){
  // SQL pour récupérer la réduction la plus récente et valide pour un produit
  $sql = "
      SELECT compare_price 
      FROM components         
      WHERE id = :COMP_id
  ";
  
  // Préparation de la requête
  $stmt = $conn->prepare($sql);
  
  // Exécution de la requête avec l'ID du produit passé en paramètre
  $stmt->execute(['COMP_id' => $componentsId]);
  
  // Récupération du résultat
  $discount = $stmt->fetch(PDO::FETCH_ASSOC);
  
  // Retourner les détails de la réduction (ou `null` s'il n'y a pas de réduction valide)
  return $discount;

}


function diviserNombre($nombre) {
  // Partie entière
  $partieEntiere = floor($nombre);
  
  // Partie décimale
  $partieDecimale = $nombre - $partieEntiere;
  
  return array('partie_entiere' => $partieEntiere, 'partie_decimale' => $partieDecimale);
}
function getProductReviews($conn, $productId) {
  try {
      // Requête SQL pour récupérer toutes les reviews d'un produit donné
      $sql = "
          SELECT r.id, r.user_id, r.product_id, r.components_id, r.rating, r.comment, r.created_at,
                 r.riagi, u.name   
          FROM reviews r
          INNER JOIN users u ON r.user_id = u.id
          WHERE r.product_id = :product_id
          ORDER BY r.created_at DESC
      ";
      
      // Préparation de la requête
      $stmt = $conn->prepare($sql);
      
      // Exécution de la requête avec l'ID du produit passé en paramètre
      $stmt->execute(['product_id' => $productId]);
      
      // Récupération des résultats
      $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      // Retourne toutes les reviews sous forme de tableau associatif
      return $reviews;
  } catch (Exception $e) {
      // Gérer les erreurs
      return ['error' => $e->getMessage()];
  }
}


function getProductComposerReviews($conn, $componentsId){
  // Requête SQL pour récupérer toutes les reviews d'un produit donné
  $sql = "
      SELECT r.id, r.user_id, r.product_id, r.components_id, r.rating, r.comment, r.created_at,
             r.riagi, u.name 
      FROM reviews r
      INNER JOIN users u ON r.user_id = u.id
      WHERE r.components_id = :COMP_id
      ORDER BY r.created_at DESC
  ";
  
  // Préparation de la requête
  $stmt = $conn->prepare($sql);
  
  // Exécution de la requête avec l'ID du produit passé en paramètre
  $stmt->execute(['COMP_id' => $componentsId]);
  
  // Récupération des résultats
  $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Retourne toutes les reviews sous forme de tableau associatif
  return $reviews;

}


function convertirDateEnEcriture($date) {
  $mois = array(
      1 => 'janvier', 
      2 => 'février', 
      3 => 'mars', 
      4 => 'avril', 
      5 => 'mai', 
      6 => 'juin', 
      7 => 'juillet', 
      8 => 'août', 
      9 => 'septembre', 
      10 => 'octobre', 
      11 => 'novembre', 
      12 => 'décembre'
  );
  
  $dateObj = new DateTime($date);
  $jour = $dateObj->format('j');
  $moisNum = (int)$dateObj->format('n');
  $annee = $dateObj->format('Y');

  $jourEcrit = ($jour == 1) ? '1er' : $jour;

  $dateEcriture = $jourEcrit . ' ' . $mois[$moisNum] . ' ' . $annee;

  return $dateEcriture;
}



//image
//image du produit 
function getSingleImagePerActiveVariant($conn, $productId) {
  // Définir la requête SQL avec condition sur les variantes actives et le produit ID
  $sql = "
        SELECT g.*
        FROM gallery g
        JOIN variant_options v ON g.product_variant_id = v.id
        WHERE v.active = TRUE
        AND v.product_id = :product_id;
  ";

  // Préparer la requête
  $stmt = $conn->prepare($sql);
  // Exécuter la requête
  $stmt->execute(['product_id'=>$productId]);

  // Récupérer les résultats
  $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $images;
}


// Initialisation des variables
$nbr = 0;
$id = 0;
$name = 'none';
$desc = 'none';
$prix = 0;
$prixv = 0;
$redu=0;
$reviews=0;
$imagvar='';

// Vérifier si un produit a été sélectionné

if (isset($_SESSION['id_p']) && !empty($_SESSION['id_p']) && is_int($_SESSION['id_p'])) {
    $nbr = 1;
    $id = intval($_SESSION['id_p']);  // Sécurisation de l'ID
    $sql_test="SELECT * FROM products WHERE id=?";
    $stm_test=$conn->prepare($sql_test);
    $stm_test->execute([$id]);
    $resulte=$stm_test->fetch();
    if(!$resulte || $resulte['active']==false){
        header("Location: ../../index.php");
        exit;
    }
}

if (isset($_SESSION['id_plien']) && !empty($_SESSION['id_plien']) && is_int($_SESSION['id_plien']) ) {
  $nbr = 1;
  $id = intval($_SESSION['id_plien']);  // Sécurisation de l'ID
}

// Vérifier si un composant a été sélectionné
if ((isset($_SESSION['id_pCompoder']) && !empty($_SESSION['id_pCompoder']) && is_int($_SESSION['id_pCompoder']))) {
    $nbr = 2;
    $id = intval($_SESSION['id_pCompoder']);  // Sécurisation de l'ID
    $sql_test="SELECT * FROM components  WHERE id=?";
    $stm_test=$conn->prepare($sql_test);
    $stm_test->execute([$id]);
    $resulte=$stm_test->fetch();
    if(!$resulte || $resulte['is_active']==false){
        header("Location: ../../index.php");
        exit;
    }
}

if(isset($_GET['id_roteur']) && !empty($_GET['id_roteur']) ){
        $idcom=intval($_GET['id_roteur']);
        $_SESSION['id_p']='id_p';
        $_SESSION['id_plien']='id_p';
        $_SESSION['id_pCompoder']=$idcom;
}

if(isset($_GET['idlvar']) && !empty($_GET['idlvar'])){
  
  $idvar=intval($_GET['idlvar']);
  $sqlvar="SELECT image  FROM gallery WHERE product_variant_id=? LIMIT 1";
  $stmvar=$conn->prepare($sqlvar);
  $stmvar->execute([$idvar]);
  $resvar=$stmvar->fetch();
  if($resvar){
    $imagvar=$resvar['image'];   
  }
}


// Si un produit a été sélectionné
if ($nbr == 1) {
    $sqlp = "SELECT * FROM products WHERE id = ?";
    $stmtp = $conn->prepare($sqlp);
    $stmtp->execute([$id]);
    $data = $stmtp->fetch();
    
    if ($data) {
        // Protection contre les injections XSS
        $name =$data['product_name'];  
        $desc = $data['description'];

        $prix = $data['price']*$taux;
        $prixv = $data['vente_price']*$taux;
    } 
    $réduction=getProductDiscount($conn, $id);
    if($réduction){
      $redu=$réduction['compare_price'];

    }
    $reviews=getProductReviews($conn, $id);
    




} 
// Si un composant a été sélectionné
elseif ($nbr == 2) {
    $sqlc = "SELECT * FROM components WHERE id = ?";
    $stmtc = $conn->prepare($sqlc);
    $stmtc->execute([$id]);
    $dataC = $stmtc->fetch();
    
    if ($dataC) {
        // Protection contre les injections XSS
        $name = $dataC['component_name'];
        $desc = $dataC['description'];
        $prix = $dataC['price']*$taux;
        $prixv = $dataC['vente_price']*$taux;

    }
    $réduction=getProductComposerDiscount($conn, $id);
    if($réduction){
      $redu=$réduction['compare_price'];

    }
    $reviews=getProductComposerReviews($conn, $id);
} 
// Si aucun produit ou composant n'a été sélectionné
else {
    header("Location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <style>
     
      .quantity-num{

        border:none;
        width: 100%;
        
        border-radius: 10px;
        background-color: #F7F8FD;
        font-size: 2em;
        
      }
      
            select {
              appearance: none; /* Removes default styling in most browsers */
              background-color: #f8f9fa; /* Light background color */
              border: 1px solid #ced4da; /* Border color */
              border-radius: 4px; /* Rounded corners */
              padding: 0.5rem 1rem; /* Padding inside the select box */
              font-size: 1rem; /* Font size */
              color: #495057; /* Font color */
              cursor: pointer; /* Cursor pointer on hover */
              width: 100%; /* Full width */
              box-sizing: border-box; /* Includes padding and border in the element's total width and height */
              margin-bottom: 1rem; /* Space below the select element */
            }

            /* Style the dropdown arrow */
            select::-ms-expand {
              display: none; /* Hide default dropdown arrow in Internet Explorer */
            }

            /* Optional: Style the dropdown arrow with a custom image */
            select::after {
              content: '▼'; /* Custom arrow character */
              font-size: 1.2rem; /* Font size for the arrow */
              position: absolute; /* Position the arrow */
              right: 0.5rem; /* Space from the right edge */
              top: 50%; /* Center vertically */
              transform: translateY(-50%); /* Adjust for vertical centering */
              pointer-events: none; /* Prevent interaction with the arrow */
            }

            /* Focus state styling */
            select:focus {
              border-color: #80bdff; /* Border color on focus */
              outline: none; /* Remove default outline */
              box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25); /* Box shadow on focus */
            }

            /* Style for option elements */
            option {
              padding: 0.5rem; /* Padding inside options */
              font-size: 1rem; /* Font size */
            }

            /* Container styling to ensure select fits properly */
            .form-group {
              position: relative; /* Required for the custom arrow */
              margin-bottom: 1rem; /* Space below the select element */
            }

            /* Additional styles for labels and buttons */
            label {
              font-weight: bold; /* Make the label text bold */
              margin-bottom: 0.5rem; /* Space below the label */
              display: block; /* Make label block to separate from select */
            }

            .btn {
              background-color: #007bff; /* Primary button color */
              color: #fff; /* Text color for buttons */
              border: none; /* Remove default border */
              border-radius: 4px; /* Rounded corners */
              padding: 0.5rem 1rem; /* Padding inside the button */
              font-size: 1rem; /* Font size */
              cursor: pointer; /* Pointer cursor on hover */
              transition: background-color 0.3s; /* Smooth background color transition */
            }

            .btn:hover {
              background-color: #0056b3; /* Darker color on hover */
            }

            .reviews{
              margin-top: 10px;
              display: flex;
              flex-direction: column;
              gap: 15px;
              margin-left: 10px;
              
            }
            /* From Uiverse.io by Yaya12085 */ 
            .prt{
              width: 95%;
            
            }
            .alert{
              padding: 15px;
              margin-bottom: 20px;
              border: 1px solid transparent;
              border-radius: 4px;
              color: #a94442; /* Text color */
              background-color: #f2dede; /* Background color */
              border-color: #ebccd1; /* Border color */
              font-size: 16px;
              font-family: Arial, sans-serif; /* Font styling */
            }
            .form {
            background-color: #fff;
            display: block;
            padding: 1rem;
            margin-top: 10px;
            width: 100%;
            max-width: 800px;
            border-radius: 0.5rem;
            box-shadow: 0px 187px 75px rgba(0, 0, 0, 0.01), 0px 105px 63px rgba(0, 0, 0, 0.05), 0px 47px 47px rgba(0, 0, 0, 0.09), 0px 12px 26px rgba(0, 0, 0, 0.1), 0px 0px 0px rgba(0, 0, 0, 0.1);

            }

            .form-title {
            font-size: 1.25rem;
            line-height: 1.75rem;
            font-weight: 600;
            text-align: center;
            color: #000;
            }

            .input-container {
            position: relative;
            }

            .input-container input, .form button {
            outline: none;
            border: 1px solid #e5e7eb;
            margin: 8px 0;
            }

            .input-container input {
            background-color: #fff;
            padding: 1rem;
            padding-right: 3rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            width: 97%;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .input-container textarea{
              background-color: #fff;
              padding: 1rem;
              padding-right: 3rem;
              font-size: 0.875rem;
              line-height: 1.25rem;
              width: 97%;
              border-radius: 0.5rem;
              box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);

            }

            .input-container span {
            display: grid;
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            padding-left: 1rem;
            padding-right: 1rem;
            place-content: center;
            }

            .input-container span svg {
            color: #9CA3AF;
            width: 1rem;
            height: 1rem;
            }

            .submit {
            display: block;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            padding-left: 1.25rem;
            padding-right: 1.25rem;
            background-color: #4F46E5;
            color: #ffffff;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            width: 100%;
            border-radius: 0.5rem;
            text-transform: uppercase;
            }




            .rating {
            display: inline-block;
            }

            .rating input {
            display: none;
            }

            .rating label {
            float: right;
            cursor: pointer;
            color: #ccc;
            transition: color 0.3s;
            }

            .rating label:before {
            content: '\2605';
            font-size: 30px;
            }

            .rating input:checked ~ label,
            .rating label:hover,
            .rating label:hover ~ label {
            color: hsl(29, 90%, 65%);
            transition: color 0.3s;
            }
            .cart-body-content




            .cardcument {
            width: 95%;
            height: fit-content;
            background-color: $card-bg;
            box-shadow: 0px 187px 75px rgba(0, 0, 0, 0.01), 0px 105px 63px rgba(0, 0, 0, 0.05), 0px 47px 47px rgba(0, 0, 0, 0.09), 0px 12px 26px rgba(0, 0, 0, 0.1), 0px 0px 0px rgba(0, 0, 0, 0.1);
            border-radius: 17px 17px 27px 27px;
            max-width: 480px;
            max-height: 404px;
            }

            .title {
            width: 100%;
            height: 50px;
            position: relative;
            display: flex;
            align-items: center;
            padding-left: 20px;
            border-bottom: 1px solid #f1f1f1;
            font-weight: 700;
            font-size: 13px;
            color: #47484b;
            }


            .title::after {
            content: '';
            width: 8ch;
            height: 1px;
            position: absolute;
            bottom: -1px;
            background-color: #47484b;
            }
            .cumant{
            max-height: 268px;
            overflow-y: scroll;
            -ms-scroll-chaining: none;
                overscroll-behavior: contain;

            }

            .comments {
            display: grid;
            grid-template-columns: 35px 1fr;
            gap: 20px;
            padding: 20px;

            }

            .comment-react {
            width: 35px;
            height: fit-content;
            display: grid;
            grid-template-columns: auto;
            margin: 0;
            background-color: #f1f1f1;
            border-radius: 5px;
            }

            .comment-react button {
            width: 35px;
            height: 35px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            border: 0;
            outline: none;
            }

            .comment-react button:after {
            content: '';
            width: 40px;
            height: 40px;
            position: absolute;
            left: -2.5px;
            top: -2.5px;
            background-color: #f5356e;
            border-radius: 50%;
            z-index: 0;
            transform: scale(0);
            }

            .comment-react button svg {
            position: relative;
            z-index: 9;
            }

            .comment-react button:hover:after {
            animation: ripple 0.6s ease-in-out forwards;
            }

            .comment-react button:hover svg {
            fill: #f5356e;
            }

            .comment-react button:hover svg path {
            stroke: #f5356e;
            fill: #f5356e;
            }

            .comment-react hr {
            width: 80%;
            height: 1px;
            background-color: #dfe1e6;
            margin: auto;
            border: 0;
            }

            .comment-react span {
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            font-size: 13px;
            font-weight: 600;
            color: #707277;
            }

            .comment-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 0;
            margin: 0;
            }

            .comment-container .user {
            display: grid;
            grid-template-columns: 40px 1fr;
            gap: 10px;
            }

            .comment-container .user .user-pic {
            width: 40px;
            height: 40px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f1f1f1;
            border-radius: 50%;
            }

            .comment-container .user .user-pic:after {
            content: '';
            width: 9px;
            height: 9px;
            position: absolute;
            right: 0px;
            bottom: 0px;
            border-radius: 50%;
            background-color: #0fc45a;
            border: 2px solid #ffffff;
            }

            .comment-container .user .user-info {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            gap: 3px;
            }

            .comment-container .user .user-info span {
            font-weight: 700;
            font-size: 12px;
            color: #47484b;
            }

            .comment-container .user .user-info p {
            font-weight: 600;
            font-size: 10px;
            color: #acaeb4;
            }

            .comment-container .comment-content {
            font-size: 12px;
            line-height: 16px;
            font-weight: 600;
            color: #5f6064;
            }
            .showcase-rating {
              display: flex;
              color: hsl(29, 90%, 65%);
              margin-bottom: 10px;
            }



            @keyframes ripple {
            0% {
              transform: scale(0);
              opacity: 0.6;
            }

            100% {
              transform: scale(1);
              opacity: 0;
            }
            }
            .has-scrollbar { padding-bottom: 5px; }

            .has-scrollbar::-webkit-scrollbar {
              width: 12px; /* for vertical scroll */
              height: 12px; /* for horizontal scroll */
            }

            .has-scrollbar::-webkit-scrollbar-thumb {
              background: transparent;
              border: 3px solid white;
              -webkit-border-radius: 20px;
                      border-radius: 20px;
            }

            .has-scrollbar:hover::-webkit-scrollbar-thumb { background: hsl(0, 0%, 90%); }

            .has-scrollbar::-webkit-scrollbar-thumb:hover { background: hsl(0, 0%, 80%); }

            @media (min-width: 824PX) {
              .reviews{
                padding-left: 15px;
              }

            }
            @media (min-width: 990PX) {
              .reviews{
                display: grid;
                grid-template-columns: 50% 50%;
              }
              .cumant{
                  max-height: 357px;
                  

                }

            }
            @media (min-width: 1024PX) {
              .reviews{
                padding-bottom: 20px;
                grid-template-columns: 40% 60%;
              }

            }

    </style>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop</title>
  

    <!--
      - favicon
    -->
    <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">
    
    <!-- BOOT STRAP LINK -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    
    <!-- CSS LINK -->
    <link rel="stylesheet" type="text/css" href="styles/style.css">
   
    <!-- FONT AWESOME LINK -->
    <script src="https://kit.fontawesome.com/4f133ed2d1.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  </head>
  <body>
   
    <div class="container">
     

      <div class="row">
       

        <nav class="navbar navbar-expand-md fixed-top">
          

          <div class="container-md navbar-container">



            <div class="mobile-nav">              
              <a href="../../index.php">
                <svg width="145"  height="60" xmlns="http://www.w3.org/2000/svg">
                    
                    <circle cx="25" cy="30" r="20" stroke="black" stroke-width="3" fill="lightgreen" />
                    <text x="25" y="37" font-size="20" font-family="Arial" text-anchor="middle" fill="white">N</text>
                    
                    
                    <text x="50" y="37" font-size="15" font-family="Arial" fill="green">Naturelle</text>
                    <text x="110" y="37" font-size="15" font-family="Arial" fill="darkgreen">Shop</text>
                </svg>          
              </a>
 

             <!-- <img src="images/logo.svg" alt="logo" class="header-img d-inline-block img-fluid">-->
            </div>



            
            
            
            <div class="navbar-cart" style="margin-right: 10px;">
              <button id="cartBtn" class="cart-qnty" aria-expanded="false" aria-label="Open cart" >
                <span id="cartIndicator" class="cartIndicator bg-accent bkg"></span>
                <i class="bi bi-cart3 shopping-cart-button"></i>
              </button>
              
            </div>

          </div>

          <div class="producstOnCart hide" id="cartPanel">
            <div class="cart-overlay"></div>
            <div class="top">
              <p>Cart</p>
            </div>

            <div id="cartBody" class="cart-body">
            <?php
                if(!empty($cart)){
                  echo '<div class="cart  has-scrollbar">';
                  $nbrdelet=0;
                  foreach($cart as $item){
                    echo'<div class="cart-body-content">';
                        if($item['type'] == 1){
                          $id_cart=(string)$item['id'];
                          $id_cart_var=intval($item['option'][$id_cart]);

                          $sql_cart_img="SELECT * FROM gallery WHERE product_variant_id=? LIMIT 1";
                          $stmt_cart_img=$conn->prepare($sql_cart_img);
                          $stmt_cart_img->execute([$id_cart_var]);
                          $data_cart_img = $stmt_cart_img->fetch();
                          if($data_cart_img){
                            echo "<img class='cart-img' src='../images/products/".htmlspecialchars($data_cart_img['image'])."' alt='shoes'>";
                          }else{
                            echo "<img class='cart-img' src='../images/products/cart.png' alt='shoes'>";
                            
                          }
                        }
                        if($item['type'] == 2){
                          $id_cart=intval($item['id']);
                          

                          $sql_cart_img="SELECT * FROM components WHERE id=?";
                          $stmt_cart_img=$conn->prepare($sql_cart_img);
                          $stmt_cart_img->execute([$id_cart]);
                          $data_cart_img = $stmt_cart_img->fetch();
                          if($data_cart_img){
                            echo "<img class='cart-img' src='../images/products/".htmlspecialchars($data_cart_img['image'])."' alt='shoes'>";
                          }else{
                            echo "<img class='cart-img' src='../images/products/cart.png' alt='shoes'>";
                            
                          }

                        }
                        echo '<div class="product-description">';
                             echo "<h5 class='cart-product-title'>".htmlspecialchars($item['title'])."</h5>";
                             echo '<div class="price-info">';
                                  echo "<span>".$ty."".htmlspecialchars($item['price'])."</span>";
                                  echo "<span>&times;</span>";
                                  echo "<span id='amt'>".htmlspecialchars($item['quantity'])."</span>";
                                  $prixTotal=floatval($item['price'])*intval($item['quantity']);
                                  echo "<span id='total'>".$ty."".htmlspecialchars($prixTotal)."</span>";
                             echo '</div>';
                        echo '</div>';
                        echo '<form action="php/delet_cart_item.php" method="post" enctype="multipart/form-data" >';
                              echo "<input type='hidden' name='ifdelet_cart' value='".htmlspecialchars($nbrdelet)."'>";
                              echo '<button type="submit">';
                                    echo '<i  class="fas fa-trash-alt"></i>';
                              echo '</button>';
                        echo '</form>';

                         
                    echo'</div>';
                    $nbrdelet++;
                  }
                  echo '</div>';
                }else{
                  echo '<div class="empty-cart-content">
                          <div></div>
                          <p class="cart-empty">Your cart is empty</p>
                          <div></div>
                        </div>';

                }
                
                
            ?>
            <!--
            <div class="cart-body-content">
              <img class="cart-img" src="images/image-product-1-thumbnail.jpg" alt="shoes">
              <div class="product-description">
                <h5 class="cart-product-title">Fall Limited Edition Sneakers</h5>
                <div class="price-info">
                  <span>$125.00</span>
                  <span>&times;</span>
                  <span id="amt">3</span>
                  <span id="total">$375</span>
                </div>
              </div>
              <i id="trash" aria-label="Remove product from cart" class="fas fa-trash-alt"></i>
            </div>
             -->
             <button id="checkout" class="btn checkout"><a href="loginPayment.php">BUY</a></button>
              
            </div>
          </div>

        </nav>
        
        <section class="container product" style="margin-top: 180px;">
          
          <div class="row">

          <div class="col-lg-6">
            <div class="main-images-container" style="padding: 25% 13%;">
              
              <?php if($nbr == 1): ?>
                <?php $image_produit = getSingleImagePerActiveVariant($conn, $id); ?>
                <div class="product-image-container">
                  <img id="main-img" class="img-fluid product-image" 
                      src="../images/products/<?= !empty($imagvar) ? htmlspecialchars($imagvar) : htmlspecialchars($image_produit[0]['image']); ?>" 
                      alt="image produit">
                </div>

                <div class="previous arrow">
                  <i class="fa fa-angle-left"></i>
                </div>

                <div class="next arrow">
                  <i class="fa fa-angle-right"></i>
                </div>

                <div class="thumbnail-container has-scrollbar">
                  <?php foreach($image_produit as $index => $img_option): ?>
                    <div class="col-3">
                      <div class="thumbnail-image-container <?= $index === 0 ? 'active' : ''; ?>">
                        <img src="../images/products/<?= htmlspecialchars($img_option['image']); ?>" alt="thumbnail">
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              
              <?php if($nbr == 2): ?>
                <?php
                $sqlimgcomposer = "SELECT image, id FROM components WHERE id=?";
                $stmimgcomposer = $conn->prepare($sqlimgcomposer);
                $stmimgcomposer->execute([$id]);
                $dataimgcomposer = $stmimgcomposer->fetch();
                ?>
                
                <div class="product-image-container">
                  <img id="main-img" class="img-fluid product-image" 
                      src="../images/products/<?= !empty($imagvar) ? htmlspecialchars($imagvar) : htmlspecialchars($dataimgcomposer['image']); ?>" 
                      alt="image produit">
                </div>

                <div class="previous arrow">
                  <i class="fa fa-angle-left"></i>
                </div>

                <div class="next arrow">
                  <i class="fa fa-angle-right"></i>
                </div>

                <div class="thumbnail-container has-scrollbar">
                  <div class="col-3">
                    <div class="thumbnail-image-container active">
                      <img src="../images/products/<?= htmlspecialchars($dataimgcomposer['image']); ?>" alt="thumbnail">
                    </div>
                  </div>
                  
                  <?php
                  $sqlimgcomposer2 = "SELECT product_id FROM product_composer WHERE component_id = ?";
                  $stmimgcomposer2 = $conn->prepare($sqlimgcomposer2);
                  $stmimgcomposer2->execute([$dataimgcomposer['id']]);
                  $dataimgcomposer2 = $stmimgcomposer2->fetchAll(PDO::FETCH_ASSOC);

                  foreach ($dataimgcomposer2 as $img2):
                    $id_provo = getSingleImagePerActiveVariant($conn, $img2['product_id']);
                    if ($id_provo):
                      foreach ($id_provo as $imgoption):
                  ?>
                    <div class="col-3">
                      <div class="thumbnail-image-container">
                        <img src="../images/products/<?= htmlspecialchars($imgoption['image']); ?>" alt="thumbnail">
                      </div>
                    </div>
                  <?php
                      endforeach;
                    endif;
                  endforeach;
                  ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

            
            <div class="col-lg-6">
              <div class="product-information-container">

                <div class="product-header">
                  <p class="header-title">NaturelleShop Company</p>
                  <h3 class="product-name"><?php echo html_entity_decode($name) ;?></h3>
                </div>

                <div class="product-body">
                  
                <p class="product-info has-scrollbar">
                  <?php 
                  
                  echo html_entity_decode($desc)?>
                  <?php 
                  if ($nbr == 2) {
                      $ss = "SELECT 
                                p.id, p.product_name 
                            FROM 
                                product_composer pc
                            JOIN 
                                products p ON pc.product_id = p.id
                            WHERE 
                                pc.component_id = ?";
                      $stt = $conn->prepare($ss);                        
                      $stt->execute([$id]);
                      $daa = $stt->fetchAll(PDO::FETCH_ASSOC);

                      if ($daa) {
                          
                          foreach ($daa as $lien) {
                            echo '<br>';
                            echo "<a href='php/product_lien_pross.php?idlien=" . htmlspecialchars($lien['id']) . "' class='submenu-title'>" . html_entity_decode($lien['product_name']) . "</a>";
                            $vvsql="SELECT * FROM variant_options WHERE product_id=? AND active= TRUE ";
                            $vvstm=$conn->prepare($vvsql);
                            $vvstm->execute([$lien['id']]);
                            $dvv=$vvstm->fetchAll(PDO::FETCH_ASSOC);
                            if($dvv){
                              $i=1;
                              
                              foreach($dvv as $variant){
                                echo '<br>';
                                echo "<a href='productpage.php?idlvar=" . htmlspecialchars($variant['id']) . "'><strong style='color:#141414;'>Option " . $i . ": </strong>" . html_entity_decode($variant['title']) . "</a>";
                                $i++;
                              }
                              echo '<br>';
                            }



                          }
                          
                      }
                  }

                  if($nbr == 1){
                    
                    $vvsql="SELECT * FROM variant_options WHERE product_id=? AND active= TRUE ";
                    $vvstm=$conn->prepare($vvsql);
                    $vvstm->execute([$id]);
                    $dvv=$vvstm->fetchAll(PDO::FETCH_ASSOC);
                    if($dvv){
                      $i=1;
                      
                      foreach($dvv as $variant){
                        echo '<br>';
                        echo "<a  href='productpage.php?idlvar=" . htmlspecialchars($variant['id']) . "'><strong style='color:#141414;'>Option " . $i . ": </strong>" . html_entity_decode($variant['title'])."</a>";
                        $i++;
                      }
                    }

                   

                  }
                  ?>
                </p>
    
                  
                
                  <div class="price-container">
                    <div class="new-price-container">
                      <h3 class="new-price">
                        <?php echo $ty ;?><span> <?php 
                        
                                   echo $prixv ;
                                   ?> </span>
                      </h3>
                      <p class="reduced-percent"><?php echo $redu ;?>%</p>
                    </div>
                    <span class="old-price"><?php echo $ty ;?><?php echo $prix ;?></span>
                  </div>
                  <form action="php/cart.php" method="post" enctype="multipart/form-data"  >
                  <div class="product-button">
                    <div class="row">
                      <?php if(isset($_GET['errorcart'])){ ?>
                                <div class="alert" role="alert">
                                    <?php echo $_GET['errorcart']; ?>
                                </div>
                      <?php } ?>
                      <?php
                          if($nbr == 1) {
                            $sqlcart="SELECT * FROM variant_options WHERE product_id=? AND active= TRUE ";
                            $stmtcart=$conn->prepare($sqlcart);
                            $stmtcart->execute([$id]);
                            $resultecart=$stmtcart->fetchAll(PDO::FETCH_ASSOC);
                            if($resultecart){
                              echo "<input type='hidden' name='idprokeycart' value='".htmlspecialchars($id)."'>";
                              echo '<label for="products">Choose a product variant:</label>';
                              echo '<select id="products" name="product">';
                              foreach($resultecart as $cartvar){
                                 echo "<option value='".htmlspecialchars($cartvar['id'])."'>".html_entity_decode($cartvar['title'])."</option>";
                              }
                              echo '</select>';

                            }
                            
                            
                          }

                          if($nbr == 2){
                              $sqlcomcart = "SELECT 
                                        p.id, p.product_name 
                                    FROM 
                                        product_composer pc
                                    JOIN 
                                        products p ON pc.product_id = p.id
                                    WHERE 
                                        pc.component_id = ?";
                              $stmtcompcart = $conn->prepare($sqlcomcart);                        
                              $stmtcompcart->execute([$id]);
                              $datacompcart = $stmtcompcart->fetchAll(PDO::FETCH_ASSOC);

                              if ($datacompcart) {
                                  $i=1;
                                  echo '<p>Choose a product variant: </p>';
                                  foreach ($datacompcart as $lien) {
                                    echo "<input type='hidden' name='idprokeycart".$i."' value='".htmlspecialchars($lien['id'])."'>";
                                    $sqlvarcomcart="SELECT * FROM variant_options WHERE product_id=? AND active= TRUE ";
                                    $stmtcompvarcart=$conn->prepare($sqlvarcomcart);
                                    $stmtcompvarcart->execute([$lien['id']]);
                                    $dcartvarcomp=$stmtcompvarcart->fetchAll(PDO::FETCH_ASSOC);
                                    if($dcartvarcomp){ 
                                      
                                      echo "<select  name='productCopm".$i."'>";                        
                                      foreach($dcartvarcomp as $variant){
                                            echo "<option value='".htmlspecialchars($variant['id'])."'>".html_entity_decode($variant['title'])."</option>";                                  
                                      }
                                      echo '</select>'; 
                                      $i++;
                                      
                                    }
                                  }
                                  
                              }
                          }                       

                      ?>

                      <input type="hidden" name="typpro" value=<?php echo $nbr ;?>>
                    

 
                      <input type="hidden" name="idprocart" value=<?php echo $id ;?>>
                      
                      <input type="hidden" name="prixcat" value=<?php echo $prixv ;?>>
                      <div class="col-xl-4 col-md-6">
                      <div id="quantity-button" class="quantity-button">                        
                          <input id="qty" name="qty" class="quantity-num" type="number" value="1" min="1"/>                          
                      </div>
                      
                      
                      </div>
                
                      <div class="col-xl-8 col-md-6">
                        <button type="submit" id="checkout" class="btn cart-button btn-block">
                          <i class="bi bi-cart3 cart-icon"></i>Add to Cart
                        </button>
                      </div>
                    </div>
                  </div>
                  </form>
                </div>

              </div>
            </div>
          </div>

          <!-- LightBox Overlay -->
          <div class="lightbox-overlay hidden">
            <div class="overlay-close">
              <i id="overlayClose" class="bi bi-x xmark"></i>
            </div>
          </div>

        </section>

      </div>

      
      <div class="reviews">
       
        <div class="prt">
         
         <?php if(isset($_GET['error'])){ ?>
                    <div class="alert" role="alert">
                        <?php echo $_GET['error']; ?>
                    </div>
          <?php } ?>

          <form class="form" action="php/submit_review.php" method="POST" enctype="multipart/form-data" >
                <p class="form-title">What is your review?</p>               
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($nbr); ?>">
                <input type="hidden" name="idp" value="<?php echo htmlspecialchars($id); ?>">
                <div class="input-container">
                  <input name="email_filed" placeholder="Enter email" type="email" value="exemple@gmail.com" required>
                  <span>
                    <svg stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
                    </svg>
                  </span>
                </div>
                
                <div class="input-container">
                  <input type="password"  name="password_filed" placeholder="Enter password"  required>
                </div>

                <div class="input-container">
                   <textarea name="comment_filed" placeholder="Enter your review" rows="1"  required></textarea>
                </div>

                <div class="rating">
                  <input value="5" name="rating" id="star5" type="radio" >
                  <label for="star5" title="5 stars"></label>
                  <input value="4" name="rating" id="star4" type="radio" >
                  <label for="star4" title="4 stars"></label>
                  <input value="3" name="rating" id="star3" type="radio" >
                  <label for="star3" title="3 stars"></label>
                  <input value="2" name="rating" id="star2" type="radio" >
                  <label for="star2" title="2 stars"></label>
                  <input value="1" name="rating" id="star1" type="radio" >
                  <label for="star1" title="1 star"></label>
                </div>

                <button class="submit" type="submit">Share</button>  
          </form>


        </div>
     
        <div class="cardcument">
          <span class="title">Comments</span>
          <div class="cumant has-scrollbar">
              <?php                  
                 if(is_array($reviews) && !empty($reviews)){
                  foreach($reviews as $rev){
                    echo '<div class="comments">';
                         echo '<div class="comment-react">';
                              echo '<form action="php/rating.php" method="POST" enctype="multipart/form-data" >';
                                    echo "<input type='hidden' name='id_rating' value='" . htmlspecialchars($rev['id']) . "'>";
                                    echo '<button type="submit">';
                                          echo '<svg fill="none" viewBox="0 0 24 24" height="16" width="16" xmlns="http://www.w3.org/2000/svg">
                                                  <path fill="#707277" stroke-linecap="round" stroke-width="2" stroke="#707277" d="M19.4626 3.99415C16.7809 2.34923 14.4404 3.01211 13.0344 4.06801C12.4578 4.50096 12.1696 4.71743 12 4.71743C11.8304 4.71743 11.5422 4.50096 10.9656 4.06801C9.55962 3.01211 7.21909 2.34923 4.53744 3.99415C1.01807 6.15294 0.221721 13.2749 8.33953 19.2834C9.88572 20.4278 10.6588 21 12 21C13.3412 21 14.1143 20.4278 15.6605 19.2834C23.7783 13.2749 22.9819 6.15294 19.4626 3.99415Z"></path>
                                                </svg>';
                                    echo '</button>';
                              echo '</form>';
                         
                              echo '<hr>';
                              echo "<span>".$rev['riagi']."</span>";                                                      
                         echo '</div>';
                         echo '<div class="comment-container">';
                              echo '<div class="user">';
                                    echo '<div class="user-pic">';
                                          echo '<svg fill="none" viewBox="0 0 24 24" height="20" width="20" xmlns="http://www.w3.org/2000/svg">
                                                  <path stroke-linejoin="round" fill="#707277" stroke-linecap="round" stroke-width="2" stroke="#707277" d="M6.57757 15.4816C5.1628 16.324 1.45336 18.0441 3.71266 20.1966C4.81631 21.248 6.04549 22 7.59087 22H16.4091C17.9545 22 19.1837 21.248 20.2873 20.1966C22.5466 18.0441 18.8372 16.324 17.4224 15.4816C14.1048 13.5061 9.89519 13.5061 6.57757 15.4816Z"></path>
                                                  <path stroke-width="2" fill="#707277" stroke="#707277" d="M16.5 6.5C16.5 8.98528 14.4853 11 12 11C9.51472 11 7.5 8.98528 7.5 6.5C7.5 4.01472 9.51472 2 12 2C14.4853 2 16.5 4.01472 16.5 6.5Z"></path>
                                                </svg>';
                                    echo '</div>';
                                    echo '<div class="user-info">';
                                         echo "<span>".$rev['name']."</span>";
                                         $date=convertirDateEnEcriture($rev['created_at']);
                                         echo "<p>".$date."</p>";
                                    echo '</div>';                                  
                              echo '</div>';
                              echo "<p class='comment-content'>".html_entity_decode($rev['comment'])."</p>";
                              echo "<div class='showcase-rating'>";
                              $arrnew=diviserNombre($rev['rating']);
                                              for ($i = 1; $i <= 5 ; $i++) {
                                                if($i<=$arrnew['partie_entiere']){
                                                  echo "<ion-icon name='star'></ion-icon>";
                                                }elseif($i==$arrnew['partie_entiere']+1){
                                                  if ($arrnew['partie_decimale'] > 0.50) {
                                                    echo "<ion-icon name='star-half-outline'></ion-icon>";
                                                  }else{
                                                    echo "<ion-icon name='star-outline'></ion-icon>";
                                                  }

                                                }else{
                                                  echo "<ion-icon name='star-outline'></ion-icon>";

                                                }
                                }
                                echo '</div>';
                              
                         echo '</div>';
                         

                         
                    echo '</div>';
                  }
                 }
              ?>            
          </div>

          
        </div>

      </div>
        
      
      <footer id="Contact" class="container-fluid">
        <div class="row">
          
          <div class="col-sm footer-text" style="gap: 5px;">
            <a href="#" class="bi bi-linkedin" aria-hidden="true" target="_blank"></a>
            <a href="#"  aria-hidden="true" target="_blank">
              <ion-icon name="logo-twitter"></ion-icon>
            </a>
            <a href="#"><ion-icon name="logo-facebook"></ion-icon></a> 
            <a href="#" class="social-link">
              <ion-icon name="logo-instagram"></ion-icon>
            </a>
          </div>
        </div>
      </footer>

    </div>






    
    <!-- JQUERY & BOOTSTRAP.JS CDNS FILES -->        
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
       <!--
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- My js file link -->
    <script src="script/scripts.js"></script>
    <script src="script/shopping-cart.js"></script>  
    <!-- <script src="script/toast.js"></script>   -->
     <script>
      /*
         // Récupérer la valeur PHP dans une variable JavaScript
    var idrot = <?php echo json_encode($idretcom); ?>;

    function isIntNonEmpty(value) {
        // Vérifie d'abord si la valeur est un nombre entier
        if (Number.isInteger(value) && value !== 0) {
            return true;
        }
        return false;
    }

    if (isIntNonEmpty(idrot)) {
        // Gérer l'événement de navigation en arrière
        window.addEventListener('popstate', function(event) {
            // Redirection avec la valeur correcte
            window.location.href = "./php/retourpross.php?idrot=" + encodeURIComponent(idrot);
        });
    }*/

     </script>
     
  
  </body>
</html>