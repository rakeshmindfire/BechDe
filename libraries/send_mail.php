<?php
/**
 * Library to send activation mail
 *
 * @category   Library
 * @package    QuickSeller
 * @author     Rakesh Ranjan Das <rakesh.das@mindfiresolutions.com>
 * @license    QuickSeller
 * @link       void
 */

require_once 'libraries/PHPMailer-master/PHPMailerAutoload.php';

/**
 * To send mail to a registered user
 *
 * @access public
 * @param string $target Target email-address 
 * @param string $body Contents of email 
 * @return void
 */
function send_mail($target, $body, $subject) {
    // create a new object
    $mail = new PHPMailer(); 
    // enable SMTP
    $mail->IsSMTP();
    // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPDebug = FALSE; 
    // authentication enabled
    $mail->SMTPAuth = TRUE;
    // secure transfer enabled REQUIRED for Gmail
    $mail->SMTPSecure = 'ssl'; 
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->IsHTML(TRUE);
    $mail->Username = SENDER_USERNAME;
    $mail->Password = SENDER_PASSWORD;
    $mail->SetFrom('support@QuickSeller.com');
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($target);

    // Log error if failure during sending of mail
    if ( ! $mail->Send()) {
       error_log_file($mail->ErrorInfo, FALSE);
       header('Location: login.php?success=3');
       exit;
    
    // Record in the database that user has been notified with activation mail
    } else {
        $db = new dbOperation;
        $db->insert_or_update(2, 'login', ['is_notified' => '1'], ['email' => $target]); 
    }
}
