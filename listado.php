<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Num_Lst = (isset($_GET['cod']) ? intval(ok($_GET['cod'])) : intval(ok($_POST['Num_Odr'])));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<LINK href="Include/estilos.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php formar_topbox ("100%%","center"); ?>
<P align="center">
<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr><td>
	<H2 style="TEXT-ALIGN: center">Listado de Reposici&oacute;n</H2>
	<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<tr><td colspan="7"><span class="encabezado1">Reposiciones Normales :</span></td></tr>
	<form ID="F4" AUTOCOMPLETE="off" method="POST" name="F4" action="ocompra_ins.php" onsubmit="return ValidaCantidades()">
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$XisPrd = false;
		$tip_doc = 1;
		$afecha = split('/', $fecha);
		$fechain = $afecha[2].$afecha[1].$afecha[0];
		
		$xis = 0;
		$xisNull = false;
		$numcol = 4;

		$result = mssql_query("vm_lstrep $Num_Lst", $db);
		while ($row = mssql_fetch_array($result)) {
			if (($row['Ctd_Rep'] - $row['Ctd_Rsv']) > 0) {
				if ($Mca != $row['Cod_Mca']) {
					if ($Mca != "") echo "<TR><TD colspan=\"$numcol\" style=\"PADDING-TOP: 10px; TEXT-ALIGN: right\" class=\"label_top\">&nbsp;</TD></TR>\n";
					$Mca = $row['Cod_Mca'];
					?>
					<TR>
						<TD class="titulo_tabla" width="100%" colspan="<?php echo $numcol ?>" align="middle">Marca: <?php echo $Mca; ?></TD>
					</TR>
					<TR>
						<TD class="titulo_tabla" align="middle">Style</TD>
						<TD class="titulo_tabla" align="middle">Patr&oacute;n</TD>
						<TD class="titulo_tabla" align="middle">Talla</TD>
						<TD class="titulo_tabla" align="middle">Cantidad a<BR>Reponer</TD>
					</TR>
				<?
				}
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
				echo "   <TD width=\"20%%\" class=\"".$clase1."\" style=\"TEXT-ALIGN: center\">".$row['Cod_Sty']."</TD>\n";
				echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Key_Pat']."</TD>\n";
				echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Val_Sze']."</TD>\n";
				echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center;\">\n";
				echo $row['Ctd_Rep'] - $row['Ctd_Rsv'];
				//if ($row['Ctd_Rep'] == -1) echo "&nbsp;</TD>\n";
				//else if ($row['Ctd_Rep'] == 0) echo "Sin Existencia";
				//else echo $row['Ctd_Rep'] - minimo($row['Ctd_Rsv'], $row['Ctd_Rep']);
				echo "</TD>\n";
				echo "</TR>\n";
				$j = 1 - $j;
				$iTotPrd++;
			}
		}
		mssql_free_result($result);
		if ($iTotPrd > 0) $XisPrd = true;
	?>
	<?php if ($iTotPrd > 0) { ?>
	<TR>
		<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right" class="label_top">&nbsp;</TD>
		</TD>
	</TR>
	<?php } else { ?>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_top_bottom">
			NO EXISTEN SOLICITUDES DE REPOSICION
			</TD>
		</TR>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right">&nbsp;</TD>
		</TR>
	<?php } ?>
	<tr><td colspan="<?php echo $numcol ?>"><span class="encabezado1">Reposiciones Reservadas :</span></td></tr>
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$tip_doc = 1;
		$afecha = split('/', $fecha);
		$fechain = $afecha[2].$afecha[1].$afecha[0];
		
		$xis = 0;

		$result = mssql_query("vm_lstrep $Num_Lst", $db);
		while ($row = mssql_fetch_array($result)) {
			if ($row['Ctd_Rsv'] > 0) {
				if ($Mca != $row['Cod_Mca']) {
					if ($Mca != "") echo "<TR><TD colspan=\"$numcol\" style=\"PADDING-TOP: 10px; TEXT-ALIGN: right\" class=\"label_top\">&nbsp;</TD></TR>\n";
					$Mca = $row['Cod_Mca'];
					?>
					<TR>
						<TD class="titulo_tabla" width="100%" colspan="<?php echo $numcol ?>" align="middle">Marca: <?php echo $Mca; ?></TD>
					</TR>
					<TR>
						<TD class="titulo_tabla" align="middle">Style</TD>
						<TD class="titulo_tabla" align="middle">Patr&oacute;n</TD>
						<TD class="titulo_tabla" align="middle">Talla</TD>
						<TD class="titulo_tabla" align="middle">Cantidad a<BR>Reponer</TD>
					</TR>
				<?
				}
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
				echo "   <TD width=\"20%%\" class=\"".$clase1."\" style=\"TEXT-ALIGN: center\">".$row['Cod_Sty']."</TD>\n";
				echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Key_Pat']."</TD>\n";
				echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Val_Sze']."</TD>\n";
				echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center;\">\n";
				echo $row['Ctd_Rsv'];
				//if ($row['Ctd_Rep'] == -1) echo "&nbsp;</TD>\n";
				//else if ($row['Ctd_Rep'] == 0) echo "Sin Existencia";
				//else echo $row['Ctd_Rep'] - minimo($row['Ctd_Rsv'], $row['Ctd_Rep']);
				echo "</TD>\n";
				echo "</TR>\n";
				$j = 1 - $j;
				$iTotPrd++;
			}
		}
		mssql_free_result($result);
		if ($iTotPrd > 0) $XisPrd = true;
	?>
	<?php if ($iTotPrd > 0) { ?>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right" class="label_top">
				<a href="javascript:window.print()">Imprimir</a>&nbsp;&nbsp;<a href="javascript:window.close()">Cerrar</a>
			</TD>
		</TR>
	<?php } else { ?>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_top_bottom">
			NO EXISTEN SOLICITUDES DE REPOSICION
			</TD>
		</TR>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right">
				<a href="javascript:window.print()">Imprimir</a>&nbsp;&nbsp;<a href="javascript:window.close()">Cerrar</a>
			</TD>
		</TR>
	<?php } ?>
	</form>
	</TABLE>
</td></tr>
</table>
</p>
<?php formar_bottombox (); ?>
<script language="javascript">
	var f1;	
	var f3;	
	var f4;	
	f1 = document.F1;
	f3 = document.F3;
	f4 = document.F4;
	
</script>
<!-- script que define y configura el calendario-->
</body>
</html>
