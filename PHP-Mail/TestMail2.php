<?php

require_once “Mail.php”;
$from=”HSTSMS Account “;
$to=$_POST['email'];
$subject=”Login Information”;
$host=”mail.hstsms.com”;
$port=”25?;
$username=”info@hstsms.com”;
$password=”*********”;
$headers=array(’MIME-Version’=>’1.0',’Content-Type’=>’text/plain; charset=iso-8859-1',’From’=>$from,’To’=>$to,’Subject’=>$subject);
$smtp=Mail::factory(’smtp’,array(’host’=>$host,’auth’=>true,
‘username’=>$username,’password’=>$password));
$mail=$smtp->send($to,$headers,$message);

?>
