<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '1');
	session_start();
	include("global_cot.php");

	$IVA = 0.0;
	$result = mssql_query("vm_getfolio_s 'IVA'",$db);
	if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;
	
	foreach ($_POST as $key => $value) {
		//echo $key." --> ".$value."<BR>";
		if ($key == "cod_cot") $cod_cot = intval($value);
		if ($key == "cod_clt") $cod_clt = intval($value);
		if ($key == "cod_per") $cod_per = intval($value);
		if ($key == "cod_suc") $cod_suc = intval($value);
		
		if ($key == "dfPrecio")       $cod_pre = intval($value);
		if ($key == "dfIVA")          $cod_iva = intval($value);
		if ($key == "dfValUSD")       $val_usd = intval($value);
		if ($key == "dfCriticidad")   $cod_cri = intval($value);
		if ($key == "dfFecCie")       $fecha = $value;
		if ($key == "dfValPro")       $val_pro = intval($value);
                
		if ($key == "comentarios")    $obs_res = $value;
		if ($key == "dfDescto")       $val_des = intval($value);
		if ($key == "totprd")         $tot_prd = intval($value);
		
		if ($key == "is_dsp")         $is_dsp = intval($value);
		if ($key == "cod_crr")        $cod_crr = intval($value);
		if ($key == "cod_svccrr")     $cod_svccrr = intval($value);
		if ($key == "cod_sucdsp")     $cod_sucdsp = intval($value);
		if ($key == "cod_cdddsp")     $cod_cdddsp = intval($value);
		if ($key == "cod_cmndsp")     $cod_cmndsp = intval($value);
		if ($key == "dir_dsp")        $dir_dsp = str_replace("\'", "''", utf8_decode ($value));
		if ($key == "val_dsp")        $val_dsp = floatval($value / (1.0 + $IVA));
		
		if ($key == "dfPesoPer")      $pesoPer = intval($value);
		if ($key == "dfEstaturaPer")  $estaturaPer = intval($value);
		if ($key == "dfFlgTer")       $flgTer = intval($value);
		if ($key == "dfTipSvrCrr")    $TipSvrCrr = intval($value);
	}

	$accion = "";
	if (isset($_GET['act'])) $accion = ok($_GET['act']);
	
	if ($accion == "DELCOT") {
            $result = mssql_query ("vm_del_cot $cod_cot", $db)  or die ("No se pudo leer datos de la persona");
            header("Location: escritorio_cot.php");
            exit(0);
	}
	
	if ($accion == "C") {
            // Guardamos posibles cambios de precios
            for ($i = 0; $i < $tot_prd; $i++)
                if (isset($_POST['dfDcto'.$i])) {
                    //echo "<BR>Producto: ".$key." --> ".$value."<BR>";
                    $cod_prd  = ok($_POST['dfCod'.$i]);
                    $val_ctd  = ok($_POST['dfCtd'.$i]);
                    $cod_sec  = ok($_POST['dfCodSec'.$i]);
                    $val_desprd = ($_POST['dfDcto'.$i] == "" ? 0 : ok($_POST['dfDcto'.$i]));
                    $prc_uni  = str_replace(".", '', ok($_POST['dfPrc'.$i] == "" ? "0" : $_POST['dfPrc'.$i]));
                    $prc_nto = str_replace(".", '', ok($_POST['dfNeto'.$i] == "" ? "0" : $_POST['dfNeto'.$i]));
                    $flg_sininv = 0;
                    if (isset($_POST['sinstock'.$i])) $flg_sininv = 1;

                    //$query = "vm_iu_Res_CotPrd $cod_cot,'$cod_prd',$val_ctd,$val_desprd,$prc_uni,$prc_nto,$cod_sec,$flg_sininv";
                    //echo $query;
                    $result = mssql_query("vm_iu_Res_CotPrd $cod_cot,'$cod_prd',$val_ctd,$val_desprd,$prc_uni,$prc_nto,$cod_sec,$flg_sininv", $db);
                }

            $afecha  = split('/', $fecha);
            $fec_cie = $afecha[2].$afecha[1].$afecha[0];
            //$query = "vm_iu_rescot $cod_cot, $cod_per, $cod_clt, $cod_iva, $val_usd, $cod_cri, '$fec_cie', $val_pro, $val_des, $cod_pre, $is_dsp, $cod_crr, $cod_svccrr, $cod_sucdsp, $cod_cmndsp, $cod_cdddsp, '$dir_dsp', $val_dsp";
            //echo $query."<BR>";
            $result = mssql_query("vm_iu_rescot $cod_cot, $cod_per, $cod_clt, $cod_iva, $val_usd, $cod_cri, '$fec_cie', $val_pro, $val_des, $cod_pre, $is_dsp, $cod_crr, $cod_svccrr, $cod_sucdsp, $cod_cmndsp, $cod_cdddsp, '$dir_dsp', $val_dsp", $db);

            $result = mssql_query("vm_s_cothdr $cod_cot",$db);
            if (($row = mssql_fetch_array($result))) {
                $num_cot = $row['Num_Cot'];
                $Mail_Ctt = $row['Mail_Ctt'];
            }

            //$query = "vm_per_s $cod_per";
            //echo $query."<BR>";
            $result = mssql_query("vm_per_s $cod_per", $db);
            if (($row = mssql_fetch_array($result))) {
                $nombre = trim($row["Nom_Per"]." ".$row["Pat_Per"]." ".$row["Mat_Per"]);
                $NumDoc = $row["Num_Doc"];
                $doc_id = 1;
                //$query = "vm_s_usrweb $doc_id, '$NumDoc'";
                //echo $query."<BR>";
                $result = mssql_query ("vm_s_usrweb $doc_id, '$NumDoc'", $db) or die ("No se pudo leer datos del usuario");
                if (($row = mssql_fetch_array($result))) {
                    $claveenc = $row["Pwd_Web"];
                    //echo $claveenc."<BR";
                    $clave = desencriptar($claveenc);
                    //echo $clave."<BR";
                }
            }
            //$query = "vm_c_rescot $cod_cot";
            //echo $query."<BR>";
            $result = mssql_query("vm_c_rescot $cod_cot", $db);

            $parametros = encriptar($NumDoc.";".$clave.";".$cod_cot.";mail",50);
            $pagina = $home."/validalogin.php?loginmail=".$parametros;

            $parametros = encriptar($NumDoc.";".$clave.";".$cod_cot.";micuenta",50);
            $pagina2 = $home."/validalogin.php?loginmail=".$parametros;

            include("mensaje.php");

            //$cuerpo_mail  = "Este mensaje se ha enviado desde una direcci�n de correo electr�nico exclusivamente de notificaci�n que no admite ";
            //$cuerpo_mail .= "respuestas. No responda a este mensaje<BR><BR>";
            //$cuerpo_mail .= "Estimado $nombre <BR><BR>";
            //$cuerpo_mail .= "La cotizaci�n # $num_cot, ya se encuentra disponible en nuestro sitio web. Esta cotizaci�n tiene una validez de 15 d�as ";
            //$cuerpo_mail .= "y puede acceder a ella mediante el siguiente link <a href=\"".$pagina."\">www.vestmed.cl</a> y ";
            //$cuerpo_mail .= "acceder al men� Cotizaciones de su cuenta de usuario utilizando su RUT y Contrase�a.";
            //$cuerpo_mail .= "En caso que usted desee adquirir alguno de los productos cotizados, podr� enviar su orden desde el mismo sitio.<BR><BR>";

            //$cuerpo_mail .= "Si experimenta alg�n problema, requiere ayuda o tiene alguna duda, por favor visite:<BR>";
            //$cuerpo_mail .= "<a href=\"http://www.vestmed.cl/faq.htm\">http://www.vestmed.cl/faq.htm</a><BR>";
            //$cuerpo_mail .= "<a href=\"http://www.vestmed.cl/como-cotizar.htm\">http://www.vestmed.cl/como-cotizar.htm</a><BR>";
            //$cuerpo_mail .= "Tel�fonos: (562) 242 1042 (562) 241 9839<BR><BR>";
            //$cuerpo_mail .= "Te esperamos !<BR><BR>";
            //$cuerpo_mail .= "Vestmed.cl";

            $asunto       = "Respuesta a cotización $num_cot";

            //$result = mssql_query ("vm_s_mailusr $cod_per, $cod_clt", $db)  or die ("No se pudo leer datos de la persona");
            //if (($row = mssql_fetch_array($result))) {
            //	$correo = $row["Mail_Ctt"];
            enviar_mail ($Mail_Ctt, $asunto, $cuerpo_mail, "HTML");
            mssql_close ($db);
            //}

            header("Location: escritorio_cot.php");
            exit(0);
	}
	if ($accion == "reenviar") {
	    $cod_cot = ok($_GET['cot']);
            $Mail_Ctt = ok($_POST['mail']);
            $result = mssql_query("vm_s_cothdr $cod_cot",$db);
            if (($row = mssql_fetch_array($result))) {
                $cod_per = $row['Cod_Per'];
                $num_cot = $row['Num_Cot'];
            }
            //$query = "vm_per_s $cod_per";
            //echo $query."<BR>";
            $result = mssql_query("vm_per_s $cod_per", $db);
            if (($row = mssql_fetch_array($result))) {
                $nombre = trim($row["Nom_Per"]." ".$row["Pat_Per"]." ".$row["Mat_Per"]);
                $NumDoc = $row["Num_Doc"];
                $doc_id = 1;
                //$query = "vm_s_usrweb $doc_id, '$NumDoc'";
                //echo $query."<BR>";
                $result = mssql_query ("vm_s_usrweb $doc_id, '$NumDoc'", $db) or die ("No se pudo leer datos del usuario");
                if (($row = mssql_fetch_array($result))) {
                    $claveenc = $row["Pwd_Web"];
                    //echo $claveenc."<BR";
                    $clave = desencriptar($claveenc);
                    //echo $clave."<BR";
                }
            }
            $parametros = encriptar($NumDoc.";".$clave.";".$cod_cot.";mail",50);
            $pagina = $home."/validalogin.php?loginmail=".$parametros;

            $parametros = encriptar($NumDoc.";".$clave.";".$cod_cot.";micuenta",50);
            $pagina2 = $home."/validalogin.php?loginmail=".$parametros;
            
            include("mensaje.php");            
            
            $asunto       = "Respuesta a cotización $num_cot";

            enviar_mail ($Mail_Ctt, $asunto, $cuerpo_mail, "HTML");
            $result = mssql_query("vm_upd_mailctt $cod_cot, '$Mail_Ctt'",$db);
            mssql_close ($db);

            header("Location: reenvia_mail.php?accion=close&cot=".$cod_cot);
            exit(0);
        }
	if ($accion == "D") {
		foreach ($_POST['seleccionadof'] as $key => $value) {
			//echo $key." --> ".$value."<BR>";
			$aSecPrd = split("-", $value);
			$cod_sec = $aSecPrd[0];
			$cod_prd = $aSecPrd[1];
			$result = mssql_query("vm_d_rescot $cod_cot, $cod_sec, '$cod_prd'", $db);
		}
		header("Location: nueva_cot.php?cot=$cod_cot"); 
		exit(0);
	}
	
	if ($cod_cot == 0) {
		$result = mssql_query("vm_i_cot_cltweb $cod_per, $cod_clt", $db);
		if (($row = mssql_fetch_array($result))) {
			$cod_cot = $row['cod_cot'];
			
			$result = mssql_query("vm_suc_s $cod_clt, $cod_suc", $db);
			if (($row = mssql_fetch_array($result))) {
				$cod_cmn = $row['Cod_Cmn'];
				$cod_cdd = $row['Cod_Cdd'];
			}
			
			$result = mssql_query("vm_i_cotsvcweb $cod_cot,0,0,0,NULL,NULL,-1,0,NULL,NULL,1,1,'_NONE',0,$cod_suc,$TipSvrCrr,$pesoPer,$estaturaPer,$flgTer", $db);
		}
		mssql_free_result($result); 
	}
	else
		$result = mssql_query("vm_u_cotsvcweb $cod_cot,$is_dsp,$cod_crr,$cod_svccrr,$cod_sucdsp,$cod_cmndsp,$cod_cdddsp,'$dir_dsp',$$cod_suc,$TipSvrCrr,$pesoPer,$estaturaPer,$flgTer", $db);
		
	
	$afecha  = split('/', $fecha);
	$fec_cie = $afecha[2].$afecha[1].$afecha[0];
	//$query = "vm_iu_rescot $cod_cot, $cod_per, $cod_clt, $cod_iva, $val_usd, $cod_cri, '$fec_cie', $val_pro, $val_des, $cod_pre, $is_dsp, $cod_crr, $cod_svccrr, $cod_sucdsp, $cod_cmndsp, $cod_cdddsp, '$dir_dsp', $val_dsp";
	//echo $query;
	$result = mssql_query("vm_iu_rescot $cod_cot, $cod_per, $cod_clt, $cod_iva, $val_usd, $cod_cri, '$fec_cie', $val_pro, $val_des, $cod_pre, $is_dsp, $cod_crr, $cod_svccrr, $cod_sucdsp, $cod_cmndsp, $cod_cdddsp, '$dir_dsp', $val_dsp", $db);

	for ($i = 0; $i < $tot_prd; $i++)
		if (isset($_POST['dfDcto'.$i])) {
			//echo "<BR>Producto: ".$key." --> ".$value."<BR>";
			$cod_prd  = ok($_POST['dfCod'.$i]);
			$val_ctd  = ok($_POST['dfCtd'.$i]);
			$cod_sec  = ok($_POST['dfCodSec'.$i]);
			$val_des = ($_POST['dfDcto'.$i] == "" ? 0 : ok($_POST['dfDcto'.$i]));
                        $prc_uni  = str_replace(".", '', ok($_POST['dfPrc'.$i] == "" ? "0" : $_POST['dfPrc'.$i]));
                        $prc_nto = str_replace(".", '', ok($_POST['dfNeto'.$i] == "" ? "0" : $_POST['dfNeto'.$i]));
			$flg_sininv = 0;
			if (isset($_POST['sinstock'.$i])) $flg_sininv = 1;
			
			//$query = "vm_iu_Res_CotPrd $cod_cot,'$cod_prd',$val_ctd,$val_des,$prc_uni,$prc_nto,$cod_sec,$flg_sininv";
			//echo $query;
			$result = mssql_query("vm_iu_Res_CotPrd $cod_cot,'$cod_prd',$val_ctd,$val_des,$prc_uni,$prc_nto,$cod_sec,$flg_sininv", $db);
		}
	mssql_close ($db);
	header("Location: nueva_cot.php?cot=$cod_cot"); 	
?>
