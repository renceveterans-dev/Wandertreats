<?php
	//RENCEVTERANS 01/06/2022
    ini_set('display_errors',1);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    
    require 'vendor/autoload.php';
    // require_once 'vendor/twilio/Services/Twilio.php';
    
    $sender  = isset($_REQUEST['sender']) ? trim($_REQUEST['sender']) : '1';
    $sender_email  = isset($_REQUEST['sender_email']) ? trim($_REQUEST['sender_email']) : 'renceveterans.dev@gmail.com';
    $recipient  = isset($_REQUEST['recipient']) ? trim($_REQUEST['recipient']) : 'sdsdd';
    $recipient_email  = isset($_REQUEST['recipient_email']) ? trim($_REQUEST['recipient_email']) : 'laurencevegerano@gmail.com';
    $subject = isset($_REQUEST['subject']) ? trim($_REQUEST['subject']) : 'WT211215PN4TI';
    $cc_email = isset($_REQUEST['cc_email']) ? trim($_REQUEST['cc_email']) : 'WT211215PN4TI';
    $message = isset($_REQUEST['message']) ? trim($_REQUEST['message']) : 'asasasasasassas';

    if($sender_email == ''){
        echo 'Invalid sender_email';
         exit();
    }

    if($recipient == ''){
        echo 'Invalid recipient';
         exit();
    }

    if( $sender == ''){
        echo 'Invalid sender';
         exit();
    }

    if($recipient_email == ''){
        echo 'Invalid recipient email';
         exit();
  
    }

    if($subject == ''){
        echo 'Invalid subject';
         exit();

    }

    if($cc_email == ''){
        echo 'Invalid cc_email';
         exit();

    }

    if( $message  == ''){
        echo 'Invalid message';
         exit();
    
    }

    try{
         $mail = new PHPMailer;
         $mail->isSMTP();
        
         $mail->SMTPDebug = SMTP::DEBUG_SERVER;

         $mail->Host = 'smtp.gmail.com';
         $mail->Port = 587;
        //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->SMTPAuth = true;
        
         $mail->Username = 'renceveterans.dev@gmail.com';
         $mail->Password = 'sytcurldrsaaaymh';
        
         $mail->setFrom( $sender_email, $sender);
         $mail->addAddress($recipient_email, $recipient);
         $mail->Subject = $subject;

         $mail->isHTML(true);  
         $mail->Body    =  $message;
         $mail->AltBody = '';

        // $mail->addAttachment($applicantId);

        // if( $none_co_borrower != "true"){
        //    $mail->addAttachment($co_applicantId);
        // }

//      $account_sid = "AC6fb45a05498750a9ffd49e84154e16f6";
//      $auth_token = "3f37e731f441f5dc82ed1c8abf8aa118";
//      $twilioMobileNum = "+13603104740";
//      $toMobileNum = "+639398296855";
//      $message= "NEW AUTO LOAN APPLICATION!. Check your email.";

//      $client = new Services_Twilio($account_sid, $auth_token);
    
//         echo $sms = $client->account->messages->sendMessage($twilioMobileNum,$toMobileNum,$message);

        
         //Attach an image file
         //$mail->addAttachment('images/phpmailer_mini.png');
        
         if (!$mail->send()) {
             echo 'Mailer Error: '. $mail->ErrorInfo;
         } else {
             echo $firstname;
            // header("Location: ../success.php?first-name=".$firstname);
         }
    
        
     }catch(Exception $e){
          echo 'Mailer Error';
     }

?>