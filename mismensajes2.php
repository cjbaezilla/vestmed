<?
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
$RutClt = "";
$xis = 0;
$EsCliente = false;
$bExisteUsr = false;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
$accion = (isset($_GET['accion'])) ? $_GET['accion'] : "";
$Cod_Cot = (isset($_GET['cot']) ? intval(ok($_GET['cot'])) : intval(ok($_POST['NumCot'])));
$Fol_Ctt = (isset($_GET['folctt'])) ? ok($_GET['folctt']) : 0;
$Tip_Bus = (isset($_POST['tipo_bus_cot']) ? ok($_POST['tipo_bus_cot']) : 'P');

if ($Cod_Per > 0) {
	$result = mssql_query ("vm_per_s ".$Cod_Per, $db)
							or die ("No se pudo leer datos del usuario");
	if ($row = mssql_fetch_array($result)) {
		$tipo = $row["Cod_TipPer"];
		$sex_ctt = $row["Sex"];
		$tip_doc = $row["Cod_TipDoc"];
		$num_doc = $row["Num_Doc"];
	}
	mssql_free_result($result); 
}

if ($Cod_Cot > 0) {
	$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
	if ($row = mssql_fetch_array($result)) {
		$Num_Cot   = $row['Num_Cot'];
		$Cod_Clt   = $row['Cod_Clt'];		
	}
}	
else {
	$result = mssql_query("vm_cttweb_s $Fol_Ctt",$db);
	if ($row = mssql_fetch_array($result)) {
		$Tip_Cna   = $row['tip_cna'];
	}
}

