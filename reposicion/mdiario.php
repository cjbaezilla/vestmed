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
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="15%" align="left" valign="top">
	<?php echo display_mnuizq($Perfil); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
<h2>Ventas</h2>
<P align="center">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="mdiario.php">
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
			<A HREF="#"><img src="../images/calendar.gif" border="0" id="lanzadorini" name="lanzadorini"></A>
		</td>
		<td width="20px" class="dato" style="TEXT-ALIGN: center">
			<strong>Hasta</strong>
		</td>
		<td width="50px" class="dato">
			<INPUT name="fechafin" id="fechafin" class="textfield" maxLength="10" size="10" readOnly value="<?php echo $fechafin; ?>">
		</td>
		<td width="10px" class="dato">
			<A HREF="#"><img src="../images/calendar.gif" border="0" id="lanzadorfin" name="lanzadorfin"></A>
		</td>
		<td width="30%" style="TEXT-ALIGN: center">
			<input type="submit" name="Enviar" value=" Consultar " class="button2">
		</td>
		</tr></table>
	</fieldset>
</td></tr>
</form>
<tr><td style="PADDING-TOP: 20px">
    <form ID="F3" AUTOCOMPLETE="off" method="POST" name="F3" ACTION="reponer_ins.php" onsubmit="return checkDataFichaOdr(this,1)">
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<TR>
		<TD class="titulo_tabla" width="3%" align="middle">&nbsp;</TD>
		<TD class="titulo_tabla" width="10%" align="middle">Fecha</TD>
		<TD class="titulo_tabla" width="10%" align="middle">Style</TD>
		<TD class="titulo_tabla" width="10%" align="middle">Patr&oacute;n</TD>
		<TD class="titulo_tabla" width="10%" align="middle">Talla</TD>
		<TD class="titulo_tabla" width="10%" align="middle">Cantidad</TD>
		<TD class="titulo_tabla" width="35%" align="middle">Vendedor</TD>
		<TD class="titulo_tabla" width="15%" align="middle">Reservado</TD>
	</TR>
	<?php
		$j = 0;
		$iTotPrd = 0;
		$tip_doc = 1;
		$afecha = split('/', $fechaini);
		$fechain = $afecha[2].$afecha[1].$afecha[0];
		$afecha = split('/', $fechafin);
		$fechafi = $afecha[2].$afecha[1].$afecha[0];
		$result = mssql_query("vm_vtadia $tipcna, '$fechain', '$fechafi'", $db);
		while ($row = mssql_fetch_array($result)) {
			echo "<TR>\n";
			if ($j == 0) {
				$clase1 = "label_left_right";
				$clase2 = "dato3";
				$clase3 = "textfieldRO2";
			}
			else {
				$clase1 = "label333";
				$clase2 = "dato33";
				$clase3 = "textfieldRO22";
			}
			echo "   <TD class=\"".$clase1."\" style=\"TEXT-ALIGN: center \">\n";
			echo "   <INPUT type=\"checkbox\" class=\"dato\" style=\"height: 14px\" name=\"seleccionadof[]\" value=\"".$row["Cod_Prd"]."-".$row['Num_Lin']."\" /></TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center \">\n";
			echo "<INPUT style=\"BORDER-BOTTOM: 1px; BORDER-LEFT: 1px; BORDER-TOP: 1px; BORDER-RIGHT: 1px\" size=\"12\" class=\"".$clase3."\" name=\"FecRO".$row['Num_Lin']."\" border=\"0\" value=\"".$row['Fec_Fct']."\" readOnly>\n";
			echo "<INPUT name=\"dfCod_Nvt".$row['Num_Lin']."\" type=\"hidden\" value=\"".$row['Cod_Nvt']."\" /></TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center \">".$row['Cod_Sty']."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Key_Pat']."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Val_Sze']."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">\n";
			echo "<INPUT style=\"BORDER-BOTTOM: 1px; BORDER-LEFT: 1px; BORDER-TOP: 1px; BORDER-RIGHT: 1px\" size=\"5\" class=\"".$clase3."\" name=\"dfCtd_Prd".$row['Num_Lin']."\" border=\"0\" value=\"".$row['Ctd_Prd']."\" readOnly></TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px\">".$row['Nom_Vdd']."</TD>\n";
			echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center;\">\n";
			echo "   <INPUT type=\"checkbox\" class=\"dato\" style=\"height: 14px\" name=\"dfRsv".$row['Num_Lin']."\" value=\"".$row["Cod_Prd"]."-".$row['Num_Lin']."\" /></TD>\n";
			echo "</TR>\n";
			$j = 1 - $j;
			$iTotPrd++;
		}
		mssql_free_result($result);
	?>
	<?php if ($iTotPrd == 0) { ?>
	<TR>
		<TD colspan="8" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
		NO EXISTEN PRODUCTOS PENDIENTES DE ENV&Iacute;O. SI DESEA AGREGAR PINCHE <A HREF="reponer_ins.php?filter=5">AQU&Iacute;</A>
		</TD>
	</TR>
	
	<?php } else { ?>
	<TR>
		<TD colspan="8" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
			<INPUT class="button2" type="button" value="Seleccionar Todos" onclick="javascript:MarcarTodos(f3,'seleccionadof[]')" name="todosf">&nbsp;
			<INPUT class="button2" type="button" value="Quitar Todos" name="quitarf" onclick="javascript:DesMarcarTodos(f3,'seleccionadof[]')">&nbsp;
			<input type="submit" name="Enviar" value=" Enviar a la Orden de Reposici&oacute;n " class="button2" />
		</TD>
	</TR>
	<?php } ?>
	</TABLE>
	</form>
</td></tr>
</table>
</form>
</p>
</td>
</tr>
</table>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<!-- script que define y configura el calendario-->
<script language="javascript">
	var f1;	
	var f3;
	f1 = document.F1;	
	f3 = document.F3;
	
	//f1.fechaini.disabled = true;
	//f1.fechafin.disabled = true;
</script>
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
