<?php
   session_start();

   include "../php/db_connect.php";
   
   // Vérifiez la connexion à la base de données
   if (!isset($conn)) {
       echo "La connexion à la base de données n'est pas établie.";
       exit;
   }
   if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Initialiser un panier vide pour un nouveau visiteur
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


  
   if(isset($_SESSION['id_cat']) && isset($_SESSION['id_pro'])){
       $id_categorie=$_SESSION['id_cat'];
       $id_produit=$_SESSION['id_pro'];

   }else{
    header("Location: ../../index.php");
    exit;
   }
   function calculerMoyenneRating($productId, $conn) {
    // Préparer la requête SQL pour récupérer les évaluations du produit
    $sql = 'SELECT AVG(rating) AS moyenne_rating FROM reviews WHERE product_id = :idpo';

    // Préparer et exécuter la requête
    $stmt = $conn->prepare($sql);
    $stmt->execute(['idpo'=>$productId]);

    // Récupérer la moyenne des évaluations
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si la requête a retourné un résultat
    if ($result && $result['moyenne_rating'] !== null) {
        return round($result['moyenne_rating'], 2); // Arrondir la moyenne à 2 décimales
    } else {
        return 0; // Retourner 0 si aucune évaluation n'est trouvée
    }
   }
   function diviserNombre($nombre) {
    // Partie entière
    $partieEntiere = floor($nombre);
    
    // Partie décimale
    $partieDecimale = $nombre - $partieEntiere;
    
    return array('partie_entiere' => $partieEntiere, 'partie_decimale' => $partieDecimale);
  }

  

?>

