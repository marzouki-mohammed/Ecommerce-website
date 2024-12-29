<?php 
    session_start();
    require '../../../vendor/autoload.php';
    require '../../../php/EmailSender.php';
    include "../../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }
    // Fonction générique pour supprimer des enregistrements d'une table
    function deleteFromTable($conn, $table, $column, $ids) {
        $placeholders = implode(",", $ids);
        $sql = "DELETE FROM $table WHERE $column IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shipper_delete_ids'])) {
         // Récupérer les IDs des cases cochées
         $delete_ids = $_POST['shipper_delete_ids'] ?? [];
         if (!empty($delete_ids)) {
            
            foreach($delete_ids as $shipper){
                $sql_orders="SELECT * FROM orders WHERE shipping_id=?";
                $stm_orders=$conn->prepare($sql_orders);
                $stm_orders->execute([intval($shipper)]);
                $result_orders=$stm_orders->fetchAll(PDO::FETCH_ASSOC);
                if($result_orders){
                    foreach($result_orders as $ord){

                        $sql_status="SELECT * FROM order_statuses WHERE orders_id=?";
                        $stm_status=$conn->prepare($sql_status);
                        $stm_status->execute([intval($ord['id'])]);
                        $result_status=$stm_status->fetch();
                        if( $result_status ){
                            $sql_order_status="SELECT * FROM statuses WHERE id=?";
                            $stm_order_status=$conn->prepare($sql_order_status);
                            $stm_order_status->execute([intval($result_status['status_id'])]);
                            $result_order_status=$stm_order_status->fetch();
                            if(!$result_order_status){
                                header("Location: delete.php");
                                exit;
                            }
                            if($result_order_status['status_name']=='Pending' 
                            || $result_order_status['status_name']=='Processing' 
                            || $result_order_status['status_name']=='Shipped' ){
                                    $sql_address = "SELECT * FROM user_address WHERE id=?";
                                    $stm_address = $conn->prepare($sql_address);
                                    $stm_address->execute([intval($ord['address_id'])]);
                                    $resulte_address = $stm_address->fetch();
                                    if (!$resulte_address) {
                                        header("Location: delete.php");
                                        exit;
                                    }
                                    $city_client = $resulte_address['city'];
                                    $adress_client = $resulte_address['address_line1'];
                                    $to = $resulte_address['contact_email'];
                                    $table=getShippersByCity($conn , $city_client , $adress_client);
                                    $id_shipper=intval($table['shipper_id']);

                                    $sql_shipper="SELECT * FROM shippers WHERE id=?";
                                    $stm_shippers=$conn->prepare($sql_shipper);
                                    $stm_shippers->execute([$id_shipper]);
                                    $result_shippers=$stm_shippers->fetch();
                                    if(!$result_shippers){
                                        header("Location: delete.php");
                                        exit;
                                    }
                                    $newShipperName=$result_shippers['shipper_name'];
                                    $newShipperContact=$result_shippers['email'];
                                    $newShipperPhone=$result_shippers['phone'];
                                    $newShipperAddress=$result_shippers['adress'];
                                    $newShipperCountry=$result_shippers['country'];
                                    $newShipperCity=$result_shippers['city'];

                                    $sql_user="SELECT * FROM users WHERE id=?";
                                    $stm_user=$conn->prepare($sql_user);
                                    $stm_user->execute([intval($ord['user_id'])]);
                                    $result_user=$stm_user->fetch();
                                    if(!$result_user){
                                        header("Location: delete.php");
                                        exit;
                                    }
                                    $customerName=$result_user['name'];
                                    $orderNumber=intval($ord['id']);
                                    $sql_update="UPDATE orders
                                                SET shipping_id = ?
                                                WHERE  id= ?";
                                    $stm_update=$conn->prepare($sql_update);
                                    $stm_update->execute([$id_shipper , $orderNumber]);
                                     // Envoi de l'email avec la pièce jointe
                                     $subject = 'Mise à jour de votre commande - Changement de livreur';
                                     $Body = '
                                     <!DOCTYPE html>
                                     <html>
                                         <head>
                                             <style>
                                                 body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                                                 .container { max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
                                                 .header { text-align: center; padding-bottom: 10px; }
                                                 .header h1 { margin: 0; color: #007bff; }
                                                 .content { padding: 10px; color: #333; line-height: 1.5; }
                                                 .footer { padding-top: 10px; text-align: center; font-size: 12px; color: #888; }
                                                 .footer a { color: #007bff; text-decoration: none; }
                                             </style>
                                         </head>
                                         <body>
                                             <div class="container">
                                                 <div class="header">
                                                     <h1>Mise à jour de votre commande</h1>
                                                 </div>
                                                 <div class="content">
                                                     <p>Bonjour ' . htmlspecialchars($customerName) . ',</p>
                                                     <p>Nous souhaitons vous informer que le livreur de votre commande n°' . htmlspecialchars($orderNumber) . ' a été changé.</p>
                                                     <p>Voici les informations du nouveau transporteur :</p>
                                                     <p><strong>Nom du transporteur:</strong> ' . htmlspecialchars($newShipperName) . '<br>
                                                     <strong>Contact:</strong> ' . htmlspecialchars($newShipperContact) . '<br>
                                                     <strong>Téléphone:</strong> ' . htmlspecialchars($newShipperPhone) . '<br>
                                                     <strong>Address:</strong> ' . htmlspecialchars($newShipperAddress) . '<br>
                                                     <strong>Country:</strong> ' . htmlspecialchars($newShipperCountry) . '<br>
                                                     <strong>City:</strong> ' . htmlspecialchars($newShipperCity) . '<br>
 
                                                     </p>
                                                     <p>La prix de livraison prévue reste inchangée.</p>
                                                 </div>
                                                 <div class="footer">
                                                     <p>Merci pour votre confiance et à très bientôt sur <strong>Naturelleshop</strong> !<br>
                                                     Pour toute question, vous pouvez nous contacter à <a href="mailto:naturelleshop.boutique@gmail.com">naturelleshop.boutique@gmail.com</a>.</p>
                                                 </div>
                                             </div>
                                         </body>
                                     </html>';
                                     $emailSender = new EmailSender();
                                     $msg = $emailSender->sendEmail($to, $subject, $Body);


                            }


                        }

                    }
                }
            }





            deleteFromTable($conn, 'shippers', 'id', $delete_ids);
            // Redirection après suppression
            header("Location: delete.php");
            exit;
         }else{
            header("Location: delete.php");
            exit;

         }
    }else{
        header("Location: delete.php");
        exit;
    }
?>






