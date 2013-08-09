<?php 
    include("../config.php");

	$EsInstitucional = false;
    $doc_id = 1;
	$ret = isset($_GET['ret']) ? ok($_GET['ret']) : 0;
    if (isset($_POST['dfRutClt']) && trim($_POST['dfRutClt']) != "") {  
	    $RutClt  = strtoupper($_POST['dfRutClt']);
	    $Cod_Suc = strtoupper($_POST['dfCodSuc']);
	    $Cod_Cot = strtoupper($_POST['dfCodCot']);
	  
	    $result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutClt'", $db)
				            or die ("No se pudo leer datos del Cliente");
        if ($row = mssql_fetch_array($result)) {
			$Cod_Clt = $row['Cod_Clt'];
			
			foreach ($_POST as $key => $value) {
				//echo $key." --> ".$value."<BR>";
				if ($key == "dfAppPat") 	 $AppPat    = str_replace("'", "&#39;", strtoupper($value));
				if ($key == "dfAppMat") 	 $AppMat    = str_replace("'", "&#39;", strtoupper($value));
				if ($key == "dfNomUsr") 	 $NombreUsr = str_replace("'", "&#39;", strtoupper($value));
				if ($key == "dfTelefonoUsr") $Telefono  = $value;
				if ($key == "dfFaxUsr") 	 $Fax       = $value;
				if ($key == "dfMovilUsr") 	 $Movil	    = $value;
				if ($key == "dfemail") 		 $email     = $value;
				if ($key == "codpro") 		 $Cod_Pro   = intval($value);
				if ($key == "codesp") 		 $Cod_Esp   = intval($value);
				if ($key == "rbSexo") 		 $nSex   	= intval($value);
				if ($key == "random") 		 $nRandom  	= intval($value);
				if ($key == "dfPassword") 	 $clave	    = encriptar($value,30);
			}
		}
	}
	
	
    $RutUsr = strtoupper($_POST['dfRutUsr']);
	$result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutUsr'", $db)
						or die ("No se pudo leer datos del Usuario");
	if (!($row = mssql_fetch_array($result))) {
		$result = mssql_query("vm_getfolio 'PER'", $db);
		if ($row = mssql_fetch_array($result)) {
			$Cod_Per	 = $row['Tbl_fol'];
			$Cod_TipPer  = 1;
			
			$result = mssql_query("vm_per_i $Cod_Per, $Cod_TipPer, $doc_id, '$RutUsr','$AppPat', '$AppMat', '$NombreUsr',
											NULL, NULL, $Cod_Pro, $Cod_Esp, $nSex, NULL, NULL", $db)
								  or die ("No se pudo actualizar datos del Cliente");
			
			$result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
											'USUARIO WEB', 0, 1",$db);
			
			$result = mssql_query("vm_usrweb_i $Cod_Per, $Cod_Clt, '$clave'", $db);
		}
	}
	else {
		$Cod_Per	 = $row['Cod_Per'];
		$Cod_TipPer  = $row['Cod_TipPer'];
		$bExiste = false;
		
		$result = mssql_query ("vm_usrweb_s $Cod_Per, $Cod_Clt", $db)
					or die ("No se pudo leer datos del Contacto Web (".$Cod_Per.")");
		if ($row = mssql_fetch_array($result))$nRandom = 0;
		
		$result = mssql_query("vm_per_u $Cod_Per, $Cod_TipPer, $doc_id, '$RutUsr','$AppPat', '$AppMat', '$NombreUsr',
										NULL, NULL, $Cod_Pro, $Cod_Esp, $nSex, NULL, NULL", $db)
							  or die ("No se pudo actualizar datos del Cliente");

		$result = mssql_query ("vm_ctt_s $Cod_Clt, $Cod_Suc", $db)
					or die ("No se pudo leer datos del Contacto (".$Cod_Per.")");
		while ($row = mssql_fetch_array($result))
			if ($Cod_Per == $row['Cod_Per']) {
				$bExiste = true;
				break;
			}
		
		if (!$bExiste)
			$result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
												'USUARIO WEB', 0, 1",$db);
		else 
			$result = mssql_query("vm_ctt_u $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
								  'USUARIO WEB', 1",$db);
			
		if ($nRandom == 1) 
			$result = mssql_query("vm_usrweb_i $Cod_Per, $Cod_Clt, '$clave'", $db);
	}

	if ($nRandom == 1) {
		//$query = "vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'";
		$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db)	or die ("No se pudo leer datos del Cliente");
		if ($row = mssql_fetch_array($result)) {
			$TipClt = $row['Cod_TipPer'];
			if ($TipClt == 1)
				$nombreclt = trim($row['Pat_Per'])." ".trim($row['Mat_Per']).", ".trim($row['Nom_Per']);
			else
				$nombreclt = trim($row['RznSoc_Per']);
				
			$result = mssql_query ("vm_sndpwd $doc_id, '$RutClt', $doc_id, '$RutUsr'", $db) 
									or die ("No se pudo leer datos del usuario");
			if ($row = mssql_fetch_array($result)) {
				$Cod_Clt    = $row["Cod_Clt"];
				$Cod_Per	= $row["Cod_Per"];
				$clave 	    = $row["Pwd_Web"];
				$nombre		= $row["Nom_Per"];
				mssql_free_result($result); 
				
				$cuerpo_mail ="Estimado ".$nombre."<BR>";
				$cuerpo_mail.="Adjuntamos la informaci&oacute;n necesaria para acceder a su cuenta de Vestmed.cl <BR><BR>";
				$cuerpo_mail.="Tipo Cliente: ".($TipClt == 1 ? "Natural" : "Institucional")."<BR>";
				$cuerpo_mail.="Rut Cliente: ".formatearRut($RutClt)."<BR>";
				$cuerpo_mail.="Contrase&ntilde;a: ".desencriptar($clave)."<BR><BR>";
				$cuerpo_mail.="Puedes ingresar a tu cuenta mediante el acceso habilitado en la parte superior de cada una de las paginas del sitio. Una vez logeado se habilitar� el Menu del Usuario. Para cambiar tu contrase�a debes ingresar a la secci&oacute;n  \"Mi Cuenta\" donde encontraras todos tus datos personales y seleccionar la opci&oacute;n \"Cambiar Clave\".<BR><BR>";
				$cuerpo_mail.="Si tiene alguna consulta por favor envianos un email a info@vestmed.cl <mailto:info@vestmed.cl> y te responderemos a la brevedad.<BR><BR>";
				$cuerpo_mail.="Te esperamos !<BR><BR>";
				$cuerpo_mail.="Vestmed.cl";
				
				$asunto       = "Recuperación Contraseña www.vestmed.cl"; 
				
				$result = mssql_query ("vm_s_mailusr $Cod_Per, $Cod_Clt", $db)  or die ("No se pudo leer datos de la persona");
				if ($row = mssql_fetch_array($result)) {
					$correo = $row["Mail_Ctt"];
					enviar_mail ($correo, $asunto, $cuerpo_mail, "HTML");
					mssql_close ($db);
				}
			}
		}
	}
	mssql_close ($db);
	
	if ($ret == 0)
		header("Location: editar.php?accion=close&clt=".$RutClt."&xis=1&suc=".$Cod_Suc."&ctt=".$Cod_Per."&cot=".$Cod_Cot); 
		//echo "Location: editar.php?accion=close&clt=".$RutClt."&xis=1&suc=".$Cod_Suc."&ctt=".$Cod_Per."&cot=".$Cod_Cot; 
	else
		header("Location: registrarse.php?accion=closeedt&clt=".$RutClt."&xis=1&suc=".$Cod_Suc); 
?>