if ($accion == "respuesta") {
	$Fol_Cna = $_GET['folio'];
	$respuesta = (isset($_POST['respuesta'.$Fol_Cna])) ? ok($_POST['respuesta'.$Fol_Cna]) : "";
	$respuesta = str_replace("\'", "''", $respuesta);
    if ($Cod_Cot > 0)
		$result = mssql_query("vm_i_rescna $Cod_Cot, $Cod_Clt, $Cod_Per, $Fol_Cna, '$respuesta'",$db);
	else 
		$result = mssql_query("vm_i_rescnactt $Fol_Ctt, $Tip_Cna, $Cod_Per, $Fol_Cna, '$respuesta'",$db);
		//echo "vm_i_rescnactt $Fol_Ctt, $Tip_Cna, $Cod_Per, $Fol_Cna, '$respuesta'";
		//exit(0);
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registro - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />

<LINK href="Include/estilos.css" type=text/css rel=stylesheet>
<script language="JavaScript" src="Include/SoloNumeros.js"></script>
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="Include/validarRut.js"></script>
<script language="JavaScript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
	function Enviar_Res(folio,cot) {
		if (eval('f2.respuesta'+folio).value == "") {
			alert("Debe ingresar una respuesta");
			return false;
		}
		if (eval('f2.respuesta'+folio).value.length > 1000) {
			alert("El mensaje debe contener a los mas 1.000 caracteres.");
			return false;
		}
		f2.action = "mismensajes2.php?<?php if ($Cod_Cot > 0) echo "cot"; else echo "folctt"; ?>="+cot+"&folio="+folio+"&accion=respuesta";
		f2.submit();
	}
	
	function filterCot(obj) {
		f2.NumCot.value = obj.value;
	}
	
	function checkDataForm(form) {
		if (form.consulta.value.length > 1000)
		{
			alert("El mensaje debe contener a los mas 1.000 caracteres.");
			return false;
		}
		return true;
	}
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=830,height=600')
}
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <div class="menu"id="menu-noselect">
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
	<?php 
		if ($Cod_Per == 0) { 
	?>
    <ul id="usuario_registro">
        <form ID="F1" AUTOCOMPLETE="off" method="POST" name="F1">
    	<li class="back-verde registro"><a href="registrarse.php">REGISTRARSE</a></li>
        <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
        <li class="back-verde"><a href="javascript:ValidarLogin()">ENTRAR</a></li>
        <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
        <li class="back-verde">CONTRASE&Ntilde;A</li>
        <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')"></li>
        <li class="back-verde">RUT</li>
		<input type="hidden" name="dfrut" id="dfrut" />
		</form>
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
    <div id="work">
		<div id="back-registro3">
			<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" />
           	<div style="width:765px; height: auto; margin:0 auto 0 100px; padding-top:10px; padding-bottom:10px;">
				<form ID="F2" method="post" name="F2" ACTION="mismensajes.php?accion=<?php if ($Cod_Cot > 0) echo 12; else echo 11; ?>" AUTOCOMPLETE="on" enctype="multipart/form-data" />
				<h2 align="center">
				<?php if ($Cod_Cot > 0) { ?>
				Mensajes Cotizaci&oacute;n <?php echo $Num_Cot; ?> 
				<?php } else { ?>
				Mensajes Contacto <?php echo $Fol_Ctt; ?>
				<?php } ?>
				</h2>
				<fieldset class="label_left_right_top_bottom">
				<legend>Detalle </legend>
					<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
					<TR>
						<TD width="60%" VALIGN="TOP" class="dato10p" colspan="2" style="PADDING-BOTTOM:5px">
						<!--textarea name="comentarios" id="comentarios" cols="80" rows="3" class="dato" ReadOnly><?php echo $obs_cot; ?></textarea></TD-->
						<table width="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
						<tr>
							<td class="titulo_tabla" valign="top" style="text-align: left" width="15%" height="15">Fecha</td>
							<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">Folio</td>
							<td class="titulo_tabla" valign="top" style="text-align: left" width="15%" height="15">Origen</td>
							<td class="titulo_tabla" valign="top" style="text-align: left" width="50%" height="15">Consulta / Respuesta</td>
							<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">&nbsp;</td>
						</tr>
						<?php 
							//$result = mssql_query("vm_msg_clt $Cod_Clt, $Cod_Cot", $db);
							if ($Cod_Cot > 0)
							   $result = mssql_query("vm_hiscna_cot $Cod_Cot, $Cod_Clt", $db);
							else
							   $result = mssql_query("vm_hiscna_ctt $Fol_Ctt, $Cod_Clt", $db);
							$bOkRespuesta = false;
							$totfilas = 0;
							while ($row = mssql_fetch_array($result)) {
								$totfilas++;
						?>
								<tr>
									<td align="left" valign="top"><?php echo $row['Fec_Dis']; ?></td>
									<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
									<td align="left" valign="top"><?php echo ($row['Num_Doc'] == "Vestmed" ? $row['Num_Doc'] : "Cliente" ); ?></td>
									<td align="left" valign="top" colspan="2"><?php echo $row['Det_Cna']; ?></td>
								</tr>
						<?php if (trim($row['Det_Res']) != "") { ?>
								<tr>
									<td align="left" valign="top"><?php echo $row['Fec_ResCnaDis']; ?></td>
									<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
									<td align="left" valign="top"><?php echo ($row['Num_DocRes'] == "Vestmed" ? $row['Num_DocRes'] : "Cliente"  ); ?></td>
									<td align="left" valign="top" colspan="2"><?php echo $row['Det_Res']; ?></td>
								</tr>
						<?php } else { 
								$autor = $row['Num_Doc'];
								if (($autor != "Vestmed" and $UsrId == "cotizador") Or ($autor == "Vestmed" and $UsrId != "cotizador")) {
						?>
								<tr>
									<td align="left" valign="top"><?php echo date("d/m/Y") ?></td>
									<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
									<td align="left" valign="top"><?php echo ($UsrId == "cotizador" ? "Vestmed" : "Cliente"); ?></td>
									<td align="left" valign="top"><textarea class="dato" rows="2" cols="50" name="respuesta<?php echo $row['Fol_Cna'] ?>"></textarea></td>
									<td align="right" valign="top"><input type="button" name="Responder<?php echo $row['Fol_Cna'] ?>" value="Responder" onclick="javascript:Enviar_Res(<?php echo $row['Fol_Cna'].",".($Cod_Cot > 0 ? $Cod_Cot : $Fol_Ctt); ?>)" class="btn"></td>
								</tr>
						<?php
								}
							  }
							}
						?>
						</table>
						</TD>
					</TR>
					<TR><TD width="60%" VALIGN="TOP" colspan="2" style="PADDING-TOP:20px; PADDING-BOTTOM: 20px; text-align: right">
					<input type="submit" name="Volver" value=" Volver " class="btn">
					<input type="hidden" name="tipo_bus" value="<?php echo $Tip_Bus; ?>">
					</TD></TR>
					</TABLE>
				</fieldset>
				</form>
			</div>
		</div>
	</div>
	<div id="footer"></div>
<script language="javascript">
	var f1;	
	var f2;
	
	f1 = document.F1;	
	f2 = document.F2;
	
</script>
</body>
</html>
