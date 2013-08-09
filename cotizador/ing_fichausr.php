<?php 
    include("../config.php");

	$EsInstitucional = false;
    $doc_id = 1;
    if (isset($_POST['dfRutClt']) && trim($_POST['dfRutClt']) != "") {  
	    $RutClt = strtoupper($_POST['dfRutClt']);
	  
	    $result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutClt'", $db)
				            or die ("No se pudo leer datos del Cliente");
        if (!($row = mssql_fetch_array($result))) {
		    $result = mssql_query("vm_getfolio 'PER'", $db);
		    if ($row = mssql_fetch_array($result)) {
				$Cod_Per	= $row['Tbl_fol'];
				$Cod_TipPer = 2;
				$Nombre     = str_replace("\'", "''", strtoupper($_POST['dfNombre']));
				$NombreF    = str_replace("\'", "''", strtoupper($_POST['dfNombreFantasia']));
				$Web        = $_POST['dfWeb'];
				  
				$result = mssql_query("vm_per_i $Cod_Per, $Cod_TipPer, $doc_id, '$RutClt', NULL, NULL, NULL,
												'$Nombre', '$NombreF', NULL, NULL, NULL, '$Web', NULL", $db)
									  or die ("No se pudo actualizar datos del Cliente");
				
				$result = mssql_query("vm_getfolio 'CLT'", $db);
				if ($row = mssql_fetch_array($result)) {
					$Cod_Clt = $row['Tbl_fol'];
					$result = mssql_query("vm_clt_i $Cod_Per, $Cod_Clt",$db);
					$EsInstitucional = true;
				}
			}
			
			foreach ($_POST as $key => $value) {
				//echo $key." --> ".$value."<BR>";
				if ($key == "dfDireccion") 	 $Direccion = str_replace("\'", "''", strtoupper($value));
				if ($key == "dfTelefonoSuc") $Telefono  = $value;
				if ($key == "dfFaxSuc") 	 $Fax       = $value;
				if ($key == "codcmn") 		 $Cod_Cmn   = intval($value);
				if ($key == "codcdd") 		 $Cod_Cdd   = intval($value);
			}
			
			$result = mssql_query("vm_getfolio 'SUC'", $db);
			if ($row = mssql_fetch_array($result)) {
				$Cod_Suc = $row['Tbl_fol'];
				$result = mssql_query("vm_suc_i $Cod_Suc, $Cod_Clt, 'CASA MATRIZ', '$Direccion', 
												$Cod_Cmn, $Cod_Cdd, '$Telefono', '$Fax', 0", $db)
									  or die ("No se pudo actualizar datos de la Sucursal");
			}

                        $result = mssql_query("vm_exp_to_flexline $Cod_Per", $db);

		}
		else {
			$Cod_Clt = $row['Cod_Clt'];
			$EsInstitucional = false;
			if ($row['Cod_TipPer'] == 2) $EsInstitucional = true;
		}
	}
	
	foreach ($_POST as $key => $value) {
		//echo $key." --> ".$value."<BR>";
		if ($key == "dfAppPat") 	 $AppPat    = str_replace("\'", "''", strtoupper($value));
		if ($key == "dfAppMat") 	 $AppMat    = str_replace("\'", "''", strtoupper($value));
		if ($key == "dfNomUsr") 	 $NombreUsr = str_replace("\'", "''", strtoupper($value));
		if ($key == "dfDireccion") 	 $Direccion = str_replace("\'", "''", strtoupper($value));
		if ($key == "dfTelefonoUsr") $Telefono  = $value;
		if ($key == "dfFaxUsr") 	 $Fax       = $value;
		if ($key == "dfMovilUsr") 	 $Movil	    = $value;
		if ($key == "dfemail") 		 $email     = $value;
		if ($key == "codcmn") 		 $Cod_Cmn   = intval($value);
		if ($key == "codcdd") 		 $Cod_Cdd   = intval($value);
		if ($key == "codpro") 		 $Cod_Pro   = intval($value);
		if ($key == "codesp") 		 $Cod_Esp   = intval($value);
		if ($key == "rbSexo") 		 $nSex   	= intval($value);
		if ($key == "random") 		 $nRandom  	= intval($value);
		if ($key == "dfPassword") 	 $clave	    = encriptar($value,30);
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
			
			if (!$EsInstitucional)	{
				$result = mssql_query("vm_getfolio 'CLT'", $db);
				if ($row = mssql_fetch_array($result)) {
					$RutClt = $RutUsr;
					$Cod_Clt = $row['Tbl_fol'];
					$result = mssql_query("vm_clt_i $Cod_Per, $Cod_Clt",$db);
				}
				$Cod_PerWeb = $Cod_Per;
			}
			
			if (!$EsInstitucional) {
				$result = mssql_query("vm_getfolio 'SUC'", $db);
				if ($row = mssql_fetch_array($result)) {
					$Cod_Suc = $row['Tbl_fol'];
					$result = mssql_query("vm_suc_i $Cod_Suc, $Cod_Clt, 'CASA MATRIZ', '$Direccion', 
													$Cod_Cmn, $Cod_Cdd, '$Telefono', '$Fax', 0", $db)
										  or die ("No se pudo actualizar datos de la Sucursal");
				}
				
				$result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
												'SOCIO', 0, 1",$db);
			}
			else
				$result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
												'USUARIO WEB', 0, 1",$db);
			
			$result = mssql_query("vm_usrweb_i $Cod_Per, $Cod_Clt, '$clave'", $db);
		}
	}
	else {
		$Cod_Per	 = $row['Cod_Per'];
		$Cod_Clt	 = $row['Cod_Clt'];
		$Cod_TipPer  = $row['Cod_TipPer'];
		
		$result = mssql_query("vm_per_u $Cod_Per, $Cod_TipPer, $doc_id, '$RutUsr','$AppPat', '$AppMat', '$NombreUsr',
										NULL, NULL, $Cod_Pro, $Cod_Esp, $nSex, NULL, NULL", $db)
							  or die ("No se pudo actualizar datos del Cliente");

		if ($Cod_Clt > 0) {
			$result = mssql_query("vm_suc_s $Cod_Clt", $db);
			if ($row = mssql_fetch_array($result)) {
				$Cod_Suc	= $row['Cod_Suc'];
				$Nom_Suc    = $row['Nom_Suc'];
				$Cod_CmnSuc = $row['Cod_Suc'];
				$Cod_CddSuc = $row['Cod_Cdd'];
				$Dir_Suc    = trim($row['Dir_Suc']);
				if ($Cod_CmnSuc != $Cod_Cmn Or $Cod_CddSuc != $Cod_Cdd Or $Dir_Suc != $Direccion) 
					$result = mssql_query("vm_suc_u $Cod_Suc, $Cod_Clt, '$Nom_Suc', '$Direccion', $Cod_Cmn, $Cod_Cdd, '$Telefono', '$Fax'", $db);
			}
			
			if ($nRandom == 1) {
				$Cod_Per = $row["Cod_Per"];
				//if (!$EsInstitucional) $Cod_Clt = $row["Cod_Clt"];
				$clave	= encriptar($_POST['dfPassword'], 30);
				$result = mssql_query("vm_usrweb_i $Cod_Per, $Cod_Clt, '$clave'", $db);
			}
		}
		
	}

	if ($nRandom == 1) {
		$result = mssql_query ("vm_sndpwd $doc_id, '$RutClt', $doc_id, '$RutUsr'", $db) or die ("No se pudo leer datos del usuario");
		if ($row = mssql_fetch_array($result)) {
			$cod_clt    = $row["Cod_Clt"];
			$cod_per	= $row["Cod_Per"];
			$clave 	    = $row["Pwd_Web"];
			$nombre		= $row["Nom_Per"];
			mssql_free_result($result); 
			
			$cuerpo_mail ="Estimado ".$nombre."<BR><BR>";
			$cuerpo_mail.="Su clave de acceso a www.vestmed.cl es : ".desencriptar($clave)."<BR><BR><BR>";
			$cuerpo_mail.="Atte<BR><BR><BR>Vestmed Ltda";
			
			$asunto       = "Clave Vestmed"; 
			
			$result = mssql_query ("vm_s_mailusr $cod_per, $cod_clt", $db)  or die ("No se pudo leer datos de la persona");
			if ($row = mssql_fetch_array($result)) {
				$correo = $row["Mail_Ctt"];
				enviar_mail ($correo, $asunto, $cuerpo_mail, "HTML");
				mssql_close ($db);
			}
		}
	}
	mssql_close ($db);
	
	header("Location: registrarse.php?accion=close&clt=".$RutClt."&xis=1"); 
	
?>
