<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Mca = isset($_GET['mca']) ? ok($_GET['mca']) : ok($_POST['dfCod_Mca']);

$titulo = "Solicitudes de Compra";
$link = "odrdiario.php";
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
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
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
<P align="left" style="PADDING-LEFT: 5px"><strong><a href="<?php echo $link ?>"><?php echo $titulo ?></a></strong> / Detalle</P>
<P align="center">
<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr><td>
	<H2 style="TEXT-ALIGN: center">Solicitudes de Compra</H2>
	<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<tr><td colspan="7"><span class="encabezado1">Compras Normales :</span></td></tr>
	<?php if ($Perfil != 2) { ?>
	<form ID="F4" AUTOCOMPLETE="off" method="POST" name="F4" action="odcompra_ins.php" onsubmit="return checkDataFichaOdr(this,1)" />
	<?php } else { ?>
	<form ID="F4" AUTOCOMPLETE="off" method="POST" name="F4" action="odrdiario.php" />	
	<?php } ?>
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$tip_doc = 1;
		$afecha = split('/', $fecha);
		$fechain = $afecha[2].$afecha[1].$afecha[0];
		
		$xis = 0;
		$xisNull = false;
		$XisLineas = false;
		$numcol = 7; 

		$result = mssql_query("vm_odcres_det $Cod_Mca, 'N'", $db);
		while ($row = mssql_fetch_array($result)) {
			if ($Mca != $Cod_Mca) {
				if ($Mca != "") echo "<TR><TD colspan=\"$numcol\" style=\"PADDING-TOP: 10px; TEXT-ALIGN: right\" class=\"label_top\">&nbsp;</TD></TR>\n";
				$Mca = $Cod_Mca;
				?>
				<TR>
					<TD class="titulo_tabla" width="100%" colspan="<?php echo $numcol ?>" align="middle">Marca: <?php echo $Mca; ?></TD>
				</TR>
				<TR>
					<TD class="titulo_tabla" align="middle">&nbsp;</TD>
					<TD class="titulo_tabla" align="middle">Orden de<Br>Reposici&oacute;n</TD>
					<TD class="titulo_tabla" align="middle">Fecha</TD>
					<TD class="titulo_tabla" align="middle">Style</TD>
					<TD class="titulo_tabla" align="middle">Patr&oacute;n</TD>
					<TD class="titulo_tabla" align="middle">Talla</TD>
					<TD class="titulo_tabla" align="middle">Cantidad a<BR>Comprar</TD>
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
			echo "   <TD width=\"10%%\" class=\"".$clase1."\" style=\"TEXT-ALIGN: center\">\n";
			echo "   <INPUT type=\"checkbox\" class=\"dato\" style=\"height: 14px\" name=\"seleccionadof[]\" value=\"".$row["Num_Odr"]."-".$row['Num_Lin']."\" /></TD>\n";
			echo "   <TD width=\"10%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Num_Odr']."</TD>\n";
			echo "   <TD width=\"15%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".fechafmt($row['Fec_Odr'])."</TD>\n";
			echo "   <TD width=\"15%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Cod_Sty']."</TD>\n";
			echo "   <TD width=\"15%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Key_Pat']."</TD>\n";
			echo "   <TD width=\"15%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Val_Sze']."</TD>\n";
			echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
			echo "<INPUT style=\"BORDER-BOTTOM: 1px; BORDER-LEFT: 1px; BORDER-TOP: 1px; BORDER-RIGHT: 1px\" size=\"5\" class=\"".$clase3."\" name=\"dfCtd_Buy".$row['Num_Lin']."\" border=\"0\" value=\"".$row['Ctd_Buy']."\" readOnly></TD>\n";
			echo "</TD>\n";
			echo "</TR>\n";
			$j = 1 - $j;
			$iTotPrd++;
		}
		mssql_free_result($result);
		if ($iTotPrd > 0) $XisLineas = true;
	?>
	<?php if ($iTotPrd > 0) { ?>
	<TR>
		<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right" class="label_top">&nbsp;</TD>
		</TD>
	</TR>
	<?php } else { ?>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_top_bottom">
			NO EXISTEN SOLICITUDES DE COMPRA
			</TD>
		</TR>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right">&nbsp;</TD>
		</TR>
	<?php } ?>
	<tr><td colspan="<?php echo $numcol ?>"><span class="encabezado1">Compras Reservadas :</span></td></tr>
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$tip_doc = 1;
		$afecha = split('/', $fecha);
		$fechain = $afecha[2].$afecha[1].$afecha[0];
		
		$xis = 0;

		$result = mssql_query("vm_odcres_det $Cod_Mca, 'R'", $db);
		while ($row = mssql_fetch_array($result)) {
			if ($Mca != $Cod_Mca) {
				if ($Mca != "") echo "<TR><TD colspan=\"$numcol\" style=\"PADDING-TOP: 10px; TEXT-ALIGN: right\" class=\"label_top\">&nbsp;</TD></TR>\n";
				$Mca = $Cod_Mca;
				?>
				<TR>
					<TD class="titulo_tabla" width="100%" colspan="<?php echo $numcol ?>" align="middle">Marca: <?php echo $Mca; ?></TD>
				</TR>
				<TR>
					<TD class="titulo_tabla" align="middle">&nbsp;</TD>
					<TD class="titulo_tabla" align="middle">Orden de<Br>Reposici&oacute;n</TD>
					<TD class="titulo_tabla" align="middle">Fecha</TD>
					<TD class="titulo_tabla" align="middle">Style</TD>
					<TD class="titulo_tabla" align="middle">Patr&oacute;n</TD>
					<TD class="titulo_tabla" align="middle">Talla</TD>
					<TD class="titulo_tabla" align="middle">Cantidad a<BR>Comprar</TD>
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
			echo "   <TD width=\"10%%\" class=\"".$clase1."\" style=\"TEXT-ALIGN: center\">\n";
			echo "   <INPUT type=\"checkbox\" class=\"dato\" style=\"height: 14px\" name=\"seleccionadof[]\" value=\"".$row["Num_Odr"]."-".$row['Num_Lin']."\" /></TD>\n";
			echo "   <TD width=\"10%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Num_Odr']."</TD>\n";
			echo "   <TD width=\"15%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".fechafmt($row['Fec_Odr'])."</TD>\n";
			echo "   <TD width=\"15%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Cod_Sty']."</TD>\n";
			echo "   <TD width=\"15%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Key_Pat']."</TD>\n";
			echo "   <TD width=\"15%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Val_Sze']."</TD>\n";
			echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
			echo "<INPUT style=\"BORDER-BOTTOM: 1px; BORDER-LEFT: 1px; BORDER-TOP: 1px; BORDER-RIGHT: 1px\" size=\"5\" class=\"".$clase3."\" name=\"dfCtd_Rsv".$row['Num_Lin']."\" border=\"0\" value=\"".$row['Ctd_Buy']."\" readOnly></TD>\n";
			echo "</TD>\n";
			echo "</TR>\n";
			$j = 1 - $j;
			$iTotPrd++;
		}
		mssql_free_result($result);
		if ($iTotPrd > 0) $XisLineas = true;
	?>
	<?php if ($iTotPrd > 0) { ?>
	<TR>
		<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right" class="label_top">
		    <?php if ($Perfil != 2) { ?>
			<INPUT class="button2" type="button" value="Seleccionar Todos" onclick="javascript:MarcarTodos(f4,'seleccionadof[]')" name="todosf">&nbsp;
			<INPUT class="button2" type="button" value="Quitar Todos" name="quitarf" onclick="javascript:DesMarcarTodos(f4,'seleccionadof[]')">&nbsp;
			<input type="submit" name="Enviar" value=" Generar Solicitud de Compra " class="button2" />
		    <?php } else { ?>
			<input type="submit" name="Enviar" value=" Volver " class="button2" />
		    <?php } ?>
		</TD>
	</TR>
	<?php } else { ?>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_top_bottom">
			NO EXISTEN SOLICITUDES DE COMPRA
			</TD>
		</TR>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right">
		    <?php if ($Perfil != 2) { ?>
			<INPUT class="button2" type="button" value="Seleccionar Todos" onclick="javascript:MarcarTodos(f4,'seleccionadof[]')" name="todosf">&nbsp;
			<INPUT class="button2" type="button" value="Quitar Todos" name="quitarf" onclick="javascript:DesMarcarTodos(f4,'seleccionadof[]')">&nbsp;
			<input type="submit" name="Enviar" value=" Generar Solicitud de Compra " class="button2" />
		    <?php } else { ?>
			<input type="submit" name="Enviar" value=" Volver " class="button2" />
		    <?php } ?>
			</TD>
		</TR>
	<?php } ?>
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
	var f4;	
	f1 = document.F1;
	f4 = document.F4;

</script>
<!-- script que define y configura el calendario-->
</body>
</html>
