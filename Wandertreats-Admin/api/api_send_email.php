<?php
	//RENCEVTERANS 01/06/2022
    ini_set('display_errors',1);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    
    require 'vendor/autoload.php';
    // require_once 'vendor/twilio/Services/Twilio.php';
    
    
    $messge = "Hello";
    
    try{
         $mail = new PHPMailer;
         $mail->isSMTP();
        
         $mail->SMTPDebug = SMTP::DEBUG_SERVER;

         $htmlContent = file_get_contents("https://wanderlustphtravel.com/wandertreats/resources/email_account_confirmation.php?username=Laurence");

        
         $mail->Host = 'smtp.gmail.com';
         $mail->Port = 587;
        //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->SMTPAuth = true;
        
         $mail->Username = 'renceveterans.dev@gmail.com';
         $mail->Password = 'ljeslkzyjigdvnpo';
        
         $mail->setFrom( 'renceveterans.dev@gmail.com', 'Wander Treats Test' );
         $mail->addAddress('laurencevegerano@gmail.com', 'Laurence Invoice');
        //$mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
         $mail->Subject = 'Hello Laurence ';

         $mail->isHTML(true);  
         $mail->Body    = $htmlContent;
         $mail->AltBody = '';
       //  $mail->addAttachment($applicantId);

        //  if( $none_co_borrower != "true"){
        //     $mail->addAttachment($co_applicantId);
        //  }
       
       
        

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