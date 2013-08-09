<?php 
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("config.php");

	
	$UserId = (isset($_POST['dfusr'])) ? $_POST['dfusr'] : "";
	$RutClt = (isset($_POST['dfrut'])) ? $_POST['dfrut'] : $_POST['dfrutusr'];
        $pass   = $_POST['dfclave'];
	$form   = "catalogo";
	$parametros = "";
	$logear = "off";
	if(isset($HTTP_GET_VARS["form"]))       $form=$HTTP_GET_VARS["form"];
	if(isset($HTTP_GET_VARS["parametros"])) $parametros=$HTTP_GET_VARS["parametros"];
	if(isset($HTTP_GET_VARS["login"]))      $logear=$HTTP_GET_VARS["login"];
	if(isset($HTTP_GET_VARS["loginmail"]))  {
		$token  = split(";", desencriptar($HTTP_GET_VARS["loginmail"]));
		//$token   = split(";", $HTTP_GET_VARS["loginmail"]);
		$RutClt  = $token[0];
		$pass    = $token[1];
		$Cod_Cot = $token[2];
		$form    = $token[3];
	}
	$parametros = str_replace("@", "=", $parametros);
	$parametros = str_replace("$", "&", $parametros);
	if ($parametros != "") $parametros = "?".$parametros;
	
	$doc_id = 1;
        $bExisteUsr = false;
	$bXisClave = false;

	if ($UserId != "") {
		//$result = mssql_query ("vm_s_usrint '$UserId'", $db)
		//						or die ("No se pudo leer datos del usuario");
		$bExisteUsr = true;
		$bXisClave 	= true;
		$perfil = 0;
		if ($UserId == "vendedor") {
			$perfil = 1;
			$form = "reposicion/mdiario";
		}
		if ($UserId == "reponedor") {
			$perfil = 2;
			//$form = "ocompra";
			$form = "reposicion/oreponer";
			$parametros = "?est=2";
		}
		if ($UserId == "comprador") {
			$perfil = 3;
			$form = "reposicion/odrdiario";
		}
		if ($UserId == "cotizador") {
			$perfil = 4;
			$form = "cotizador/mivestmed";
		}
		if ($perfil == 0) $bExisteUsr = false;
	}
	else {
		// Buscamos a que cliente pertenece el Rut
		$result = mssql_query ("vm_s_usrweb $doc_id, '$RutClt'", $db)
								or die ("No se pudo leer datos del usuario");
		while ($row = mssql_fetch_array($result)) {
			$bExisteUsr = true;
			$clave 	    = $row["Pwd_Web"];
			if ($pass == desencriptar($clave)) {
				$bXisClave 	= true;
				$cod_clt	= $row["Cod_Clt"];
				$cod_per	= $row["Cod_Per"];
				$nombre		= $row["Nom_Per"];
				break;
			}
		}
		mssql_free_result($result);
	}
	
        if (!$bExisteUsr) {
		if ($form != "mdiario") header("Location: aviso.php?idmsg=2"); 
		else header("Location: index2.htm"); 
		exit(0);
	}
  
	else if (!$bXisClave) {
		header("Location: aviso.php?idmsg=3"); 
		exit(0);
	}
	
	if ($form == "index") {
		$form = "catalogo";
		$parametros="?idu=".$cod_per."&idc=".$cod_clt;
	}
	else if ($form == "mail") {
		$form = "preview_cot";
		$parametros="?cot=".$Cod_Cot;
	}
	else if ($form == "mensajescot") {
		$form = "verdetallemensajes";
		$parametros="?cot=".$Cod_Cot."&pag=1";
	}
	else if ($form == "mensajesctt") {
		$form = "verdetallemensajes";
		$parametros="?folctt=".$Cod_Cot."&pag=1";
	}
	else if ($form == 'detalle-producto') {
		foreach ($_POST as $key => $value) {
			if ($key == "cantidad")	$cantidad  = intval($value);
			if ($key == "dfDsg")	$cod_dsg   = $value;
			if ($key == "dfSze")	$val_sze   = $value;
			if ($key == "dfPat")	$cod_pat   = $value;
			if ($key == "dfPrd")	$cod_prd   = $value;
			if ($key == "dfTitle")	$cod_title = $value;
		}
		if ($logear == "on") {
			//$sp = "vm_i_cotweb $cod_per, $cod_clt, $cantidad, '$cod_dsg', '$val_sze', '$cod_pat', $cod_prd";
			$result = mssql_query ("vm_i_cotweb $cod_per, $cod_clt, $cantidad, '$cod_dsg', '$val_sze', '$cod_pat', '$cod_prd'", $db) 
                                               or die ("No se pudo insertar la cotizacion<BR>");
			if ($row = mssql_fetch_array($result)) {
				$cod_cot = $row["cod_cot"];
				$_SESSION['CodCot'] = $cod_cot;     
			}
			eliminarLastLevelSession();
			$parametros="?producto=".$cod_prd."&title=".$cod_title;
		}
	}
        if ($cod_clt != "") {
		$_SESSION['CodClt'] = $cod_clt;     
		$_SESSION['CodPer'] = $cod_per;     
		$_SESSION['UsrId']  = $RutClt;
	}
	else if ($UserId != "") {
		$_SESSION['Fecha']    = date("d/m/Y");
		$_SESSION['Perfil']   = $perfil;
		$_SESSION['UsrIntra'] = $UserId;
	}
		
	//echo $HTTP_GET_VARS["parametros"]."<BR>";
	//echo "Location: ".$form.".php?idu=".$cod_per.$parametros;
	header("Location: ".$form.".php".$parametros);
?>
