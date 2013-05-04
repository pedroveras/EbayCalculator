<?php
require_once('class.phpmailer.php');

$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 587; 
	$mail->Username = 'pedroveras@gmail.com';  
	$mail->Password = 'ordep87.';           
	$mail->SetFrom('pedroveras@gmail.com', 'Pedro Henrique');
	$mail->Subject = 'Test';
	$mail->Body = 'Test';
	$mail->AddAddress('pedrow_veras@hotmail.com', 'Pedro');
	if(!$mail->Send()) {
		echo 'Mail error: '.$mail->ErrorInfo; 
	} else {
		echo 'Message sent!';
	}

?>