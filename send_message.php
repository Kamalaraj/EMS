<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $subject = htmlspecialchars(strip_tags(trim($_POST['subject'])));
    $message = htmlspecialchars(strip_tags(trim($_POST['message'])));

    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();                                         // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                  // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                              // Enable SMTP authentication
        $mail->Username   = '2021csc054@univ.jfn.ac.lk';            // SMTP username
        $mail->Password   = 'Kamalaraj@2001';                   // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = 587;                               // TCP port to connect to

        //Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('2021csc054@univ.jfn.ac.lk');       // Add a recipient

        // Content
        $mail->isHTML(true);                                   // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = nl2br($message);
        $mail->SMTPDebug = 2; // Enables verbose debug output


        $mail->send();
        echo 'Message sent successfully!';
    } catch (Exception $e) {
        echo "Failed to send message. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

