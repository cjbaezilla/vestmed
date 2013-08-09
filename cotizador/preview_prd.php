<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$UsrId = (isset($_SESSION['UsrIntra'])) ? $UsrId = $_SESSION['UsrIntra'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$Cod_Prd = (isset($_GET['prd'])) ? ok($_GET['prd']) : 0;

if ($Cod_Cot > 0) {
	$result = mssql_query("vm_s_cotcna $Cod_Cot",$db);
	if ($row = mssql_fetch_array($result)) {
		$Cod_GrpPrd = $row['Cod_GrpPrd'];
		$Nom_GrpPrd = $row['Nom_GrpPrd'];
		$Des_GrpPrd = $row['Des_GrpPrd'];
		$Cod_Mca	= $row['Cod_Mca'];
		$Cod_LinMca = $row['Cod_LinMca'];
		$Cod_Sty	= $row['Cod_Sty'];
		$Cod_Pat	= $row['Cod_Pat'];
		$Key_Pat	= $row['Key_Pat'];
		$Des_Pat	= $row['Des_Pat'];
		$Val_Sze	= $row['Val_Sze'];
	}
}
else {
	$result = mssql_query("vm_prd_s '$Cod_Prd'", $db);
	if ($row = mssql_fetch_array($result)) {
		$Cod_Dsg = $row['Cod_Dsg'];
		$Cod_Pat = $row['Cod_Pat'];
		$Cod_Sze = $row['Cod_Sze'];
		$result = mssql_query("vm_dsg_s '$Cod_Dsg'", $db);
		if ($row = mssql_fetch_array($result)) {
			$Cod_Mca = $row['Cod_Mca'];
			$Cod_LinMca = $row['Cod_LinMca'];
			$Cod_Sty	= $row['Cod_Sty'];
			$Cod_GrpPrd = $row['Cod_GrpPrd'];
			$result = mssql_query("vm_grpprd_s '$Cod_GrpPrd'", $db);
			if ($row = mssql_fetch_array($result)) {
				$Nom_GrpPrd = $row['Nom_GrpPrd'];
			}
			
			$result = mssql_query("vm_pat_s '$Cod_Pat'", $db);
			if ($row = mssql_fetch_array($result)) {
				$Des_Pat	= $row['Des_Pat'];
				$Key_Pat	= $row['Key_Pat'];
			}
			
			$result = mssql_query("vm_sze_s '$Cod_Sze'", $db);
			if ($row = mssql_fetch_array($result)) {
				$Val_Sze	= $row['Val_Sze'];
			}
		}
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
</script>
</head>
<BODY>

<?php formar_topbox ("100%%","center"); ?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="80%" ALIGN="center">
<tr>
	<td class="titulo-producto" colspan="2"><?php echo str_replace("#","'",$Nom_GrpPrd) ?></td>
</tr>
<tr>
	<td class="descripcion-producto" colspan="2" style="PADDING-TOP:10px">STYLE: <?php echo $Cod_Sty ?></td>
</tr>
<tr>
	<td class="descripcion-producto" width="40%" ROWSPAN="2" style="PADDING-TOP:5px"><img src="<?php echo printimg_addr("img1_grupo",$Cod_GrpPrd) ?>" width="200" class="cursor image-producto" /></td>
	<td height="50%" valign="top">
	<fieldset class="label_left_right_top_bottom">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
		<tr><td width="50%" class="descripcion-producto" style="TEXT-ALIGN: center">COLOR SELECCIONADO</td>
			<td width="50%" class="descripcion-producto" style="TEXT-ALIGN: center">TALLA</td>
		</tr>
		<tr>
		<td width="50%" style="TEXT-ALIGN: center">
					<img src="<?php echo printimg_addr("img_pattern",$Cod_Pat) ?>" height="80px" width="80px" /><br />
					<b><?php echo $Key_Pat ?></b><br /><?php echo $Des_Pat ?>
		</td>
		<td width="50%">
		  <div id="wrap-talla-seleccionada">
			  <div id="talla" style="COLOR: black"><?php echo $Val_Sze ?></div>
		  </div>
		</td></tr>
		</TABLE>
	</fieldset>
	</td>
</tr>
<tr>
<td width="60%" valign="top" height="50%" align="left">
	<fieldset class="label_left_right_top_bottom" style="TEXT-ALIGN: left">
<?php echo $Des_GrpPrd ?><br /><br /><b>Marca: </b><?php echo $Cod_Mca ?><br /><b>Linea: </b><?php echo $Cod_LinMca ?>
	</fieldset>
</td>
</tr>
</TABLE>
<?php formar_bottombox (); ?>

</BODY>
</HTML>
