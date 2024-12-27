<?php
session_start();
 //Load Composer's autoloader
 require '../../vendor/autoload.php';
 // Inclure le fichier contenant la classe EmailSender
 require '../../php/EmailSender.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username_filed']) && 
    isset($_POST['password_hash_filed']) && isset($_POST['email_filed']) && 
    isset($_POST['full_name_filed'])) {
    // Connexion à la base de données
    include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    // Récupérer les données
    $username = trim($_POST['username_filed']);
    $password = trim($_POST['password_hash_filed']);
    $email = trim($_POST['email_filed']);
    $full_name = trim($_POST['full_name_filed']);

    // Vérification des champs
    if (!empty($username) && !empty($password) && !empty($email) && !empty($full_name)) {
        // Vérifier si l'email ou le nom d'utilisateur existe déjà
        $sql = "SELECT * FROM admin WHERE username= ? OR email= ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            header("Location: add.php?error=Le nom d'utilisateur ou l'adresse e-mail existe déjà.");
            exit;            
        } else {
            // Insérer le nouvel administrateur
            $sql = "INSERT INTO admin (username, password_hash, email, full_name) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $password, $email, $full_name]);
                $subject = 'Bienvenue à Bord, NaturelleShope !';
                $Body    = "<html>
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
                                <h1>Bienvenue à Bord, NaturelleShope !</h1>
                            </div>
                            <div class='content'>
                                <p>Cher $full_name,</p>
                                <p>Nous sommes heureux de vous informer que vous avez été ajouté en tant que nouvel administrateur de notre équipe. Nous sommes ravis de vous accueillir et de travailler avec vous.</p>
                                <p>Voici quelques informations importantes pour vous aider à commencer :</p>
                                <ul>
                                    <li><strong>Accès au Tableau de Bord :</strong> Vous pouvez vous connecter au tableau de bord d'administration en utilisant vos identifiants.</li>
                                    <li><strong>Ressources et Documentation :</strong> Vous trouverez des ressources utiles et des guides dans la section [lien vers la documentation].</li>
                                    <li><strong>Support :</strong> Si vous avez des questions ou avez besoin d'aide, n'hésitez pas à contacter l'équipe de support à [<a href='mailto:naturelleshop.boutique@gmail.com'  aria-hidden='true' target='_blank'>naturelleshop.boutique@gmail.com</a>
].</li>
                                </ul>
                                <p>Nous sommes impatients de vous voir contribuer et apporter de la valeur à notre équipe. Merci encore pour votre engagement et bienvenue parmi nous !</p>
                                <p>Cordialement,</p>
                                <p>L'Équipe d'Administration</p>
                            </div>
                            <div class='footer'>
                                <p>&copy; 2024 NaturelleShope. Tous droits réservés.</p>
                            </div>
                        </div>
                    </body>
                </html>";

                $emailSender = new EmailSender();
                $msg=$emailSender->sendEmail($email, $subject, $Body);
           // Redirect after successful insert and email send
           header("Location: add.php?success=L'insertion a été effectuée avec succès.&msg=$msg");
           exit;
        }
    } else {
        header("Location: add.php?error=One or more empty fields");
        exit;
    }
} else {
    header("Location: add.php?error=Error");
    exit;
}

