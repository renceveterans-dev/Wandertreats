<?php
	//RENCEVTERANS 01/08/2022

    ini_set('display_errors',1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
     ini_set('display_errors',1);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    
    require 'vendor/autoload.php';

    include_once('general_functions.php');

	$messageArray = array();
    $purchaseData = array();
    $purchaseDetails = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $name  = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : 'Laurence Vegerano';
    $fullname  = isset($_REQUEST['fullname']) ? trim($_REQUEST['fullname']) : 'Laurence';
    $subject = isset($_REQUEST['subject']) ? trim($_REQUEST['subject']) : 'Log In';
    $email  = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : 'laurencevegerano@gmail.com';
    $message  = isset($_REQUEST['message']) ? trim($_REQUEST['message']) : '1sdsdsdsdsd';

    $ticketNo = GenerateToken();
    $ticketSubject = "[WanderTreats Support] ".  $subject . " - #".$ticketNo;

    try{

        $mail = new PHPMailer;
        $mail->isSMTP();
        
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $urlExtension = "?username=".$name;
        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));

        $htmlContent = file_get_contents('https://wanderlustphtravel.com/wandertreats/resources/contact_support_deafult_message.php?username='.$name, false, $context);
        
        // $htmlContent =  file_contents("https://wanderlustphtravel.com/wandertreats/resources/contact_support_deafult_message.php?username=".$name."");
        //$htmlContent = "jasjhas";
        
         $mail->Host = 'smtp.gmail.com';
         $mail->Port = 587;
            //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->SMTPAuth = true;
        
         $mail->Username = 'wandertreatsph@gmail.com';
         $mail->Password = 'whuzologyhzkqdmq';//sytcurldrsaaaymh';
        
         $mail->setFrom( 'wandertreatsph@gmail.com', 'WanderTreats Support' );
         $mail->addAddress($email,  $fullname);
        //$mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
         $mail->Subject = $ticketSubject;

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

    
   // Returns the contents of a file
    function file_contents($path) {
        $str = @file_get_contents($path);
        if ($str === FALSE) {
            throw new Exception("Cannot access '$path' to read contents.");
        } else {
            return $str;
        }
    }
  

?>