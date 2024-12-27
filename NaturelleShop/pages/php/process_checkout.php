<?php
  session_start();
  // Connexion à la base de données
  include "../../php/db_connect.php";
  if (!isset($conn)) {
      echo "Database connection is not set.";
      exit;
  }
  if (!isset($_SESSION['user_id_cart']) || empty($_SESSION['user_id_cart'])) {
    header("Location: ../loginPayment.php");
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




if ($_SERVER["REQUEST_METHOD"] == "POST" 
   && isset($_POST['Phone_number']) && !empty($_POST['Phone_number'])
   && isset($_POST['email']) && !empty($_POST['email'])
   && isset($_POST['address']) && !empty($_POST['address'])
   && isset($_POST['city']) && !empty($_POST['city'])
   && isset($_POST['country']) && !empty($_POST['country'])
   && isset($_POST['state']) && !empty($_POST['state'])
   && isset($_POST['zip']) && !empty($_POST['zip'])
   
   && isset($_POST['cardName']) && !empty($_POST['cardName'])
   && isset($_POST['cardNum']) && !empty($_POST['cardNum'])
   && isset($_POST['expMonth']) && !empty($_POST['expMonth'])
   && isset($_POST['expYear']) && !empty($_POST['expYear'])
   && isset($_POST['cvv']) && !empty($_POST['cvv']) 

) {
    //add adress
    $adress=$_POST['address'];
    $contact_email=$_POST['email'];
    $city=$_POST['city'];
    $coutry=$_POST['country'];
    $state=$_POST['state'];
    $code_post=$_POST['zip'];
    $numb_phone=$_POST['Phone_number'];


    $sql_adress = "INSERT INTO user_address (user_id, address_line1 , contact_email, city, state, postal_code, phone_number, country)
    VALUES (?,?,?,?,?,?,?,?)";
    $stm_adress=$conn->prepare($sql_adress);
    $stm_adress->execute([$id_users , $adress , $contact_email,$city,$state,$code_post,$numb_phone, $coutry]);
    $adress_id = $conn->lastInsertId();
    $_SESSION['id_address']= $adress_id;

    $name_cart=$_POST['cardName'];
    $num_cart=$_POST['cardNum'];
    $card_ex_mont=intval($_POST['expMonth']);
    $card_ex_year=intval($_POST['expYear']);
    $card_cvv=$_POST['cvv'];

    $_SESSION['payment']= [
        'name' => $name_cart,
        'card_num' => $num_cart,
        'ex_month' => $card_ex_mont,
        'ex_yeae' => $card_ex_year,
        'cvv' => $card_cvv
    ];
    //add cart 
    $sql_cart="INSERT INTO carts (user_id) VALUES (?)";
    $stm_cart=$conn->prepare($sql_cart);
    $stm_cart->execute([$id_users]);
    //add cart item
    $cart_id = $conn->lastInsertId();
    $_SESSION['id_cart']= $cart_id;

    foreach($cart as $item){
        $type=intval($item['type']);
        if($type == 1 ){
            $id_pro=intval($item['id']);
            $sql_pro="SELECT * FROM products WHERE id=?";
            $stm_pro=$conn->prepare($sql_pro);
            $stm_pro->execute([$id_pro]);
            $result_pro=$stm_pro->fetch();
            if($result_pro){
                $id_var=$item['option'][(string)$id_pro];
                $qunty=intval($item['quantity']);
                $prixtotale=$result_pro['vente_price']*$taux*$qunty;
                $sql_cart_item="INSERT INTO cart_items  (cart_id , product_id , product_variant_id ,quantity ,price )
                 VALUES (?,?,?,?,?)";
                $stm_cart_item=$conn->prepare($sql_cart_item);
                $stm_cart_item->execute([$cart_id,$id_pro,$id_var,$qunty,$prixtotale]);
               
            }else{
                header("Location: ../payment_page.php");
                exit; 
            }
            
        }
        elseif($type == 2){
            $id_pro_comp=intval($item['id']);
            $sql_pro="SELECT * FROM components  WHERE id=?";
            $stm_pro=$conn->prepare($sql_pro);
            $stm_pro->execute([$id_pro_comp]);
            $result_pro=$stm_pro->fetch();
            if($result_pro){
                $sql_pro_var="SELECT * FROM product_composer WHERE component_id =?";
                $stm_pro_var=$conn->prepare($sql_pro_var);
                $stm_pro_var->execute([$id_pro_comp]);
                $result_pro_var=$stm_pro_var->fetchAll(PDO::FETCH_ASSOC);
                if($result_pro_var){
                    $qunty=intval($item['quantity']);
                    $prixtotale=$result_pro['vente_price']*$taux*$qunty;
                    $sql_cart_item="INSERT INTO cart_items  (cart_id , component_id,quantity ,price )
                    VALUES (?,?,?,?)";
                    $stm_cart_item=$conn->prepare($sql_cart_item);
                    $stm_cart_item->execute([$cart_id,$id_pro_comp,$qunty,$prixtotale]);
                    $id_cart_item=$conn->lastInsertId();
                    foreach($result_pro_var as $var){
                        $id_pro_var=$item['option'][(string)$var['product_id']];
                        $sql_cart_items_compo="INSERT INTO cart_items_compo (cart_items_id, product_id, product_variant_id)
                                               VALUES (?,?,?)";
                        $stm_cart_items_compo=$conn->prepare($sql_cart_items_compo);
                        $stm_cart_items_compo->execute([$id_cart_item , $var['product_id'] , $id_pro_var]);
                    }
                    
                }else{
                    header("Location: ../payment_page.php");
                    exit;
                }
            }else{
                header("Location: ../payment_page.php");
                exit;
            }
        }else{
            header("Location: ../payment_page.php");
            exit;
        }

    }
    header("Location: ../select_shippers.php");
    exit;

}else{
    header("Location: ../loginPayment.php");
    exit; 
}
?>
