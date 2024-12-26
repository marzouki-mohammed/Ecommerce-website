
<?php
        session_start();
        // Connexion à la base de données
        include "../php/db_connect.php";
        if (!isset($conn)) {
            echo "Database connection is not set.";
            exit;
        }

        //client
        if (!isset($_SESSION['user_id_cart']) || empty($_SESSION['user_id_cart'])) {
            echo "client not seet";
            exit;   
        }

        
        $id_users = intval($_SESSION['user_id_cart']);
        //cart
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: ../../index.php");
            exit; 
        }
        $cart = $_SESSION['cart'];
        //info payment
        if (!isset($_SESSION['payment']) || empty($_SESSION['payment'])) {
            echo 'info payement not set';
            exit; 
        }
        if (!isset($_SESSION['id_cart']) || empty($_SESSION['id_cart'])) {
            echo 'cart not set ';
            exit; 
        }
        //calcule du prix
        $apiKey = 'YOUR_API_KEY';
        $url = "https://api.exchangerate-api.com/v4/latest/USD";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $usdToEur = $data['rates']['EUR'];
        $usdToMad = $data['rates']['MAD'];

        $type = isset($_SESSION['type_Argant']) ? $_SESSION['type_Argant'] : '';
        $taux = 1;
        $ty = '&dollar;';

        if ($type == 'usd') {
            $taux = 1;
            $ty = '&dollar;';
        } elseif ($type == 'eur') {
            $taux = $usdToEur;
            $ty = '&euro;';
        } elseif ($type == 'mad') {
            $taux = $usdToMad;
            $ty = 'DH';
        }
        //addres
        if (!isset($_SESSION['id_address']) || empty($_SESSION['id_address'])) {
            echo 'address not set';
            exit; 
        }      
        $id_address = intval($_SESSION['id_address']);
        $sql_address = "SELECT * FROM user_address WHERE id=?";
        $stm_address = $conn->prepare($sql_address);
        $stm_address->execute([$id_address]);
        $resulte_address = $stm_address->fetch();
        if (!$resulte_address) {
            header("Location: ../../index.php");
            exit; 
        }
        $city_client = $resulte_address['city'];
        $adress_client = $resulte_address['address_line1'];

        function getCoordinatesNominatim($address) {
            $address = urlencode($address);
            $url = "https://nominatim.openstreetmap.org/search?q={$address}&format=json&limit=1";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'User-Agent: Mozilla/5.0 (compatible; MyEcommerceSite/1.0; +http://votre-site-web.com)'
            ]);

            // Désactiver temporairement la vérification SSL (ne pas utiliser en production)
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'Erreur cURL : ' . curl_error($ch);
                return false;
            }

            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data[0])) {
                $lat = $data[0]['lat'];
                $lng = $data[0]['lon'];
                return ['lat' => $lat, 'lng' => $lng];
            } else {
                return false;
            }
        }

        function haversineDistance($coords1, $coords2) {
            $earthRadius = 6371;
            $latFrom = deg2rad($coords1['lat']);
            $lonFrom = deg2rad($coords1['lng']);
            $latTo = deg2rad($coords2['lat']);
            $lonTo = deg2rad($coords2['lng']);
            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;
            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            return $earthRadius * $angle;
        }

        function getShippersByCity($conn, $city, $address) { 
            // Préparer la requête SQL pour trouver les expéditeurs dans la ville donnée
            $sql = 'SELECT * FROM shippers WHERE city = :city';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':city', $city, PDO::PARAM_STR);
            $stmt->execute();
            $shippers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            // Initialiser la distance minimale à une valeur très grande et le shipper ID à null
            $min_distance = PHP_INT_MAX; 
            $closest_shipper_id = null;
        
            // Vérifier s'il y a des expéditeurs dans la ville donnée
            if ($shippers) {
                foreach ($shippers as $shipp) {
                    // Récupérer les coordonnées GPS des deux adresses
                    $warehouse_coords = getCoordinatesNominatim($shipp['adress']);
                    $client_coords = getCoordinatesNominatim($address);
                    
                    if ($warehouse_coords && $client_coords) {
                        // Calculer la distance entre l'entrepôt et l'adresse du client
                        $distance = haversineDistance($warehouse_coords, $client_coords);
                        
                        // Mettre à jour la distance minimale et le shipper ID si nécessaire
                        if ($distance < $min_distance) {
                            $min_distance = $distance;
                            $closest_shipper_id = $shipp['id'];
                        }
                    }
                    sleep(1); // Respecter les limites de l'API
                }
            } else {
                // Si aucun expéditeur n'est trouvé dans la ville, chercher parmi tous les expéditeurs
                $sql = 'SELECT * FROM shippers';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $shippers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($shippers as $shipp) {
                    // Récupérer les coordonnées GPS des deux adresses
                    $warehouse_coords = getCoordinatesNominatim($shipp['adress']);
                    $client_coords = getCoordinatesNominatim($address);
                    
                    if ($warehouse_coords && $client_coords) {
                        // Calculer la distance entre l'entrepôt et l'adresse du client
                        $distance = haversineDistance($warehouse_coords, $client_coords);
                        
                        // Mettre à jour la distance minimale et le shipper ID si nécessaire
                        if ($distance < $min_distance) {
                            $min_distance = $distance;
                            $closest_shipper_id = $shipp['id'];
                        }
                    }
                    sleep(1); // Respecter les limites de l'API
                }
            }
        
            // Retourner un tableau avec l'ID du shipper le plus proche et la distance minimale
            return [
                'shipper_id' => $closest_shipper_id,
                'distance' => $min_distance
            ];
        }
        

        function calculateShippingCost($distance) {    
            global $taux;       
            // Exemple de logique 
            $cost_per_km_endellar = 0.2;
            $prix_km = $cost_per_km_endellar ;
            // Calcul des frais
            $shipping_cost = $distance * $prix_km;
            // Retourner les frais avec 2 décimales
            return number_format(floatval($shipping_cost), 2, '.', '');
        }
        

        $table=getShippersByCity($conn , $city_client , $adress_client);
        $_SESSION['id_shipper']=intval($table['shipper_id']);
        $frais_livresion= calculateShippingCost(floatval($table['distance'])) * $taux;
        $_SESSION['fraisen']=calculateShippingCost(floatval($table['distance']));

       

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop</title>
    <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">
     <!-- Montserrat Font -->
     <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style_fin.css" />
   
    
