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
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>

<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>

<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="15%" align="left" valign="top">
	<?php echo display_mnuizq($Perfil); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
<h2>Solicitudes de Compras</h2>
<P align="center">
<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr><td style="PADDING-TOP: 20px">
	<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<TR>
		<TD class="titulo_tabla" width="20%" align="middle">Marca</TD>
		<TD class="titulo_tabla" width="20%" align="middle">Normales</TD>
		<TD class="titulo_tabla" width="20%" align="middle">Rservados</TD>
		<TD class="titulo_tabla" width="40%" align="middle">&nbsp;</TD>
	</TR>
	<form ID="F4" AUTOCOMPLETE="off" method="POST" name="F4" action="reponer_ins.php?filter=4">
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$tip_doc = 1;
		
		$xis = 0;
		$result = mssql_query("vm_odcres", $db);
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
			$Cod_Mca = $row['Cod_Mca'];
			echo "   <TD class=\"".$clase1."\" style=\"TEXT-ALIGN: center \"><a href=\"odrdiario_det.php?mca=$Cod_Mca\">".$row['Cod_Mca']."</a></TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Qty_LinNor']."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Qty_LinRsv']."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left\">&nbsp;</TD>\n";
			echo "</TR>\n";
			$j = 1 - $j;
			$iTotPrd++;
		}
		mssql_free_result($result);
	?>
	<TR>
		<TD colspan="4" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
		<!--input type="submit" name="CreateOdr" value="Generar Solicitud" tabindex="20" class="button2" style="width:220px;" /-->
		&nbsp;
		</TD>
	</TR>
	</form>
	</TABLE>
</td></tr>
</table>
</p>
</td>
</tr>
</table>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;	
	var f4;	
	f1 = document.F1;
	f4 = document.F4;
</script>
</body>
</html>
