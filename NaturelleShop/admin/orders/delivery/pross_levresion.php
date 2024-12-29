<?php
session_start();
require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
require '../../../php/EmailSender.php';
include "../../../php/db_connect.php";

if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orders_id_delivery'])) {
    $levresion_ids = $_POST['orders_id_delivery'] ?? [];

if (!empty($levresion_ids)) {
    // Créer des placeholders pour la requête SQL
    $groupedOrdersByShipping = [];
    $id_shipping = [];
    foreach($levresion_ids as $id){
        $sql = "SELECT * FROM orders WHERE id=?";
        $stmt = $conn->prepare($sql);
        // Exécuter la requête avec les paramètres
        $stmt->execute([intval($id)]);
        $grouped_orders = $stmt->fetch();
        if(!$grouped_orders){
            echo "error";
            exit;
        }
        $shipping_id = $grouped_orders['shipping_id'];
        if (!isset($groupedOrdersByShipping[$shipping_id])) {
            $groupedOrdersByShipping[$shipping_id] = [];
            $id_shipping[] = $shipping_id;
        }
        $groupedOrdersByShipping[$shipping_id][] = [
            'nbr_orders'   => $grouped_orders['id'],
            'user_id'      => $grouped_orders['user_id'],
            'address_id'   => $grouped_orders['address_id'],
            'prix'         => $grouped_orders['price']
        ];

    }
    

   


        
        foreach ($id_shipping as $id) {
            $sql_shippers = "SELECT * FROM shippers WHERE id = ?";
            $stm_shippers = $conn->prepare($sql_shippers);
            $stm_shippers->execute([$id]);
            $result_shippers = $stm_shippers->fetch();
            if (!$result_shippers) {
                header("Location: levresion.php");
                exit;
            }
            $to = $result_shippers['email'];
            $data = [];
            foreach ($groupedOrdersByShipping[$id] as $cop) {
                $id_users = intval($cop['user_id']);
                $sql_users = "SELECT * FROM users WHERE id = ?";
                $stm_users = $conn->prepare($sql_users);
                $stm_users->execute([$id_users]);
                $result_users = $stm_users->fetch();

                $id_address = intval($cop['address_id']);
                $sql_address = "SELECT * FROM user_address WHERE id = ?";
                $stm_address = $conn->prepare($sql_address);
                $stm_address->execute([$id_address]);
                $result_address = $stm_address->fetch();

                $num_orders = intval($cop['nbr_orders']);
                $data[] = [
                    $num_orders,
                    $result_users['name'],
                    $result_address['contact_email'],
                    $result_address['address_line1'],
                    $result_address['city'],
                    $result_address['country'],
                    $result_address['phone_number'],
                ];

                // Mise à jour du statut des commandes
                $sql_status = "SELECT * FROM order_statuses WHERE orders_id = ?";
                $stm_status = $conn->prepare($sql_status);
                $stm_status->execute([$num_orders]);
                $result_status = $stm_status->fetch();

                if ($result_status) {
                    $sql_update = "UPDATE order_statuses SET status_id = 4, description = 'attendre sa commande', updated_at = CURRENT_TIMESTAMP WHERE orders_id = ?";
                    $stm_update = $conn->prepare($sql_update);
                    $stm_update->execute([$num_orders]);
                } else {
                    $sql_insert = "INSERT INTO order_statuses (status_id, orders_id, description) VALUES (4, ?, 'commande en cours')";
                    $stm_insert = $conn->prepare($sql_insert);
                    $stm_insert->execute([$num_orders]);
                }
            }
            //1
             // Création du fichier Excel
             $spreadsheet_dwn = new Spreadsheet();
             $sheet_dwn = $spreadsheet_dwn->getActiveSheet();

            $sheet_dwn->setCellValue('A1', 'Order Number');
            $sheet_dwn->setCellValue('B1', 'Username');
            $sheet_dwn->setCellValue('C1', 'User Email');
            $sheet_dwn->setCellValue('D1', 'User Address');
            $sheet_dwn->setCellValue('E1', 'City');
            $sheet_dwn->setCellValue('F1', 'Country');
            $sheet_dwn->setCellValue('G1', 'User Phone');

            $row = 2;
            foreach ($data as $rowData) {
                $sheet_dwn->setCellValue('A' . $row, $rowData[0]);
                $sheet_dwn->setCellValue('B' . $row, $rowData[1]);
                $sheet_dwn->setCellValue('C' . $row, $rowData[2]);
                $sheet_dwn->setCellValue('D' . $row, $rowData[3]);
                $sheet_dwn->setCellValue('E' . $row, $rowData[4]);
                $sheet_dwn->setCellValue('F' . $row, $rowData[5]);
                $sheet_dwn->setCellValue('G' . $row, $rowData[6]);
                $row++;
            }

            // Définir le nom du fichier Excel
            $fileName_dwn = "liste_orders_dwn_$id.xlsx";

            // Créer un fichier Excel à télécharger
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName_dwn . '"');
            header('Cache-Control: max-age=0');

            // Générer et envoyer le fichier Excel
            $writer_dwn = new Xlsx($spreadsheet_dwn);
            $writer_dwn->save('php://output');


            // Supprimer le fichier après envoi
            unlink($fileName_dwn);




            //2
            // Création du fichier Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
           

            $sheet->setCellValue('A1', 'Order Number');
            $sheet->setCellValue('B1', 'Username');
            $sheet->setCellValue('C1', 'User Email');
            $sheet->setCellValue('D1', 'User Address');
            $sheet->setCellValue('E1', 'City');
            $sheet->setCellValue('F1', 'Country');
            $sheet->setCellValue('G1', 'User Phone');


        
            
            
            $row = 2;
            foreach ($data as $rowData) {
                $sheet->setCellValue('A' . $row, $rowData[0]);
                $sheet->setCellValue('B' . $row, $rowData[1]);
                $sheet->setCellValue('C' . $row, $rowData[2]);
                $sheet->setCellValue('D' . $row, $rowData[3]);
                $sheet->setCellValue('E' . $row, $rowData[4]);
                $sheet->setCellValue('F' . $row, $rowData[5]);
                $sheet->setCellValue('G' . $row, $rowData[6]);
                $row++;
            }

            $fileName = "liste_orders_$id.xlsx";
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileName);

            // Envoi de l'email avec pièce jointe
            $subject = 'Alerte de Transaction - Vérification de Livraison';
            $Body ="<html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; }
                                .container { width: 600px; margin: 0 auto; }
                                .header { background-color: #f4f4f4; padding: 20px; text-align: center; }
                                .content { padding: 20px; }
                                .footer { background-color: #f4f4f4; padding: 10px; text-align: center; }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <div class='header'>
                                    <h1>Nouvelle Alerte de Transaction</h1>
                                </div>
                                <div class='content'>
                                    <p>Bonjour,</p>
                                    <p>Vous avez reçu de nouvelles commandes clients à livrer. Veuillez consulter le fichier joint contenant les détails de ces commandes.</p>
                                    <p><strong>Important :</strong> Avant de procéder à la livraison, vous devez absolument <strong>vérifier l'identité</strong> du destinataire .</p>
                                    <p>De plus, nous vous prions de bien vouloir <strong>répondre à cet email</strong> pour confirmer que vous avez bien reçu les détails et que vous avez pris en compte nos instructions.</p>
                                    <p>Merci de votre collaboration.</p>
                                    <p>Cordialement,</p>
                                    <p>L'équipe NaturelleShope</p>
                                </div>
                                <div class='footer'>
                                    <p>&copy; 2024 NaturelleShope. Tous droits réservés.</p>
                                </div>
                            </div>
                        </body>
                      </html>";

            $emailSender = new EmailSender();
            $msg = $emailSender->sendEmail($to, $subject, $Body, $fileName);

            // Supprimer le fichier après envoi
            unlink($fileName);
        }

        header("Location: levresion.php");
        exit;

    } else {
        header("Location: levresion.php");
        exit;
    }
        
}
else{
    header("Location: levresion.php");
        exit;
}
