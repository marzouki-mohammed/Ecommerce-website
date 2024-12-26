<?php 
     
    session_start();
    include "../php/db_connect.php";

    if (!isset($conn)) {
        echo "La connexion à la base de données n'est pas établie.";
        exit;
    }

    if (!isset($_SESSION['user_id_cart']) || empty($_SESSION['user_id_cart'])) {
        header("Location: loginPayment.php");
        exit;   
    }
    $id_users=intval($_SESSION['user_id_cart']);
    
    if (!isset($_SESSION['cart']) && empty($_SESSION['cart'])) {
        header("Location: ../../index.php");
        exit; 
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
        $sql = "SELECT * FROM users  WHERE id=?";
         $stmt = $conn->prepare($sql);
         $stmt->execute([$id_users]);
         $result = $stmt->fetch();

        

        





    
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" 
          content="width=device-width, initial-scale=1.0">
          <title>NaturelleShop</title>
          <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">
     <!-- Montserrat Font -->
     <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/stylespayment.css">
</head>

<body>
<div class="total">


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
                             echo "<h5 class='cart-product-title'>".htmlspecialchars($item['title'])."</h5>";
                             echo '<div class="price-info">';
                                  echo "<span>".$ty."".htmlspecialchars($item['price'])."</span>";
                                  echo "<span>&times;</span>";
                                  echo "<span id='amt'>".htmlspecialchars($item['quantity'])."</span>";
                                 
                                  $prixTotalitem=floatval($item['price'])*intval($item['quantity']);
                                  $prix_total+=$prixTotalitem;
                                  echo "<span id='total'>".$ty."".htmlspecialchars($prixTotalitem)."</span>";
                             echo '</div>';
                        echo '</div>';
                        

                        echo '<form class="forme1" action="php/delet_cart_item.php" method="post" enctype="multipart/form-data" >';
                              echo "<input type='hidden' name='ifdelet_cart' value='".htmlspecialchars($nbrdelet)."'>";
                              echo '<button type="submit" class="btn_delet" style="background-color:#ffd4d4;">';
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
        </div>


        <div class="card checkout">
            <!--
            <label class="title">Checkout</label>
            <div class="details">
            <span>Your cart total:</span>
            <span><?php echo $prix_total." ".$ty ;?></span>
            <span>Eversion costs:</span>
            <span>3.99$</span>
            <span>Shipping fees:</span>
            <span>4.99$</span>
            </div>
            -->
            <div class="checkout--footer">
               <label class="price"><sup><?php echo $ty ;?></sup><?php echo $prix_total;?></label>
               <p>without Evasion costs</p>
            </div>
        </div>

    </div>

    <div class="container">

        <form action="php/process_checkout.php"  method="post" enctype="multipart/form-data">

            <div class="row">

                <div class="col">
                    <h3 class="title">
                        Billing Address
                    </h3>

                    <div class="inputBox">
                        <label for="Phone_number">
                              Phone number:
                          </label>
                          
                          <input type="number" name="Phone_number" id="Phone_number" 
                                placeholder="Enter your Phone number"  required>

                    </div>



                    <div class="inputBox">
                        <label for="email">
                               Contact email:
                          </label>
                        <input type="text" name="email" id="email" 
                               placeholder="Enter email address" 
                               value="<?php echo htmlspecialchars($result['email']); ?>" required>
                    </div>


                    <div class="inputBox">
                        <label for="address">
                              Address:
                          </label>
                        <input type="text" name="address" id="address" 
                               placeholder="neighborhood, city, country" 
                               required>
                    </div>


                    

                    <div class="flex">

                        <div class="inputBox">
                            <label for="country">
                               Country :
                              </label>
                            <input type="text" name="country" id="country" 
                                   placeholder="Enter state" 
                                   required>
                        </div>

                        <div class="inputBox">  
                            <label for="city">
                                City:
                            </label>
                            <input type="text" name="city" id="city" 
                                placeholder="Enter city" 
                                required>
                        </div>

                    </div>


                    <div class="flex">

                        <div class="inputBox">
                            <label for="state">
                                  State:
                              </label>
                            <input type="text" name="state" id="state" 
                                   placeholder="Enter state" 
                                   required>
                        </div>

                        <div class="inputBox">
                            <label for="zip">
                                  Zip Code:
                              </label>
                            <input type="number" name="zip" id="zip" 
                                   placeholder="123 456" 
                                   required>
                        </div>

                    </div>

                </div>

                <div class="col">
                    <h3 class="title">Payment</h3>

                    <div class="inputBox">
                        <label >
                              Card Accepted:
                          </label>
                        <img src="https://media.geeksforgeeks.org/wp-content/uploads/20240715140014/Online-Payment-Project.webp" 
                             alt="credit/debit card image">
                    </div>


                    <div class="inputBox">
                        <label for="cardName">
                              Name On Card:
                          </label>
                        <input type="text" name="cardName" id="cardName" 
                               placeholder="Enter card name" 
                               required>
                    </div>


                    <div class="inputBox">
                        <label for="cardNum">
                              Credit Card Number:
                          </label>
                        <input type="text" name="cardNum" id="cardNum" 
                               placeholder="1111-2222-3333-4444" 
                               maxlength="19" required>
                    </div>


                    <div class="inputBox">
                        <label for="expMonth">Exp Month:</label>
                        <select name="expMonth" id="expMonth">
                            <option value="">Choose month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>


                    <div class="flex">
                        <div class="inputBox">
                            <label for="expYear">Exp Year:</label>
                            <select name="expYear" id="expYear">
                                <option value="">Choose Year</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                            </select>
                        </div>

                        <div class="inputBox">
                            <label for="cvv">CVV</label>
                            <input type="number" name="cvv" id="cvv" 
                                   placeholder="1234" required>
                        </div>
                    </div>

                </div>

            </div>

            <input type="submit" value="Proceed to Checkout" 
                   class="submit_btn">
        </form>

    </div>
</div>
    <script type="text/javascript" src="assets/js/scriptPayment.js"></script>
</body>

</html>
