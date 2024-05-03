<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Include the PHPMailer autoload file
    require 'vendor/autoload.php';

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'oniludematthew@gmail.com';
        $mail->Password   = 'Project123$';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        // $mail->SMTPSecure = 'tls';

        //Recipient
        $mail->setFrom('oniludematthew@gmail.com', 'EnergyXchange');
        $mail->addAddress('olugbengaraymond20@gmail.com', 'Olugbenga Raymond');

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Subject';
        $mail->Body    = 'This is the HTML message body';

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        echo 'Message could not be sent. Please try again later.';
    }
?>
