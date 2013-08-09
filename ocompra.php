<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";

$fechaini = date("d/m/Y", time());
$fechafin = $fechaini;
$tipcna = 1;
//if (isset($_SESSION['FechaIni'])) $fecha = ok($_SESSION['FechaIni']);
foreach ($_POST as $key => $value) {
	if ($key == "fechaini") $fechaini = ok($value);
	if ($key == "fechafin") $fechafin = ok($value);
	if ($key == "tipcna")   $tipcna   = ok($value);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>

<link rel="stylesheet" type="text/css" media="all" href="Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="Include/calendar.js"></script>
<script type="text/javascript" src="Include/calendar-es.js"></script>
<script type="text/javascript" src="Include/calendar-setup.js"></script>
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>

<LINK href="Include/estilos.css" type="text/css" rel="stylesheet" />
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="left" style="PADDING-LEFT: 5px"><strong>Ordenes de Compras</strong></P>
<P align="center">
<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="ocompra.php">
<tr><td class="dato">
	<fieldset>
		<legend>&nbsp;<strong>Tipo de Busqueda&nbsp;</strong></legend>
		<table width="100%" border="0"><tr>
		<td width="1%" class="dato" style="TEXT-ALIGN: center"><INPUT id="tipcna" type="radio" name="tipcna" value="1" <?php if ($tipcna == 1) echo "CHECKED"; ?>></TD>
		<td width="25%" class="dato"><strong>Todas</strong></td>
		<td width="1%" class="dato" style="TEXT-ALIGN: center"><INPUT id="tipcna" type="radio" name="tipcna"  value="2" <?php if ($tipcna == 2) echo "CHECKED"; ?>></TD>
		<td width="15px" class="dato"><strong>Desde</strong></td>
		<td width="50px" class="dato">
			<INPUT name="fechaini" id="fechaini" class="textfield" maxLength="10" size="10" readOnly value="<?php echo $fechaini; ?>">
		</td>
		<td width="10px" class="dato">
			<A HREF="#"><img src="images/calendar.gif" border="0" id="lanzadorini" name="lanzadorini"></A>
		</td>
		<td width="20px" class="dato" style="TEXT-ALIGN: center">
			<strong>Hasta</strong>
		</td>
		<td width="50px" class="dato">
			<INPUT name="fechafin" id="fechafin" class="textfield" maxLength="10" size="10" readOnly value="<?php echo $fechafin; ?>">
		</td>
		<td width="10px" class="dato">
			<A HREF="#"><img src="images/calendar.gif" border="0" id="lanzadorfin" name="lanzadorfin"></A>
		</td>
		<td width="30%" style="TEXT-ALIGN: center">
			<input type="submit" name="Enviar" value=" Consultar " class="button2">
		</td>
		</tr></table>
	</fieldset>
</form>
<tr><td style="PADDING-TOP: 20px">
	<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<TR>
		<TD class="titulo_tabla" width="20%" align="middle">Fecha</TD>
		<TD class="titulo_tabla" width="20%" align="middle">Numero</TD>
		<TD class="titulo_tabla" width="20%" align="middle">Estado</TD>
		<TD class="titulo_tabla" width="40%" align="middle">&nbsp;</TD>
	</TR>
	<form ID="F4" AUTOCOMPLETE="off" method="POST" name="F4" action="reponer_ins.php?filter=6">
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$tip_doc = 1;
		
		$xis = 0;
		$result = mssql_query("vm_odc_s $tipcna, '$fechain', '$fechafi'", $db);
		while ($row = mssql_fetch_array($result)) {
			echo "<TR>\n";
			if ($j == 0) {
				$clase1 = "label_left_right";
				$clase2 = "dato3";
			}
			else {
				$clase1 = "label333";
				$clase2 = "dato33";
			}
			$afecha  = split("/", $row['Fec_OdcDis']);
			$Fec_Mov = $afecha[2].$afecha[1].$afecha[0];
			echo "   <TD class=\"".$clase1."\" style=\"TEXT-ALIGN: center \">".$row['Fec_OdcDis']."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Num_Odc']."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".($row['Est_Odc'] == "1" ? "Pendiente" : "Vigente")."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left\"><a href=\"detcompra.php?cod=".$row['Num_Odc']."&fec=$Fec_Mov\">Detalle</a></TD>\n";
			echo "</TR>\n";
			$j = 1 - $j;
			$iTotPrd++;
		}
		mssql_free_result($result);
	?>
	<TR>
		<TD colspan="4" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
		<input type="submit" name="CreateOdr" value="Crear Solicitud de Compra" tabindex="20" class="button2" style="width:220px;" />
        <input name="dftipcna" type="hidden" id="dftipcna" value="<?php echo $tipcna ?>" />
        <input name="dffechaini" type="hidden" id="dffechaini" value="<?php echo $fechain ?>" />
        <input name="dffechafin" type="hidden" id="dffechafin" value="<?php echo $fechafi ?>" />
		</TD>
	</TR>
	</form>
	</TABLE>
</td></tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;	
	var f3;	
	var f4;	
	f1 = document.F1;
	f3 = document.F3;
	f4 = document.F4;
</script>
<!-- script que define y configura el calendario-->
<script type="text/javascript">
Calendar.setup({
	inputField : "fechaini", // id del campo de texto
	ifFormat : "%d/%m/%Y", // formato de la fecha que se escriba en el campo de texto
	button : "lanzadorini" // el id del botón que lanzará el calendario
});
Calendar.setup({
	inputField : "fechafin", // id del campo de texto
	ifFormat : "%d/%m/%Y", // formato de la fecha que se escriba en el campo de texto
	button : "lanzadorfin" // el id del botón que lanzará el calendario
});
</script>
</body>
</html>
