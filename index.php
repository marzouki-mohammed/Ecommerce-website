<!--the main file index.php-->
<?php        
        // Début de la session
        session_start();
        // Inclure le fichier de connexion à la base de données
        include "NaturelleShop/php/db_connect.php";
        
        // Vérifier si la connexion à la base de données est établie
        if (!isset($conn)) {
            echo "Database connection is not set.";
            exit;
        }
        $apiKey = 'YOUR_API_KEY';
        $url = "https://api.exchangerate-api.com/v4/latest/USD";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $usdToEur = $data['rates']['EUR'];
        $usdToMad = $data['rates']['MAD'];


        $type='';
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // Initialiser un panier vide pour un nouveau visiteur
        }
        $cart=$_SESSION['cart'];

        if(isset($_SESSION['type_Argant']) && !empty($_SESSION['type_Argant'])){
          $type= $_SESSION['type_Argant'];
        }

        if(isset($_GET['typeargant']) && !empty($_GET['typeargant'])){
          $type=$_GET['typeargant'];
        }

        $_SESSION['type_Argant']=$type;
       

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

        function getTopProductsByRating($conn, $nbr) {
          // Préparer la requête SQL pour obtenir les meilleurs produits par évaluation
          /*$sql = 'SELECT product_id, AVG(rating) AS average_rating
                  FROM reviews
                  WHERE product_id IS NOT NULL 
                  GROUP BY product_id
                  ORDER BY average_rating DESC
                  LIMIT :nbr';
          */
          $sql = 'SELECT p.id, AVG(r.rating) AS average_rating
                          FROM products p
                          JOIN reviews r ON p.id = r.product_id
                          WHERE p.active = TRUE
                          GROUP BY p.id
                          HAVING average_rating IS NOT NULL
                          ORDER BY average_rating DESC
                          LIMIT :nbr';
          
          // Préparer la requête
          $stmt = $conn->prepare($sql);
      
          // Lier le paramètre :nbr
          $stmt->bindValue(':nbr', (int)$nbr, PDO::PARAM_INT);
      
          // Exécuter la requête
          $stmt->execute();
      
          // Récupérer les résultats
          $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
          return $topProducts;
      }
      
       
        function diviserNombre($nombre) {
          // Partie entière
          $partieEntiere = floor($nombre);
          
          // Partie décimale
          $partieDecimale = $nombre - $partieEntiere;
          
          return array('partie_entiere' => $partieEntiere, 'partie_decimale' => $partieDecimale);
        }




        function getTopOrderedProducts($conn) {
          $sql = "
              SELECT 
                  p.id, 
                  p.product_name, 
                  SUM(oi.quantity) AS total_quantity_sold
              FROM 
                  products p
              JOIN 
                  variant_options vo ON p.id = vo.product_id
              JOIN 
                  order_items oi ON vo.id = oi.product_variant_id
              JOIN 
                  orders o ON oi.order_id = o.id
              GROUP BY 
                  p.id, p.product_name
              ORDER BY 
                  total_quantity_sold DESC
              LIMIT 8;
          ";
      
            $stmt = $conn->prepare($sql);
            $stmt->execute();
          
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
      }



    /*
      function getDealOfTheDay($conn) {
        $sql = "
            SELECT 
                p.id, 
                p.product_name, 
                p.description, 
                p.vente_price, 
                p.price, 
                p.compare_price, 
                p.stock_quantity,
                g.image AS image_path
            FROM 
                products p
            LEFT JOIN 
                variant_options v ON p.id = v.product_id
            LEFT JOIN 
                gallery g ON v.id = g.product_variant_id
            WHERE 
                g.is_thumbnail = 1
            ORDER BY 
                (p.compare_price - p.vente_price) DESC, p.created_at DESC
            LIMIT 1;
        ";
    
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getTotalOrdersForProduct($conn, $productId) {
          // SQL query to count the distinct orders for the product
          $sql = "
              SELECT 
                  COUNT(DISTINCT oi.order_id) AS total_orders
              FROM 
                  order_items oi
              JOIN 
                  variant_options vo ON oi.product_variant_id = vo.id
              WHERE 
                  vo.product_id = :product_id";
          
          // Prepare and execute the query
          $stmt = $conn->prepare($sql);
          $stmt->execute(['product_id' => $productId]);
          
          // Fetch the result
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          // Return the total number of orders
          return $result['total_orders'] ?? 0;  // Default to 0 if no orders
      }
    */


    function getTodayOrderedProducts($conn) {
      $sql = "
        SELECT 
            p.id, 
            p.product_name,
            p.description, 
            p.price, 
            p.vente_price, 
            p.stock_quantity, 
            SUM(oi.quantity) AS total_ordered, 
            DAY(o.created_at) AS day_of_month, 
            HOUR(o.created_at) AS hour, 
            MINUTE(o.created_at) AS minute, 
            SECOND(o.created_at) AS second 
        FROM 
            products p  
        INNER JOIN 
            variant_options vo ON p.id = vo.product_id  
        INNER JOIN 
            order_items oi ON vo.id = oi.product_variant_id  
        INNER JOIN 
            orders o ON oi.order_id = o.id  
        WHERE 
            o.id = (
                SELECT id 
                FROM orders 
                WHERE DATE(created_at) = CURDATE() 
                ORDER BY created_at DESC 
                LIMIT 1
            )
        GROUP BY 
            vo.product_id  
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
      
      return $data;
    }
  

    
      


    
    //a tester
    function displayDealOfTheDay($conn) {
      global $type;
      global $ty;
      global $usdToMad;
      global $usdToEur;
      
        $deal = getTodayOrderedProducts($conn);
    
        if ($deal) {
            echo '<div class="product-featured">';
            echo '<h2 class="title">Deal of the Day</h2>';
            echo '<div class="showcase-wrapper has-scrollbar">';
            foreach($deal as $deler){
               $productId=$deler['id'];
               echo '<div class="showcase-container">';
               echo '<div class="showcase">';
               // Image du produit
               // Préparer la requête pour récupérer les images des produits
               $sql_image = "SELECT g.image AS image_path
               FROM variant_options v
               INNER JOIN gallery g ON v.id = g.product_variant_id
               WHERE v.product_id = :product_id
               ORDER BY g.is_thumbnail DESC, g.created_at ASC
               LIMIT 1";
               $stm_image=$conn->prepare( $sql_image);
               $stm_image->execute(['product_id'=> $productId]);
               $resulte_image= $stm_image->fetch(PDO::FETCH_ASSOC);
               if($resulte_image){
                echo '<div class="showcase-banner">';
                echo '<img src="NaturelleShop/images/products/' . htmlspecialchars($resulte_image['image_path']) . '" alt="' . htmlspecialchars($deler['product_name']) . '" class="showcase-img">';
                echo '</div>';

               }else{
                echo '<div class="showcase-banner">';
                echo '<img src="NaturelleShop/images/products/default.WEBP" alt="' . htmlspecialchars($deler['product_name']) . '" class="showcase-img">';
                echo '</div>';

               }
               

                
                
                // Contenu du produit
                echo '<div class="showcase-content">';
                // Étoiles de notation
                  echo '<div class="showcase-rating">';
                  $arting=calculerMoyenneRating($productId, $conn);
                  $arr=diviserNombre($arting);
                  for ($i = 1; $i <= 5 ; $i++) {
                    if($i<=$arr['partie_entiere']){
                      echo "<ion-icon name='star'></ion-icon>";
                    }elseif($i==$arr['partie_entiere']+1){
                      if ($arr['partie_decimale'] > 0.50) {
                        echo "<ion-icon name='star-half-outline'></ion-icon>";
                      }else{
                        echo "<ion-icon name='star-outline'></ion-icon>";
                      }

                    }else{
                      echo "<ion-icon name='star-outline'></ion-icon>";

                    }
                  }
                  echo '</div>';
                  // Lien vers le produit
                  echo "<a href='NaturelleShop/php/product_pross.php?id=".htmlspecialchars($deler["id"])."'><h3 class='showcase-title'>" . htmlspecialchars($deler['product_name']) . "</h3></a>";
          
                  
                  // Prix et réduction
                  echo '<div class="price-box">';
                  if($type == 'usd'){
                    $Price=$deler['price'];
                    $vente_Price=$deler['vente_price'];
                    $ty='&dollar;';
                      
                   }elseif($type == 'eur'){
                    $Price=$deler['price']*$usdToEur;
                    $vente_Price=$deler['vente_price']*$usdToEur;
                    $ty='&euro;';

                     
                   }elseif($type == 'mad'){
                    $Price=$deler['price']*$usdToMad;
                    $vente_Price=$deler['vente_price']*$usdToMad;
                    $ty='DH';
                    
                   }else{
                    $Price=$deler['price'];
                    $vente_Price=$deler['vente_price'];
                    $ty='&dollar;';
                   }
                  echo "<p class='price'>".$ty."" . htmlspecialchars($vente_Price) . '</p>';
                  if ($deler['price'] > $deler['vente_price']) {
                      echo "<del>".$ty."" . htmlspecialchars($Price) . '</del>';
                  }
                  echo '</div>';
                   // Bouton pour ajouter au panier
                   echo '<button class="add-cart-btn">add to cart</button>';
                   // Statut du produit (vendu et disponible)

                   echo '<div class="showcase-status">';
                   echo '<div class="wrapper">';
                       echo "<p>already sold: <b>" . $deler['total_ordered'] . "</b></p>";
                       echo "<p>available: <b>" . $deler['stock_quantity'] . "</b></p>";
                   echo '</div>';
               
                   // Calculer la proportion
                   $total_quantity = $deler['total_ordered'] + $deler['stock_quantity'];
                   $percentage_sold = ($total_quantity > 0) ? ($deler['total_ordered'] / $total_quantity) * 100 : 0;
               
                   // Conteneur de la barre de statut avec fond
                   echo '<div class="showcase-status-bar-container" style="background-color: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">';
                       
                       // Barre de statut dynamique avec couleur de progression
                       echo '<div class="showcase-status-bar" style="width: ' . $percentage_sold . '%; background-color: #ff5733; height: 100%;"></div>';
                   
                   echo '</div>';
               
               echo '</div>';
               
                    // Compte à rebours
                    echo '<div class="countdown-box">';
                    echo '<p class="countdown-desc">Hurry Up! Offer ends in:</p>';
                    echo '<div class="countdown">';
                    echo "<div class='countdown-content'><p class='display-number'></p>".$deler['day_of_month']."<p class='display-text'>Days</p></div>";
                    echo "<div class='countdown-content'><p class='display-number'>".$deler['hour']."</p><p class='display-text'>Hours</p></div>";
                    echo "<div class='countdown-content'><p class='display-number'>".$deler['minute']."</p><p class='display-text'>Min</p></div>";
                    echo "<div class='countdown-content'><p class='display-number'>".$deler['second']."</p><p class='display-text'>Sec</p></div>";
                    echo '</div>';
                    echo '</div>'; // Fin de countdown-box





               echo '</div>'; // Fin de showcase-content
               echo '</div>'; // Fin de showcase
               echo '</div>'; // Fin de showcase-container


            }
            
            echo '</div>'; // Fin de showcase-wrapper
            echo '</div>'; // Fin de product-featured
         
      }
    }



    function getTopThreeReviews($conn) {
      // Préparez la requête SQL pour récupérer les 3 meilleurs avis avec statut 'granted', triés par rating (note) et date
      $stmt = $conn->prepare("
          SELECT  r.comment
          FROM reviews  r
          JOIN users u ON r.user_id = u.id
          JOIN products p ON r.product_id = p.id         
          ORDER BY r.rating DESC, r.created_at DESC
          LIMIT 3
      ");
      
      // Exécutez la requête
      $stmt->execute();
      
      // Récupérer les avis
      $topReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
      // Retourner les avis (ou null si aucun avis trouvé)
      return $topReviews;
  }
  
  function getCTA($conn){
    $sql = "SELECT * 
            FROM coupons
            WHERE discount_amount = (
                SELECT MAX(discount_amount) 
                FROM coupons
            ) AND NOW() BETWEEN valid_from AND valid_until
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $cta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $cta;   
}

  

      
        
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NaturelleShop</title>
  

  <!--
    - favicon
  -->
  <link rel="shortcut icon" href="NaturelleShop/images/icons/icons.png" type="image/x-icon">

  <!--
    - custom css link
  -->
  <link rel="stylesheet" href="NaturelleShop/assets/css/style.css">

  <!--
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

</head>

<body>




  <!--
    - MODAL
  -->
  <div id="modele" class="modal" data-modal>
        <div class="modal-close-overlay" data-modal-overlay></div>
        <div class="modal-content">
            <button class="modal-close-btn" data-modal-close>
                <ion-icon name="close-outline"></ion-icon>
            </button>
            <div class="newsletter-img">
                <img src="NaturelleShop/images/img6.jpg" alt="subscribe newsletter" width="400" height="400">
            </div>
            <div class="newsletter">
            <?php if(isset($_GET['error'])){ ?>
            <div class="alert  " role="alert">
                <?php echo $_GET['error']; ?>
            </div>
            <?php } ?>
            <form action="NaturelleShop/php/login.php" method="post">
                    <div class="newsletter-header">
                        <h3 class="newsletter-title">Subscribe Newsletter.</h3>
                        <p class="newsletter-desc">
                            Subscribe to <b>NaturelleShop</b> to get the latest products and discount updates.
                        </p>
                    </div>
                    <input type="email" name="email" class="email-field" placeholder="Email Address" value="exemple@gmail.com" required>
                    <input type="password" name="password" class="email-field" placeholder="Password" value="" required>
                    <div class="btn">
                    <button type="submit" class="btn-newsletter">Login</button>
                    <button type="submit" class="btn-newsletter"><a href="NaturelleShop/pages/sinupPayment.php">Sing Up</a></button>
                    </div>
              </form>
            </div>
        </div>
    </div>


  











  <!--
    - HEADER
  -->

<header id="header">

    <div class="header-top">

      <div class="container">

        <ul class="header-social-container">

          <li>
            <a href="#" class="social-link">
              <ion-icon name="logo-facebook"></ion-icon>
            </a>
          </li>

          <li>
            <a href="#" class="social-link">
              <ion-icon name="logo-twitter"></ion-icon>
            </a>
          </li>

          <li>
            <a href="#" class="social-link">
              <ion-icon name="logo-instagram"></ion-icon>
            </a>
          </li>

          <li>
            <a href="#" class="social-link">
              <ion-icon name="logo-linkedin"></ion-icon>
            </a>
          </li>

        </ul>

        <div class="header-top-actions">

        <select name="currency1" onchange="redirectToPage(this)" >
            <?php
                if($type == 'usd'){
                   echo '<option value="usd" selected>USD &dollar;</option>
                        <option value="eur">EUR &euro;</option>
                        <option value="mad">MAD Dh</option>';
                }elseif($type == 'eur'){
                  echo '<option value="usd">USD &dollar;</option>
                        <option value="eur" selected>EUR &euro;</option>
                        <option value="mad">MAD Dh</option>';
                }elseif($type == 'mad'){
                  echo '<option value="usd">USD &dollar;</option>
                        <option value="eur">EUR &euro;</option>
                        <option value="mad" selected>MAD Dh</option>';
                }else{
                  echo '<option value="usd" selected>USD &dollar;</option>
                        <option value="eur">EUR &euro;</option>
                        <option value="mad">MAD Dh</option>';
                }
            ?>
            
        </select>

          

        </div>

      </div>

    </div>




    <div class="header-main">

      <div class="container">

        <div class="header-logo">
            <a href="index.php" >
                <svg width="145"  height="60" xmlns="http://www.w3.org/2000/svg">
                    
                    <circle cx="25" cy="30" r="20" stroke="black" stroke-width="3" fill="lightgreen" />
                    <text x="25" y="37" font-size="20" font-family="Arial" text-anchor="middle" fill="white">N</text>
                    
                    
                    <text x="50" y="37" font-size="15" font-family="Arial" fill="green">Naturelle</text>
                    <text x="110" y="37" font-size="15" font-family="Arial" fill="darkgreen">Shop</text>
                </svg>          
            </a>
           
            <select name="currency2" onchange="redirectToPage(this)" >
                <?php
                    if($type == 'usd'){
                      echo '<option value="usd" selected>USD &dollar;</option>
                            <option value="eur">EUR &euro;</option>
                            <option value="mad">MAD Dh</option>';
                    }elseif($type == 'eur'){
                      echo '<option value="usd">USD &dollar;</option>
                            <option value="eur" selected>EUR &euro;</option>
                            <option value="mad">MAD Dh</option>';
                    }elseif($type == 'mad'){
                      echo '<option value="usd">USD &dollar;</option>
                            <option value="eur">EUR &euro;</option>
                            <option value="mad" selected>MAD Dh</option>';
                    }else{
                      echo '<option value="usd" selected>USD &dollar;</option>
                            <option value="eur">EUR &euro;</option>
                            <option value="mad">MAD Dh</option>';
                    }
                ?>
            
            </select>

        </div>

        

        <div class="header-search-container">
            <form id="searchForm" action="NaturelleShop/php/serch_pross.php" method="POST" enctype="multipart/form-data" class="search_form">                
                <input placeholder="search" type="search" class="input" name="search_input">
                <button type="submit" class="serch_btn">
                <svg class="icon" aria-hidden="true" viewBox="0 0 24 24">
                    <g>
                        <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                    </g>
                </svg>
                </button> 
            </form>
        </div>

        <div class="header-user-actions">

          <button id="btn_singup" class="action-btn">
            <ion-icon name="person-outline"></ion-icon>
          </button>

          <button id="btn_cart" class="action-btn">
            <ion-icon name="cart-outline"></ion-icon>           
          </button>

        </div>





      </div>

    </div>



    <div class="mobile-bottom-navigation">

      <button id="open-menu-cat" class="action-btn" >
        <ion-icon name="menu-outline"></ion-icon>
      </button>

      <button id="btn_cart2" class="action-btn">
         <ion-icon name="cart-outline"></ion-icon>
      </button>

      <button id="header_btn" class="action-btn">
        <ion-icon name="home-outline"></ion-icon>
      </button>

      <button id="btn_singup2" class="action-btn">
         <ion-icon name="person-outline"></ion-icon>
      </button>

     

    </div>


    <nav class="desktop-navigation-menu">

          <div class="container">

            <ul class="desktop-menu-category-list">

                <li class="menu-category">
                   <a href="#" class="menu-title">Blog</a>
                </li>

                <?php 
                    echo "<li class='menu-category'>";
                    echo "<p class='menu-title'>Categories</p>";
                    echo "<div class='dropdown-panel'>";
                    // Requête pour récupérer toutes les catégories
                    $sql1 = "SELECT * FROM categories";
                    $stmt1 = $conn->prepare($sql1);
                    $stmt1->execute();
                    $categories1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                    // Vérifier s'il y a des catégories
                    if (count($categories1) > 0) {
                                // Requête pour récupérer les catégories principales (celles dont parent_id = -1)
                                $sql_main1 = "SELECT * FROM categories WHERE parent_id = 1";
                                $stmt_main1 = $conn->prepare($sql_main1);
                                $stmt_main1->execute();
                                $main_categories1 = $stmt_main1->fetchAll(PDO::FETCH_ASSOC);
                                // Vérifier s'il y a des catégories principales
                                if (count($main_categories1) > 0) {
                                        foreach ($main_categories1 as $main_category1) {
                                                $main_category_id1 = $main_category1["id"];
                                                echo "<ul class='dropdown-panel-list'>";
                                                echo "<li class='menu-title'>";
                                                    echo " <a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($main_category1["id"])."'>" .html_entity_decode($main_category1["name"]). "</a>";
                                                echo "</li>";
                                                // Requête pour récupérer les sous-catégories de la catégorie principale
                                                $sql_sub1 = "SELECT * FROM categories WHERE parent_id = :parent_id1";
                                                $stmt_sub1 = $conn->prepare($sql_sub1);
                                                $stmt_sub1->execute(['parent_id1' => $main_category_id1]);
                                                $sub_categories1 = $stmt_sub1->fetchAll(PDO::FETCH_ASSOC);
                                                // Vérifier s'il y a des sous-catégories
                                                if (count($sub_categories1) > 0) {
                                                foreach ($sub_categories1 as $sub_category1) {
                                                        echo "<li class='panel-list-item'>";
                                                                echo "<a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($sub_category1["id"])."'>".html_entity_decode($sub_category1["name"])."</a>";
                                                        echo "</li>";
                                                        
                                                }                                                    
                                                }
                                                // Requête pour récupérer les image du catégorie principale
                                                $sql_img= "SELECT image FROM gallery WHERE categorie_id = :categorie_id ORDER BY id DESC LIMIT 1";
                                                $stmt_img = $conn->prepare($sql_img);
                                                $stmt_img->execute(['categorie_id' => $main_category_id1]);
                                                $sub_img = $stmt_img->fetch();
                                                if ($sub_img) {
                                                echo "<li class='panel-list-item'>";                                         
                                                    echo "<a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($main_category1["id"])."'>";
                                                        echo '<img src="NaturelleShop/images/categorie/' . htmlspecialchars($sub_img['image']) . '" alt="' . html_entity_decode($main_category1['name']) . '" width="250" height="20">';
                                                    echo "</a>";
                                                echo "</li>";
                                                }
                                                echo "</ul>";
                                        }
                                }
                                // Requête pour récupérer les catégories secondaires (celles dont parent_id != -1)
                                $sql_secondary1 = "SELECT * FROM categories WHERE parent_id != 1";
                                $stmt_secondary1 = $conn->prepare($sql_secondary1);
                                $stmt_secondary1->execute();
                                $secondary_categories1 = $stmt_secondary1->fetchAll(PDO::FETCH_ASSOC);
                                // Vérifier s'il y a des catégories secondaires
                                if (count($secondary_categories1) > 0) {
                                        foreach ($secondary_categories1 as $secondary_category1) {
                                                $secondary_category_id1 = $secondary_category1["id"];
                                                // Requête pour récupérer les sous-catégories des catégories secondaires
                                                $sql_sub_secondary1 = "SELECT * FROM categories WHERE parent_id = :parent_id1";
                                                $stmt_sub_secondary1 = $conn->prepare($sql_sub_secondary1);
                                                $stmt_sub_secondary1->execute(['parent_id1' => $secondary_category_id1]);
                                                $sub_secondary_categories1 = $stmt_sub_secondary1->fetchAll(PDO::FETCH_ASSOC);
                                                // Vérifier s'il y a des sous-catégories pour les catégories secondaires
                                                if (count($sub_secondary_categories1) > 0) {
                                                        echo "<ul class='dropdown-panel-list'>";
                                                                echo "<li class='menu-title'>";
                                                                    echo " <a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($secondary_category1["id"])."'>" .html_entity_decode($secondary_category1["name"]). "</a>";
                                                                echo "</li>";
                                                                foreach ($sub_secondary_categories1 as $sub_secondary_category1) {
                                                                echo "<li class='panel-list-item'>";
                                                                        echo "<a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($sub_secondary_category1["id"])."'>".html_entity_decode($sub_secondary_category1["name"])."</a>";
                                                                echo "</li>";
                                                                }
                                                                $sub_sql_img = "SELECT image FROM gallery WHERE categorie_id = :categorie_id ORDER BY id desc LIMIT 1";
                                                                $sub_stmt_img = $conn->prepare($sub_sql_img);
                                                                $sub_stmt_img->execute(['categorie_id' => $secondary_category_id1]);
                                                                $sub_img1 = $sub_stmt_img->fetch();
                                                                if ($sub_img1) {
                                                                echo "<li class='panel-list-item'>";
                                                                    echo "<a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($secondary_category1["id"])."'>";
                                                                        echo '<img src="NaturelleShop/images/categorie/' . htmlspecialchars($sub_img1['image']) . '" alt="' . html_entity_decode($secondary_category1['name']) . '" width="250" height="119">';
                                                                    echo "</a>";
                                                                echo "</li>";
                                                                } 
                                                        echo "</ul>";                                                       
                                                }
                                        }
                                } 

                    }
                ?>
            
                <li class="menu-category">
                <p class="menu-title">Product Composer</p>
                <?php 
                   $sql_Composer="SELECT * FROM components WHERE is_active = true";
                   $stmt_Composer= $conn->prepare($sql_Composer);
                    $stmt_Composer->execute();
                    $Product_Composer = $stmt_Composer->fetchAll(PDO::FETCH_ASSOC);
                    if( $Product_Composer){
                      echo '<ul class="dropdown-list">';
                          foreach($Product_Composer as $procom){
                            echo '<li class="dropdown-item">';
                                 echo "<a href='NaturelleShop/php/prodComposer_pross.php?idC=".htmlspecialchars($procom["id"])."'>".html_entity_decode($procom['component_name'])."</a>";
                            echo '</li>';
                            
                          }
                      echo '</ul>';
                    }

                ?> 

                </li>      

                <li class="menu-category">
                <a href="#" class="menu-title">Hot Offers</a>
                </li>

            </ul>

          </div>

    </nav>


     <!-- nb:dans cette sesion reste la langage et la floss et la home -->
    <nav id="menu-cat" class="mobile-navigation-menu has-scrollbar" >
            <div class="menu-top">
            <h2 class="menu-title">Menu</h2>
            <button id="close-menu-cat" class="menu-close-btn" >
                <ion-icon name="close-outline"></ion-icon>
            </button>
            </div>

          <ul class="mobile-menu-category-list">
            <li class="menu-category">
                <a href="#" class="menu-title">Blog</a>
            </li>
            <?php
                                
                                // Requête pour récupérer toutes les catégories
                                $sql = "SELECT * FROM categories";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Vérifier s'il y a des catégories
                                if (count($categories) > 0) {

                                    // Requête pour récupérer les catégories principales (celles dont parent_id = -1)
                                    $sql_main = "SELECT * FROM categories WHERE parent_id = 1";
                                    $stmt_main = $conn->prepare($sql_main);
                                    $stmt_main->execute();
                                    $main_categories = $stmt_main->fetchAll(PDO::FETCH_ASSOC);

                                    // Vérifier s'il y a des catégories principales
                                    if (count($main_categories) > 0) {
                                        foreach ($main_categories as $main_category) {
                                            $main_category_id = $main_category["id"];
                                            echo "<li class='menu-category'>";
                                            echo "<button class='accordion-menu' accordion-btn>
                                                    <p class='menu-title'>".html_entity_decode($main_category["name"])."</p>
                                                    <div>
                                                        <ion-icon name='add-outline' class='add-icon'></ion-icon>
                                                        <ion-icon name='remove-outline' class='remove-icon'></ion-icon>
                                                    </div>
                                                    </button>";

                                            // Requête pour récupérer les sous-catégories de la catégorie principale
                                            $sql_sub = "SELECT * FROM categories WHERE parent_id = :parent_id";
                                            $stmt_sub = $conn->prepare($sql_sub);
                                            $stmt_sub->execute(['parent_id' => $main_category_id]);
                                            $sub_categories = $stmt_sub->fetchAll(PDO::FETCH_ASSOC);

                                            // Vérifier s'il y a des sous-catégories
                                            if (count($sub_categories) > 0) {
                                                echo "<ul class='submenu-category-list' accordion-data>";
                                                    
                                                foreach ($sub_categories as $sub_category) {
                                                    echo "<li class='submenu-category'>
                                                            <a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($sub_category["id"])."' class='submenu-title'>".html_entity_decode($sub_category["name"])."</a>
                                                          </li>";
                                                }
                                                echo "</ul>";
                                            }
                                            echo "</li>";
                                        }
                                    }

                                    // Requête pour récupérer les catégories secondaires (celles dont parent_id != -1)
                                    $sql_secondary = "SELECT * FROM categories WHERE parent_id != 1";
                                    $stmt_secondary = $conn->prepare($sql_secondary);
                                    $stmt_secondary->execute();
                                    $secondary_categories = $stmt_secondary->fetchAll(PDO::FETCH_ASSOC);

                                    // Vérifier s'il y a des catégories secondaires
                                    if (count($secondary_categories) > 0) {
                                        foreach ($secondary_categories as $secondary_category) {
                                            $secondary_category_id = $secondary_category["id"];

                                            // Requête pour récupérer les sous-catégories des catégories secondaires
                                            $sql_sub_secondary = "SELECT * FROM categories WHERE parent_id = :parent_id";
                                            $stmt_sub_secondary = $conn->prepare($sql_sub_secondary);
                                            $stmt_sub_secondary->execute(['parent_id' => $secondary_category_id]);
                                            $sub_secondary_categories = $stmt_sub_secondary->fetchAll(PDO::FETCH_ASSOC);

                                            // Vérifier s'il y a des sous-catégories pour les catégories secondaires
                                            if (count($sub_secondary_categories) > 0) {
                                                echo "<li class='menu-category'>";
                                                echo "<button class='accordion-menu' accordion-btn>
                                                        <p class='menu-title'>".htmlspecialchars($secondary_category["name"])."</p>
                                                        <div>
                                                            <ion-icon name='add-outline' class='add-icon'></ion-icon>
                                                            <ion-icon name='remove-outline' class='remove-icon'></ion-icon>
                                                        </div>
                                                        </button>";
                                                echo "<ul class='submenu-category-list' accordion-data>";
                                                     

                                                foreach ($sub_secondary_categories as $sub_secondary_category) {
                                                    echo "<li class='submenu-category'>
                                                            <a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($sub_secondary_category["id"])."' class='submenu-title'>".html_entity_decode($sub_secondary_category["name"])."</a>
                                                            </li>";
                                                }
                                                echo "</ul>";
                                                echo "</li>";
                                            }
                                        }
                                    }

                                } else {
                                    // Afficher un message s'il n'y a pas de catégories
                                    echo "<li class='submenu-category'>";
                                    echo "<a href='#' class='submenu-title'>Pas de catégories pour le moment</a>";
                                    echo "</li>";
                                }
                               
             ?>
             <li class="menu-category">
                  <button class="accordion-menu" accordion-btn>
                        <p class="menu-title">Product Composer</p>
                        <div>
                              <ion-icon name="add-outline" class="add-icon"></ion-icon>
                              <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                        </div>
                  </button>
                  <?php 
                   $sql_Composer_min="SELECT * FROM components WHERE is_active = true";
                   $stmt_Composer_min= $conn->prepare($sql_Composer_min);
                    $stmt_Composer_min->execute();
                    $Product_Composer_min = $stmt_Composer_min->fetchAll(PDO::FETCH_ASSOC);
                    if( $Product_Composer_min){
                      echo '<ul class="submenu-category-list" accordion-data>';
                          foreach($Product_Composer_min as $procomin){
                            echo '<li class="submenu-category">';
                                 echo "<a href='NaturelleShop/php/prodComposer_pross.php?idC=".htmlspecialchars($procomin["id"])."' class='submenu-title'>".html_entity_decode($procomin['component_name'])."</a>";
                            echo '</li>';
                            
                          }
                      echo '</ul>';
                    }

                  ?> 


             </li>

          </ul>




            <div class="menu-bottom">
               <!--
                <ul class="menu-category-list">
                    
                    <li class="menu-category">
                        <button class="accordion-menu" accordion-btn>
                            <p class="menu-title">Language</p>
                            <ion-icon name="caret-back-outline" class="caret-back"></ion-icon>
                        </button>
                        <ul class="submenu-category-list" accordion-data>
                            <li class="submenu-category"><a href="#" class="submenu-title">English</a></li>
                            <li class="submenu-category"><a href="#" class="submenu-title">Español</a></li>
                            <li class="submenu-category"><a href="#" class="submenu-title">Français</a></li>
                        </ul>
                    </li>
                     
                    
                    <li class="menu-category">
                        <button class="accordion-menu" accordion-btn>
                            <p class="menu-title">Currency</p>
                            <ion-icon name="caret-back-outline" class="caret-back"></ion-icon>
                        </button>
                        <ul class="submenu-category-list" accordion-data>
                            <li class="submenu-category"><a href="#" class="submenu-title">USD $</a></li>
                            <li class="submenu-category"><a href="#" class="submenu-title">EUR €</a></li>
                        </ul>
                    </li>
                </ul>
                -->

                <ul class="menu-social-container">
                    <li><a href="#" class="social-link"><ion-icon name="logo-facebook"></ion-icon></a></li>
                    <li><a href="#" class="social-link"><ion-icon name="logo-twitter"></ion-icon></a></li>
                    <li><a href="#" class="social-link"><ion-icon name="logo-instagram"></ion-icon></a></li>
                    <li><a href="#" class="social-link"><ion-icon name="logo-linkedin"></ion-icon></a></li>
                </ul>
            </div>
    </nav>

</header>


      <!--
    - MAIN
  -->

  <main>

    <!--
      - BANNER
    -->

    <div class="banner">
      
      <div class="container">

        <div class="slider-container has-scrollbar">
          <!--banner 1-->
          <div class="slider-item">

            <img src="NaturelleShop/images/banner/bnr1.jpg" alt="women's latest fashion sale" class="banner-img">
            <!--contenu-->
            <!--
            <div class="banner-content">

              <p class="banner-subtitle">Trending item</p>

              <h2 class="banner-title">Women's latest fashion sale</h2>

              <p class="banner-text">
                starting at &dollar; <b>20</b>.00
              </p>

              <a href="#" class="banner-btn">Shop now</a>

            </div>
                              -->

          </div>
           <!--banner 2-->
          <div class="slider-item">

            <img src="NaturelleShop/images/banner/bnr2.jpg" alt="modern sunglasses" class="banner-img">
             <!--
            <div class="banner-content">

              <p class="banner-subtitle">Trending accessories</p>

              <h2 class="banner-title">Modern sunglasses</h2>

              <p class="banner-text">
                starting at &dollar; <b>15</b>.00
              </p>

              <a href="#" class="banner-btn">Shop now</a>

            </div>
                              -->

          </div>
          <!--
          <div class="slider-item">

            <img src="NaturelleShop/images/banner/bnr3.jpg" alt="new fashion summer sale" class="banner-img">
             
            <div class="banner-content">

              <p class="banner-subtitle">Sale Offer</p>

              <h2 class="banner-title">New fashion summer sale</h2>

              <p class="banner-text">
                starting at &dollar; <b>29</b>.99
              </p>

              <a href="#" class="banner-btn">Shop now</a>

            </div>
                            

          </div>
          -->

        </div>

      </div>

    </div>





    
    <!--
      - PRODUCT
    -->

    <div class="product-container">
      <!--ajouter class container -->
      <div class="container">


        <!--
          - SIDEBAR
        -->

        <div id="menu-2"  class="sidebar  has-scrollbar" data-mobile-menu>
          <!--pano the carts change -->
          <div class="sidebar-category">

            <!--name change to carts -->
            <div class="sidebar-top">
              <h2 class="sidebar-title">Cart</h2>

              <button id="close-menu-2" class="sidebar-close-btn" data-mobile-menu-close-btn>
                <ion-icon name="close-outline"></ion-icon>
              </button>


            </div>
            <div class="cart has-scrollbar">
            <?php
                if(!empty($cart)){
                  echo '<ul class="sidebar-menu-category-list">';
                        $nbrdelet=0;
                        foreach($cart as $item){

                          $id_cart=intval($item['id']);
                          $vente_Price=0;
                          echo '<li class="sidebar-menu-category">';

                               echo '<button class="sidebar-accordion-menu" accordion-btn>';
                                    echo '<div class="menu-title-flex">';
                                         if($item['type'] == 1){
                                            $id_cart=(string)$item['id'];
                                            $id_cart_var=intval($item['option'][$id_cart]);
                                            $sql_cart_img="SELECT * FROM gallery WHERE product_variant_id=? LIMIT 1";
                                            $stmt_cart_img=$conn->prepare($sql_cart_img);
                                            $stmt_cart_img->execute([$id_cart_var]);
                                            $data_cart_img = $stmt_cart_img->fetch();
                                            if($data_cart_img){
                                              echo "<img src='NaturelleShop/images/products/".htmlspecialchars($data_cart_img['image'])."' alt='clothes' width='30' height='30'
                                                    class='menu-title-img'>";
                                            }else{
                                              echo "<img src='NaturelleShop/images/products/cart.png' alt='clothes' width='30' height='30'
                                                    class='menu-title-img'>";
                                              
                                            }
                                               $sqlpeix="SELECT vente_price FROM products WHERE id=?";
                                               $stmprix=$conn->prepare($sqlpeix);
                                               $stmprix->execute([$id_cart]);
                                               $data_cart_prix = $stmprix->fetch();

                                               if($type == 'usd'){                                                
                                                $vente_Price=$data_cart_prix['vente_price'];
                                                $ty='&dollar;';
                                                  
                                               }elseif($type == 'eur'){                                                
                                                $vente_Price=$data_cart_prix['vente_price']*$usdToEur;
                                                $ty='&euro;';                                                
                                               }elseif($type == 'mad'){                                            
                                                $vente_Price=$data_cart_prix['vente_price']*$usdToMad;
                                                $ty='DH';
                                                
                                               }else{                                                
                                                $vente_Price=$data_cart_prix['vente_price'];
                                                $ty='&dollar;';
                                               }

                                         }
                                         if($item['type'] == 2){                   
                                              $sql_cart_img="SELECT * FROM components WHERE id=?";
                                              $stmt_cart_img=$conn->prepare($sql_cart_img);
                                              $stmt_cart_img->execute([$id_cart]);
                                              $data_cart_img = $stmt_cart_img->fetch();
                                              if($data_cart_img){
                                                echo "<img src='NaturelleShop/images/products/".htmlspecialchars($data_cart_img['image'])."' alt='clothes' width='30' height='30'
                                                        class='menu-title-img'>";
                                              }else{
                                                echo "<img src='NaturelleShop/images/products/cart.png' alt='clothes' width='30' height='30'
                                                        class='menu-title-img'>";
                                                
                                              }
                                              $sqlpeix="SELECT vente_price FROM components WHERE id=?";
                                               $stmprix=$conn->prepare($sqlpeix);
                                               $stmprix->execute([$id_cart]);
                                               $data_cart_prix = $stmprix->fetch();

                                               if($type == 'usd'){                                                
                                                $vente_Price=$data_cart_prix['vente_price'];
                                                $ty='&dollar;';
                                                  
                                               }elseif($type == 'eur'){                                                
                                                $vente_Price=$data_cart_prix['vente_price']*$usdToEur;
                                                $ty='&euro;';                                                
                                               }elseif($type == 'mad'){                                            
                                                $vente_Price=$data_cart_prix['vente_price']*$usdToMad;
                                                $ty='DH';
                                                
                                               }else{                                                
                                                $vente_Price=$data_cart_prix['vente_price'];
                                                $ty='&dollar;';
                                               }
                
                                         }
                                         echo "<p class='menu-title'>".html_entity_decode($item['title'])."</p>";                                        
                                    echo '</div>';                                   
                                    echo '<div>
                                                <ion-icon name="add-outline" class="add-icon"></ion-icon>
                                                <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                                          </div>';
                               echo '</button>';

                               echo '<ul class="sidebar-submenu-category-list" accordion-data>';
                                     echo '<li class="sidebar-submenu-category">';
                                          echo '<pre  class="sidebar-submenu-title">';
                                               echo '<p class="product-name">Price</p>';                                            
                                               echo "<data  value='".$vente_Price."' class='stock' title='Price'>".$ty."".htmlspecialchars($vente_Price)."</data>";                                                
                                          echo '</pre>';
                                     echo '</li>';

                                     echo '<li class="sidebar-submenu-category">';
                                          echo '<pre  class="sidebar-submenu-title">';
                                               echo '<p class="product-name">Quantity</p>';                                            
                                               echo "<data  value='".$item['quantity']."' class='stock' title='Price'>".htmlspecialchars($item['quantity'])."</data>";                                                
                                          echo '</pre>';
                                     echo '</li>';

                                     echo '<li class="sidebar-submenu-category">';
                                          echo '<pre  class="sidebar-submenu-title">';
                                               echo '<p class="product-name">Total Price</p>'; 
                                               $prix_total=$vente_Price*intval($item['quantity']);                                           
                                               echo "<data  value='".$prix_total."' class='stock' title='Price'>".$ty."".htmlspecialchars($prix_total)."</data>";                                                
                                          echo '</pre>';
                                     echo '</li>';
                                     
                                     echo '<li class="sidebar-submenu-category">';
                                          echo '<form action="NaturelleShop/pages/php/delet_cart_item.php" method="post" enctype="multipart/form-data">';
                                                echo "<input type='hidden' name='ifdelet_cart' value='".htmlspecialchars($nbrdelet)."'>";
                                                echo '<button type="submit" class="sidebar-submenu-title">';
                                                      echo '<ion-icon name="trash-outline" class="delete-icon"></ion-icon>';
                                                echo '</button>';
                                          echo '</form>';
                              
                                     echo '</li>';
                                     

                               echo '</ul>';

                          echo '</li>';  
                          
                          $nbrdelet++;
                        }
                  echo '</ul>';
                 }
            ?>            
            </div>
            <div class="btncart">
              <form action="">
                 <button id="checkout" class="btn checkout"><a href="NaturelleShop/pages/loginPayment.php">BUY</a></button>
              </form>
            </div>


            

          </div>

          <!--product avec top rating -->
          <div class="product-showcase">
            <!--name-->
            <h3 class="showcase-heading">best sellers</h3>
             <!--liste des top rating vvv  -->
            <div class="showcase-wrapper">

              <div class="showcase-container">

              <?php
                  $nbr=4;
                  $id_pro_top_rating = getTopProductsByRating($conn,$nbr);
                  if (count($id_pro_top_rating) > 0) {
                      foreach ($id_pro_top_rating as $po) {
                          $productId = $po['id'];
                          
                          // Récupérer les détails du produit
                          
                          $posql = "SELECT * FROM products WHERE id = ?";
                          $postm = $conn->prepare($posql);
                          $postm->execute([$productId]);
                          $podata = $postm->fetch(PDO::FETCH_ASSOC);

                          if ($podata) {
                              echo "<div class='showcase'>";

                              // Récupérer l'image du produit
                              $posql_image_produit = "SELECT g.image AS image_path
                                                      FROM variant_options v
                                                      INNER JOIN gallery g ON v.id = g.product_variant_id
                                                      WHERE v.product_id = :product_id
                                                      ORDER BY g.is_thumbnail DESC, g.created_at ASC
                                                      LIMIT 1";
                              $postm_img = $conn->prepare($posql_image_produit);
                              $postm_img->execute(['product_id' => $productId]);
                              $podata_img = $postm_img->fetch(PDO::FETCH_ASSOC);

                              if ($podata_img) {
                                echo "<form action='NaturelleShop/php/product_pross.php' method='POST' class='showcase-img-box'>";
                                echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($po['id']) . "'>";
                                echo "<button type='submit' style='background: none; border: none; padding: 0; cursor: pointer;'>";
                                echo "<img src='NaturelleShop/images/products/" . htmlspecialchars($podata_img['image_path']) . "' alt='Product image' width='70' height='70' class='showcase-img'>";
                                echo "</button>";
                                echo "</form>";
                                
                              }

                              echo "<div class='showcase-content'>";
                                echo "<form action='NaturelleShop/php/product_pross.php' method='POST'>";
                                echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($po['id']) . "'>";
                                echo "<button type='submit' style='background: none; border: none; cursor: pointer; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;'>";
                                echo "<span class='showcase-title'>" . html_entity_decode($podata['product_name']) . "</span>";
                                echo "</button>";

                                echo "</form>";
                              

                              // Afficher la note
                              echo "<div class='showcase-rating'>";
                              $nbr_rating = calculerMoyenneRating($productId, $conn);
                              $arr = diviserNombre($nbr_rating);
                              for ($i = 1; $i <= 5 ; $i++) {
                                if($i<=$arr['partie_entiere']){
                                  echo "<ion-icon name='star'></ion-icon>";
                                }elseif($i==$arr['partie_entiere']+1){
                                  if ($arr['partie_decimale'] > 0.50) {
                                    echo "<ion-icon name='star-half-outline'></ion-icon>";
                                  }else{
                                    echo "<ion-icon name='star-outline'></ion-icon>";
                                  }

                                }else{
                                  echo "<ion-icon name='star-outline'></ion-icon>";

                                }

                                
                                  
                              }
                              
                              echo "</div>";

                              // Afficher le prix
                              echo "<div class='price-box'>";
                             if($type == 'usd'){
                              $Price=$podata['price'];
                              $vente_Price=$podata['vente_price'];
                              $ty='&dollar;';
                                
                             }elseif($type == 'eur'){
                              $Price=$podata['price']*$usdToEur;
                              $vente_Price=$podata['vente_price']*$usdToEur;
                              $ty='&euro;';

                               
                             }elseif($type == 'mad'){
                              $Price=$podata['price']*$usdToMad;
                              $vente_Price=$podata['vente_price']*$usdToMad;
                              $ty='DH';
                              
                             }else{
                              $Price=$podata['price'];
                              $vente_Price=$podata['vente_price'];
                              $ty='&dollar;';
                             }
                              echo "<del>".$ty."" . htmlspecialchars($Price) . "</del>";
                              echo "<p class='price'>".$ty."" . htmlspecialchars($vente_Price) . "</p>";
                              echo "</div>";

                              echo "</div>";
                              echo "</div>";
                          }
                      }
                  }
              ?>

               

              </div>

            </div>

          </div>

        </div>
  


        <div class="product-box">

          <!--
            - PRODUCT MINIMAL
          -->

          <div class="product-minimal">

            <!--New Arrivals product   vvv-->
            <div class="product-showcase">
              <!--name-->
         
              <h2 class="title">New Arrivals</h2>
              <!--contenu-->
              <div class="showcase-wrapper has-scrollbar">
                <?php
                     $sql_produit_Arrivals = "SELECT * FROM products
                                              WHERE active = TRUE
                                              ORDER BY created_at DESC
                                              LIMIT 8";
                     $stm_produit_Arrivals=$conn->prepare($sql_produit_Arrivals);
                     $stm_produit_Arrivals->execute();
                     $rows_produit_Arrivals=$stm_produit_Arrivals->fetchAll(PDO::FETCH_ASSOC);
                     if(count($rows_produit_Arrivals)>0){
                        $count=1;
                        if(count($rows_produit_Arrivals)>4){
                          $count=2 ;
                        }
                        
                        for ($i = 0; $i <$count ; $i++) {
                     echo '<div class="showcase-container">';
                          // Récupérer les produits pour le groupe actuel
                          $next_4_produits = array_slice($rows_produit_Arrivals, $i * 4, 4);
                          foreach ($next_4_produits as $produit) {
                         echo '<div class="showcase">';
                            $id_produit_Arrivals=$produit['id'];
                            $sql_image_produit_Arrivals = "SELECT g.image AS image_path
                                                            FROM variant_options v
                                                            INNER JOIN gallery g ON v.id = g.product_variant_id
                                                            WHERE v.product_id = :product_id
                                                              AND v.active = TRUE
                                                            ORDER BY g.is_thumbnail DESC, g.created_at ASC
                                                            LIMIT 1";

                            $stm_image_produit_Arrival=$conn->prepare( $sql_image_produit_Arrivals);
                            $stm_image_produit_Arrival->execute(['product_id'=> $id_produit_Arrivals]);
                            $resulte_image_produit_Arrival= $stm_image_produit_Arrival->fetch(PDO::FETCH_ASSOC);
                              if($resulte_image_produit_Arrival){
                                #lien avec image 
                                echo '<form action="NaturelleShop/php/product_pross.php" method="POST">';
                                     echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($produit['id']) . "'>";
                                    echo '<button type="submit" class="showcase-img-box">';
                                         echo "<img src='NaturelleShop/images/products/" . htmlspecialchars($resulte_image_produit_Arrival['image_path']) . "' width='70' height='72' class='showcase-img'>";
                                    echo '</button>';
                                echo  '</form> ';                            
                              }
                              #contenu
                          echo '<div class="showcase-content">';
                                    #lien avec titre 

                                    echo '<form action="NaturelleShop/php/product_pross.php" method="POST">';
                                       echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($produit['id']) . "'>";
                                        echo '<button type="submit" class="showcase-link" style="background: none; border: none; padding: 0; cursor: pointer;">';
                                             echo "<h6 class='showcase-title'>" . html_entity_decode($produit['product_name']) . "</h6>";
                                        echo '</button>';
                                    echo '</form>';
                                    #prace and solde 
                                    echo  "<div class='price-box'>";
                                    if($type == 'usd'){
                                      $Price=$produit['price'];
                                      $vente_Price=$produit['vente_price'];
                                      $ty='&dollar;';
                                        
                                     }elseif($type == 'eur'){
                                      $Price=$produit['price']*$usdToEur;
                                      $vente_Price=$produit['vente_price']*$usdToEur;
                                      $ty='&euro;';
        
                                       
                                     }elseif($type == 'mad'){
                                      $Price=$produit['price']*$usdToMad;
                                      $vente_Price=$produit['vente_price']*$usdToMad;
                                      $ty='DH';
                                      
                                     }else{
                                      $Price=$produit['price'];
                                      $vente_Price=$produit['vente_price'];
                                      $ty='&dollar;';
                                     }
                                        echo "<p class='price'>".$ty."".htmlspecialchars($vente_Price)."</p>";
                                        echo " <del>".$ty."".htmlspecialchars($Price)."</del>";
                                    echo  "</div>";
                                                    

                              
                             echo"</div>";
                             echo"</div>";

                          }
                            echo "</div>";
                         
                            
                        }
                     }
                ?>                             
              </div>

            </div>
            


            <!--Trending product vvv-->
            <div class="product-showcase">
            
              <h2 class="title">Trending</h2>
            
              <div class="showcase-wrapper  has-scrollbar">


                 <?php 
                    $top_trending=getTopOrderedProducts($conn);
                    if($top_trending){
                      $nbrrep=1;

                      if(count($top_trending)>4){
                        $nbrrep=2;

                      }
                      for($i=0 ; $i<$nbrrep ; $i++){
                        $toptrending_next_4_produits = array_slice($top_trending, $i * 4, 4);

                         echo '<div class="showcase-container">';
                         foreach($toptrending_next_4_produits as $toptrending){
                             $id_trending=$toptrending['id'];
                             $trending="SELECT * FROM products WHERE id=?";
                             $tren=$conn->prepare($trending);
                             $tren->execute([$id_trending]);
                             $trending_data=$tren->fetch(PDO::FETCH_ASSOC);
                             echo '<div class="showcase">';
                                      $sql_image_produit_trending = "SELECT g.image AS image_path
                                      FROM variant_options v
                                      INNER JOIN gallery g ON v.id = g.product_variant_id
                                      WHERE v.product_id = :product_id
                                      ORDER BY g.is_thumbnail DESC, g.created_at ASC
                                      LIMIT 1";
                                      $stm_image_produit_trending=$conn->prepare( $sql_image_produit_trending);
                                      $stm_image_produit_trending->execute(['product_id'=> $id_trending]);
                                      $resulte_image_produit_trinding= $stm_image_produit_trending->fetch(PDO::FETCH_ASSOC);
                                      if($resulte_image_produit_trinding){
                                        #lien avec image 
                                        echo "<form action='NaturelleShop/php/product_pross.php' method='POST' class='showcase-img-box'>";
                                        echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($toptrending['id']) . "'>";
                                        echo "<button type='submit' style='background: none; border: none; padding: 0; cursor: pointer;'>";
                                        echo "<img src='NaturelleShop/images/products/" . htmlspecialchars($resulte_image_produit_trinding['image_path']) . "' alt='relaxed short full sleeve t-shirt' width='70' height='72' class='showcase-img'>";
                                        echo "</button>";
                                        echo "</form>";                             
                                      }
                                      #contenu
                                      echo "<div class='showcase-content'>";
                                      #lien avec titre 
                                      echo "<form action='NaturelleShop/php/product_pross.php' method='POST'>";
                                      echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($toptrending['id']) . "'>";
                                      echo "<button type='submit' style='background: none; border: none; padding: 0; cursor: pointer;'>";
                                      echo "<h4 class='showcase-title'>" . html_entity_decode($trending_data['product_name']) . "</h4>";
                                      echo "</button>";
                                      echo "</form>";

                                      #prace and solde 
                                      echo  '<div class="price-box">';
                                      if($type == 'usd'){
                                        $Price=$trending_data['price'];
                                        $vente_Price=$trending_data['vente_price'];
                                        $ty='&dollar;';
                                          
                                       }elseif($type == 'eur'){
                                        $Price=$trending_data['price']*$usdToEur;
                                        $vente_Price=$trending_data['vente_price']*$usdToEur;
                                        $ty='&euro;';
          
                                         
                                       }elseif($type == 'mad'){
                                        $Price=$trending_data['price']*$usdToMad;
                                        $vente_Price=$trending_data['vente_price']*$usdToMad;
                                        $ty='DH';
                                        
                                       }else{
                                        $Price=$trending_data['price'];
                                        $vente_Price=$trending_data['vente_price'];
                                        $ty='&dollar;';
                                       }
                                          echo "<p class='price'>".$ty."".htmlspecialchars($vente_Price)."</p>";
                                          echo " <del>".$ty."".$Price."</del>";
                                      echo  "</div>";
                                              

                        
                        echo"</div>";
                                       



                                      

                             echo "</div>";
                             
                               
                         }
                         echo "</div>";


                      }
                    }
                      
                 ?>



                              
                              
            
                
            
              </div>
            
            </div>



            <!--Top Rated product  vvv-->
            <div class="product-showcase">
            
              <h2 class="title">Top Rated</h2>
            
              <div class="showcase-wrapper  has-scrollbar">
              <?php 
              
                    $nbr = 8;
                    $id_pro_top_rating = getTopProductsByRating($conn, $nbr);

                    if (count($id_pro_top_rating) > 0) {
                        // Déterminer le nombre de groupes à afficher
                        $nbrrep = (count($id_pro_top_rating) > 4) ? 2 : 1;


                        // Préparer la requête pour récupérer les détails des produits
                        $product_ids = array_column($id_pro_top_rating, 'id');
                        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
                        $posql_top = "SELECT * FROM products WHERE id IN ($placeholders)";
                        $postm_top = $conn->prepare($posql_top);
                        $postm_top->execute($product_ids);
                        $products = $postm_top->fetchAll(PDO::FETCH_ASSOC);
                        $products_data = [];
                        foreach ($products as $product) {
                            $products_data[$product['id']] = $product;
                        }

                        for ($i = 0; $i < $nbrrep; $i++) {
                            // Récupérer les produits pour le groupe actuel
                            $toprating_next_4_produits = array_slice($id_pro_top_rating, $i * 4, 4);
                            echo "<div class='showcase-container'>";
                            
                            foreach ($toprating_next_4_produits as $top) {
                                $id_produit_toprating = $top['id'];
                                $product_data = $products_data[$id_produit_toprating];
                                // Récupérer l'image du produit
                              $posql_image_produit = "SELECT g.image AS image_path
                                                      FROM variant_options v
                                                      INNER JOIN gallery g ON v.id = g.product_variant_id
                                                      WHERE v.product_id = :product_id
                                                      ORDER BY g.is_thumbnail DESC, g.created_at ASC
                                                      LIMIT 1";
                              $postm_img = $conn->prepare($posql_image_produit);
                              $postm_img->execute(['product_id' => $id_produit_toprating]);
                              $podata_img = $postm_img->fetch(PDO::FETCH_ASSOC);


                                
                                

                                echo "<div class='showcase'>";
                                if($podata_img){
                                  $image_path= $podata_img['image_path'];
                                    echo "<form action='NaturelleShop/php/product_pross.php' method='POST' class='showcase-img-box'>";
                                    echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($id_produit_toprating) . "'>";
                                    echo "<button type='submit' style='background: none; border: none; padding: 0; cursor: pointer;'>";
                                    echo "<img src='NaturelleShop/images/products/" . htmlspecialchars($image_path) . "' alt='Image du produit' width='70' height='72' class='showcase-img'>";
                                    echo "</button>";
                                    echo "</form>";
                                }

                                


                                if ($product_data) {
                                    echo '<div class="showcase-content">';

                                    echo "<form action='NaturelleShop/php/product_pross.php' method='POST'>";
                                    echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($id_produit_toprating) . "'>";
                                    echo "<button type='submit' style='background: none; border: none; padding: 0; cursor: pointer;'>";
                                    echo "<h4 class='showcase-title'>" . html_entity_decode($product_data['product_name']) . "</h4>";
                                    echo "</button>";
                                    echo "</form>";

                                    echo "<div class='price-box'>";
                                    if($type == 'usd'){
                                      $Price=$product_data['price'];
                                      $vente_Price=$product_data['vente_price'];
                                      $ty='&dollar;';
                                        
                                     }elseif($type == 'eur'){
                                      $Price=$product_data['price']*$usdToEur;
                                      $vente_Price=$product_data['vente_price']*$usdToEur;
                                      $ty='&euro;';
        
                                       
                                     }elseif($type == 'mad'){
                                      $Price=$product_data['price']*$usdToMad;
                                      $vente_Price=$product_data['vente_price']*$usdToMad;
                                      $ty='DH';
                                      
                                     }else{
                                      $Price=$product_data['price'];
                                      $vente_Price=$product_data['vente_price'];
                                      $ty='&dollar;';
                                     }
                                    echo "<p class='price'>".$ty."" . htmlspecialchars($vente_Price) . "</p>";
                                    echo "<del>".$ty."" . htmlspecialchars($Price) . "</del>";
                                    echo "</div>";
                                    echo "</div>"; // .showcase-content
                                }

                                echo "</div>"; // .showcase
                            }
                            
                            echo "</div>"; // .showcase-container
                        }
                    }
                  
              ?>
              

            
              </div>
            
            </div>

          </div>

      

          <!--
            - PRODUCT FEATURED
          -->

        

          <!--
            - PRODUCT GRID vvv
          -->

          <div class="product-main">
            <!--name-->
            <h2 class="title">New Products</h2>
            <!--contenu-->
            <div class="product-grid">
              <?php
                   $sql_new = "SELECT * FROM products
                   WHERE active = TRUE
                   ORDER BY created_at DESC
                   LIMIT 16";
       
                    $stm_new=$conn->prepare($sql_new);
                    $stm_new->execute();
                    $rows_new=$stm_new->fetchAll(PDO::FETCH_ASSOC);
                   if(count($rows_new)>0){
                    foreach ($rows_new as $produit_two) {
                      #prodcts
                      echo "<div class='showcase'>";
                            $id_produit_Arrivals_two=$produit_two['id'];
                            #l'image et l'action 
                            echo "<div class='showcase-banner'>";
                                  #les image de ce produit
                                  $sql_image_produit_Arrivals_tow = "SELECT g.image AS image_path
                                  FROM variant_options v
                                  INNER JOIN gallery g ON v.id = g.product_variant_id
                                  WHERE v.product_id = :product_id
                                  ORDER BY g.is_thumbnail DESC, g.created_at ASC
                                  LIMIT 2";
                                   $stm_image_produit_Arrival_tow=$conn->prepare( $sql_image_produit_Arrivals_tow);
                                   $stm_image_produit_Arrival_tow->execute(['product_id'=> $id_produit_Arrivals_two]);
                                   $resulte_image_produit_Arrival_tow= $stm_image_produit_Arrival_tow->fetchAll(PDO::FETCH_ASSOC);
                                     

                                    // Assigner les images récupérées aux variables
                                    if (isset($resulte_image_produit_Arrival_tow[0]['image_path'])) {
                                        $image_default = $resulte_image_produit_Arrival_tow[0]['image_path'];
                                    }else{
                                      $image_default='default.webp';

                                    }
                                    if (isset($resulte_image_produit_Arrival_tow[1]['image_path'])) {
                                        $image_hover = $resulte_image_produit_Arrival_tow[1]['image_path'];
                                    }else{
                                      $image_hover ='default.webp';

                                    }


                                   
                                    echo "<img src='NaturelleShop/images/products/" . htmlspecialchars($image_default) . "' alt='Mens Winter Leathers Jackets' width='300' class='product-img default'>";                                     
                                    
                                      echo "<img src='NaturelleShop/images/products/" . htmlspecialchars($image_hover) . "' alt='Mens Winter Leathers Jackets' width='300' class='product-img hover'>";
                                     

                                  
                                   
                                   #réduction en porsentage
                                   
                            if($produit_two['compare_price'] > 0){
                              echo "<p class='showcase-badge'>".$produit_two['compare_price']."%</p>";
                            } 
                             #action que tu es peut faire
                             echo 
                             "<div class='showcase-actions'>";

                                  
                                  echo "<form action='NaturelleShop/php/product_pross.php' method='POST'>";
                                  echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($produit_two['id']) . "'>";
                                  echo "<button type='submit' class='btn-action' style='background: none; border: none; cursor: pointer;'>";
                                  echo "<ion-icon name='eye-outline'></ion-icon>";
                                  echo "</button>";
                                  echo "</form>";



                              echo '</div>';
                                                              
                            echo "</div>";
                            #d'information 
                            echo "<div class='showcase-content'>";
                                  #name
                                  echo "<form action='NaturelleShop/php/product_pross.php' method='POST' class='showcase-category'>";
                                  echo "<input type='hidden' name='id_product_simple' value='" . htmlspecialchars($produit_two['id']) . "'>";
                                  echo "<button type='submit' style='background: none; border: none; cursor: pointer;'>";
                                  echo html_entity_decode($produit_two['product_name']);
                                  echo "</button>";
                                  echo "</form>";

                                  #rating
                                  echo  "<div class='showcase-rating'>";
                                             $new=calculerMoyenneRating($id_produit_Arrivals_two, $conn);
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
                                   echo      "</div>";
                                  #price and solde
                                  if($type == 'usd'){
                                    $Price=$produit_two['price'];
                                    $vente_Price=$produit_two['vente_price'];
                                    $ty='&dollar;';
                                      
                                   }elseif($type == 'eur'){
                                    $Price=$produit_two['price']*$usdToEur;
                                    $vente_Price=$produit_two['vente_price']*$usdToEur;
                                    $ty='&euro;';
      
                                     
                                   }elseif($type == 'mad'){
                                    $Price=$produit_two['price']*$usdToMad;
                                    $vente_Price=$produit_two['vente_price']*$usdToMad;
                                    $ty='DH';
                                    
                                   }else{
                                    $Price=$produit_two['price'];
                                    $vente_Price=$produit_two['vente_price'];
                                    $ty='&dollar;';
                                   }
                                  echo    "<div class='price-box'>
                                              <p class='price'>".$ty."".$vente_Price."</p>
                                              <del>".$ty."".$Price."</del>
                                          </div>";
                                  
                                  
                            echo "</div>";



                      echo "</div>";


                    }
                      
                   }else{
                        echo "Aucun produit trouvé.";

                   }
              ?>

              

            </div>

          </div>

        </div>

      </div>

    </div>





    <!--
      - TESTIMONIALS, CTA & SERVICE
    -->
    <!--
    <div>

      <div class="container">

        <div class="testimonials-box">

          
            - TESTIMONIALS
        
            <?php 
                $dataTestimonial=getTopThreeReviews($conn);
                if($dataTestimonial){
                  echo '<div class="testimonial">';
                        #name
                        echo '<h2 class="title">testimonial</h2>';
                        #contenu
                        echo '<div class="showcase-wrapper has-scrollbar">';
                              foreach($dataTestimonial as $testimonial){
                                echo '<div class="testimonial-card">';
                                 
              
                                  echo '<img src="NaturelleShop/images/icons/quotes.svg" alt="quotation" class="quotation-img" width="26">';
                                  echo "<p class='testimonial-desc'>".htmlspecialchars($testimonial['comment'])."</p>";
                                  echo '<img src="NaturelleShop/images/icons/quotes.svg" alt="quotation" class="quotation-img" width="26">';


                                          
                                        
                                     
                                echo '</div>';                               
                              }
                        echo '</div>';                      
                  echo "</div>";

                }
            ?>

        

        
            - CTA
          
          <?php 
               $cta=getCTA($conn);
               if($cta){
                $sl="SELECT product_id FROM product_coupons WHERE coupon_id=?";
                $sm=$conn->prepare($sl);
                $sm->execute([$cta['id']]);
                $ct=$sm->fetchAll(PDO::FETCH_ASSOC);
                if($ct){
                  $_SESSION['tpore_proid']=$ct;
                }
                echo '<div class="cta-container">';
                  echo ' <img src="NaturelleShop/images/cta-banner.jpg" alt="summer collection" class="cta-banner">';
                  echo '<a href="#" class="cta-content">';
                       echo "<p class='discount'>".htmlspecialchars($cta['discount_amount'])." Discount</p>";
                       echo '<h2 class="cta-title">collection</h2>';
                       echo '<p class="cta-text">Starting @ $10</p>';
                       echo '<button class="cta-btn">Shop now</button>';
                  echo '</a>';

                echo '</div>';


                    

                
          
              }

          ?>
        
          

           

            

              

              

              

              

          



        
            - SERVICE
          

        <div class="service">

            <h2 class="title">Our Services</h2>

            <div class="service-container">

              <a href="#" class="service-item">

                <div class="service-icon">
                  <ion-icon name="boat-outline"></ion-icon>
                </div>

                <div class="service-content">

                  <h3 class="service-title">Worldwide Delivery</h3>
                  <p class="service-desc">For Order Over $100</p>

                </div>

              </a>

              <a href="#" class="service-item">
              
                <div class="service-icon">
                  <ion-icon name="rocket-outline"></ion-icon>
                </div>
              
                <div class="service-content">
              
                  <h3 class="service-title">Next Day delivery</h3>
                  <p class="service-desc">UK Orders Only</p>
              
                </div>
              
              </a>

              <a href="#" class="service-item">
              
                <div class="service-icon">
                  <ion-icon name="call-outline"></ion-icon>
                </div>
              
                <div class="service-content">
              
                  <h3 class="service-title">Best Online Support</h3>
                  <p class="service-desc">Hours: 8AM - 11PM</p>
              
                </div>
              
              </a>

              <a href="#" class="service-item">
              
                <div class="service-icon">
                  <ion-icon name="arrow-undo-outline"></ion-icon>
                </div>
              
                <div class="service-content">
              
                  <h3 class="service-title">Return Policy</h3>
                  <p class="service-desc">Easy & Free Return</p>
              
                </div>
              
              </a>

              <a href="#" class="service-item">
              
                <div class="service-icon">
                  <ion-icon name="ticket-outline"></ion-icon>
                </div>
              
                <div class="service-content">
              
                  <h3 class="service-title">30% money back</h3>
                  <p class="service-desc">For Order Over $100</p>
              
                </div>
              
              </a>

            </div>

          </div>

        </div>

      </div>

    </div>
      -->
  </main>















  <!--
    - FOOTER
  -->

  <footer>
        <!--
        <?php 
             if (count($categories1) > 0) {
                echo "
                      <div class='footer-category'>

                         <div class='container'>
                              <h2 class='footer-category-title'>Brand directory</h2>
                              

                     ";
                // Requête pour récupérer les catégories principales (celles dont parent_id = 1)
                $sql_footer = "SELECT * FROM categories WHERE parent_id = 1";
                $stmt_footer= $conn->prepare($sql_footer);
                $stmt_footer->execute();
                $footer_categories1 = $stmt_footer->fetchAll(PDO::FETCH_ASSOC);
                // Vérifier s'il y a des catégories principales
                if (count($footer_categories1) > 0) {
                    foreach ($footer_categories1 as $footer_cat) {
                        $footer_category_id1 = $footer_cat["id"];
                        echo"<div class='footer-category-box'>";
                        echo "<h3 ><a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($footer_cat["id"])."' class='category-box-title'>".html_entity_decode($footer_cat["name"]).":</a></h3>";
                        // Requête pour récupérer les sous-catégories de la catégorie principale
                        $footer_sql_sub1 = "SELECT * FROM categories WHERE parent_id = :parent_id1";
                        $footer_stmt_sub1 = $conn->prepare($footer_sql_sub1);
                        $footer_stmt_sub1->execute(['parent_id1' => $footer_category_id1]);
                        $footre_sub_categories1 = $footer_stmt_sub1->fetchAll(PDO::FETCH_ASSOC);
                        // Vérifier s'il y a des sous-catégories
                        if (count($footre_sub_categories1) > 0) {
                          foreach ($footre_sub_categories1 as $footre_sub_cat) {
                                   echo "<a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($footre_sub_cat["id"])."' class='footer-category-link'>".html_entity_decode($footre_sub_cat["name"])."</a>";
                         } 
                          echo "</div>";                                                   
                        }
                    }
                }
                // Requête pour récupérer les catégories secondaires (celles dont parent_id != 1)
                $footre_sql_secondary1 = "SELECT * FROM categories WHERE parent_id != 1";
                $footre_stmt_secondary1 = $conn->prepare($footre_sql_secondary1);
                $footre_stmt_secondary1->execute();
                $footre_secondary_categories1 = $footre_stmt_secondary1->fetchAll(PDO::FETCH_ASSOC);
                // Vérifier s'il y a des catégories secondaires
                if (count($footre_secondary_categories1) > 0) {
                  foreach ($footre_secondary_categories1 as $footre_secondary_cat) {
                    $footre_secondary_category_id1 = $footre_secondary_cat["id"];
                     // Requête pour récupérer les sous-catégories des catégories secondaires
                     $footre_sql_sub_secondary1 = "SELECT * FROM categories WHERE parent_id = :parent_id1";
                     $footre_stmt_sub_secondary1 = $conn->prepare($footre_sql_sub_secondary1);
                     $footre_stmt_sub_secondary1->execute(['parent_id1' => $footre_secondary_category_id1]);
                     $footre_sub_secondary_categories1 = $footre_stmt_sub_secondary1->fetchAll(PDO::FETCH_ASSOC);
                     // Vérifier s'il y a des sous-catégories pour les catégories secondaires
                     if (count($footre_sub_secondary_categories1) > 0) {
        
                      echo "<div class='footer-category-box'>";
                      echo "<h3 ><a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($footre_secondary_cat["id"])."'' class='category-box-title'>".html_entity_decode($footre_secondary_cat["name"]).":</a></h3>";
                      foreach ($footre_sub_secondary_categories1 as $fopter_sub_secondary_cat1) {
                        echo "<a href='NaturelleShop/php/categorie_pross.php?id=".htmlspecialchars($fopter_sub_secondary_cat1["id"])."' class='footer-category-link'>".html_entity_decode($fopter_sub_secondary_cat1["name"])."</a>";
                      }
                      echo "</div>";
                       

                     }



                  }

                }

                echo "  </div>

                            </div>";
                
             }
             
        ?>
        -->



    <div class="footer-nav">

      <div class="container">

       

        

        <ul class="footer-nav-list">
        
          <li class="footer-nav-item">
            <h2 class="nav-title">Our Company</h2>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">Delivery</a>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">Legal Notice</a>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">Terms and conditions</a>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">About us</a>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">Secure payment</a>
          </li>
        
        </ul>

        <ul class="footer-nav-list">
        
          <li class="footer-nav-item">
            <h2 class="nav-title">Services</h2>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">Prices drop</a>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">New products</a>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">Best sales</a>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">Contact us</a>
          </li>
        
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">Sitemap</a>
          </li>
        
        </ul>

        <ul class="footer-nav-list">

          <li class="footer-nav-item">
            <h2 class="nav-title">Contact</h2>
          </li>

          <li class="footer-nav-item flex">
            <div class="icon-box">
              <ion-icon name="location-outline"></ion-icon>
            </div>

            <address class="content">
              419 State 414 Rte
              Beaver Dams, New York(NY), 14812, USA
            </address>
          </li>

          <li class="footer-nav-item flex">
            <div class="icon-box">
              <ion-icon name="call-outline"></ion-icon>
            </div>

            <a href="tel:+607936-8058" class="footer-nav-link">(607) 936-8058</a>
          </li>

          <li class="footer-nav-item flex">
            <div class="icon-box">
              <ion-icon name="mail-outline"></ion-icon>
            </div>

            <a href="mailto:example@gmail.com" class="footer-nav-link">example@gmail.com</a>
          </li>

        </ul>

        <ul class="footer-nav-list">

          <li class="footer-nav-item">
            <h2 class="nav-title">Follow Us</h2>
          </li>

          <li>
            <ul class="social-link">

              <li class="footer-nav-item">
                <a href="#" class="footer-nav-link">
                  <ion-icon name="logo-facebook"></ion-icon>
                </a>
              </li>

              <li class="footer-nav-item">
                <a href="#" class="footer-nav-link">
                  <ion-icon name="logo-twitter"></ion-icon>
                </a>
              </li>

              <li class="footer-nav-item">
                <a href="#" class="footer-nav-link">
                  <ion-icon name="logo-linkedin"></ion-icon>
                </a>
              </li>

              <li class="footer-nav-item">
                <a href="#" class="footer-nav-link">
                  <ion-icon name="logo-instagram"></ion-icon>
                </a>
              </li>

            </ul>
          </li>

        </ul>

      </div>

    </div>

    <div class="footer-bottom">

      <div class="container">

        <img src="NaturelleShop/images/payment.png" alt="payment method" class="payment-img">

        <p class="copyright">
          Copyright &copy; <a href="#">Anon</a> all rights reserved.
        </p>

      </div>

    </div>

  </footer>
   








  <!--
    - custom js link
  -->
  <script src="NaturelleShop/assets/js/script.js"></script>

  <!--
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script>
            // NOTIFICATION 
      

            function checkForNewProduct() {
                fetch('NaturelleShop/php/check_new_product.php')
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
                                    <img src="NaturelleShop/images/products/${imagePath}" alt="${data.product_name}" width="80" height="70">
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


          function redirectToPage(selectElement) {
            // Obtenez la valeur sélectionnée
            var selectedValue = selectElement.value;

            // Créez l'URL de redirection
            var url = "index.php?typeargant=" + encodeURIComponent(selectedValue);

            // Redirigez vers l'URL si une valeur est sélectionnée
            if (selectedValue) {
                window.location.href = url;
            }
        }



  </script>

</body>

</html>
