<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$UsrId     = (isset($_SESSION['UsrIntra'])) ? $UsrId = $_SESSION['UsrIntra'] : "";
$Perfil    = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot   = (isset($_GET['cot'])) ? ok($_GET['cot']) : ok($_POST['dfCod_Cot']);
$Cod_Clt   = (isset($_GET['clt'])) ? ok($_GET['clt']) : ok($_POST['dfCod_Clt']);
$Fol_Cna   = (isset($_POST['dfFol_Cna'])) ? ok($_POST['dfFol_Cna']) : 0;
$respuesta = (isset($_POST['respuesta'])) ? ok($_POST['respuesta']) : "";
$Res_Cna   = "";

if ($respuesta != "") {
	$respuesta = str_replace("'", "#", $respuesta);
	$result = mssql_query("vm_i_rescna $Cod_Cot, $Cod_Clt, $Fol_Cna, '$respuesta'",$db);
} else {
	$result = mssql_query("vm_s_cotcna $Cod_Cot, $Cod_Clt",$db);
	while ($row = mssql_fetch_array($result)) 
		if ($row['Tip_Cna'] == 'C') {
			$Fec_Cna = $row['Fec_Dis'];
			$Det_Cna = str_replace("#", "'", $row['Det_Cna']);
			$Fol_Cna = $row['Fol_Cna'];
			break;
		}	
	if ($Fol_Cna > 0) { 
		$result = mssql_query("vm_s_rescna $Cod_Cot, $Cod_Clt, $Fol_Cna",$db);
		if ($row = mssql_fetch_array($result)) $Det_Res = $row['Det_Res'];
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<link href="../css/headers.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<link href="../css/clearfix.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
function CheckRespuesta(form) {
	if (form.respuesta.value == "") {
		alert("Debe ingresar un respuesta");
		return false;
	}
	return true;
}
<?php 
	if ($respuesta != "")  {
		$page  = "nueva_cot.php?cot=".$Cod_Cot;
		echo "\tparent.opener.document.F2.action=\"".$page."\"\n";
		echo "\tparent.opener.document.F2.submit();\n";
		echo "\twindow.close();\n";
	}
?>
</script>
</head>
<BODY>

<?php formar_topbox ("100%%","center"); ?>
<fieldset class="label_left_right_top_bottom">
<legend>Consulta</legend>
	<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="historial_cna.php" onsubmit="return CheckRespuesta(this)">
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" width="100%" ALIGN="center">
	<tr><td align="left" width="100px"><b>Fecha:</b></td><td align="left"><?php echo $Fec_Cna ?></td></tr>
	<tr><td align="left"><b>Consulta:</b></td><td align="left"><?php echo $Det_Cna ?></td></tr>
	<tr><td align="left"><b>Respuesta:</b></td><td align="left"><textarea name="respuesta" rows="5" cols="37"><?php echo $Det_Res; ?></textarea></td></tr>
	<tr><td align="right" colspan="2">
	<input type="submit" name="Responder" value="Responder" class="btn">&nbsp;
	<input type="button" name="Cerrar" value="Cerrar" class="btn" onclick="window.close()">
	<input type="hidden" name="dfCod_Cot" value="<?php echo $Cod_Cot; ?>">
	<input type="hidden" name="dfCod_Clt" value="<?php echo $Cod_Clt; ?>">
	<input type="hidden" name="dfFol_Cna" value="<?php echo $Fol_Cna; ?>">
	</td></tr>
	</TABLE>
	</form>
</fieldset>
<?php formar_bottombox (); ?>
<script language="javascript">
	var f2;
	f2 = document.F2;
</script>
</BODY>
</HTML>
