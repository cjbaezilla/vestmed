<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("config.php");

	$doc_id = 1;
	
	foreach ($_POST as $key => $value) {
            //echo $key." --> ".$value."<BR>";
            if ($key == "dfRutUsr") $RutUsr = $value;
            if ($key == "dfRutClt") $RutClt = $value;
            //if ($key == "rbTipoClt") $TipClt = $value;
	}
	
	//$query = "vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'";
	$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db)	or die ("No se pudo leer datos del Cliente");
	if (($row = mssql_fetch_array($result))) {
            $TipClt = $row['Cod_TipPer'];
            if ($TipClt == 1)
                $nombreclt = trim($row['Pat_Per'])." ".trim($row['Mat_Per']).", ".trim($row['Nom_Per']);
            else
                $nombreclt = trim($row['RznSoc_Per']);

            $result = mssql_query ("vm_sndpwd $doc_id, '$RutClt', $doc_id, '$RutUsr'", $db) or die ("No se pudo leer datos del usuario");
            if (($row = mssql_fetch_array($result))) {
                $cod_clt    = $row["Cod_Clt"];
                $cod_per	= $row["Cod_Per"];
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

                $result = mssql_query ("vm_s_mailusr $cod_per, $cod_clt", $db)  or die ("No se pudo leer datos de la persona");
                if (($row = mssql_fetch_array($result))) {
                        $correo = $row["Mail_Ctt"];
                        enviar_mail ($correo, $asunto, $cuerpo_mail, "HTML");
                        mssql_close ($db);

                        header("Location: aviso.php?&idu=$cod_per&idc=$cod_clt&idmsg=21"); 	
                }
                else {
                        mssql_close ($db);
                        header("Location: aviso.php?idmsg=22"); 	
                }
            }
            else {
                mssql_close ($db);
                header("Location: aviso.php?idmsg=23"); 	
            }
	}
?>
