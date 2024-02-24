<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require './PHPMailer/src/Exception.php';
    require './PHPMailer/src/PHPMailer.php';
    require './PHPMailer/src/SMTP.php';
    
    
// configure
$from = 'admin@opraizz.com.ng'; // Replace it with Your Hosting Admin email. REQUIRED!
$sendTo = 'opra.temmy@gmail.com'; // Replace it with Your email. REQUIRED!
$subject = 'New message from '.$_POST["name"];
$fields = array('name' => 'Name', 'email' => 'Email', 'subject' => 'Subject', 'message' => 'Message'); // array variable name => Text to appear in the email. If you added or deleted a field in the contact form, edit this array.
$okMessage = 'Contact form successfully submitted. Thank you, I will get back to you soon!';
$errorMessage = 'There was an error while submitting the form. Please try again later';





// let's do the sending

if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
    //your site secret key
    $secret = '6LdqmCAUAAAAANONcPUkgVpTSGGqm60cabVMVaON';
    //get verify response data

    $c = curl_init('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $verifyResponse = curl_exec($c);

    $responseData = json_decode($verifyResponse);
    if($responseData->success):

        try
        {
            $emailText = nl2br("You have new message from Contact Form\n");

            foreach ($_POST as $key => $value) {

                if (isset($fields[$key])) {
                    $emailText .= nl2br("$fields[$key]: $value\n");
                }
            }

            $headers = array('Content-Type: text/html; charset="UTF-8";',
                'From: ' . $from,
                'Reply-To: ' . $from,
                'Return-Path: ' . $from,
            );
            
            
            
            
            
            $mail = new PHPMailer; //$mail->SMTPDebug = 3;      // Enable verbose debug output
            $mail->isSMTP();     // Set mailer to use SMTP
            $mail->Host = 'mail.opraizz.com.ng;servername.truehost.cloud';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;   // Enable SMTP authentication
            $mail->Username = 'admin@opraizz.com.ng';     // SMTP username
            $mail->Password = 'Tijesunimi437!';              // SMTP password
            $mail->SMTPSecure = 'tls';        // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;      // TCP port to connect to or 25 for non secure
            $mail->setFrom('admin@opraizz.com.ng', 'opraizz.com.ng');
            $mail->addAddress('opra.temmy@gmail.com', 'Praise Oyedele');     // Add a recipient
            $mail->addReplyTo('admin@opraizz.com.ng', 'Praise Oyedele');
            $mail->addCC('admin@opraizz.com.ng');
            $mail->addBCC('admin@opraizz.com.ng');
            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            $mail->isHTML(true);            // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $emailText;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();





            
            mail($sendTo, $subject, $emailText, implode("\n", $headers));

            $responseArray = array('type' => 'success', 'message' => $okMessage);
            


        }
        catch (\Exception $e)
        {
            $responseArray = array('type' => 'danger', 'message' => $errorMessage);
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $encoded = json_encode($responseArray);

            header('Content-Type: application/json');

            echo $encoded;
        }
        else {
            echo $responseArray['message'];
        }

    else:
        $errorMessage = 'Robot verification failed, please try again.';
        $responseArray = array('type' => 'danger', 'message' => $errorMessage);
        $encoded = json_encode($responseArray);

            header('Content-Type: application/json');

            echo $encoded;
    endif;
else:
    $errorMessage = 'Please click on the reCAPTCHA box.';
    $responseArray = array('type' => 'danger', 'message' => $errorMessage);
    $encoded = json_encode($responseArray);

            header('Content-Type: application/json');

            echo $encoded;
endif;


        