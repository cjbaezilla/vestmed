<?php 
    include("../config.php");
	
	$Cod_Clt = ok($_GET['clt']);
	$Cod_Suc = ok($_GET['suc']);
	$ret = isset($_GET['ret']) ? ok($_GET['ret']) : 0;
	
	foreach ($_POST as $key => $value) 
		if ($key == "seleccionadoCtt")
			foreach ($value as $key2 => $Cod_Per) {
				//$result = mssql_query ("vm_ctt_d $Cod_Clt, $Cod_Suc, $Cod_Per", $db)
				//            or die ("No se pudo eliminar los contactos");
				$keychars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$length = 16;
				// RANDOM KEY GENERATOR
				$randkey = "";
				$max=strlen($keychars)-1;
				for ($i=0;$i<$length;$i++) $randkey .= substr($keychars, rand(0, $max), 1);
				$clave = encriptar($randkey, 30);
				
				$result = mssql_query ("vm_usrweb_s $Cod_Per, $Cod_Clt", $db)
					or die ("No se pudo leer los datos de la clave");
				if ($row = mssql_fetch_array($result)) 
					$result = mssql_query ("vm_usrweb_u $Cod_Clt, $Cod_Per, '$clave'", $db);
				else
					$result = mssql_query ($query = "vm_usrweb_i $Cod_Per, $Cod_Clt, '$clave'", $db);
				//$result = mssql_query ($query, $db);
				
				$result = mssql_query ("vm_per_s $Cod_Per", $db);
				if ($row = mssql_fetch_array($result)) {
					$doc_id = $row["Cod_TipDoc"];
					$RutUsr = $row["Num_Doc"];
				}
				$result = mssql_query ("vm_cli_s $Cod_Clt", $db);
				if ($row = mssql_fetch_array($result)) {
					$doc_id = $row["Cod_TipDoc"];
					$TipClt = $row["Cod_TipPer"];
					$RutClt = $row["Num_Doc"];
				}
					
				$result = mssql_query ("vm_sndpwd $doc_id, '$RutClt', $doc_id, '$RutUsr'", $db) or die ("No se pudo leer datos del usuario");
				if ($row = mssql_fetch_array($result)) {
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
					
					$asunto       = "Recuperaci�n Contrase�a www.vestmed.cl"; 
					
					$result = mssql_query ("vm_s_mailusr $Cod_Per, $Cod_Clt", $db)  or die ("No se pudo leer datos de la persona");
					if ($row = mssql_fetch_array($result)) {
						$correo = $row["Mail_Ctt"];
						enviar_mail ($correo, $asunto, $cuerpo_mail, "HTML");
					}
				}
				
			}

	mssql_close ($db);
	
	header("Location: escritorio_edtclt.php?clt=".$RutClt."&suc=".$Cod_Suc); 
	
?>
