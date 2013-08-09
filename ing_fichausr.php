<?php 
    include("config.php");

    $EsInstitucional = false;
    $doc_id = 1;
    if (isset($_POST['dfRutClt']) && trim($_POST['dfRutClt']) != "") {  
        $EsInstitucional = true;
        $RutClt = strtoupper($_POST['dfRutClt']);

        $result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutClt'", $db)  or die ("No se pudo leer datos del Cliente");
        if (!(($row = mssql_fetch_array($result)))) {
		    $result = mssql_query("vm_getfolio 'PER'", $db);
		    if (($row = mssql_fetch_array($result))) {
				$Cod_Per    = $row['Tbl_fol'];
				$Cod_TipPer = 2;
				$Nombre     = str_replace("\'", "''", strtoupper(utf8_decode($_POST['dfNombre'])));
				$NombreF    = str_replace("\'", "''", strtoupper(utf8_decode($_POST['dfNombreFantasia'])));
				$Web        = $_POST['dfWeb'];
				  
				$result = mssql_query("vm_per_i $Cod_Per, $Cod_TipPer, $doc_id, '$RutClt', NULL, NULL, NULL,
												'$Nombre', '$NombreF', NULL, NULL, NULL, '$Web', NULL", $db)
									  or die ("No se pudo actualizar datos del Cliente");
				
				$result = mssql_query("vm_getfolio 'CLT'", $db);
				if (($row = mssql_fetch_array($result))) {
					$Cod_Clt = $row['Tbl_fol'];
					$result = mssql_query("vm_clt_i $Cod_Per, $Cod_Clt",$db);
					$EsInstitucional = true;
                                        
				}
			}
			
			foreach ($_POST as $key => $value) {
				//echo $key." --> ".$value."<BR>";
				if ($key == "dfDireccion")   $Direccion = str_replace("\'", "''", utf8_decode (strtoupper($value)));
				if ($key == "dfTelefonoSuc") $Telefono  = $value;
				if ($key == "dfFaxSuc")      $Fax       = $value;
				if ($key == "codcmn") 	     $Cod_Cmn   = intval($value);
				if ($key == "codcdd") 	     $Cod_Cdd   = intval($value);
			}
			
			$result = mssql_query("vm_getfolio 'SUC'", $db);
			if (($row = mssql_fetch_array($result))) {
				$Cod_Suc = $row['Tbl_fol'];
				$result = mssql_query("vm_suc_i $Cod_Suc, $Cod_Clt, 'CASA MATRIZ', '$Direccion', 
												$Cod_Cmn, $Cod_Cdd, '$Telefono', '$Fax', 0", $db)
									  or die ("No se pudo actualizar datos de la Sucursal");
			}

                        // Flexline
                        $result = mssql_query("vm_exp_to_flexline $Cod_Per", $db);                                        
		}
		else {
			$Cod_Per    = $row['Cod_Per'];
			$Cod_TipPer = $row['Cod_TipPer'];
			$Cod_Clt    = $row['Cod_Clt'];
			if ($Cod_Clt == null or $Cod_Clt == "") {
				$result = mssql_query("vm_getfolio 'CLT'", $db);
				if (($row = mssql_fetch_array($result))) {
					$Cod_Clt = $row['Tbl_fol'];
					$result = mssql_query("vm_clt_i $Cod_Per, $Cod_Clt",$db);
					$EsInstitucional = ($Cod_TipPer == 1) ? false : true;
                                        
				}
				
				foreach ($_POST as $key => $value) {
					//echo $key." --> ".$value."<BR>";
					if ($key == "dfDireccion") 	  $Direccion = str_replace("\'", "''", utf8_decode (strtoupper($value)));
					if ($key == "dfTelefonoSuc")      $Telefono  = $value;
					else if ($key == "dfTelefonoUsr") $Telefono  = $value;
					if ($key == "dfFaxSuc") 	  $Fax       = $value;
					else if ($key == "dfFaxUsr") 	  $Fax       = $value;
					if ($key == "codcmn") 		  $Cod_Cmn   = intval($value);
					if ($key == "codcdd") 		  $Cod_Cdd   = intval($value);
					if ($key == "dfMovilUsr") 	  $Movil     = $value;
					if ($key == "dfemail") 		  $email     = $value;
				}
				
				$result = mssql_query("vm_getfolio 'SUC'", $db);
				if (($row = mssql_fetch_array($result))) {
					$Cod_Suc = $row['Tbl_fol'];
					$result = mssql_query("vm_suc_i $Cod_Suc, $Cod_Clt, 'CASA MATRIZ', '$Direccion', 
													$Cod_Cmn, $Cod_Cdd, '$Telefono', '$Fax', 0", $db)
										  or die ("No se pudo actualizar datos de la Sucursal");
										  
					$result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
												'USUARIO WEB', 0, 1",$db);
				}
                                
                                // Flexline
                                $result = mssql_query("vm_exp_to_flexline $Cod_Per", $db);                                        
			}
			else {
				if (!isset($_POST['dfPassword'])) $email = $_POST['dfemail'];
				if (isset($_POST['dfCodSuc'])) $Cod_Suc = intval(ok($_POST['dfCodSuc']));
				$EsInstitucional = ($Cod_TipPer == 1) ? false : true;
				//echo "Suc = ".$Cod_Suc."<BR>";
			}
		}
	}
	
        $RutUsr = strtoupper($_POST['dfRutUsr']);
	$result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutUsr'", $db)
						or die ("No se pudo leer datos del Usuario");
	if (!(($row = mssql_fetch_array($result)))) {
		$result = mssql_query("vm_getfolio 'PER'", $db);
		if (($row = mssql_fetch_array($result))) {
                    $Cod_Per	 = $row['Tbl_fol'];
                    $Cod_TipPer  = 1;
			
                    foreach ($_POST as $key => $value) {
                        //echo $key." --> ".$value."<BR>";
                        if ($key == "dfAppPat") 	$AppPat    = str_replace("\'", "''", utf8_decode (strtoupper($value)));
                        if ($key == "dfAppMat") 	$AppMat    = str_replace("\'", "''", utf8_decode (strtoupper($value)));
                        if ($key == "dfNomUsr") 	$NombreUsr = str_replace("\'", "''", utf8_decode (strtoupper($value)));
                        if ($key == "dfDireccion") 	$Direccion = str_replace("\'", "''", utf8_decode (strtoupper($value)));
                        if ($key == "dfTelefonoUsr")    $Telefono  = $value;
                        if ($key == "dfFaxUsr") 	$Fax       = $value;
                        if ($key == "dfMovilUsr") 	$Movil	    = $value;
                        if ($key == "dfemail") 	 $email     = $value;
                        if ($key == "codcmn") 	 $Cod_Cmn   = intval($value);
                        if ($key == "codcdd") 	 $Cod_Cdd   = intval($value);
                        if ($key == "codpro") 	 $Cod_Pro   = intval($value);
                        if ($key == "codesp") 	 $Cod_Esp   = intval($value);
                        if ($key == "rbSexo") 	 $nSex      = intval($value);
                        if ($key == "dfPassword") 	 $clave	    = encriptar($value,30);
                    }
                    //echo "Mail= ".$email."<BR>";
			  
                    $result = mssql_query("vm_per_i $Cod_Per, $Cod_TipPer, $doc_id, '$RutUsr','$AppPat', '$AppMat', '$NombreUsr',
                                                                                    NULL, NULL, $Cod_Pro, $Cod_Esp, $nSex, NULL, NULL", $db)
                                                              or die ("No se pudo actualizar datos del Cliente");

                    if (!$EsInstitucional)	{
                            $result = mssql_query("vm_getfolio 'CLT'", $db);
                            if (($row = mssql_fetch_array($result))) {
                                    $Cod_Clt = $row['Tbl_fol'];
                                    $result = mssql_query("vm_clt_i $Cod_Per, $Cod_Clt",$db);
                            }
                            $Cod_PerWeb = $Cod_Per;
                    }

                    if (!$EsInstitucional) {
                            $result = mssql_query("vm_getfolio 'SUC'", $db);
                            if (($row = mssql_fetch_array($result))) {
                                    $Cod_Suc = $row['Tbl_fol'];
                                    $result = mssql_query("vm_suc_i $Cod_Suc, $Cod_Clt, 'CASA MATRIZ', '$Direccion',
                                                                    $Cod_Cmn, $Cod_Cdd, '$Telefono', '$Fax', 0", $db)
                                                                              or die ("No se pudo actualizar datos de la Sucursal");
                            }

                            $result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
                                                                                            'SOCIO', 0, 1",$db);
                    }
                    else {
                            //echo "vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email','USUARIO WEB', 0, 1<BR>";
                            $result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
                                                                                            'USUARIO WEB', 0, 1",$db);
                    }

                    $result = mssql_query("vm_usrweb_i $Cod_Per, $Cod_Clt, '$clave'", $db);
                    
                    // Flexline
                    $result = mssql_query("vm_exp_to_flexline $Cod_Per", $db);                                        
                    
		}
	}
	else {
		$Cod_Per = $row["Cod_Per"];
		if (!$EsInstitucional) $Cod_Clt = $row["Cod_Clt"];
		if (!isset($_POST['dfPassword'])) {
			$result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutClt'", $db) or die ("No se pudo leer datos del Cliente");
			if (($row = mssql_fetch_array($result))) {
				$Cod_Clt = $row['Cod_Clt'];
				$TipClt  = $row['Cod_TipPer'];
				mssql_free_result($result); 
		    
				$result = mssql_query ("vm_ini_mailctt $Cod_Clt, $Cod_Per, '$email'", $db) or die ("No se pudo leer datos del Usuario");
				
				$keychars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$length = 16;
				// RANDOM KEY GENERATOR
				$randkey = "";
				$max=strlen($keychars)-1;
				for ($i=0;$i<$length;$i++) $randkey .= substr($keychars, rand(0, $max), 1);
				$clave = encriptar($randkey, 30);
				$result = mssql_query("vm_usrweb_i $Cod_Per, $Cod_Clt, '$clave'", $db);
				
				$result = mssql_query ("vm_sndpwd $doc_id, '$RutClt', $doc_id, '$RutUsr'", $db) or die ("No se pudo leer datos del usuario");
				if (($row = mssql_fetch_array($result))) {
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
					if (($row = mssql_fetch_array($result))) {
						$correo = $row["Mail_Ctt"];
						enviar_mail ($correo, $asunto, $cuerpo_mail, "HTML");
						mssql_close ($db);
						
						header("Location: aviso.php?&idu=$Cod_Per&idc=$Cod_Clt&idmsg=21"); 	
					}
				}
			}
		}
		else {
		    $result = mssql_query ("vm_cttper_s $Cod_Clt, $Cod_Suc, $Cod_Per", $db);
			if (!(($row = mssql_fetch_array($result)))) {
				foreach ($_POST as $key => $value) {
					//echo $key." --> ".$value."<BR>";
					if ($key == "dfTelefonoUsr") $Telefono  = $value;
					if ($key == "dfMovilUsr") 	 $Movil	    = $value;
					if ($key == "dfemail") 		 $email     = $value;
					if ($key == "dfPassword") 	 $clave	    = encriptar($value,30);
				}
				$result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Movil', '$email',
												'USUARIO WEB', 0, 1",$db);
			}
			else $clave	= encriptar($_POST['dfPassword'], 30);
			
			$result = mssql_query("vm_usrweb_i $Cod_Per, $Cod_Clt, '$clave'", $db);
		}
		
	}

	mssql_close ($db);
	
	header("Location: aviso.php?idu=".$Cod_Per."&idc=".$Cod_Clt."&idmsg=1"); 
?>
