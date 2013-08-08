<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();

if (!isset($_SESSION['usuario'])) {
    if (!isset($_POST["usuario"])) header("Location: ../index.php");
    $_SESSION['usuario'] = $_POST["usuario"];     
}
$UsrId = (isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : "";

include("global_cot.php");

$OkOpc = false;
$sp = mssql_query("vm_seg_usr_opcmodweb '$UsrId'",$db);
while (($row = mssql_fetch_array($sp))) 
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 3 && $row['CodUsr'] == $UsrId) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

function fechafmt2 ($fecha) {
	return right($fecha, 2)."/".substr($fecha, 4, 2)."/".left($fecha,4);
}

$RutClt = ok($_GET['clt']);
$tipcna = isset($_GET['cna']) ? intval(ok($_GET['cna'])) : 1;
$fechaini = date("d/m/Y", time());
$fechafin = $fechaini;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="../dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="../Include/SoloNumeros.js"></script>
<script language="JavaScript" src="../Include/validarRut.js"></script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script type="text/javascript" src="menu.js"></script>
<script type="text/javascript">
	new UvumiDropdown('dropdown-scliente');

	function MarcarTodos(form,nombrecheckbox) {
	   for (i=0; i<form.elements.length; i++) {
		if (form.elements[i].name == nombrecheckbox)
			form.elements[i].checked = true;
	   }	
	}

	function DesMarcarTodos(form,nombrecheckbox) {
	   for (i=0; i<form.elements.length; i++) {
		if (form.elements[i].name == nombrecheckbox)
			form.elements[i].checked = false;
	   }	
	}
	
	function popwindow(ventana,ancho,altura){
	   window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width="+ancho+",height="+altura);
	}
	
	function BuscarCliente(contexto) {
		//popwindow("busqueda.php?contexto="+contexto,600)
		f1.action = "escritorio_bus.php";
		f1.submit();
	}
	
	function NuevoCliente() {
		popwindow("registrarse.php",800,600);
	}
	
	function Editar() {
		f2.action = "escritorio_edtclt.php?clt=<?php echo $RutClt; ?>";
		f2.submit();	
	}
	
	function VerPrd(sCodPrd) {
		popwindow("preview_prd.php?prd="+sCodPrd,800,400);
	}
	
	function mnuCliente() {
		f1.action = "escritorio_cot.php?opc=clt";
		f1.submit();
	}
	
	function mnuVestmed() {
		f1.action = "escritorio_cot.php?opc=";
		f1.submit();
	}
	
	function PrintHistorico() {
		popwindow("previewhis.php?clt=<?php echo $RutClt; ?>",750,600);
	}
	
	function PrintCotizaciones() {
		popwindow("previewhiscot.php?clt=<?php echo $RutClt; ?>",750,600);
	}

	function ver_preview(cot) {
		popwindow("preview.php?cot="+cot,750,600);
	}
	
	function consultar(obj) {
		//alert(obj.value);
		f3.action = "escritorio_histo.php?clt=<?php echo $RutClt; ?>&cna="+obj.value;
		f3.submit();
	}

	function ver_previeworden(cot) {
		popwindow("../viewordendecompra.php?cot="+cot,965,600);
	}
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="left"><strong>Escritorio</strong></P>
<P align="center">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="170px" align="left" valign="top">
	<?php echo display_mnuizq($UsrId, $db); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
	<tr><td align="right">
		<form ID="F3" method="post" name="F3" AUTOCOMPLETE="on">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<tr>
		<td width="5px"><input type="radio" name="consulta" value="1" onclick="javascript:consultar(this)"<?php if ($tipcna == 1) echo " checked"; ?>></td><td width="100px">Compras</td>
		<td width="5px"><input type="radio" name="consulta" value="2" onclick="javascript:consultar(this)"<?php if ($tipcna == 2) echo " checked"; ?>></td><td width="100px">Cotizaciones</td>
		<td width="5px"><input type="radio" name="consulta" value="3" onclick="javascript:consultar(this)"<?php if ($tipcna == 3) echo " checked"; ?>></td><td width="100px">Ordenes</td>
		</tr>
		<tr>
		</table>
		</form>
	</td></tr>
	<?php if ($tipcna == 1) { ?>
	<tr><td>
		<h2>Hist&oacute;rico de Compras</h2>
		<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="" />
		<TABLE WIDTH="98%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<?php
			$j = 0;
			$iTotPrd = 0;
			$RutAnt = "";
			$result = mssql_query("vm_hisusr_esc 1, '$RutClt', 1", $db);
			while ($row = mssql_fetch_array($result)) {
				if ($row['Num_Doc'] != $RutAnt) {
					$RutAnt = $row['Num_Doc'];
		?>
		<TR><TD colspan="9" style="PADDING-BOTTOM: 5px"><b>Cliente: </b><?php echo ($row['Cod_TipPer'] == 1 ? $row['Pat_Per'].' '.$row['Mat_Per'].', '.$row['Nom_Per'] : $row['RznSoc_Per']) ?></TD></TR>
		<TR>
			<TD class="titulo_tabla" width="5%" align="left"   style="TEXT-ALIGN: left"># NV</TD>
			<TD class="titulo_tabla" width="10%" align="middle" style="TEXT-ALIGN: center">Fecha</TD>
			<TD class="titulo_tabla" width="15%" align="left"   style="text-align: left">Sucursal</TD>
			<TD class="titulo_tabla" width="10%" align="left"   style="text-align: left">Titulo</TD>
			<TD class="titulo_tabla" width="10%" align="center" style="TEXT-ALIGN: center">Style</TD>
			<TD class="titulo_tabla" width="10%" align="middle" style="TEXT-ALIGN: center">Pattern</TD>
			<TD class="titulo_tabla" width="10%" align="middle" style="TEXT-ALIGN: center">Talla</TD>
			<TD class="titulo_tabla" width="10%" align="middle" style="TEXT-ALIGN: center">Cantidad</TD>
			<TD class="titulo_tabla" width="10%" align="right"   style="text-align:right">Precio<BR>(IVA Incl)</TD>
		</TR>
		<?php
				}
				echo "<TR>\n";
				if ($j == 0) {
					$clase1 = "";
					$clase2 = "";
				}
				else {
					$clase1 = "";
					$clase2 = "";
				}
		?>
			<TD align="left"><?php echo $row['Cod_Nvt']; ?></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Fec_Nvt_Display']; ?></TD>
			<TD style="TEXT-ALIGN: left"><?php echo $row['Nom_Suc'] ?></TD>
			<TD style="TEXT-ALIGN: left"><?php echo $row['Nvt_Tlt'] ?></TD>
			<TD style="TEXT-ALIGN: center"><a href="javascript:VerPrd('<?php echo $row['Cod_Prd'] ?>')"><?php echo $row['Cod_Sty'] ?></a></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Key_Pat'] ?></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Val_Sze'] ?></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Ctd_Prd'] ?></TD>
			<TD style="TEXT-ALIGN: right"><?php echo formatearMillones($row['Mto_Prd']) ?></TD>
		<?php
				echo "</TR>\n";
				$j = 1 - $j;
				$iTotPrd++;
			}
			mssql_free_result($result);
		?>
		<?php if ($iTotPrd == 0) { ?>
		<TR>
			<TD colspan="9" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
			NO EXISTEN COMPRAS DEL CLIENTE.
			</TD>
		</TR>
		
		<?php } else { ?>
		<TR>
			<TD colspan="9" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
				<input type="button" name="Imprimir" value=" Imprimir " class="btn" onclick="PrintHistorico()" />
			</TD>
		</TR>
		<?php } ?>
		</TABLE>
		</form>
	</td></tr>
	<?php } else if ($tipcna == 2) { ?>
	<tr><td>
		<h2>Hist&oacute;rico de Cotizaciones</h2>
		<form ID="F2" method="post" name="F2" AUTOCOMPLETE="on">
		<TABLE WIDTH="98%" BORDER="0" align="center" CELLSPACING="0" CELLPADDING="1" style="margin-top:3px;">
		<?php
			$j = 0;
			$iTotPrd = 0;
			$doc_id = 1;
			$result = mssql_query("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db);
			if ($row = mssql_fetch_array($result)) {
				$Cod_Clt = $row['Cod_Clt'];
		?>
		<TR><TD colspan="5" style="PADDING-BOTTOM: 5px"><b>Cliente: </b><?php echo ($row['Cod_TipPer'] == 1 ? $row['Pat_Per'].' '.$row['Mat_Per'].', '.$row['Nom_Per'] : $row['RznSoc_Per']) ?></TD></TR>
		<TR>
			<TD class="titulo_tabla" width="10%" align="center">Cotizaci&oacute;n</TD>
			<TD class="titulo_tabla" width="20%" style="TEXT-ALIGN:left">Estado</TD>
			<TD class="titulo_tabla" width="20%" style="TEXT-ALIGN:left">Fecha</TD>
			<TD class="titulo_tabla" width="20%" style="TEXT-ALIGN:left">Vigencia</TD>
			<TD class="titulo_tabla" width="30%" style="TEXT-ALIGN:left">Opciones</TD>
		</TR>
		<?php
				$result = mssql_query("vm_s_cot_cltweb $Cod_Clt, 1", $db);
				while ($row = mssql_fetch_array($result)) {
					switch($row['is_svc1']*10 + $row['is_svc2']) {
					case 0:
						$servicio = "Sin Servicio";
						break;
					case 1:
						$servicio = "Bordado 2 lineas";
						break;
					case 10:
						$servicio = "Bordado 1 lineas";
						break;
					case 11:
						$servicio = "Bordado 1 y 2 lineas";
						break;
					}
					$despacho = ($row['is_dsp'] == 0) ? "sin despacho" : $row['Dir_Suc'];
					$fecha = $row['Fec_Cot'];
					$Cod_Cot = $row['Cod_Cot'];
					?>
					<TR>
					<TD style="TEXT-ALIGN:center"><?php echo $row['Num_Cot'] ?></TD>
					<?php if ($row['Est_Res'] == "3") ?>
					<TD style="TEXT-ALIGN:left"><?php echo ($row['Est_Res'] == "3" ? "Concluida" : "En Proceso"); ?></TD>
					<TD style="TEXT-ALIGN:left"><?php echo date("d/m/Y",strtotime($fecha)); ?></TD>
					<TD style="TEXT-ALIGN:left"><?php echo date("d/m/Y",strtotime($row['Fec_TerVig'])); ?></TD>
					<?php if ($row['Est_Res'] == "3") { ?>
					<TD style="TEXT-ALIGN:left"><a href="javascript:ver_preview(<?php echo $Cod_Cot; ?>)">Ver</a></TD>
					<?php } else { ?>
					<TD style="TEXT-ALIGN:left"><a href="nueva_cot.php?cot=<?php echo $Cod_Cot; ?>">Ver</a></TD>
					<?php } ?>
					</TR>
					<?php
					$j = 1 - $j;
					$iTotPrd++;
				}
				mssql_free_result($result);
			}
		?>
		<?php if ($iTotPrd == 0) { ?>
		<TR>
			<TD colspan="5" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
			NO EXISTEN COTIZACIONES DEL CLIENTE.
			</TD>
		</TR>
		
		<?php } else { ?>
		<TR>
			<TD colspan="5" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
				<input type="button" name="ImprimirCot" value=" Imprimir " class="btn" onclick="PrintCotizaciones()" />
			</TD>
		</TR>
		<?php } ?>
		</TABLE>
		</form>
	</td></tr>
	<?php } else if ($tipcna == 3) { ?>
	<tr><td>
		<h2>Hist&oacute;rico de Ordenes</h2>
		<form ID="F2" method="post" name="F2" AUTOCOMPLETE="on">
		<TABLE WIDTH="98%" BORDER="0" align="center" CELLSPACING="0" CELLPADDING="1">
		<?php
			$doc_id = 1;
			$result = mssql_query("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db);
			if ($row = mssql_fetch_array($result)) $Cod_Clt = $row['Cod_Clt'];
		?>
		<TR><TD colspan="9" style="PADDING-BOTTOM: 5px"><b>Cliente: </b><?php echo ($row['Cod_TipPer'] == 1 ? $row['Pat_Per'].' '.$row['Mat_Per'].', '.$row['Nom_Per'] : $row['RznSoc_Per']) ?></TD></TR>
		<TR>
			<TD class="titulo_tabla" width="10%" align="center">Cotizaci&oacute;n</TD>
			<TD class="titulo_tabla" width="10%" style="TEXT-ALIGN:center">NV</TD>
			<TD class="titulo_tabla" width="15%" style="TEXT-ALIGN:center">F.Envio</TD>
			<TD class="titulo_tabla" colspan="2" width="20%" style="TEXT-ALIGN:center">Estado</TD>
			<TD class="titulo_tabla" colspan="2" width="25%" style="TEXT-ALIGN:center">Mensaje</TD>
			<TD class="titulo_tabla" colspan="2" width="20%" style="TEXT-ALIGN:center">Opciones</TD>
		</TR>
		<?php
			$iTotPrd = 0;
			$result = mssql_query("vm_s_cot_tracking $Cod_Clt, 1", $db);
			while ($row = mssql_fetch_array($result)) {
				$Cod_Cot = $row['Cod_Cot'];
				$Num_Cot = $row['Num_Cot'];
				$Cod_Nvt = $row['Cod_Odc'];
				$fecha   = $row['Fec_Nvt'];
				$Arc_Adj = $row['Arc_Adj'];
				$Num_Trn = $row['Num_TrnBco'];
				if (trim($Arc_Adj) == "" and trim($Num_Trn) == "") {
					$images = "001_30.gif";
					$estado = "Enviada sin Pago";
				}
				else {
					$images = "001_06.gif";
					$estado = "Enviada con Pago";
				}
				$Tot_Cna = $row['Tot_Cna'];
				$Tot_New = $row['Tot_SinRes'];
				if ($Tot_Cna == 0) $mensaje = "Ninguno";
				else {
					$mensaje = "(".$Tot_Cna.")";
					if ($Tot_New > 0) $mensaje .= "/(".$Tot_New.") Nuevos";
				}
		?>
				<TR>
				<TD style="TEXT-ALIGN:center"><?php echo $Num_Cot; ?></TD>
				<TD style="TEXT-ALIGN:center"><?php echo $Cod_Nvt; ?></TD>
				<TD style="TEXT-ALIGN:center"><?php echo date("d/m/Y",strtotime($fecha)); ?></TD>
				<TD style="TEXT-ALIGN:left"><img src="../images/<?php echo $images; ?>"></TD>
				<TD style="TEXT-ALIGN:left"><?php echo $estado; ?></TD>
				<TD style="TEXT-ALIGN:right"><img src="../images/mail.png"></TD>
				<TD style="TEXT-ALIGN:left"><?php echo $mensaje; ?></TD>
				<TD style="TEXT-ALIGN:right"><img src="../images/001_38.gif"></TD>
				<TD style="TEXT-ALIGN:left"><a href="javascript:ver_previeworden(<?php echo $Cod_Cot ?>)">Ver</a></TD>
				</TR>
		<?php
				$iTotPrd++;
			}
			mssql_free_result($result);
		?>
		<?php if ($iTotPrd == 0) { ?>
		<TR>
			<TD colspan="9" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
			NO EXISTEN ORDENES DEL CLIENTE.
			</TD>
		</TR>
		
		<?php } else { ?>
		<TR>
			<TD colspan="9" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
				<input type="button" name="ImprimirCot" value=" Imprimir " class="btn" onclick="PrintCotizaciones()" />
			</TD>
		</TR>
		<?php } ?>
		</TABLE>
		</form>
	</td></tr>
	<?php } ?>
	</TABLE>
</td>
</tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;
	var f2;
	var f3;
	f1 = document.F1;
	f2 = document.F2;
	f3 = document.F3;
</script>


</body>
</html>
