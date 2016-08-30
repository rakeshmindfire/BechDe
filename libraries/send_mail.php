<?php
require_once 'libraries/PHPMailer-master/PHPMailerAutoload.php';

function send_mail($target, $body) {
    $mail = new PHPMailer(); // create a new object
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465; // or 587
    $mail->IsHTML(true);
    $mail->Username = SENDER_USERNAME;
    $mail->Password = SENDER_PASSWORD;
    $mail->SetFrom('support@QuickSeller.com');
    $mail->Subject = "Account Activation Link : QickSeller";
    $mail->Body = $body;
    $mail->AddAddress($target);

    if ( ! $mail->Send()) {
       error_log_file($mail->ErrorInfo, FALSE);
       header('Location: login.php?success=3');
       exit;
    
    } else {
        $db = new dbOperation;
        $db->insert_or_update(2, 'login', ['is_notified' => '1'], ['email' => $target]); 
    }
}
