<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("global_cot.php");

	$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
	$Paso = (isset($_GET['paso'])) ? ok($_GET['paso']) : 0;
	
	if ($Paso == 2 or $Paso == 23) {
        $archivo = $_FILES['documento']['name'];
        if ($archivo != "") {
            for ($i = strlen($archivo)-1; $i > 0; $i--)
                if (substr($archivo, $i, 1) == ".") break;

            $ext = substr($archivo, $i, strlen($archivo));
            $archivo_adj = "archivo".$ext;
            $result = mssql_query("vm_getfolio 'ADJ'");

		if (($row = mssql_fetch_array($result)))
                    $archivo_adj = "adjunto".sprintf("%06d", $row['Tbl_fol']).$ext;
                $fileupload = '../'.$pathadjuntos.$archivo_adj;
                //$fileupload = $pathadjuntos.$archivo;
                if (!move_uploaded_file($_FILES['documento']['tmp_name'], $fileupload)){
                   echo $_FILES['documento']['tmp_name']."<BR>".$fileupload."<BR>";
                   echo "Ocurri&oacute; alg&uacute;n error al subir el fichero. No pudo guardarse.";
                   exit(0);
                }
            }
	}
	
	if ($Cod_Cot > 0) {
		$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
		if (($row = mssql_fetch_array($result))) {
                        $Num_Cot = $row['Num_Cot'];
			$Cod_Clt = $row['Cod_Clt'];
			$Cod_Per = $row['Cod_Per'];
			$Cod_Suc = $row['Cod_Suc'];
			$is_dsp  = $row['is_dsp'];
			$Arc_Adj = $row['arc_Adj'];
			$Num_TrnBco = $row['Num_TrnBco'];
	                $Cod_Nvt = $row['Cod_Odc'];
			$mail_ctt  = $row['Mail_Ctt'];
                        $TipDocSII = $row['Tip_DocSII'];
                        $Cod_PerFct = $row['Cod_PerFct'];
			
			switch ($Paso) {
			case 1:
				$CostoDsp = ($is_dsp == 0 ? 0 : intval(str_replace (".", "", ok($_POST["dfDespacho"]))));

				// Vemos si es usuario
                                $result = mssql_query("vm_codusr_s $Cod_Clt, $Cod_Per");
                                if (!($row = mssql_fetch_array($result))) {
                                    // Insertamos Usuario
                                    $result = mssql_query("vm_agr_usr $Cod_Clt, $Cod_Per");
                                    if (($row = mssql_fetch_array($result))) $Cod_Usr = $row['Cod_Usr'];
                                }

				$result = mssql_query("vm_i_ocweb $Cod_Clt, $Cod_Per, $Cod_Suc, $Cod_Cot, $CostoDsp, ''",$db);
				if (($row = mssql_fetch_array($result))) {
					$Cod_Nvt = $row['Cod_Nvt'];
					$Cod_Usr = $row['Cod_Usr'];
					$Cod_Trn = $row['Cod_Trn'];
					foreach ($_POST as $key => $value) {
						//echo $key." --> ".$value."<BR>";
						if ($key == "seleccionPrd") {
							foreach ($value as $key2 => $Cod_Prd) {
								$Val_Ctd = $_POST["dfCtd$Cod_Prd"];
								$Mto_Prd = $_POST["Neto$Cod_Prd"];
								$Cod_Sec = $_POST["CodSec$Cod_Prd"];
								//echo $key2." --> ".$Cod_Prd."=";
								//echo $_POST["dfCtd$Cod_Prd"]."/";
								//echo $_POST["Neto$Cod_Prd"]."<BR>";
								$result = mssql_query("vm_i_ocweb_det $Cod_Trn, $Cod_Nvt, $Cod_Usr, $Cod_Cot, '$Cod_Prd', $Cod_Sec, $Val_Ctd, $Mto_Prd",$db)
                                                                                    or die ("No pudo insertar detalle compra<br>"."vm_i_ocweb_det $Cod_Trn, $Cod_Nvt, $Cod_Usr, $Cod_Cot, $Cod_Prd, $Cod_Sec, $Val_Ctd, $Mto_Prd");
							}
						}
					}
				}
				header("Location: ../pagar.php?cot=".$Cod_Cot); 	
				break;
				
			case 2:
				$TipDocSII = intval(ok($_POST["TipDocSII"]));
				$Cod_PerFct = intval(ok($_POST['Cod_PerFct']));
				$Nom_Bco = ok($_POST["Bco"]);
				$NumTrnBco = ok($_POST["NumTrnbco"]);
				$valorNvt = ($_GET['montoNvt'] == "") ? "0" : ok($_GET['montoNvt']);
                                //$valorCmp = ($_POST['montoCmp'] == "") ? "0" : ok($_POST['montoCmp']);
                                $valorCmp = $valorNvt;

                                //echo $valorNvt;
				//echo $valorCmp;
				//echo $TipDocSII.",".$Nom_Bco.",".$NumTrnBco.",".$archivo;
				$result = mssql_query("vm_u_ocweb $Cod_Cot, $TipDocSII, '$Nom_Bco', '$archivo', '$NumTrnBco', $Cod_PerFct, '$archivo_adj',$valorNvt,$valorCmp", $db)
                                                      or die ("No pudo actualizar datos compra<br>"."vm_u_ocweb $Cod_Cot, $TipDocSII, '$Nom_Bco', '$archivo', '$NumTrnBco', $Cod_PerFct, '$archivo_adj',$valorNvt,$valorCmp");
				if (($row = mssql_fetch_array($result))) {
					$Cod_Trn = $row['Cod_Trn'];	
					$result = mssql_query("vm_close_trn $Cod_Clt, $Cod_Trn",$db)
                                                or die ("No pudo cerrar compra<br>"."vm_close_trn $Cod_Clt, $Cod_Trn");
					
					$cuerpo_mail  = cuerpo_envioODC(51,$Cod_Cot,$home,$pathadjuntos,$archivo,$archivo_adj,$db);
					$asunto       = "Orden de Compra para la cotizacion ".$Num_Cot; 
					$correos = split(";", $correovestmed);
					foreach ($correos as $key => $destinatario) {
                                            enviar_mail ($destinatario, $asunto, $cuerpo_mail, "HTML");
					}
					
					$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
					if (($row = mssql_fetch_array($result))) {
                                            $Cod_Clt = $row['Cod_Clt'];
                                            $result = mssql_query("vm_cli_s $Cod_Clt",$db);
                                            if (($row = mssql_fetch_array($result))) {
                                                    if ($row['Cod_TipPer'] == 1)
                                                            $nombre = $row['Nom_Per']." ".$row['Pat_Per']." ".$row['Mat_Per'];
                                                    else
                                                            $nombre = $row['RznSoc_Per'];
                                            }
					}
					
					$cuerpo_mailclt = "Este mensaje se ha enviado desde una direcci&oacute;n de correo electr&oacute;nico exclusivamente de notificaci&oacute;n que no admite respuestas.<BR><BR>";
					$cuerpo_mailclt .= "<BR>Estimado ".$nombre."<BR><BR><BR>";
					if ($archivo != "") {
						$cuerpo_mailclt .= "Hemos recibido satisfactoriamente tu orden de compra, la cual ser&aacute; procesada una vez confirmado el pago.";
						$cuerpo_mailclt .= "Usted podr&aacute; revisar en todo momento estado de su pedido a trav&eacute;s de la opci&oacute;n Ordenes del men&uacute; de su cuenta";
					} else {
						$cuerpo_mailclt .= "Hemos recibido satisfactoriamente tu orden de compra, sin embargo hemos detectado que no se ha adjuntado toda la ";
						$cuerpo_mailclt .= "informaci&oacute;n relacionada con el pago de esta.";
						$cuerpo_mailclt .= "La Orden Nro ".$Cod_Nvt." en este momento se encuentra en estado pendiente.";
						//$cuerpo_mailclt .= "Usted podr&aacute; completar la informaci&oacute;n pendiente a trav&eacute;s de la opci&oacute;n Ordenes del men&uacute; de su cuenta.";
					}
					$cuerpo_mailclt .= "<BR><BR>";
					$cuerpo_mailclt .= $cuerpo_mail;
					$cuerpo_mailclt .= "<BR><BR>";
					$cuerpo_mailclt .= "Si experimenta alg&uacute;n problema, requiere ayuda o tiene alguna duda, por favor visite:<BR>";
					$cuerpo_mailclt .= "<a href=\"http://www.vestmed.cl/faq.htm\">http://www.vestmed.cl/faq.htm<a><BR>";
					$cuerpo_mailclt .= "<a href=\"http://www.vestmed.cl/como-cotizar.htm\">http://www.vestmed.cl/como-cotizar.htm<a><BR>";
					$cuerpo_mailclt .= "Tel&eacute;fonos: (562) 242 1042 (562) 241 9839";

					$asunto = "Aviso de recepcion Orden de Compra";
					enviar_mail ($mail_ctt, $asunto, $cuerpo_mailclt, "HTML");
					//echo "Mail enviado a: $mail_ctt<BR>";
                                        header("Location: ../aviso.php?id=".$Cod_Cot."&idmsg=44");
				}
                                else {
					echo "MSG_ERR:: " . mssql_get_last_message() . "<BR>";
					//echo "vm_u_ocweb $Cod_Cot, $TipDocSII, '$Nom_Bco', '$archivo', '$NumTrnBco', $Cod_PerFct"; Llamada antigua.
					echo "vm_u_ocweb $Cod_Cot, $TipDocSII, '$Nom_Bco', '$archivo', '$NumTrnBco', $Cod_PerFct, '$archivo_adj',$valorNvt,$valorCmp";
				}
                                    
                                
				mssql_close ($db);
				
				break;
				
			case 22:
				
				$fono = ok($_POST['fono']);
				$email = ok($_POST['email']);
				
				$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
				if (((($row = mssql_fetch_array($result))))) {
                                    $Cod_Clt = $row['Cod_Clt'];
                                    $result = mssql_query("vm_cli_s $Cod_Clt",$db);
                                    if (($row = mssql_fetch_array($result))) {
                                        if ($row['Cod_TipPer'] == 1)
                                            $nombre = $row['Nom_Per']." ".$row['Pat_Per']." ".$row['Mat_Per'];
                                        else
                                            $nombre = $row['RznSoc_Per'];
                                        $Fon_Ctt = $row['Fon_Ctt'];
                                        $mail_ctt = $row['Mail_Ctt'];
                                    }
				}
				
				$cuerpo_mail  = cuerpo_envioSinODC(51,$Cod_Cot,$fono,$email,$db);
				
				$asunto       = "Solicitud de compra para la Cotización # ".$Num_Cot; 
				$correos = split(";", $correovestmed);
				foreach ($correos as $key => $destinatario)
					enviar_mail ($destinatario, $asunto, $cuerpo_mail, "HTML");
				
				$cuerpo_mailclt = "Este mensaje se ha enviado desde una direcci&oacute;n de correo electr&oacute;nico exclusivamente de notificaci&oacute;n que no admite respuestas.<BR><BR>";
				$cuerpo_mailclt .= "<BR>Estimado ".$nombre."<BR><BR><BR>";
				$cuerpo_mailclt .= "Hemos recibido satisfactoriamente tu intenci&oacute;n de compra, y un vendedor se comunicar&aacute; contigo a la brevedad";
				$cuerpo_mailclt .= "<BR><BR>";
				//$cuerpo_mailclt .= $cuerpo_mail;
				//$cuerpo_mailclt .= "<BR><BR>";
				$cuerpo_mailclt .= "Si experimenta alg&uacute;n problema, requiere ayuda o tiene alguna duda, por favor visite:<BR>";
				$cuerpo_mailclt .= "<a href=\"http://www.vestmed.cl/faq.htm\">http://www.vestmed.cl/faq.htm<a><BR>";
				$cuerpo_mailclt .= "<a href=\"http://www.vestmed.cl/como-cotizar.htm\">http://www.vestmed.cl/como-cotizar.htm<a><BR>";
				$cuerpo_mailclt .= "Tel&eacute;fonos: (562) 242 1042 (562) 241 9839";

				$asunto = "Aviso de intención de compra para la Cotización # ".$Num_Cot;
				enviar_mail ($mail_ctt, $asunto, $cuerpo_mailclt, "HTML");
				
				header("Location: ../catalogo.php"); 	
				break;

                        case 23:
				$Nom_Bco = ok($_POST["Bco"]);
                        	$valorCmp = ($_POST['montoCmp'] == "") ? "0" : ok($_POST['montoCmp']);
				$valorNvt = ($_GET['montoNvt'] == "") ? "0" : ok($_GET['montoNvt']);
                                //$query = "vm_u_ocweb $Cod_Cot, $TipDocSII, '$Nom_Bco', '$archivo', '$NumTrnBco', $Cod_PerFct, '$archivo_adj', $valorNvt, $valorCmp";
                                //echo $query;
				$result = mssql_query("vm_u_ocweb $Cod_Cot, $TipDocSII, '$Nom_Bco', '$archivo', '$NumTrnBco', $Cod_PerFct, '$archivo_adj', $valorNvt, $valorCmp", $db);
				if (($row = mssql_fetch_array($result))) {
                                    header("Location: ../IngPago.php?cot=".$Cod_Cot."&status=ok");
                                }
			}
			
		}
	}
	//header("Location: aviso.php?id=".$Cod_Per."&idmsg=4"); 	
?>