<!DOCTYPE html>
<html lang="en">
  <head>
 
    <style>
 

            .cart-img {
              width: 60px;
              height: 60px;
              border-radius: 5px;
              padding-top: 25px;
            }
            .cart{
              max-height: calc(100vh - 140px);
              overflow-x: hidden;
              overflow-y: scroll;
            }




            .empty-cart-content {
              padding: 30px 10px;
              display: -webkit-box;
              display: -ms-flexbox;
              display: flex;
              -ms-flex-line-pack: center;
                  align-content: center;
              -webkit-box-pack: center;
                  -ms-flex-pack: center;
                      justify-content: center;
            }

            .empty-cart-content .cart-empty {
              color: #68707D;
              text-align: center;
              font-weight: bold;
            }

            .cart-body-content {
              display: -webkit-box;
              display: -ms-flexbox;

              -webkit-box-align: center;
                  -ms-flex-align: center;
                      align-items: center;
              -webkit-box-pack: justify;
                  -ms-flex-pack: justify;
                    
              padding: 10px 20px;
              max-height: 189px;
              gap:15px;
            }

            .cart-body-content .cart-product-title {
              color: #e1e1e1;
                font-size: 1.1em;
                max-width: 150px;
                margin-bottom: 0;
                text-align: left; 
            }

            .cart-body-content .price-info {
              display: -webkit-box;
              display: -ms-flexbox;
              display: flex;
              -webkit-box-align: baseline;
                  -ms-flex-align: baseline;
                      align-items: baseline;
            }

            .cart-body-content .price-info span {
              padding-right: 5px;
              text-align: center;
            }

            .cart-body-content .price-info #total {
              font-weight: bold;
            }

            .cart-body-content .fa-trash-alt {
              font-size: 1em;
              color: #B6BCC8;
            }

            .cart-body-content .fa-trash-alt:hover {
              color: #FF7D1A;
              cursor: pointer;
            }

            .checkout {
              display: block;
              width: 90%;
              color: #FFFFFF;
              background-color: #FF7D1A;
              cursor: pointer;
              font-weight: bold;
              font-size: 1rem;
              padding: 12px 5px;
              border-radius: 10px;
              margin: 10px auto;
              position: absolute;
                left: 20px;
                bottom: 4px;
            }

            .checkout:hover {
              -webkit-box-shadow: 0 2px 15px #FF7D1A;
                      box-shadow: 0 2px 15px #FF7D1A;
              color: #FFFFFF;
            }

            @media (min-width: 400px) {
              .cart-body-content .cart-product-title {
              
                max-width: 250px;
              
              }
            }
            @media (min-width: 576px) {
              .cart-body-content .cart-product-title {
              
                max-width: 200px;
              
              }
            }
            @media (min-width: 800px) {
              .cart-body-content .cart-product-title {
              
                max-width: 250px;
              
              }
            }
            @media (min-width: 1000px) {
              .cart-body-content .cart-product-title {
              
                max-width: 150px;
              
              }
              .checkout {
                width: 340px;
              }
              
            }
            .forme1 button{
              height: 30px;
                border: none;
                background: none;
            }
            .btn_delet span{
              color:#ff7373;
            }


    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>NaturelleShop</title>
  

  <!--
    - favicon
  -->
  <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">


  


    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    
    <link rel="stylesheet" href="assets/css/StylesSerch.css">

    
  </head>
  <body>






    <div class="grid-container">

      <!-- Header -->
      <header class="header">
       <div class="group">
            <form id="searchForm" class="form_serch" action="../php/serch_pross.php" method="POST" enctype="multipart/form-data" class="search_form">                   
                    <input name="search_input" placeholder="Search" type="search" class="input">
                    <button class="btn_search">
                        <svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g></svg>
                    </button> 
            </form>
        </div>
        <div>
            <div class="menu-icon sider_menu" onclick="openSidebar()">
                <span class="material-icons-outlined">shopping_cart</span> 
            </div>
          
        </div>
       
       
        
    
      </header>
    
      <!-- End Header -->

      <!-- Sidebar -->
      <aside id="sidebar">
          <div class="sidebar-title">
            <div class="sidebar-brand">
              <span class="material-icons-outlined">shopping_cart</span> Cart
            </div>
            <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
          </div>
          <hr>

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
                             echo "<h5 class='cart-product-title'>".html_entity_decode($item['title'])."</h5>";
                             echo '<div class="price-info">';
                                  echo "<span>".$ty."".htmlspecialchars($item['price'])."</span>";
                                  echo "<span>&times;</span>";
                                  echo "<span id='amt'>".htmlspecialchars($item['quantity'])."</span>";
                                  $prixTotal=floatval($item['price'])*intval($item['quantity']);
                                  echo "<span id='total'>".$ty."".htmlspecialchars($prixTotal)."</span>";
                             echo '</div>';
                        echo '</div>';
                        

                        echo '<form class="forme1" action="php/delet_cart_item.php" method="post" enctype="multipart/form-data" >';
                              echo "<input type='hidden' name='ifdelet_cart' value='".htmlspecialchars($nbrdelet)."'>";
                              echo '<button type="submit" class="btn_delet">';
                                    echo '<span class="material-icons-outlined">delete</span>';
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
            
            <button id="checkout" class="btn checkout"><a href="loginPayment.php">BUY</a></button>
              
          </div>
          


        



        
      </aside>
      <!-- End Sidebar -->
      
      <!-- Main -->
      <main class="main-container" id="main">
      
            <?php if(isset($_GET['error'])){ ?>
                    <div class="alert" role="alert" >
                        <?php echo $_GET['error']; ?>
                    </div>
            <?php } ?>
           
      

            <div class="product-main">
            
            <!--contenu-->
            <div class="product-grid">
              <?php

                    $id_aff = [];

                    // Function to fetch product information and display it
                    function displayProductInfo($conn, $productId) {
                        // Fetch product info
                        global $taux;
                        global $ty;
                        $sql_info_pro = "SELECT * FROM products WHERE id = ?";
                        $stm_info_pro = $conn->prepare($sql_info_pro);
                        $stm_info_pro->execute([$productId]);
                        $produit = $stm_info_pro->fetch();

                        // Fetch product images
                        $sql_image_produit = "SELECT g.image AS image_path
                                              FROM variant_options v
                                              INNER JOIN gallery g ON v.id = g.product_variant_id
                                              WHERE v.product_id = :product_id
                                              ORDER BY g.is_thumbnail DESC, g.created_at ASC
                                              LIMIT 2";
                        $stm_image_produit = $conn->prepare($sql_image_produit);
                        $stm_image_produit->execute(['product_id' => $productId]);
                        $resulte_image_produit = $stm_image_produit->fetchAll(PDO::FETCH_ASSOC);

                        $image_default = $resulte_image_produit[0]['image_path'] ?? null;
                        $image_hover = $resulte_image_produit[1]['image_path'] ?? null;

                        

                        // Display product information
                        echo "<div class='showcase'>";
                        echo "<div class='showcase-banner'>";

                        if ($image_default) {             
                            echo "<img src='../images/products/" . htmlspecialchars($image_default) . "' alt='Product Image' width='300' class='product-img default'>";
                            if ($image_hover) {
                                echo "<img src='../images/products/" . htmlspecialchars($image_hover) . "' alt='Product Image' width='300' class='product-img hover'>";
                            }
                        } else {
                            $default_image = "default.jpg";  // Provide a path for the default image
                            echo "<img src='../images/products/" . htmlspecialchars($default_image) . "' alt='Product Image' width='300' class='product-img default'>";
                        }
                        
                        if ($produit['compare_price'] > 0) {
                            echo "<p class='showcase-badge'>{$produit['compare_price']}%</p>";
                        }

                        echo "<div class='showcase-actions'>";
                                
                              echo "<form action='../php/product_pross.php' method='POST' class='showcase-actions'>";
                              echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($produit['id']) . "'>";
                              echo "<button type='submit'  class='btn-action' style='background: none; border: none; cursor: pointer;'>";
                              echo "<ion-icon name='eye-outline'></ion-icon>";
                              echo "</button>";
                              echo "</form>";

                              
                              echo "</div>";
                        echo "</div>";

                        echo "<div class='showcase-content'>";
                        echo "<form action='../php/product_pross.php' method='POST' class='showcase-category'>";
                        echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($produit['id']) . "'>";
                        echo "<button type='submit' style='background: none; border: none; cursor: pointer;'>";
                        echo html_entity_decode($produit['product_name']);
                        echo "</button>";
                        echo "</form>";

                        echo "<div class='showcase-rating'>";
                                $new=calculerMoyenneRating($productId, $conn);
                                                                  $arrnew=diviserNombre($new);
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
                              echo "</div>";
                        echo "<div class='price-box'>";
                                $pricev=$produit['vente_price']*$taux;
                                $price=$produit['price']*$taux;
                                echo "<p class='price'>".$ty."" . htmlspecialchars($pricev) . "</p>";
                                echo "<del >".$ty."" . htmlspecialchars($price) . "</del>";
                        echo  "</div>";
                        echo "</div>";
                        echo "</div>";
                    }

                    // Loop through each category
                    foreach ($id_categorie as $nbrcat) {
                      $sql_categorie = "SELECT p.id 
                                        FROM products p
                                        INNER JOIN product_categories pc ON p.id = pc.product_id
                                        INNER JOIN categories c ON pc.category_id = c.id
                                        WHERE c.id = :category_id 
                                          AND p.active = TRUE";

                        $stm_categorie = $conn->prepare($sql_categorie);
                        $stm_categorie->execute(['category_id' => $nbrcat['id']]);
                        $data_categorie = $stm_categorie->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($data_categorie as $data) {
                            if (!in_array($data['id'], $id_aff)) {
                                $id_aff[] = $data['id'];
                                displayProductInfo($conn, $data['id']);
                            }
                        }
                    }

                    // Display products not in the category-based list
                    if(is_array($id_produit)){
                        foreach ($id_produit as $nbrpro) {
                            
                                if (!in_array($nbrpro['id'], $id_aff)) {
                                    displayProductInfo($conn, $nbrpro['id']);
                                }
                            
                        }
                    }

              ?>
            </div>
        
        
      </main>
      <!-- End Main -->
    </div>

   
    <!--
        - custom js link
    -->
     
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="./assets/js/ScriptSerch.js"></script>
    <script>
            // NOTIFICATION 
      

