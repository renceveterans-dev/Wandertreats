<?php
	//RENCEVTERANS 01/06/2022
    ini_set('display_errors',1);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    
    require 'vendor/autoload.php';
    // require_once 'vendor/twilio/Services/Twilio.php';

    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $name  = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : 'Laurence';
    $fullname  = isset($_REQUEST['fullname']) ? trim($_REQUEST['fullname']) : 'Laurence Vegerano';
    $email  = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : 'laurencevegerano@gmail.com';
    $token  = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : '132312455';
    $url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : 'https://wanderlustphtravel.com/wandertreats/resources/email_account_successfully_confirmed.php';

    
    try{



        $mail = new PHPMailer;
        $mail->isSMTP();
        
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $params = array(
            'userId'=> $userId,
            'token'=> $token,
            'name'=> $name,
            'fullname'=> $fullname,
            'email'=> $email,
            'url'=> $url
         );


        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));

        $urlExtension = "?username=".$params['name']."&url=".$params['url']."&userId=".$params['userId']."&token=".$params['token']."";
        $htmlContent = @file_get_contents("https://wanderlustphtravel.com/wandertreats/resources/email_account_confirmation.php".$urlExtension, false, $context);
        //$htmlContent =curl_get_contents("https://wanderlustphtravel.com/wandertreats/resources/email_account_confirmation.php".$urlExtension);
        //$htmlContent = "jasjhas";
        
         $mail->Host = 'smtp.gmail.com';
         $mail->Port = 587;
        //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->SMTPAuth = true;
        
         $mail->Username = 'wandertreatsph@gmail.com';
         $mail->Password = 'whuzologyhzkqdmq';//sytcurldrsaaaymh';
        
         $mail->setFrom( 'renceveterans.dev@gmail.com', 'WanderTreats' );
         $mail->addAddress($params['email'], $params['fullname']);
        //$mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
         $mail->Subject = 'WanderTreats Verification';

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

     function curl_get_contents($url){
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          $data = curl_exec($ch);
          curl_close($ch);
          return $data;
    }

?>