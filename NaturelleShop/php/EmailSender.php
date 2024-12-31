<?php
/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    private $mail;

    public function __construct() {
        // Créer une instance de PHPMailer
        $this->mail = new PHPMailer(true);

        // Configurer les options SMTP
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Configurer les paramètres du serveur
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'naturelleshop.boutique@gmail.com'; // Remplacez par votre adresse email
        $this->mail->Password   = 'qkqvdemdyflgwjzq'; // Remplacez par votre mot de passe
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        $this->mail->setFrom('naturelleshop.boutique@gmail.com', 'NaturelleShop'); // Expéditeur
    }

    public function sendEmail($to, $subject, $body) {
        try {
            // Ajouter le destinataire
            $this->mail->addAddress($to);

            // Configurer le contenu de l'email
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            // Vérifier si $body est null avant d'utiliser strip_tags()
            $this->mail->AltBody = $body !== null ? strip_tags($body) : ''; // Version texte du corps de l'email

            // Envoyer l'email
            $this->mail->send();
            $msg = 'Message has been sent';
        } catch (Exception $e) {
            $msg = "Message could not be sent.";
        }
        return $msg;
    }
}
    */


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    private $mail;

    public function __construct() {
        // Créer une instance de PHPMailer
        $this->mail = new PHPMailer(true);

        // Configurer les options SMTP
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Configurer les paramètres du serveur
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'naturelleshop.boutique@gmail.com'; // Remplacez par votre adresse email
        $this->mail->Password   = 'qkqvdemdyflgwjzq'; // Remplacez par votre mot de passe d'application Gmail
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        $this->mail->setFrom('naturelleshop.boutique@gmail.com', 'NaturelleShop'); // Expéditeur
    }

    public function sendEmail($to, $subject, $body, $attachmentPath = null) {
        try {
            // Ajouter le destinataire
            $this->mail->addAddress($to);

            // Configurer le contenu de l'email
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            // Version texte du corps de l'email
            $this->mail->AltBody = strip_tags($body);

            // Ajouter la pièce jointe si elle est fournie
            if ($attachmentPath && file_exists($attachmentPath)) {
                $this->mail->addAttachment($attachmentPath);
            }

            // Envoyer l'email
            $this->mail->send();
            return 'Message has been sent';
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
}