function checkForNewProduct() {
    fetch('../php/check_new_product.php')
    .then(response => response.json())
    .then(data => {
        console.log('Response from server:', data);
        if (data && !data.error && data.product_id) {
            const dismissedProductId = sessionStorage.getItem('dismissedProductId');

            if (dismissedProductId !== data.product_id.toString()) {
                const notification = document.createElement('div');
                notification.classList.add('notification-toast');
                notification.setAttribute('data-toast', '');

                // Use the image returned by the server or a default image
                const imagePath = data.image_path;

                // Calculate time difference between now and the product's creation time
                const createdAt = new Date(data.created_at); // Assuming data.created_at is in ISO format
                const now = new Date();
                const timeDifference = Math.round((now - createdAt) / 60000); // Difference in minutes

                // If the difference is less than 1 minute, show "Just now", otherwise show the number of minutes
                const timeAgoText = timeDifference < 1 ? "Just now" : `${timeDifference} minutes ago`;

                notification.innerHTML = `
                    <button class="toast-close-btn" data-toast-close>
                        <ion-icon name="close-outline"></ion-icon>
                    </button>
                    <div class="toast-banner">
                        <img src="../images/products/${imagePath}" alt="${data.product_name}" width="80" height="70">
                    </div>
                    <div class="toast-detail">
                        <p class="toast-message">Someone just bought</p>
                        <p class="toast-title">${data.product_name}</p>
                        <p class="toast-meta"><time datetime="${data.created_at}">${timeAgoText}</time></p>
                    </div>
                `;

                document.body.appendChild(notification);

                const toastCloseBtn = notification.querySelector('[data-toast-close]');
                toastCloseBtn.addEventListener('click', function () {
                    notification.classList.add('closed');
                    sessionStorage.setItem('dismissedProductId', data.product_id);
                });

                // Automatically disappear after 10 seconds
                setTimeout(() => {
                    notification.remove();
                }, 10000); // 10 seconds before disappearing
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}






        function checkAll() {
          checkForNewProduct()
        }


        // Vérifier toutes les 10 secondes
        setInterval(checkAll, 10000);

        // Réinitialiser la notification après chaque actualisation de la page
        window.addEventListener('beforeunload', function() {
        sessionStorage.removeItem('dismissedProductId');
        });
        // Vérifier au chargement de la page
        window.addEventListener('load', function() {
            if (window.innerWidth > 1000) {
                sidebar.classList.remove('sidebar-responsive');
            } 
        });




  </script>
    
  
  </body>
</html>