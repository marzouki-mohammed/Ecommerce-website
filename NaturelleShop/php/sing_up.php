<?php 
        // Début de la session
        session_start();
        require '../vendor/autoload.php';
        // Inclure le fichier contenant la classe EmailSender
        require 'EmailSender.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST" && 
            isset($_POST['first_name_filde']) && isset($_POST['last_name_filde']) &&
            isset($_POST['phone_number_filde']) && isset($_POST['email_filde']) && 
            isset($_POST['sex_filde']) && isset($_POST['password_hash_filde'])) {
            
            // Connexion à la base de données
            include "./db_connect.php";
            if (!$conn) {
                header("Location: ../users/form_singup.php?error=Database connection failed");
                exit;
            }

            // Récupérer les données
            $first_name = trim($_POST['first_name_filde']);
            $last_name = trim($_POST['last_name_filde']);
            $phone_number = trim($_POST['phone_number_filde']);
            $email = trim($_POST['email_filde']);
            $sex = trim($_POST['sex_filde']);
            $password_hash = trim($_POST['password_hash_filde']);

            // Vérifier si les champs ne sont pas vides
            if (!empty($first_name) && !empty($last_name) && !empty($phone_number) && 
                !empty($email) && !empty($sex) && !empty($password_hash)) {
                
                // Vérifier si l'email existe déjà
                $sql = "SELECT * FROM users  WHERE email = :email";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['email' => $email]);
                
                if ($stmt->rowCount() > 0) {
                    header("Location: ../users/form_singup.php?error=Email already exists");
                    exit;
                }

                
                // Hachage du mot de passe
                $emailSender = new EmailSender();
                $password_hash = password_hash($password_hash, PASSWORD_DEFAULT);
                $subject = 'Bienvenue à Bord, NaturelleShope !';
                $Body    = "
                        <!DOCTYPE html>
                        <html lang='en>
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <title>Welcome Email</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    background-color: #f4f4f4;
                                    color: #333;
                                    margin: 0;
                                    padding: 20px;
                                }
                                .container {
                                    max-width: 600px;
                                    margin: auto;
                                    background: #ffffff;
                                    padding: 20px;
                                    border-radius: 8px;
                                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                                }
                                h1 {
                                    color: #007BFF;
                                }
                                p {
                                    font-size: 16px;
                                    line-height: 1.5;
                                }
                                .button {
                                    display: inline-block;
                                    padding: 10px 20px;
                                    font-size: 16px;
                                    color: #ffffff;
                                    background-color: #007BFF;
                                    text-decoration: none;
                                    border-radius: 5px;
                                    margin-top: 20px;
                                }
                                .footer {
                                    font-size: 14px;
                                    color: #777;
                                    margin-top: 20px;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <h1>Welcome to Our NaturelleShope!</h1>
                                <p>Hello '".$first_name." ".$last_name."',</p>
                                <p>Thank you for signing up on our site. We are excited to have you on board. Explore our wide range of products and enjoy exclusive deals and offers.</p>
                                <a href='http://localhost/ecommerce_web_site/' class='button'>NaturelleShope</a>
                                <p class='footer'>If you have any questions, feel free to <a href='mailto:naturelleshop.boutique@gmail.com'>contact us</a>.</p>
                            </div>
                        </body>
                        </html>
                            ";
                
                // Gérer le téléchargement de l'image
                if (isset($_FILES['image_filde']) && $_FILES['image_filde']['error'] === UPLOAD_ERR_OK) {
                    $img_name = $_FILES['image_filde']['name'];
                    $tmp_name = $_FILES['image_filde']['tmp_name'];
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                    $img_ex_to_lc = strtolower($img_ex);
                    $allowed_exs = ['jpg', 'jpeg', 'png'];
                    
                    // Vérifier l'extension de l'image
                    if (in_array($img_ex_to_lc, $allowed_exs)) {
                        $new_img_name = uniqid($first_name, true) . '.' . $img_ex_to_lc;
                        $img_upload_path = '../upload/' . $new_img_name;
                        move_uploaded_file($tmp_name, $img_upload_path);
                        
                        // Insertion dans la base de données
                        $sql = "INSERT INTO users (first_name, last_name, phone_number, email, sex, password_hash, image) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$first_name, $last_name, $phone_number, $email, $sex, $password_hash, $new_img_name]);
                        $msg=$emailSender->sendEmail($email, $subject, $Body);
                        header('Location: ../../index.php'); // Redirection par défaut si HTTP_REFERER n'est pas défini                   
                        exit;
                        
                    } else {
                        header("Location: ../users/form_singup.php?error=You can't upload files of this type");
                        exit;
                    }
                } else {
                    // Insertion dans la base de données sans image
                    $sql = "INSERT INTO users (first_name, last_name, phone_number, email, sex, password_hash) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$first_name, $last_name, $phone_number, $email, $sex, $password_hash]);
                    
                    $msg=$emailSender->sendEmail($email, $subject, $Body);
                    
                    header('Location: ../../index.php'); // Redirection par défaut si HTTP_REFERER n'est pas défini
                    
                    exit;
                    
                }
                

            } else {
                header("Location: ../users/form_singup.php?error=One or more fields are empty");
                exit;
            }
        } else {
            header("Location: ../users/form_singup.php?error=Invalid request");
            exit;
        }
?>