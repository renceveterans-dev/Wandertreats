<?php
	//RENCEVTERANS 11/14/2021

    ini_set('display_errors',1);
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    require 'vendor/autoload.php';
    include_once('general_functions.php');

	$messageArray = array();
    $deviceArr = array();
	$where = array();
	$result = array();

    unset($messageArray);
    

    $email  = isset($_POST['email']) ? trim($_POST['email']) : 'K-anne07k';
    $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'User';
    $deviceInfo = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : '';
    $vFirebaseDeviceToken = isset($_POST['vFirebaseDeviceToken']) ? trim($_POST['vFirebaseDeviceToken']) : '';
    $sourceLat = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
    $sourceLong  = isset($_POST['longitude']) ? trim($_POST['longitude']) :'';
    $deviceArray['deviceHeight'] = isset($_POST['deviceHeight']) ? trim($_POST['deviceHeight']) : '';
    $deviceArray['deviceWidth'] = isset($_POST['deviceWidth']) ? trim($_POST['deviceWidth']) : '';
    $deviceArray['GeneralAppVersionCode'] =  isset($_POST['GeneralAppVersionCode']) ? trim($_POST['GeneralAppVersionCode']) : '';
    $deviceArray['GeneralAppVersion'] = isset($_POST['GeneralAppVersion']) ? trim($_POST['GeneralAppVersion']) : '';
    $deviceArray['GeneralDeviceType'] = isset($_POST['GeneralDeviceType']) ? trim($_POST['GeneralDeviceType']) : '';
    $deviceArray['vUserDeviceCountry'] = isset($_POST['vUserDeviceCountry']) ? trim($_POST['vUserDeviceCountry']) : '';

   
    $emailResult =  checkEmail($userType, $email);
    
   
    //If Email Result is equals to zero, email exist.
    if($emailResult == 0 ){

        $messge = "Hello";
        
        try{
             $mail = new PHPMailer;
             $mail->isSMTP();
            
             $mail->SMTPDebug = SMTP::DEBUG_SERVER;

             $htmlContent = file_get_contents("https://wanderlustphtravel.com/wandertreats/resources/email_reset_password_notice.php?username=Laurence");

            
             $mail->Host = 'smtp.gmail.com';
             $mail->Port = 587;
            //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->SMTPAuth = true;
            
             $mail->Username = 'renceveterans.dev@gmail.com';
             $mail->Password = 'ljeslkzyjigdvnpo';
            
             $mail->setFrom( 'renceveterans.dev@gmail.com', 'Wandertreats' );
             $mail->addAddress( $email, $email);
            //$mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
             $mail->Subject = 'Reset Password';

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
            $messageArray['response'] = 0;
            $messageArray['label'] = "Warnings";
            $messageArray['error'] = "Mailer Error";
        
         }


        
    }else{
        
        $messageArray['response'] = 0;
        $messageArray['label'] = "Warnings";
        $messageArray['error'] = "Email doesn't exist";
    }
  
    
   echo json_encode($messageArray);
   
// }


?>