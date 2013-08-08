<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	include("config.php");
	
	$folio = 848;
	$cuerpo_mail  = cuerpo_cotizacion (51, $folio, $db);
	echo $cuerpo_mail;
        exit(0);

        
	$RutClt = $_GET['rut'];
	$doc_id = 1;

	//$result = mssql_query ("vm_sndpwd $doc_id, '$RutClt', $doc_id, '$RutClt'", $db) or die ("No se pudo leer datos del usuario");
	$result = mssql_query ("vm_s_usrweb $doc_id, '$RutClt'", $db) or die ("No se pudo leer datos del usuario");
	while (($row = mssql_fetch_array($result))) {
		$cod_clt    = $row["Cod_Clt"];
		$cod_per    = $row["Cod_Per"];
		$clave 	    = $row["Pwd_Web"];
		$nombre     = $row["Nom_Per"];
		mssql_free_result($result); 
		
		echo "Cod_Clt=".$cod_clt.", Contrase&ntilde;a: ".desencriptar($clave)."<BR><BR>";
	}
?>
