<?php 

session_start();

//connextion avec la base de donner 
    include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }
//fin

//appler la bib du ticket
    require('../../ticket/fpdf.php');
    require ('../../phpqrcode/qrlib.php'); 
    require '../../vendor/autoload.php';
    require '../../php/EmailSender.php';
//fin

// Désactiver l'affichage des erreurs pour éviter la sortie prématurée    
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
//fin

//client
    if (!isset($_SESSION['user_id_cart']) || empty($_SESSION['user_id_cart'])) {
        echo "client not seet";
        exit;   
    }    
    $id_users = intval($_SESSION['user_id_cart']);
    $sql_user="SELECT * FROM users WHERE id=?";
    $stm_user=$conn->prepare($sql_user);
    $stm_user->execute([$id_users]);
    $result_user=$stm_user->fetch();
    if(!$result_user){
        echo "client not seet";
        exit;
    }
    $customerName=$result_user['name'];
//fin

//cart
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo 'cart vider';
        exit; 
    }
    $cart = $_SESSION['cart'];
//fin

//info payment
    if (!isset($_SESSION['payment']) || empty($_SESSION['payment'])) {
        echo 'info payement not set';
        exit; 
    }
    $payment=$_SESSION['payment'];
//fin

//id cart 
    if (!isset($_SESSION['id_cart']) || empty($_SESSION['id_cart'])) {
        echo 'cart not set ';
        exit; 
    }
//fin