</head>
<body>
 

    <div class="master-container">
            <div class="card">
                <label class="title">Your cart</label>
                
                <?php
                    $prix_total=0;
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
                                    
                                    $prixTotalitem=floatval($item['price'])*intval($item['quantity']);
                                    $prix_total+=$prixTotalitem;
                                    echo "<span id='total'>".$ty."".htmlspecialchars($prixTotalitem)."</span>";
                                echo '</div>';
                            echo '</div>';                           
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
                        
            </div>

            
            <div class="card checkout">
                <label class="title">Delivery info</label>
                <?php 
                    $sql_Shipper="SELECT * FROM shippers WHERE id=?";
                    $stm_Shipper=$conn->prepare($sql_Shipper);
                    $stm_Shipper->execute([intval($table['shipper_id'])]);
                    $resulte_Shipper=$stm_Shipper->fetch();
                    if($resulte_Shipper){
                        echo '<div class="details_Delivery">';
                              echo "<span>".html_entity_decode($resulte_Shipper['shipper_name'])."</span>";
                              echo "<span>".html_entity_decode($resulte_Shipper['email'])."</span>";
                              echo "<span>".html_entity_decode($resulte_Shipper['phone'])."</span>";
                              echo "<span>".html_entity_decode($resulte_Shipper['adress'])."</span>";
                              echo "<span>".html_entity_decode($resulte_Shipper['country'])."</span>";
                              echo "<span>".html_entity_decode($resulte_Shipper['city'])."</span>";
                        echo '</div>';
                    }
                ?>                                       
            </div>

            <div class="card checkout">
                
                <label class="title">Checkout</label>
                <div class="details">
                <span>Your cart total:</span>
                <span><?php echo $prix_total." ".$ty ;?></span>
                <span>Eversion costs:</span>
                <span><?php echo $frais_livresion." ".$ty ;?></span>
                </div>            
                <div class="checkout--footer">
                    <label class="price"><sup><?php echo $ty ;?></sup>
                    <?php 
                    $vent=$prix_total+$frais_livresion;
                    $_SESSION['prix_orders']=$vent;
                    echo $vent ;?>
                    </label>
                    <form action="php/fin.php" method="post" enctype="multipart/form-data">
                         <button type="submit" id="checkout" class="checkout-btn"><span class="material-icons-outlined">check_circle</span></button>
                    </form>
                </div>
            </div>
    </div>
    <div class="checkout--footer">
        
    </div>
    

    
   




</body>
</html>
