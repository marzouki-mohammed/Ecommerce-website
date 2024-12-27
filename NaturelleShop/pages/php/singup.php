<?php 
    // Début de la session
    session_start();
    require '../../vendor/autoload.php';
    require '../../php/EmailSender.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && 
        isset($_POST['name_filde']) && isset($_POST['email_filde']) && 
        isset($_POST['sex_filde']) && isset($_POST['password_hash_filde'])) {
        
        include "../../php/db_connect.php";
        if (!$conn) {
            header("Location: ../sinupPayment.php?error=Database connection failed");
            exit;
        }

        $name = trim($_POST['name_filde']);
        $email = trim($_POST['email_filde']);
        $sex = trim($_POST['sex_filde']);
        $password_hash = trim($_POST['password_hash_filde']);

        if (!empty($name) && !empty($email) && !empty($sex) && !empty($password_hash)) {
            
            // Vérifier si l'email existe déjà
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['email' => $email]);
            
            if ($stmt->rowCount() > 0) {
                header("Location: ../sinupPayment.php?error=Email already exists");
                exit;
            }

            // Hachage du mot de passe et préparation de l'email de bienvenue
            $emailSender = new EmailSender();
            $password_hash = password_hash($password_hash, PASSWORD_DEFAULT);
            $subject = 'Bienvenue à Bord, NaturelleShope !';
            $Body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Welcome Email</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 20px; }
                    .container { max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
                    h1 { color: #007BFF; }
                    p { font-size: 16px; line-height: 1.5; }
                    .button { display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background-color: #007BFF; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                    .footer { font-size: 14px; color: #777; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h1>Welcome to Our NaturelleShope!</h1>
                    <p>Hello " . $name . ",</p>
                    <p>Thank you for signing up on our site. We are excited to have you on board. Explore our wide range of products and enjoy exclusive deals and offers.</p>
                    <a href='http://localhost/ecommerce_web_site/' class='button'>NaturelleShope</a>
                    <p class='footer'>If you have any questions, feel free to <a href='mailto:naturelleshop.boutique@gmail.com'>contact us</a>.</p>
                </div>
            </body>
            </html>";

            // Insertion dans la base de données
            $sql = "INSERT INTO users (name, email, sex, password_hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $email, $sex, $password_hash]);

            // Envoi de l'email de bienvenue
            $msg = $emailSender->sendEmail($email, $subject, $Body);

            // Redirection après l'inscription
            if (isset($_SERVER['HTTP_REFERER'])) {
                echo '
                <script type="text/javascript">
                    window.history.go(-2);
                </script>';
                exit;
            } else {
                header('Location: ../sinupPayment.php');
                exit;
            }
        } else {
            header("Location: ../sinupPayment.php?error=One or more fields are empty");
            exit;
        }
    } else {
        header("Location: ../sinupPayment.php?error=Invalid request");
        exit;
    }
?>
