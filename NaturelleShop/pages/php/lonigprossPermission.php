<?php 
   session_start();
   
   if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['emailfiels']) && isset($_POST['passfields'])) {
    $email = $_POST['emailfiels'] ?? '';
    $pass = $_POST['passfields'] ?? '';
    if (!empty($email) && !empty($pass)) {
        // Connexion à la base de données
            include "../../php/db_connect.php";
            if (!isset($conn)) {
                echo "Database connection is not set.";
                exit;
            }

           $sql = "SELECT * FROM users WHERE email = ?";
               $stmt = $conn->prepare($sql);
               $stmt->execute([$email]);
               if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                   $useremail = $user['email'];
                   $password = $user['password_hash'];
                   if($useremail === $email && password_verify($pass, $password)) {
                       $_SESSION['user_id_cart'] = $user['id']; // Assurez-vous que la table users a une colonne 'id'
                       
                       header("Location: ../payment_page.php");
                       exit;


                   }else{
                    header("Location: ../loginPermission.php?error=Incorrect username or password");
                       exit;

                   }



               }else{
                $em = "Incorrect username or password";
                   header("Location: ../loginPermission.php?error=$em");
                   exit;

               }



    }else{
        $em = "User or admin Email and Password are required";
        header("Location: ../loginPermission.php?error=$em");
        exit;
    }


       
   }else {
       header("Location: loginPermission.php?error=error");
       exit;
   }
   
    
?>