//price orders
    if (!isset($_SESSION['prix_orders']) || empty($_SESSION['prix_orders'])) {
        echo 'price not set ';
        exit; 
    }
    $price=$_SESSION['prix_orders'];

    if (!isset($_SESSION['fraisen']) || empty($_SESSION['fraisen'])) {
        echo 'price not set ';
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
    $ty = '$';

    
    

    if ($type == 'usd') {
        $taux = 1;
        $ty = '$';
        


        

    } elseif ($type == 'eur') {
        $taux = $usdToEur;
        $ty = '€';
        

    } elseif ($type == 'mad') {
        $taux = $usdToMad;
        $ty = 'DH';
       $ty_aff="DH";

    }

    $price_orders=$price/$taux;
    $frais_livrision=$_SESSION['fraisen']*$taux;
//fin

//addres
    if (!isset($_SESSION['id_address']) || empty($_SESSION['id_address'])) {
        echo 'address not set';
        exit; 
    }      
    $id_address = intval($_SESSION['id_address']);
    $sql_addres="SELECT * FROM user_address WHERE id=?";
    $stm_addres=$conn->prepare($sql_addres);
    $stm_addres->execute([$id_address]);
    $result_addres=$stm_addres->fetch();
    if(!$result_addres){
        echo 'address not set';
        exit;
    }
//fin


//shipper
    if (!isset($_SESSION['id_shipper']) || empty($_SESSION['id_shipper'])) {
        echo 'shipper not set';
        exit; 
    }      
    $id_shipper = intval($_SESSION['id_shipper']);
    $sql_shipper="SELECT * FROM shippers WHERE id=?";
    $stm_shipper=$conn->prepare($sql_shipper);
    $stm_shipper->execute([$id_shipper]);
    $result_shipper=$stm_shipper->fetch();
    if(!$result_shipper){
        echo 'shipper not set';
        exit;
    }

    $search = array('é', 'è', 'à', 'ù', 'â', 'ê', 'î', 'ô', 'û', 'ë', 'ï', 'ö');
    $replace= array('e', 'e', 'a', 'u', 'a', 'e', 'i', 'o', 'u', 'e', 'i', 'o');


    // Shipper information
    $shipperName = str_replace($search, $replace,$result_shipper['shipper_name']);
    $shipperContact = str_replace($search, $replace,$result_shipper['email']);
    $shipperPhone = $result_shipper['phone'];
    $shipperAdress = str_replace($search, $replace,$result_shipper['adress']);
    $shipperoCuntry =$result_shipper['country'];
    $shipperoCity =str_replace($search, $replace,$result_shipper['city']);
//fin



if ($_SERVER["REQUEST_METHOD"] == "POST"){
//inserssion into users_payment
    $sql_payment="INSERT INTO user_payment (user_id  , address_id , cardholder_name , card_number , expiration_month , expiration_year , cvv) 
    VALUES (?,?,?,?,?,?,?)";
    $stm_payment=$conn->prepare($sql_payment);
    $stm_payment->execute([$id_users , $id_address , $payment['name'] , $payment['card_num'] , intval($payment['ex_month']) , intval($payment['ex_yeae']) , $payment['cvv']]);
//fin 

//insersion into orders

    $sql_orders="INSERT INTO orders (user_id, shipping_id , address_id  , price ) 
    VALUES (?,?,?,?)";
    $stm_orders=$conn->prepare($sql_orders);
    $stm_orders->execute([$id_users,$id_shipper , $id_address , $price_orders ]);
    $id_orders=$conn->lastInsertId();

//fin

//orders items  
    $products_ticket=[];
    foreach($cart as $item){
        $products_ticket_item=[];
        $products_ticket_item['name']=$item['title'];


        $type=intval($item['type']);
        $qunty=intval($item['quantity']); 
        $prixtotale=0;
        $prix=0;

        if($type == 1 ){
            
            $id_pro=intval($item['id']);
            //test
                $sql_pro="SELECT * FROM products WHERE id=?";
                $stm_pro=$conn->prepare($sql_pro);
                $stm_pro->execute([$id_pro]);
                $result_pro=$stm_pro->fetch();

                if(!$result_pro){
                    header("Location: ../select_shippers.php");
                    exit; 
                }
            //fin

            $id_var=intval($item['option'][(string)$id_pro]);

            //test
                $sql_pro_var="SELECT * FROM variant_options  WHERE id=?";
                $stm_pro_var=$conn->prepare($sql_pro_var);
                $stm_pro_var->execute([$id_var]);
                $result_pro_var=$stm_pro_var->fetch();

                if(!$result_pro_var){
                    header("Location: ../select_shippers.php");
                    exit; 
                }
            //fin

            $prixtotale=$result_pro['vente_price']*$taux*$qunty;
            $prix=$result_pro['vente_price']*$taux;
            $price_admin=$result_pro['vente_price']*$qunty;


            //insert into order_items
                $sql_orders_item="INSERT INTO order_items (order_id  , product_id  , product_variant_id ,quantity ,price )
                VALUES (?,?,?,?,?)";
                $stm_orders_item=$conn->prepare($sql_orders_item);
                $stm_orders_item->execute([$id_orders,$id_pro,$id_var,$qunty,$price_admin]);
                $id_order_items=$conn->lastInsertId();
            //fin
            
            //gestion du stock
                $stockvar=0;
                $active_var=true;
                $stock_oblig_var=0;
                $stock_pro=0;
                $active_pro=true;
                $delete=true;


                if($result_pro_var['active']){
                    if($result_pro_var['quantity'] < $qunty){
                        $active_var=false;
                        $stock_oblig_var=$qunty-$result_pro_var['quantity'];
                        $stock_pro=$result_pro['stock_quantity']-$result_pro_var['quantity'];
                        if($stock_pro>0){
                            $delete=false;
                        }else{
                            $active_pro=false;
                        }
                    }elseif($result_pro_var['quantity'] > $qunty){
                        $stockvar=$result_pro_var['quantity']-$qunty;
                        $stock_pro=$result_pro['stock_quantity']-$qunty;
                        $delete=false;
                    }else{
                        $active_var=false;
                        $stock_pro=$result_pro['stock_quantity']-$qunty;
                        if($stock_pro>0){
                            $delete=false;
                        }else{
                            $active_pro=false;
                        }
                    }
                }else{
                    
                    $active_var=false;
                    $stock_oblig_var=$qunty;
                    $stock_pro=$result_pro['stock_quantity'];
                    if($stock_pro>0){
                        $delete=false;
                    }else{
                        $active_pro=false;
                    }
                }



                
                $sql_update_var="UPDATE variant_options SET active = ?, quantity = ? WHERE id = ?";
                $stm_update_var=$conn->prepare($sql_update_var);
                $stm_update_var->execute([$active_var,$stockvar,$id_var]);
                if($delete){
                    $sql_delete_warehouse="DELETE FROM warehouse_inventory WHERE product_id  = ?";
                    $stm_delete_warehouse=$conn->prepare($sql_delete_warehouse);
                    $stm_delete_warehouse->execute([$id_pro]);

                }else{
                    $sql_updat_warehouse="UPDATE warehouse_inventory  SET quantity=? WHERE product_id = ?";
                    $stm_updat_warehouse=$conn->prepare($sql_updat_warehouse);
                    $stm_updat_warehouse->execute([$stock_pro , $id_pro]);
                }
                $sql_update_pro="UPDATE products  SET active = ?, stock_quantity = ? WHERE id = ?";
                $stm_update_pro=$conn->prepare($sql_update_pro);
                $stm_update_pro->execute([$active_pro,$stock_pro,$id_pro]);
            //fin

            //insersion
                $sql_porso_statuses="INSERT INTO porso_statuses (orders_id, order_items_id , product_id, product_variant_id, quantity_obli_var)
                                    VALUES (?,?,?,?,?)";
                $stm_porso_statuses=$conn->prepare($sql_porso_statuses);
                $stm_porso_statuses->execute([
                    $id_orders,
                    $id_order_items,
                    $id_pro,
                    $id_var,
                    $stock_oblig_var
                ]);
            //fin


            



        }elseif($type == 2){
            $id_pro_comp=intval($item['id']);

            //test
                $sql_pro_comp="SELECT * FROM components  WHERE id=?";
                $stm_pro_comp=$conn->prepare($sql_pro_comp);
                $stm_pro_comp->execute([$id_pro_comp]);
                $result_pro_comp=$stm_pro_comp->fetch();
                if(!$result_pro_comp){
                    header("Location: ../select_shippers.php");
                    exit;
                }
            //fin
            

            //test
                $sql_pro="SELECT * FROM product_composer WHERE component_id =?";
                $stm_pro=$conn->prepare($sql_pro);
                $stm_pro->execute([$id_pro_comp]);
                $result_pro=$stm_pro->fetchAll(PDO::FETCH_ASSOC);
                if(!$result_pro){
                    header("Location: ../select_shippers.php");
                    exit;
                }
            //fin

            $prixtotale=$result_pro_comp['vente_price']*$taux*$qunty;
            $price_admin=$result_pro_comp['vente_price']*$qunty;
            $prix=$result_pro_comp['vente_price']*$taux;

            //insert into order_items
                $sql_ordrs_item="INSERT INTO order_items   (order_id , component_id,quantity ,price )
                    VALUES (?,?,?,?)";
                $stm_orders_item=$conn->prepare($sql_ordrs_item);
                $stm_orders_item->execute([$id_orders,$id_pro_comp,$qunty,$price_admin]);
                $id_ordres_item=$conn->lastInsertId();
            //fin

            

            //gestion stock 

                $stock_comp=0;
                $stock_obl=0;
                $active=true;
                $quanty_update=$qunty;

                if($result_pro_comp['is_active']){
                    if($result_pro_comp['stock_quantity'] < $qunty){
                        $stock_obl= $qunty-$result_pro_comp['stock_quantity'];
                        $active=false;
                        $quanty_update=$result_pro_comp['stock_quantity'];
                    }elseif($result_pro_comp['stock_quantity'] > $qunty){
                        $stock_comp=$result_pro_comp['stock_quantity']-$qunty;

                    }else{
                        $active=false;
                    }
                }else{
                    $active=false;  
                    $stock_obl=$qunty;  
                    $quanty_update=0;              
                }
            

                $sql_updat_stock_pro_composer = "UPDATE components SET stock_quantity = ?, is_active = ? WHERE id = ?";
                $stm_updat_stock_pro_composer=$conn->prepare($sql_updat_stock_pro_composer);
                $stm_updat_stock_pro_composer->execute([$stock_comp ,$active, $id_pro_comp]);
            //fin


            //insersion
                $sql_porso_statuses="INSERT INTO porso_statuses (orders_id, order_items_id , component_id, quantity_obli_comp)
                                    VALUES (?,?,?,?)";
                $stm_porso_statuses=$conn->prepare($sql_porso_statuses);
                $stm_porso_statuses->execute([
                    $id_orders,
                    $id_ordres_item,
                    $id_pro_comp,
                    $stock_obl                    
                ]);
            //fin


            $prossstatu=$conn->lastInsertId();

            foreach($result_pro as $produit){

                $quanty_ramning_par_pro=$produit['quantity']*$quanty_update;
                $quanty_buy_par_pro=$produit['quantity']*$qunty;
                




                //test
                    $sql_test="SELECT * FROM products WHERE id=?";
                    $stm_test=$conn->prepare($sql_test);
                    $stm_test->execute([$produit['product_id']]);
                    $result_test=$stm_test->fetch();
                    if(!$result_test){
                        header("Location: ../select_shippers.php");
                        exit;
                    }
                //fin

                
                $id_pro_var=$item['option'][(string)$produit['product_id']];


                //test
                    $sql_test_var="SELECT * FROM variant_options  WHERE id=?";
                    $stm_test_var=$conn->prepare($sql_test_var);
                    $stm_test_var->execute([$id_pro_var]);
                    $result_test_var=$stm_test_var->fetch();
                    if(!$result_test_var){
                        header("Location: ../select_shippers.php");
                        exit;
                    }
                //fin




                //insertion
                    $sql_ordrs_items_compo="INSERT INTO orders_items_compo  (orders_items_id , product_id, product_variant_id , quantity )
                                        VALUES (?,?,?,?)";
                    $stm_orders_items_compo=$conn->prepare($sql_ordrs_items_compo);
                    $stm_orders_items_compo->execute([$id_ordres_item , $produit['product_id'] , $id_pro_var , $quanty_buy_par_pro ]);
                //fin



                //gestion du stock
                
                
                    $stockvar=0;
                    $active_var=true;
                    $stock_oblig_var = $quanty_buy_par_pro-$quanty_ramning_par_pro;
                    $stock_pro=0;
                    $active_pro=true;
                    $delete=true;                    
                    if($result_test_var['active']){
                        if($result_test_var['quantity'] < $quanty_ramning_par_pro ){
                            $active_var=false;
                            $stock_oblig_var+=$quanty_ramning_par_pro-$result_test_var['quantity'];
                            $stock_pro=$result_test['stock_quantity']-$result_test_var['quantity'];
                            if($stock_pro>0){
                                $delete=false;
                            }else{
                                $active_pro=false;
                            }
                        }elseif($result_test_var['quantity'] > $quanty_ramning_par_pro ){
                            $stockvar=$result_test_var['quantity'] - $quanty_ramning_par_pro;
                            $stock_pro=$result_test['stock_quantity']-$quanty_ramning_par_pro;                           
                                $delete=false;                           
                        }else{
                            $active_var=false;
                            $stock_pro=$result_test['stock_quantity']-$quanty_ramning_par_pro;
                            if($stock_pro>0){
                                $delete=false;
                            }else{
                                $active_pro=false;
                            }
                        }

                    }else{
                        $active_var=false;
                        $stock_oblig_var+=$quanty_ramning_par_pro;
                        $stock_pro=$result_test['stock_quantity'];
                        if($stock_pro>0){
                            $delete=false;
                        }else{
                            $active_pro=false;
                        }
                    }


                    
                    $sql_update_var="UPDATE variant_options SET active = ?, quantity = ? WHERE id = ?";
                    $stm_update_var=$conn->prepare($sql_update_var);
                    $stm_update_var->execute([$active_var,$stockvar,$id_pro_var]);
                    if($delete){
                        $sql_delete_warehouse="DELETE FROM warehouse_inventory WHERE product_id  = ?";
                        $stm_delete_warehouse=$conn->prepare($sql_delete_warehouse);
                        $stm_delete_warehouse->execute([$produit['product_id']]);

                    }else{
                        $sql_updat_warehouse="UPDATE warehouse_inventory  SET quantity=? WHERE product_id = ?";
                        $stm_updat_warehouse=$conn->prepare($sql_updat_warehouse);
                        $stm_updat_warehouse->execute([$stock_pro , $produit['product_id']]);

                    }
                    $sql_update_pro="UPDATE products  SET active = ?, stock_quantity = ? WHERE id = ?";
                    $stm_update_pro=$conn->prepare($sql_update_pro);
                    $stm_update_pro->execute([$active_pro,$stock_pro,$produit['product_id']]);

                    

                
                //fin



                //insersion
                    $sql_insert_porso_statuses_comp="INSERT INTO porso_statuses_comp (porso_statuses_id, product_id, product_variant_id, quantity_obli_var) VALUES (?, ?, ?, ?)";
                    $stm_insert_porso_statuses_comp=$conn->prepare($sql_insert_porso_statuses_comp);
                    $stm_insert_porso_statuses_comp->execute([
                        $prossstatu,
                        $produit['product_id'],
                        $id_pro_var,
                        $stock_oblig_var
                    ]);
                //fin
            }




        }else{
            header("Location: ../select_shippers.php");
            exit;
        }

        
        //fin
            $products_ticket_item['price']=$prix;
            $products_ticket_item['quantity']=$qunty;
            $products_ticket_item['price_total']=$prixtotale;
            $products_ticket[]=$products_ticket_item;
        //fin
    }
//fin



//statuses du order
             // Récupérer le total des enregistrements
             $sql_porss_total = "SELECT COUNT(*) AS total FROM porso_statuses WHERE orders_id = ?";
             $stm_porss_total = $conn->prepare($sql_porss_total);
             $stm_porss_total->execute([$id_orders]);
             $result_porss_total = $stm_porss_total->fetch();
 
             if ($result_porss_total) {
                 $total = $result_porss_total['total'];
                 $Pending = 0;
                 $Completed = 0;
 
                 // Requête pour compter les 'Completed'
                 $sql_porss_Completed = "SELECT COUNT(*) AS Completed
                                         FROM porso_statuses 
                                         WHERE orders_id = ?
                                         AND quantity_obli_comp = 0 
                                         AND quantity_obli_var = 0";
                 $stm_porss_Completed = $conn->prepare($sql_porss_Completed);
                 $stm_porss_Completed->execute([$id_orders]);
                 $result_porss_Completed = $stm_porss_Completed->fetch();
 
                 if ($result_porss_Completed) {
                     $Completed = $result_porss_Completed['Completed'];
                 }
 
                 // Requête pour compter les 'Pending'
                 $sql_porss_Pending = "SELECT COUNT(*) AS Pending
                                     FROM porso_statuses 
                                     WHERE orders_id = ?
                                     AND (quantity_obli_comp > 0 OR quantity_obli_var > 0)";
                 $stm_porss_Pending = $conn->prepare($sql_porss_Pending);
                 $stm_porss_Pending->execute([$id_orders]);
                 $result_porss_Pending = $stm_porss_Pending->fetch();
 
                 if ($result_porss_Pending) {
                     $Pending = $result_porss_Pending['Pending'];
                 }
 
                 // Calcul des pourcentages
                 if ($total > 0) {
                     $Pending_percentage = ($Pending / $total) * 100;
                     $Completed_percentage = ($Completed / $total) * 100;
 
                 } 
 
                 // En fonction des pourcentages, vous pouvez attribuer un statut à l'ordre
                 if ($Pending_percentage == 100) {
                     // Statut : "Pending"
                     $status_id = 1; // Remplacez par l'ID correspondant à "Pending" dans votre table `statuses`
                     $description = "Commande en attente";
                 } elseif ($Completed_percentage == 100) {
                     // Statut : "Completed"
                     $status_id = 3; // Remplacez par l'ID correspondant à "Completed"
                     $description = "Commande terminée tout les produits existe";
                 } else {
                     // Statut : "En cours" si ni 100% Pending ni 100% Completed
                     $status_id = 2; // Remplacez par l'ID correspondant à "In Progress"
                     $description = "Commande en cours";
                 }
                 // Insertion dans la table `order_statuses`
                 $sql_insert_order_status = "INSERT INTO order_statuses (status_id, orders_id, description)
                                             VALUES (?, ?, ?)";
                 $stmt_insert = $conn->prepare($sql_insert_order_status);
                 $stmt_insert->execute([$status_id, $id_orders, $description]);
             }
//fin




    // Démarrer la mise en tampon de sortie
        ob_start();
        $totalPrice = $frais_livrision;
        foreach ($products_ticket as $product) {
            $totalPrice += $product['price_total'];
        }

        // Création du PDF
        $pdf = new FPDF();
        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Naturelleshop', 0, 1, 'C');

        // Titre du ticket
        $pdf->Cell(0, 10, 'Ticket du Client', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Client: ' . htmlspecialchars($customerName), 0, 1);

        // En-têtes de tableau
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(90, 10, 'Produit', 1);
        $pdf->Cell(40, 10, 'Prix Unitaire', 1);
        $pdf->Cell(25, 10, "Quantite", 1);
        $pdf->Cell(40, 10, 'Prix Total', 1, 1);

        // Corps du tableau
        $pdf->SetFont('Arial', '', 12);
        foreach ($products_ticket as $product) {
            $pdf->Cell(90, 10, htmlspecialchars($product['name']), 1);
            $pdf->Cell(40, 10, number_format($product['price'], 2) . $ty, 1);
            $pdf->Cell(25, 10, $product['quantity'], 1);
            $pdf->Cell(40, 10, number_format($product['price_total'], 2) . $ty, 1, 1);
        }

        // Prix total
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(155, 10, 'Total', 1);
        $pdf->Cell(40, 10, number_format($totalPrice, 2) . $ty, 1, 1);

        // Informations du transporteur
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Informations du Transporteur:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Transporteur: ' . htmlspecialchars($shipperName), 0, 1);
        $pdf->Cell(0, 10, 'Contact: ' . htmlspecialchars($shipperContact), 0, 1);
        $pdf->Cell(0, 10, "Telephone: " . htmlspecialchars($shipperPhone), 0, 1);
        $pdf->Cell(0, 10, 'Adresse: ' . htmlspecialchars($shipperAdress), 0, 1);
        $pdf->Cell(0, 10, 'Pays: ' . htmlspecialchars($shipperoCuntry), 0, 1);
        $pdf->Cell(0, 10, 'Ville: ' . htmlspecialchars($shipperoCity), 0, 1);

        // Génération du QR Code
        $orderID = $id_orders;
        $trackingUrl = "http://localhost/ecommerce_web_site/NaturelleShop/pages/suivi_commande.php?id=" . $orderID;
        $qrCodePath = "../../images/QR/qrcode$orderID.png";
        QRcode::png($trackingUrl, $qrCodePath, QR_ECLEVEL_L, 4, 4);

        // Ajout du QR Code au PDF
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'QR Code:', 0, 1);
        $pdf->Image($qrCodePath, 10, $pdf->GetY(), 40, 40);

        // Sortie du PDF
        $pdfFilePath = "../../images/ticket/ticket$orderID.pdf"; 

        if (ob_get_length()) {
            ob_end_clean(); // Nettoyer le tampon de sortie si actif
        }
        $pdf->Output('F', $pdfFilePath); 
        $pdf->Output('D', "ticket$orderID.pdf");

        // Envoi de l'email avec la pièce jointe
        $to = $result_addres['contact_email'];
        $subject = 'Votre ticket de commande - Naturelleshop';
        $Body = '
        <!DOCTYPE html>
        <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: auto; padding: 20px; }
                    .header { text-align: center; padding-bottom: 10px; }
                    .header h1 { margin: 0; }
                    .content { padding: 10px; }
                    .footer { padding-top: 10px; text-align: center; font-size: 12px; }
                    .footer a { color: #007bff; text-decoration: none; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>Naturelleshop</h1>
                    </div>
                    <div class="content">
                        <p>Bonjour ' . htmlspecialchars($customerName) . ',</p>
                        <p>Veuillez trouver ci-joint votre ticket de commande au format PDF.</p>
                        <p>Informations du Transporteur:</p>
                        <p><strong>Transporteur:</strong> ' . htmlspecialchars($shipperName) . '<br>
                        <strong>Contact:</strong> ' . htmlspecialchars($shipperContact) . '<br>
                        <strong>Téléphone:</strong> ' . htmlspecialchars($shipperPhone) . '</p>
                    </div>
                    <div class="footer">
                        <p>Merci pour votre achat ! <br>
                        Pour toute question, veuillez nous contacter à <a href="mailto:naturelleshop.boutique@gmail.com">naturelleshop.boutique@gmail.com</a>.</p>
                    </div>
                </div>
            </body>
        </html>';

        $emailSender = new EmailSender();
        $msg = $emailSender->sendEmail($to, $subject, $Body, $pdfFilePath);
        $_SESSION['cart'] = array();

// Proposer le fichier au téléchargement
/*
if (file_exists($pdfFilePath)) {
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="ticket' . $orderID . '.pdf"');
header('Content-Length: ' . filesize($pdfFilePath));
readfile($pdfFilePath);



exit; // Terminer le script après le téléchargement

} 
*/



    //fin 
    
    
    // Finalement, détruire la session

    
   
    header("Location: ../../../index.php");
    exit;





    



}else{
    header("Location: ../select_shippers.php");
    exit; 
}











?>



























/*    

    
    
    

    


    




    


    


    




    









    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        
        
       
        
        

        

        
         
        






    }else{
        
    }
/*
    
     
?>


































