<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	include("config.php");

	$folio = ok($_GET['folio']);
	
	$tipo = 1;
	   
    $cuerpo_mail  = cuerpo_contactoweb ($home, $pathadjuntos, 51, $folio, $db);
    $asunto       = "Contacto Nro ".$folio; 
    $correos = split(";", $correovestmed);
    foreach ($correos as $key => $destinatario)
		enviar_mail ($destinatario, $asunto, $cuerpo_mail, "HTML");
	   
	mssql_close ($db);
?>
