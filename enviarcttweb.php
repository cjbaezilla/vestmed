<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	include("config.php");

	foreach ($_POST as $key => $value) {
		//echo $key." --> ".$value."<BR>";
		if ($key == "dfrut")        $rut_ctt = strtoupper($value);
		if ($key == "sexo")         $sex_ctt = intval($value);
		//if ($key == "nombre")       $nom_ctt = str_replace("'", "&#39;", strtoupper($value));
		if ($key == "nombre")       $nom_ctt = str_replace("\'", "''", strtoupper(utf8_decode ($value)));
		if ($key == "apppat")       $pat_ctt = str_replace("\'", "''", strtoupper(utf8_decode ($value)));
		if ($key == "appmat")       $mat_ctt = str_replace("\'", "''", strtoupper(utf8_decode ($value)));
		if ($key == "institucion")  $nom_itt = str_replace("\'", "''", strtoupper(utf8_decode ($value)));
		if ($key == "email")        $email   = $value;
		if ($key == "fono")         $fono    = $value;
		if ($key == "comentarios")  $obs_ctt = str_replace("\'", "''", utf8_decode($value));
		if ($key == "tipcna")       $tip_cna = intval($value);
		if ($key == "fichero")      $fichero = $value;
	}

        /*
	$fileupload = "";
           if ($_FILES['documento']['tmp_name'] != "") {
	    if (is_uploaded_file($_FILES['documento']['tmp_name'])) {
		    $fileupload = $pathadjuntos.$fichero;
		    if (!copy ($_FILES['documento']['tmp_name'], $fileupload))
		    {
			   echo "no pudo copiar archivo:".$_FILES['documento']['tmp_name']."<BR>To:".$fileupload;
		    }
	    } else {
		    echo "Posible ataque de carga de archivo: ";
		    echo "nombre de archivo '". $_FILES['documento']['tmp_name'] . "'.";
		    exit(0);
	    }
	}
	*/
	$archivo = $_FILES['documento']['name'];
	if ($archivo != "") {
		$fileupload = $pathadjuntos.$archivo;
		if (!move_uploaded_file($_FILES['documento']['tmp_name'], $fileupload)){
		   echo $_FILES['documento']['tmp_name']."<BR>".$fileupload."<BR>";
		   echo "Ocurri&oacute; alg&uacute;n error al subir el fichero. No pudo guardarse.";
		   exit(0);
		} 
	}

	$tipo = 1;
	$doc_id = 1;
	$nombre = trim($nom_ctt." ".$pat_ctt." ".$mat_ctt);
	if ($nom_itt != "") $tipo = 2;

	//$query = "vm_s_per_tipdoc $doc_id, '$rut_ctt'";
	$result = mssql_query ("vm_s_per_tipdoc $doc_id, '$rut_ctt'", $db) 
				or die ("No se pudo leer datos de la Persona<BR>");
	if (!(($row = mssql_fetch_array($result)))) {
		$result = mssql_query("vm_getfolio 'PER'", $db);
		if (($row = mssql_fetch_array($result))) {
			$Cod_Per	= $row['Tbl_fol'];
			$Cod_TipPer = 1;
			$result = mssql_query("vm_per_i $Cod_Per, $Cod_TipPer, $doc_id, '$rut_ctt','$pat_ctt', '$mat_ctt', '$nom_ctt',
										NULL, NULL, NULL, NULL, $sex_ctt, NULL, NULL", $db)
									or die ("No se pudo actualizar datos de la Persona");
		}
    }
	else
		$Cod_Per = $row['Cod_Per'];

	$query = "vm_i_cttweb $tipo, '$nombre', 1, '$rut_ctt', $sex_ctt, '$nom_itt', '$email', '$fono', $tip_cna, '$obs_ctt', '$archivo'";
        //echo $query;
	$result = mssql_query($query,$db) 
                              or die ("No se pudo insertar registro en Contactos Web<br>".$query);
	if (($row = mssql_fetch_array($result)))
	{
		$folio = $row["fol_cttweb"];

	   
	    $cuerpo_mail  = cuerpo_contactoweb ($home, $pathadjuntos, 51, $folio, $db);
	    $asunto       = "Contacto Nro ".$folio; 
	    $correos = split(";", $correovestmed);
	    foreach ($correos as $key => $destinatario)
			enviar_mail ($destinatario, $asunto, $cuerpo_mail, "HTML");
	   
	    header("Location: aviso.php?idmsg=30"); 
	}
	mssql_close ($db);
?>
