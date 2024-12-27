<?php 
session_start();

// Connexion à la base de données
include "../../php/db_connect.php";

if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type']) && isset($_POST['idp']) && isset($_POST['email_filed']) && isset($_POST['password_filed']) && isset($_POST['comment_filed'])) {
    $email = $_POST['email_filed'] ?? '';
    $pass = $_POST['password_filed'] ?? '';
    $idpro = intval($_POST['idp']) ?? 0;
    $communt = $_POST['comment_filed'] ?? '';
    $typetret = intval($_POST['type']) ?? 0;

    
    if (!empty($email) && !empty($pass)) {        
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch();
            $password = $user['password_hash'];

            if ($email === $user['email'] && password_verify($pass, $password)) {
                $id_user = $user['id']; // Assurez-vous que la table users a une colonne 'id'
                
                if (!empty($idpro) && !empty($communt)) {
                    // Vérifier si le produit ou le composant existe
                    if(!empty($typetret)){
                        if($typetret == 1){
                            $reve = "SELECT id FROM products WHERE id = ?";
                            $stmreve = $conn->prepare($reve);
                            $stmreve->execute([$idpro]);
                            $type = 'product_id';
    
                        }elseif($typetret == 2){
                            $reve = "SELECT id FROM components WHERE id = ?";
                            $stmreve = $conn->prepare($reve);
                            $stmreve->execute([$idpro]);
                            $type = 'components_id';
                        }else{
                            header("Location: ../productpage.php?error=Item does not exist");
                            exit;
                        }

                    }else{
                        header("Location: ../productpage.php?error=Item does not exist");
                        exit;
                    }
                   
                    

                    if ($stmreve->rowCount() === 1) {
                        // Préparer la requête SQL d'insertion
                        $sqlrevinsert = "INSERT INTO reviews (user_id, $type, rating, comment) 
                        VALUES (:user_id, :item_id, :rating, :comment)";
                        $stmtrevinsert = $conn->prepare($sqlrevinsert);
                        $stmtrevinsert->execute([
                            ':user_id' => $id_user,
                            ':item_id' => $idpro,
                            ':rating' => isset($_POST['rating']) ? intval($_POST['rating']) : 0,
                            ':comment' => $communt
                        ]);
                        // Redirection vers la page du produit
                        header("Location: ../productpage.php");
                        exit;
                    } else {
                        // Redirection vers la page du produit
                        header("Location: ../productpage.php?error=Item does not exist");
                        exit;                      
                    }                   
                } else {
                    header("Location: ../productpage.php?error=Comment or ID missing");
                    exit;
                }
            } else {
                header("Location: ../productpage.php?error=Incorrect username or password");
                exit;
            }
        } else {
            header("Location: ../productpage.php?error=Incorrect username or password");
            exit;
        }
    } else {
        header("Location: ../productpage.php?error=Email and Password are required");
        exit;
    }
} else {
    header("Location: ../productpage.php?error=Invalid request");
    exit;
}
?>
