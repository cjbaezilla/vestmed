<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $_SESSION['UsrId'] : "";
$caso = (isset($_GET['filter'])) ? intval(ok($_GET['filter'])) : 1;
$tipo = (isset($_GET['tipo'])) ? intval(ok($_GET['tipo'])) : 0;

if (isset($_POST['fecha'])) 
	$fecha = ok($_POST['fecha']);
else if (isset($_GET['fec'])) 
	$fecha = ok($_GET['fec']);

$Num_Odr = isset($_GET['cod']) ? ok($_GET['cod']) : ok($_POST['dfNum_Odr']);
	
if (isset($_GET['prd'])) {
	$p_codprd = ok($_GET['prd']);

	$query = mssql_query("vm_prd_s '$p_codprd'",$db);
	if ($row = mssql_fetch_array($query)) {
		$p_coddsg = $row['Cod_Dsg'];
		$p_codpat = $row['Cod_Pat'];
		$p_grpprd = $row['Cod_GrpPrd'];
	}	
}
else {
	$p_coddsg = ok($_GET['dsg']);
	$p_codpat = ok($_GET['pat']);
}
	
$query = mssql_query("vm_pat_s '$p_codpat'",$db);
if ($row = mssql_fetch_array($query)) {
	$Cod_Mca = $row['Cod_Mca'];
	$Key_Pat = $row['Key_Pat'];
	$Des_Pat = $row['Des_Pat'];
	$Cod_GrpPat = $row['Cod_GrpPat'];
}

$query = mssql_query("vm_dsg_s '$p_coddsg'",$db);
while ($row = mssql_fetch_array($query)) {
	$Cod_Sty = $row['Cod_Sty'];
	$Nom_Dsg = $row['Nom_Dsg'];
	$Des_LinMca = $row['Des_LinMca'];
}	

