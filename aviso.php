<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$idmsg = intval($HTTP_GET_VARS["idmsg"]);
$Cod_Per = 0;
$Cod_Clt = 0;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />

<LINK href="Include/estilos.css" type="text/css" rel="stylesheet"/> 
<script language="JavaScript" src="Include/SoloNumeros.js"></script>
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="Include/validarRut.js"></script>
<script language="JavaScript" src="Include/fngenerales.js"></script>

<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=660,height=480')
}
</script>
</head>
<body>
<div id="body">
	<div id="header"></div>
    <div class="menu" id="menu-noselect">
    	<a id="home" href="index.htm">home</a>
    	<a id="empresa" href="empresa.htm">empresa</a>
        <a id="marcas" href="marcas.htm">marcas</a>
        <a id="telas" href="telas.htm">telas</a>
        <a id="bordados" href="bordados.htm">bordados</a>
        <a id="despachos" href="despachos.htm">despachos</a>
        <a id="clientes" href="clientes.htm">clientes</a>
        <div id="servicio-cliente-selected" style="z-index:1000;padding-top:0px;" class="selected">
        <ul id="dropdown-scliente" class="dropdown">
                <li>
                    <a class="normal" href="servicio-cliente.htm">servicio al cliente</a>
                    <ul>
                       <li>
                            <a href="faq.htm">Faq</a>
                        </li>
                        <li>
                            <a href="como-tomar-medidas.htm">C&oacute;mo Tomar Medidas</a>
                        </li>
                        <li>
                            <a href="despachos.htm">Despachos</a>
                        </li>
                        <li>
                            <a href="clean-care.htm">Clean & Care</a>
                        </li>
                        <li>
                            <a href="tracking-ordenes.htm">Tracking de &Oacute;rdenes</a>
                        </li>
                        <li>
                            <a href="como-cotizar.htm">C&oacute;mo Cotizar</a>
                        </li>
                       
                        <li>
                            <a href="politicas-privacidad.htm">Pol&iacute;ticas de Privacidad</a>
                        </li>
                    </ul>
                </li>
            </ul>		
        </div>
        <a id="catalogo" href="catalogo.php">catalogo</a>
        <a id="contacto" href="contacto.htm">contacto</a>
  
  	</div>
	<?php if ($idmsg > 1) { ?>
	<?php 
		if ($Cod_Per == 0) { 
	?>
        <ul id="usuario_registro">
                    <?php echo solicitar_login(); ?>
        </ul>
	<?php }
		  else {
	?>
        <ul id="usuario_registro">
                    <?php 	echo display_login($Cod_Per, $Cod_Clt, $db); ?>
        </ul>
	<?php 
		}
	?>
 
	<?php } ?>
        <div id="work">
          <?php

            switch ($idmsg) {
            case 1:
          ?>
                <div id="back-avisos">
                    <img src="images/registro/bienvenida.png" class="titulo-principal-avisos" />
                    <img src="images/registro/imagen2.png" class="imagen-avisos" />
                    <div class="titulo-avisos2">BIENVENIDA</div>
                    <div class="texto-bienvenida">
                              Los datos entregados en esta solicitud ser&aacute;n guardados
                                     bajo absoluta reserva. Su incorporaci&oacute;n como usuario
                                     ya es efectiva y pueda comenzar a operar desde este momento.
                                     En ese mismo acto usted acepta las
                            <u><a href="javascript:popwindow('politicas-privacidad.htm');">Pol&iacute;ticas de Privacidad</u></a>
                    </div>
                    <form ID="F2" class="form_bien" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="catalogo.php?idu=<?php echo $HTTP_GET_VARS["idu"]; ?>&idc=<?php echo $HTTP_GET_VARS["idc"]; ?>">
                            <input type="submit" id="btn_submit_bien" name="Enviar" value="">
                            <input type="button" name="Cerrar" id="btn_cerrar_bien" value="" onclick="volverMain('catalogo.php')">
                    </form>
                            <div class="texto-bienvenida-mini">Nota: Los datos entregados en esta solicitud ser&aacute;n guardados bajo absoluta reserva. Su incorporaci&oacute;n como usuario ya es efectiva y pueda comenzar a operar desde este momento. En ese mismo acto usted acepta las Pol&iacute;ticas de Privacidad. </div>

                    </div>
          <?php
                    break;

                case 2:
          ?>
                    <div style="height:410px;">
                            <div class="back-msg-aviso">
                                    <div class="titulo-msg-aviso">LOGIN INCORRECTO</div>
                                    <div class="wrap-btn-reg-aviso">
                                            <form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="registrarse.php">
                                            <input type="submit" value="" id="btn-reg-aviso" />
                                            </form>
                                    </div>
                                    <div class="texto-pq-aviso">
                                            Usuario no se encuentra registrado en nuestro sitio.
                                            <br />
                                            Es necesario que se registre para poder cotizar.
                                    </div>
                            </div>
                    </div>
			  
          <?php
                    break;

                case 3:
          ?>
                    <div style="height:410px;">
                            <div class="back-msg-aviso">
                                    <div class="titulo-msg-aviso">CLAVE INCORRECTA</div>
                                    <div class="wrap-btn-reg-aviso">
                                      <form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="registrarse.php">
                                      <input type="submit" name="Enviar" value="Registrarse" class="btn">
                                      </form>
                                    </div>
                                    <div class="texto-pq-aviso">
                                            Clave Incorrecta. Verifique que no se encuentre la tecla <strong>Bloq May&uacute;s</strong> activada .<br />
                                    En caso de que olvid&oacute; su clave pinche <a href=#>aqu&iacute;</a> para que se la enviemos.
                                    </div>
                            </div>
                    </div>
                
          <?php
                        break;

                case 4:
                        if (isset($_SESSION['CodCot'])) unset($_SESSION['CodCot']);
          ?>
              <div style="height:410px;">
              	<div class="back-msg-aviso">
                    <div class="titulo-msg-aviso">COTIZACION</div>
                    <div class="wrap-btn-reg-aviso">
                      <form ID="F2" method="POST" name="F2" ACTION="catalogo.php">
                       <input type="submit" name="Enviar" value=" Aceptar " class="btn" />
                      </form>
                    </div>
                    <div class="texto-pq-aviso">
                    	Gracias por visitar <strong>www.vestmed.cl</strong><BR>Su cotizaci&oacute;n ser&aacute; enviada a su correo electr&oacute;nico<br>
				Tiempo promedio de proceso 1 d&iacute;a h&aacute;bil
                    </div>
                </div>
              </div>
				
			  
			  <?php
					break;
				
				case 5:
			  ?>
              	 <div style="height:410px;">
              	<div class="back-msg-aviso">
                	<div class="titulo-msg-aviso">LOGIN INCORRECTO</div>
                    <div class="wrap-btn-reg-aviso">
                   <form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="index2.htm">
					  <input type="submit" name="Enviar" value="Registrarse" class="btn">
					</form>
                    </div>
                    <div class="texto-pq-aviso">
                    	Usuario no se encuentra registrado en nuestro sitio. Es necesario que se registre
				para poder ingresar.
                    </div>
                </div>
                </div>
				<?php
					break;
					
				case 6:
					$Cod_Cot = intval(ok($_GET['cot']));
					$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
					if ($row = mssql_fetch_array($result)) {
						$Cod_Suc = $row['Cod_Suc'];
						$Cod_Clt = $row['Cod_Clt'];
					}
					$result = mssql_query("vm_per_s $Cod_Per", $db);
					if ($row = mssql_fetch_array($result)) $nombre = trim($row['Nom_Per']." ".$row['Pat_Per']." ".$row['Mat_Per']);
					$result = mssql_query("vm_usrweb_ctt_s $Cod_Per, $Cod_Clt", $db);
					while ($row = mssql_fetch_array($result)) 
						if ($row['Cod_Suc'] == $Cod_Suc) {
							$Fon_Ctt = $row['Fon_Ctt'];
							$Mail_Ctt = $row['Mail_Ctt'];
							break;
						}
			    ?>
              	 <div style="height:510px;">
              	<div class="back-msg-aviso2">
                	<div class="titulo-msg-aviso">SOLICITUD DE COMPRA</div>
                    <div class="texto-pq-aviso">
					   <form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" onsubmit="return validarDataCompra(this)" ACTION="cotizador/enviaroc.php?cot=<?php echo $Cod_Cot; ?>&paso=22">
                    	<BR>Estimado/a <?php echo $nombre; ?><BR>momentaneamente no se encuentra disponible el 
						sistema de compras<BR> autom&aacute;ticas, sin embargo su intenci&oacute;n de compra ha sido capturada<BR> 
						y un vendedor se comunicar&aacute; con usted a la brevedad.<BR><BR>Por favor confirme el n&uacute;mero telef&oacute;nico de contacto y su email <BR><BR>
<table align="center" border="0" width="70%">
<tr><td width="100%">Fono:</td>
<td><input name="fono" id="fono" type="text" class="text-contacto" value="<?php echo $Fon_Ctt; ?>" /></td></tr>
<tr><td width="100%">e-mail:</td>
<td><input name="email" id="email" type="text" class="text-contacto" value="<?php echo $Mail_Ctt; ?>" /></td></tr>
</table><br>
						Disculpe las molestias. Vestmed Ltda
					    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="Enviar" value="Enviar" class="btn">
						</form>
                    </div>
                </div>
                </div>
				<?php
					break;
					
				case 20:
			    ?>
                <div id="back-avisos">
                    <img src="images/registro/recuperar.png" class="titulo-principal-avisos" />
                    <div class="titulo-avisos">Para obtener su clave debe indicar lo siguiente:</div>
                    <ul id="olvido-pass">
                        <form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="enviarclave.php" onsubmit="return checkRut(this)">
                            <table align="center" border="0" width="750">
                                <tr>
                                    <td align="left" width="50%" valign="top">
                                            1&nbsp;&nbsp;&nbsp;&nbsp;<strong>Tipo de Cliente</strong>
                                    </td>
                                    <td align="left" width="50%" valign="top">
                                            2&nbsp;&nbsp;&nbsp;&nbsp;<strong>Rut</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left" valign="top">
                                            <div style="float:left; width:145px; padding-left:35px">Institucional&nbsp;</div><input style="float:left;" type="radio" value="1" name="rbTipoClt" onclick="displayLabel(this)" />
                                    </td>
                                    <td align="left" valign="top">
                                            <div id="parte1">
                                            <span style="float:left; margin-right:10px; width:105px; padding-left:35px">Usuario</span>
                                            <INPUT name="dfRutUsrIn" id="dfRutUsrIn" size="12" maxLength="12" onblur="formatearRut('dfRutUsrIn','dfRutUsr')" onKeyPress="javascript:return soloRUT(event)" class="txt-input" />
                                            </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left" valign="top">
                                            <div style="float:left; width:145px; padding-left:35px">Usuario&nbsp;</div><input style="float:left;" type="radio" value="2" name="rbTipoClt" onclick="displayLabel(this)" />
                                    </td>
                                    <td align="left" valign="top">
                                            <div id="parte2">
                                            <span style="float:left; margin-right:10px; width:105px; padding-left:35px">Instituci&oacute;n</span><INPUT name="dfRutCltUsrIn" id="dfRutCltUsrIn" size="12" maxLength="12" onblur="formatearRut('dfRutCltUsrIn','dfRutClt')" onKeyPress="javascript:return soloRUT(event)" class="txt-input" />
                                            </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 100px" colspan="2">
                                            <input type="hidden" name="dfRutClt" id="dfRutClt">
                                            <input type="hidden" name="dfRutUsr" id="dfRutUsr">
                                            <input type="submit" id="av-btn-enviar" value="" />
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </ul>
                </div>
                <script language="javascript">
                        usuario = document.getElementById("parte1");
                        institucion = document.getElementById("parte2");

                        institucion.style.visibility = "hidden";
                        usuario.style.visibility = "hidden";

                        function displayLabel(obj) {
                                if (obj.value == "1") {
                                        institucion.style.visibility = "visible";
                                        usuario.style.visibility = "visible";
                                }
                                else {
                                        institucion.style.visibility = "hidden";
                                        usuario.style.visibility = "visible";
                                }
                        }
                </script>
				
			  <?php
					break;
					
				case 21:
					$cod_per = ok($_GET['idu']);
					$cod_clt = ok($_GET['idc']);
					$result = mssql_query ("vm_s_mailusr $cod_per, $cod_clt", $db)  or die ("No se pudo leer datos de la persona");
					if (($row = mssql_fetch_array($result))) $correo = $row["Mail_Ctt"];
					mssql_free_result($result); 
			  ?>
                                <div style="height:410px;">
					<div class="back-msg-aviso">
						<div class="titulo-msg-aviso">RECUPERAR CLAVE</div>
						<div class="wrap-btn-reg-aviso">
						<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="catalogo.php">
						  <input type="submit" name="Enviar" value="Aceptar" class="btn">
						</form>
						</div>
						<div class="texto-pq-aviso">
							Su clave ha sido enviada con exito a su correo <strong><?php echo $correo; ?></strong>.
						</div>
					</div>
                                </div>
				
			  <?php
					break;
					
				case 22:
			  ?>
                <div style="height:410px;">
              	<div class="back-msg-aviso">
                	<div class="titulo-msg-aviso">RECUPERAR CLAVE</div>
                    <div class="wrap-btn-reg-aviso">
                   <form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="catalogo.php">
					  <input type="submit" name="Enviar" value="Aceptar" class="btn">
					</form>
                    </div>
                    <div class="texto-pq-aviso">
                    	Se ha producido un error al tratar de enviar su clave al correo electr&ocute;nico. 
				Favor contactase con nuestro Call Center.
                    </div>
                </div>
                </div>
             	<?php
					break;
					
				case 23:
			  ?>
               <div style="height:410px;">
              	<div class="back-msg-aviso">
                	<div class="titulo-msg-aviso">RECUPERAR CLAVE</div>
                    <div class="wrap-btn-reg-aviso">
                   <form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="catalogo.php">
					  <input type="submit" name="Enviar" value="Aceptar" class="btn">
					</form>
                    </div>
                    <div class="texto-pq-aviso">
                    	El rut que ha ingresado no se encuentra registrado como usuario de Vestmed.
                    </div>
                </div>
                </div>
				
			  <?php
					break;
					
				case 30:
			  ?>
                <div style="height:410px;">
					<div class="back-msg-aviso">
						<div class="titulo-msg-aviso">INFORMACION DE CONTACTO</div>
						<div class="wrap-btn-reg-aviso">
						<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="catalogo.php">
						  <input type="submit" name="Enviar" value="Aceptar" class="btn">
						</form>
						</div>
						<div class="texto-pq-aviso">
							Gracias por visitar <strong>www.vestmed.cl</strong><BR>
							Nos contactaremos con Usted a la brevedad para resolver<BR>la consulta u observaci&oacute;n enviada
							a nuestro sitio.
						</div>
					</div>
                </div>
        
			  <?php
					break;
					
				case 31:
					foreach ($_POST as $key => $value) {
						//echo $key." --> ".$value."<BR>";
						if ($key == "cantidad")	$cantidad  = intval($value);
						if ($key == "dfDsg")	$cod_dsg   = $value;
						if ($key == "dfSze")	$val_sze   = $value;
						if ($key == "dfPat")	$cod_pat   = $value;
						if ($key == "dfPrd")	$cod_prd   = $value;
						if ($key == "dfTitle")	$cod_title = $value;
					}
	 				
			  ?>
                <div id="back-avisos" style="margin-left: 2px; margin-top: 5px;">
              <img src="images/registro/ingreso.png" class="titulo-principal-avisos" />
              <!--img src="images/registro/imagen1.png" class="imagen-avisos" style="right:40px;" /-->
                  <TABLE border="0" cellpadding="1" cellspacing="0" width="80%" align="center" style="PADDING-TOP: 50px" />
				<TR><TD class="bienvenida" style="TEXT-ALIGN: center">
					<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2">
					<TABLE border="0" cellpadding="1" cellspacing="0" width="100%" align="center">
					<TR>
						<TD class="labelBig" colspan="2" WIDTH="45%" style="PADDING-TOP: 20px; PADDING-BOTTOM: 20px">Por favor ingrese a su cuenta</TD>
						<TD class="labelBig" colspan="2" WIDTH="55%" STYLE="BORDER-LEFT: #023131 3px solid; PADDING-LEFT: 20px; PADDING-TOP: 20px; PADDING-BOTTOM: 20px">Nuevo Usuario ?</TD>
					</TR>
					<TR>
						<TD class="datoj" valign="top" style="PADDING-TOP: 10px"><strong>RUT : </strong></TD>
						<TD class="datoj" valign="top" style="PADDING-TOP: 10px">
							 <INPUT name="dfrutIn" id="dfrutIn" size="20" maxLength="10" class="txt-input" style="TEXT-TRANSFORM: uppercase" onblur="formatearRut('dfrutIn','dfrutusr')" />
						</TD>
						<TD class="datoj" valign="top" style="PADDING-TOP: 10px; BORDER-LEFT: #023131 3px solid; PADDING-LEFT: 20px;">
							<TABLE border="0" cellpadding="1" cellspacing="0" width="100%" align="center">
							<TR>
								<TD width="5%"><INPUT id="rbTipoClt" name="rbTipoClt" type="radio" class="button2" value="1"></TD>
							    <TD width="45%"><strong>Institucional</strong></TD>
							    <TD width="5%"><INPUT id="rbTipoClt" name="rbTipoClt" type="radio" class="button2" value="2"></TD>
								<TD width="45%"><strong>Persona Natural</strong></TD>
							</TR>
							</TABLE>
						</TD>
					</TR>
					<TR>
						<TD class="datoj" valign="top" style="PADDING-TOP: 15px"><strong>Password : </strong></TD>
						<TD class="datoj" valign="top" style="PADDING-TOP: 15px">
							 <INPUT name="dfclave" type="password" size="20" maxLength="10" class="txt-input" style="TEXT-TRANSFORM: uppercase">
						</TD>
						<TD class="datoj" valign="top" style="PADDING-TOP: 10px; BORDER-LEFT: #023131 3px solid; PADDING-LEFT: 25px;">
							<TABLE border="0" cellpadding="1" cellspacing="0" width="100%" align="center">
							<TR>
							    <TD width="15%" valign="top"><strong>RUT</strong></TD>
							    <TD width="85%" valign="top"><INPUT id="dfRutCltIn" name="dfRutCltIn" class="txt-input" style="TEXT-TRANSFORM: uppercase" onblur="formatearRut('dfRutCltIn','dfRutClt')">
							</TR>
							</TABLE>
						</TD>
					</TR>
					<TR>
						<TD class="datoj" valign="top" style="PADDING-TOP: 30px; TEXT-ALIGN: right; PADDING-RIGHT: 60px" colspan="2">
							 <INPUT name="Enviar1" type="BUTTON" class="btn" value="Ingresar" onclick="javascript:ValidarLogin3();">
							 <input type="hidden" name="dfrutusr" id="dfrutusr" />
						</TD>
						<TD class="datoj" valign="top" style="PADDING-TOP: 30px; BORDER-LEFT: #023131 3px solid; PADDING-LEFT: 25px; ; TEXT-ALIGN: right; PADDING-RIGHT: 40px" colspan="2">
							 <INPUT name="Enviar2" type="BUTTON" class="btn" value="Registrar" onclick="javascript:IrAInscripcion();">
							 <INPUT name="dfRutClt" id="dfRutClt" type="HIDDEN" />
						</TD>
					</TR>
					<TR>
						<TD class="datoj" valign="top" style="PADDING-TOP: 30px; TEXT-ALIGN: right; PADDING-RIGHT: 60px" colspan="2">&nbsp;
							 
						</TD>
						<TD class="datoj" valign="top" style="PADDING-TOP: 30px; BORDER-LEFT: #023131 3px solid; PADDING-LEFT: 25px; ; TEXT-ALIGN: right; PADDING-RIGHT: 40px" colspan="2">&nbsp;
							 
						</TD>
					</TR>
					</TABLE>
					<INPUT type="hidden" name="dfDsg" value="<?php echo $cod_dsg ?>">
					<INPUT type="hidden" name="dfSze" value="<?php echo $val_sze ?>">
					<INPUT type="hidden" name="dfPat" value="<?php echo $cod_pat ?>">
					<INPUT type="hidden" name="dfPrd" value="<?php echo $cod_prd; ?>">
					<INPUT type="hidden" name="dfTitle" value="<?php echo $cod_title; ?>">
					<INPUT type="hidden" name="cantidad" value="<?php echo $cantidad; ?>">
					</form>
				</TD></TR>
				</TABLE>      
                </div>
				
			<?php
					break;
					
				case 32:
			  ?>
            
                    
				<TABLE border="0" cellpadding="1" cellspacing="0" width="70%" align="center">
				<TR>
				<TD><H1>ORDEN DE COMPRA ENVIADA</H2>
				</TD>
				</TR>
				<TR>
				<TD class="bienvenida" style="TEXT-ALIGN: center">
				Gracias por comprar en <strong>www.vestmed.cl</strong><BR>. Su compra ser&aacute despachada
				al lugar que nos indic&oacute; una vez que validemos el pago.
				</TD>
				</TR>
				<TR><TD class="datoj" style="PADDING-TOP: 50px">
					<p class"datoc">
					<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="javascript:window.close()">
					  <input type="submit" name="Cerrar" value=" Cerrar " class="bnt">
					</form>
					</p></td></Tr>
				</TABLE>
				
			  <?php
					break;
					
				case 44:
					$Cod_Cot = intval($HTTP_GET_VARS["id"]);

					$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
					if ($row = mssql_fetch_array($result)) {
						$Arc_Adj    = $row['arc_Adj'];
						$Num_TrnBco = $row['Num_TrnBco'];
						$cod_clt    = $row['Cod_Clt'];
						$cod_per    = $row['Cod_Per'];
						$cod_odc    = $row['Cod_Odc'];
					}
			  ?>
              <div style="height:410px;">
              	<div class="back-msg-aviso">
                	<div class="titulo-msg-aviso">ORDEN DE COMPRA <?php echo $cod_odc; ?></div>
                    <div class="wrap-btn-reg-aviso">
                  <form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="tracking.php">
					  <input type="submit" name="Enviar" value=" Aceptar " class="btn">
					  <input type="button" name="Enviar" value=" Imprimir " class="btn" onclick="javascript:imprimirodc(<?php echo $Cod_Cot; ?>)">
					</form>
                    </div>
                    <div class="texto-pq-aviso">
					<?php if ($Arc_Adj != '' Or $Num_TrnBco != null) { ?>
					Su orden ha sido recibida satisfactoriamente ser&aacute; procesada una vez<BR>confirmado el pago. Usted podr&aacute; 
					revisar en todo	momento estado de su pedido<BR>a trav&eacute;s de la opci&oacute;n Ordenes del men&uacute; de su cuenta
					<?php } else { ?>
					Su orden ha sido recibida satisfactoriamente, hemos detectado que no se ha<BR>completado toda la informaci&oacute;n 
					referente al pago, usted podr&aacute; completar<BR>esta informaci&oacute;n a trav&eacute;s de la opci&oacute;n Ordenes del 
					men&uacute; de su cuenta
					<?php } ?>
                    </div>
                </div>
                </div>
				
			  
			  <?php
					break;
					}
			  ?>
    </div>
	<div id="footer"></div>
</div>
<script language="javascript">
	var f1;	
	var f2;
	
	f1 = document.F1;	
	f2 = document.F2;
<?php	
    if ($idmsg == 32) {
		$page = "miscotizaciones.php";
		echo "	parent.opener.document.F2.action=\"".$page."\"\n";
		echo "	parent.opener.document.F2.submit();\n";
	}
?>
</script>
</body>
</html>
