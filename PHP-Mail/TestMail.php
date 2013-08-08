<?php

require_once('class.phpgmailer.php');
$mail = new PHPGMailer();
//$mail->Host = "mail.vtr.net";
//$mail->Port = 110;
$mail->Username = 'mario.labrin@microsign.cl';
$mail->Password = 'micro2009';
$mail->From = 'mario.labrin@microsign.cl';
$mail->FromName = 'Mario Labrin';
$mail->Subject = 'Prueba';
$mail->AddAddress('mlabrin@labcor.cl');
$mail->Body = 'Hola, si recibiste este mail estonces estamos perfectos!';
$mail->IsHTML(true);
$mail->Send();

?>