if ($caso == 1) {
	$menu0 = "Reposiciones Pendientes";
	$pagina0 = "oreponer.php?est=1";
	$menuini = "Detalle";
	$paginaini = "reponer.php?cod=$Num_Odr&fec=$fecha";
}
else if ($caso == 2 or $caso == 20) {
	$menu0 = "Reposiciones Vigentes";
	$pagina0 = "oreponer.php?est=2";
	$menuini = "Detalle";
	$paginaini = "compra.php?cod=$Num_Odr&fec=$fecha&filter=$caso";
}
else if ($caso == 3) {
	$menu0 = "Ordenes de Compra";
	$pagina0 = "ocompra.php";
	$menuini = "Detalle";
	$paginaini = "detcompra.php?cod=$Num_Odr&fec=$fecha";
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
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
</script>
<script language="JavaScript">
function volver(pagina) {
	f3.action=pagina;
	f3.submit();
}

function HabilitacionCtd (obj) {
	if (obj.checked) {
		eval("f3.dfSze"+obj.value).value = "0";
		eval("f3.dfSze"+obj.value).readOnly = true;
	}
	else {
		eval("f3.dfSze"+obj.value).value = "";
		eval("f3.dfSze"+obj.value).readOnly = false;
	}
}

</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="left" style="PADDING-LEFT: 5px">
<strong><a href="<?php echo $pagina0; ?>"><?php echo $menu0; ?></a></strong> / 
<strong><a href="<?php echo $paginaini; ?>"><?php echo $menuini; ?></a> / Grilla Style Color</P>
<P align="center">
<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
	<tr><td>
	<table WIDTH="100%">
	<tr>
		<td width="44%" class="label_left_right_top_bottom" valign="top">
		<table width="100%">
			<tr>
			<td align= "left" valign="top" width="40%">
                <img src="<?php echo printimg_addr("img1_grupo",$p_grpprd) ?>" height="200px" />
			</td>
			<td align= "left" valign="top">
				<?php
				echo "<strong>Style: </strong>".$Cod_Sty."<BR>";
				echo "<strong>Nombre: </strong>".$Nom_Dsg."<BR>";
				echo "<strong>Linea: </strong>".$Des_LinMca."<BR>";
				?>
			</td>
			</tr>
		</table>
		</td>
		<td width="2%">&nbsp;</td>
		<td width="44%" class="label_left_right_top_bottom" valign="top">
		<table width="100%">
			<tr>
			<td align= "left" valign="top" width="25%">
                <img src="<?php echo printimg_addr("img_pattern",$p_codpat) ?>" height="80px" width="80px" />
			</td>
			<td align= "left" valign="top">
				<?php
				echo "<strong>Color: </strong>".$Key_Pat."<BR>";
				echo "<strong>Nombre: </strong>".$Des_Pat."<BR>";
				echo "<strong>Grupo: </strong>".$Cod_GrpPat."<BR>";
				?>
			</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="3px">
		&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="3" width="100%" valign="top">
		<form ID="F3" AUTOCOMPLETE="off" method="POST" name="F3" action="reponer_gri.php?filter=<?php echo $caso; ?>">
		<table width="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<?php
			$col = 100;
			$tallas = "";
			$tabindex = 1;
			$XisNoManuales = false;
			if ($caso == 3)
				$query = mssql_query("vm_valsze_dsgodc '$p_coddsg', '$p_codpat', $Num_Odr, '$fecha'",$db);
			else
				$query = mssql_query("vm_valsze_dsgodr '$p_coddsg', '$p_codpat', $Num_Odr, '$fecha'",$db);
			while ($row = mssql_fetch_array($query)) {
				$col+=1;
				if ($col > 7) {
					if ($col < 100) {
						echo "</TR>\n";
						echo "<TR>\n<TD class=\"label_left_right\" width=\"16%%\"><strong>Solicitado</strong></TD>\n";
						//$aTallas = split('/', $tallas);
						foreach ($aListaTallas as $key => $value) {
							$valorini = ($caso == 1 || $caso == 3 || $caso == 20) ? "" : "Manual";
							if ($value > 0) $valorini = $value; 
							echo "<TD width=\"12%%\" class=\"dato3\" style=\"TEXT-ALIGN: center\">\n";
							echo "<input name=\"TxtSze".$key."\" size=\"6\" tabindex=\"".$tabindex++."\" value=\"".$valorini."\" \n";
							if ($caso == 1 || $caso == 3 || $caso == 20) echo "class=\"textfield\"></TD>\n";
							else echo "class=\"textfieldRO2\" style=\"BORDER-BOTTOM: 1px; BORDER-LEFT: 1px; BORDER-TOP: 1px; BORDER-RIGHT: 1px; PADDING-TOP: 3px; PADDING-BOTTOM: 3px\" border=\"0\"></TD>\n";
							if ($value != "Manual") $XisNoManuales = true;
						}
						echo "</TR>\n";
						if ($caso == 2) {
							echo "<TR>\n<TD class=\"label_left_right\" width=\"16%%\"><strong>Repuesto</strong></TD>\n";
							//$aTallas = split('/', $tallas);
							foreach ($aListaRepos as $key => $value) {
								$valorini = "";
								if ($value > 0) $valorini = $value; 
								echo "<TD width=\"12%%\" class=\"dato5\" style=\"TEXT-ALIGN: center\"><input class=\"textfield\" name=\"dfSze".$key."\" size=\"6\" tabindex=\"".$tabindex++."\" value=\"".$valorini."\"".($caso == 3 ? " readOnly" : "")."></TD>\n";
							}
							echo "</TR>\n";
							if ($tipo == 0 && $XisNoManuales) {
								echo "<TR>\n<TD  width=\"16%%\" class=\"label_left_right_top\" style=\"TEXT-ALIGN: center\"><strong>Sin Existencia</strong></TD>\n";
								//$aTallas = split('/', $tallas);
								foreach ($aListaRepos as $key => $value) {
									$valorini = "";
									if ($value > 0) $valorini = $value; 
									echo "<TD width=\"12%%\" class=\"dato5\" style=\"TEXT-ALIGN: center; height: 14px\">\n";
									if ($aListaTallas[$key] == "Manual") echo "&nbsp;";
									else
										echo "<input type=\"checkbox\" onclick=\"HabilitacionCtd(this)\" class=\"dato\" name=\"cbXis".$key."\" tabindex=\"".$tabindex++."\" value=\"".$key."\"".($aListaRepos[$key] == 0 ? " checked" : "").">\n";
									echo "</TD>\n";
								}
								if ($col < 7) echo "<TD class=\"dato3\" colspan=\"".(7-$col)."\">&nbsp;</TD>\n";
								echo "</TR>\n";
							}
							//echo "<TR>\n<TD class=\"label_left_right_top\" style=\"TEXT-ALIGN: center\" width=\"16%%\"><strong>Comprado</strong></TD>\n";
							//$aTallas = split('/', $tallas);
							//foreach ($aListaCompra as $key => $value) {
							//	$valorini = "";
							//	if ($value > 0) $valorini = $value; 
							//	echo "<TD width=\"12%%\" class=\"dato5\" style=\"TEXT-ALIGN: center\"><input class=\"textfield\" name=\"dfSzeBuy".$key."\" size=\"6\" tabindex=\"".$tabindex++."\" value=\"".$valorini."\"></TD>\n";
							//}
							//echo "</TR>\n";
						}
						echo "<TR>\n<TD class=\"label_left_right_top\" colspan=\"8\">&nbsp;</TD>\n</TR>\n";
					}
					echo "<TR><TD class=\"subtitulo_tabla\">Size</TD>";
					$tallas = "";
					$aListaTallas = array ($row['Cod_Sze'] => $row['Ctd_Prd']);
					$aListaRepos = array ($row['Cod_Sze'] => $row['Ctd_Rep']);
					//$aListaCompra = array ($row['Cod_Sze'] => $row['Ctd_Buy']);
					$col = 1;
				}
				echo "<TD class=\"subtitulo_tabla\">".$row['Val_Sze']."</TD>";
				//$tallas.=$row['Cod_Sze']."/";
				$aListaTallas[$row['Cod_Sze']] = $row['Ctd_Prd'];
				$aListaRepos[$row['Cod_Sze']] = $row['Ctd_Rep'];
				//$aListaCompra[$row['Cod_Sze']] = $row['Ctd_Buy'];
			}
			if ($col < 7) echo "<TD class=\"label_right_top\" colspan=\"".(7-$col)."\">&nbsp;</TD>\n";
			echo "</TR>\n";
			echo "<TR>\n<TD  width=\"16%%\" class=\"label_left_right\"><strong>Solicitado</strong></TD>\n";
			//$aTallas = split('/', $tallas);
			foreach ($aListaTallas as $key => $value) {
				$valorini = ($caso == 1 || $caso == 3 || $caso == 20) ? "" : "Manual";
				if ($value > 0) $valorini = $value; 
				echo "<TD width=\"12%%\" class=\"dato3\" style=\"TEXT-ALIGN: center\">\n";
				echo "<input name=\"TxtSze".$key."\" size=\"6\" tabindex=\"".$tabindex++."\" value=\"".$valorini."\"";
				if ($caso == 1 || $caso == 3 || $caso == 20) echo " class=\"textfield\"></TD>\n";
				else echo " class=\"textfieldRO2\" style=\"BORDER-BOTTOM: 1px; BORDER-LEFT: 1px; BORDER-TOP: 1px; BORDER-RIGHT: 1px; TEXT-ALIGN: center; PADDING-TOP: 3px; PADDING-BOTTOM: 3px\" border=\"0\"></TD>\n";
				if ($value != "Manual") $XisNoManuales = true;
			}
			if ($col < 7) echo "<TD class=\"dato3\" colspan=\"".(7-$col)."\">&nbsp;</TD>\n";
			echo "</TR>\n";
			if ($caso == 2) {
				echo "<TR>\n<TD  width=\"16%%\" class=\"label_left_right_top\" style=\"TEXT-ALIGN: center\"><strong>Repuesto</strong></TD>\n";
				//$aTallas = split('/', $tallas);
				foreach ($aListaRepos as $key => $value) {
					$valorini = "";
					if ($value > 0) $valorini = $value; 
					echo "<TD width=\"12%%\" class=\"dato5\" style=\"TEXT-ALIGN: center;\"><input class=\"textfield\" name=\"dfSze".$key."\" size=\"6\" tabindex=\"".$tabindex++."\" value=\"".$valorini."\"".($caso == 3 ? " readOnly" : "")."></TD>\n";
				}
				if ($col < 7) echo "<TD class=\"dato3\" colspan=\"".(7-$col)."\">&nbsp;</TD>\n";
				echo "</TR>\n";
				if ($tipo == 0&& $XisNoManuales) {
					echo "<TR>\n<TD  width=\"16%%\" class=\"label_left_right_top\" style=\"TEXT-ALIGN: center\"><strong>Sin Existencia</strong></TD>\n";
					//$aTallas = split('/', $tallas);
					foreach ($aListaRepos as $key => $value) {
						$valorini = "";
						if ($value > 0) $valorini = $value; 
						echo "<TD width=\"12%%\" class=\"dato5\" style=\"TEXT-ALIGN: center; height: 14px\">\n";
						if ($aListaTallas[$key] == "Manual") echo "&nbsp;";
						else
							echo "<input type=\"checkbox\" onclick=\"HabilitacionCtd(this)\" class=\"dato\" name=\"cbXis".$key."\" tabindex=\"".$tabindex++."\" value=\"".$key."\"".($aListaRepos[$key] == 0 ? " checked" : "").">\n";
						echo "</TD>\n";
					}
					if ($col < 7) echo "<TD class=\"dato3\" colspan=\"".(7-$col)."\">&nbsp;</TD>\n";
					echo "</TR>\n";
				}
				//echo "<TR>\n<TD class=\"label_left_right_top\" style=\"TEXT-ALIGN: center\" width=\"16%%\"><strong>Comprado</strong></TD>\n";
				//$aTallas = split('/', $tallas);
				//foreach ($aListaCompra as $key => $value) {
				//	$valorini = "";
				//	if ($value > 0) $valorini = $value; 
				//	echo "<TD width=\"12%%\" class=\"dato5\" style=\"TEXT-ALIGN: center\"><input class=\"textfield\" name=\"dfSzeBuy".$key."\" size=\"6\" tabindex=\"".$tabindex++."\" value=\"".$valorini."\"></TD>\n";
				//}
				//echo "<TD class=\"dato3\" colspan=\"".(7-$col)."\">&nbsp;</TD></TR>\n";
			}
		?>
			<TR>
				<TD style="PADDING-TOP: 10px; TEXT-ALIGN: right" colspan="8" class="label_top">
				<input type="button" name="returnToOrder" value="Volver" tabindex="90" class="button2" onclick="javascript:volver('<?php echo $paginaini; ?>')" />&nbsp;
				<input type="submit" name="guardar" value="Guardar" tabindex="91" class="button2" />
				<input type="hidden" name="dfCod_Sty" id="dfCod_Sty" value="<?php echo $Cod_Sty ?>" />
				<input type="hidden" name="dfCod_Mca" id="dfCod_Mca" value="<?php echo $Cod_Mca ?>" />
				<input type="hidden" name="dfCod_Pat" id="dfCod_Pat" value="<?php echo $p_codpat ?>" />
				<input type="hidden" name="dfFec_Mov" id="dfFec_Mov" value="<?php echo $fecha ?>" />
				<input type="hidden" name="dfNum_Odr" id="dfNum_Odr" value="<?php echo $Num_Odr ?>" />
				</TD>
			</TR>
		</table>
		</form>
		</td>
	</tr>
	</table>
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
	f1 = document.F1;	
	f3 = document.F3;
	
	for (i=0; i<f3.elements.length; i++) {
		if (f3.elements[i].name.substr(0,5) == "cbXis") 
			if (f3.elements[i].checked) HabilitacionCtd(f3.elements[i]);
    }
	
	<?php 
	
	?>
</script>
</body>
</html>
