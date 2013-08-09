<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;

$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if ($row = mssql_fetch_array($result)) {
	$fec_cot   = date("d/m/Y", strtotime($row['Fec_Cot']));
	$cod_clt   = $row['Cod_Clt'];
	if ($row['Cod_TipPer'] == 1)
		$nom_clt = $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'];
	else
		$nom_clt = $row['RznSoc_Per'];

	$num_doc   = $row['Num_Doc'];
	$cod_suc   = $row['Cod_Suc'];
	$dir_suc   = $row['Dir_Suc'];
	$cod_cmn   = $row['Cod_Cmn'];
	$cod_cdd   = $row['Cod_Cdd'];
	$cod_per   = $row['Cod_Per'];
	$fon_ctt   = $row['Fon_Ctt'];
	$mail_ctt  = $row['Mail_Ctt'];
	$cod_pre   = $row['Cod_Pre'];
	$obs_cot   = ($row['Obs_Cot'] == "_NONE" ? "" : $row['Obs_Cot']);

	$result = mssql_query("vm_suc_s $cod_clt, $cod_suc", $db);
	if ($row = mssql_fetch_array($result)) $nom_suc = $row['Nom_Suc'];

	$result = mssql_query("vm_ctt_s $cod_clt, $cod_suc", $db);
	while($row = mssql_fetch_array($result))
		if ($row['Cod_Per'] == $cod_per) $nom_ctt = $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'];
							
	$result = mssql_query("vm_s_rescot $Cod_Cot, $cod_clt, $cod_per",$db);
	if ($row = mssql_fetch_array($result)) {
		$Cod_Iva  = $row['Cod_Iva'];
		$Val_Usd  = $row['Val_Usd'];
		$Cod_Cri  = $row['Cod_Cri'];
		$Fec_Cie  = date("d/m/Y", strtotime($row['Fec_Cie']));
		$Val_Pro  = $row['Val_Pro'];
		$Obs_Res  = $row['Obs_Res'];
		$Val_DesG = $row['Val_Des'];
	}
}
$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if ($row = mssql_fetch_array($result)) $IVA = $row['Tbl_fol'] / 10000.0;

function GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Val_Des, $Tallas) {
	$Des_Sze = "";
	foreach ($Tallas as $key => $value) $Des_Sze = $Des_Sze.$key."(".$value.") ";
	echo "<TABLE BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" width=\"100%%\" height=\"150\" ALIGN=\"center\">\n";
	echo "<tr><td width=\"20%%\" class=\"titulo_tabla5p\">Producto</td><td  class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\"><b>$Cod_Sty - $Nom_Dsg<b></td></tr>\n";
	echo "<tr><td valign=\"top\" class=\"titulo_tabla5p\">Descripci&oacute;n</td><td  class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\">$Des_GrpPrd</td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Marca</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Cod_Mca</td><td class=\"titulo_tabla5p\">Origen</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Org_Mca</td></tr>\n";
	echo "<tr><td valign=\"top\" class=\"titulo_tabla5p\">Material</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Des_Mat</td><td valign=\"top\" class=\"titulo_tabla5p\">Descuento</td><td valign=\"top\" class=\"label_bottom\" style=\"text-align: left; padding-left: 5px; padding-top: 3px\">$Val_Des</td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Pattern</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\">$Des_Pat</td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Tallas</td><td class=\"dato5p\" colspan=\"3\">$Des_Sze</td></tr>\n";
	echo "</TABLE>\n";
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
</head>
<BODY>

<?php formar_topbox ("100%%","center"); ?>
<P align="center">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
<TR>
	<TD width="60%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><IMG SRC="logo.gif" width="235" HEIGHT="130"></TD>
	<TD class="dato" style="text-align: right; FONT-SIZE: 2.5em; FONT-FAMILY: Arial, Verdana;" width="40%" VALIGN="bottom" COLSPAN="2"><b>COTIZACI&Oacute;N <?php echo $Cod_Cot; ?></b></TD>
</TR>
<TR>
	<TD class="dato" style="text-align: right" width="40%" VALIGN="TOP" COLSPAN="2" HEIGHT="65"><b>Fecha: </b><?php echo $fec_cot ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p12s" style="padding-top: 20px;"><B>Cliente: <?php echo $nom_clt ?></B></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" style="padding-top: 20px">&nbsp;</TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Rut: <?php echo formatearRut($num_doc) ?></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2">&nbsp;</TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Sucursal: <?php echo $nom_suc ?></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Contacto: <?php echo $nom_ctt ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Direcci&oacute;n: <?php echo $dir_suc ?></FONT></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Tel&eacute;fono: <?php echo $fon_ctt ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Ciudad: Santiago</TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Fax: <?php echo $fon_ctt ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Comuna: Vitacura</TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Email: <?php echo $mail_ctt; ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP">&nbsp;</TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2">&nbsp;</TD>
</TR>
<TR>
	<TD VALIGN="TOP" COLSPAN="3" class="dato5p12s"><B>Productos Seleccionados</B></TD>
</TR>
<TR><TD VALIGN="TOP" COLSPAN="3" style="padding-bottom: 10px">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
<?php
$item = 0;
$Neto = 0;
$Cod_Mca = "";
$Cod_GrpPrd = "";
$Cod_Dsg = "";
$Cod_Pat = "";
$Prc_Uni = 0;
$Val_Des = 0;
$Tallas = "";
$bPrimero = true;
$result = mssql_query("vm_pvw_rescot $Cod_Cot",$db);
while ($row = mssql_fetch_array($result)) {
	if ($Cod_Mca != $row['Cod_Mca'] Or $Cod_GrpPrd != $row['Cod_GrpPrd'] Or	$Cod_Dsg != $row['Cod_Dsg'] Or 
		$Cod_Pat != $row['Cod_Pat'] Or $Prc_Uni != $row['Prc_Uni'] Or $Val_Des != $row['Val_Des']) {
		if (!$bPrimero) {
			$item++;
			$Prc_UniDes = intval($Prc_Uni - $Prc_Uni*$Val_Des/100.0 + 0.5);
			$Neto = $Neto + $Tot_Ctd * $Prc_UniDes;
?>
<tr>
	<td rowspan="2" class="label_left_top" style="text-align: center" width="5%"><?php echo $item ?></td>
	<td rowspan="2" class="label_left_top" style="text-align: center" width="10%">
	<img src="<?php echo printimg_addr("img1_grupo",$Cod_GrpPrd) ?>" width="100" class="cursor image-producto" />
	</td>
	<td rowspan="2" class="label_left_top" valign="top" style="text-align: center" width="55%"><?php GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Val_Des, $Tallas); ?></td>
	<td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">Cantidad</td>
	<td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">P.Unitario</td>
	<td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">Total</td>
</tr>
<tr>
	<td class="label_left" style="text-align: center" width="5%"><?php echo $Tot_Ctd ?></td>
	<td class="label_left" style="text-align: center" width="10%">
	<?php if ($Val_Des > 0) { ?>
	<STRIKE><?php echo formatearMillones($Prc_Uni); ?></STRIKE><BR><?php echo formatearMillones($Prc_UniDes); ?>
	<?php } else echo formatearMillones($Prc_UniDes) ?>
	</td>
	<td class="label_left_right" style="text-align: center" width="10%"><?php echo formatearMillones($Tot_Ctd * $Prc_UniDes) ?></td>
</tr>
<?php 
		}
		$Cod_Mca    = $row['Cod_Mca'];
		$Cod_GrpPrd = $row['Cod_GrpPrd'];
		$Cod_Dsg 	= $row['Cod_Dsg'];
		$Cod_Pat 	= $row['Cod_Pat'];
		$Prc_Uni 	= $row['Prc_Uni'];
		$Prc_Nto 	= $row['Prc_Nto'];
		$Val_Des 	= $row['Val_Des'];
		$Org_Mca    = $row['Org_Mca'];
		$Cod_Sty	= $row['Cod_Sty'];
		$Nom_Dsg	= $row['Nom_Dsg'];
		$Des_GrpPrd = $row['Des_GrpPrd'];
		$Des_Mat	= $row['Des_Mat'];
		$Des_Pat	= $row['Des_Pat'];
		if (isset($Tallas)) unset($Tallas);
		$bPrimero = false;
		$Tot_Ctd	= 0;
	} 
	if (!isset($Tallas)) $Tallas = array($row['Val_Sze'] => $row['Val_Ctd']);
	else $Tallas[$row['Val_Sze']] = $Tallas[$row['Val_Sze']] + $row['Val_Ctd'];
	$Tot_Ctd += $row['Val_Ctd'];
}
$item++;
$Prc_UniDes = intval($Prc_Uni - $Prc_Uni*$Val_Des/100.0 + 0.5);
$Neto = $Neto + $Tot_Ctd * $Prc_UniDes;
?>
<tr>
	<td rowspan="2" class="label_left_top" style="text-align: center" width="5%"><?php echo $item ?></td>
	<td rowspan="2" class="label_left_top" style="text-align: center" width="10%">
	<img src="<?php echo printimg_addr("img1_grupo",$Cod_GrpPrd) ?>" width="100" class="cursor image-producto" />
	</td>
	<td rowspan="2" class="label_left_top" valign="top" style="text-align: center" width="55%"><?php GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Val_Des, $Tallas); ?></td>
	<td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">Cantidad</td>
	<td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">P.Unitario</td>
	<td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">Total</td>
</tr>
<tr>
	<td class="label_left" style="text-align: center" width="5%"><?php echo $Tot_Ctd ?></td>
	<td class="label_left" style="text-align: center" width="10%">
	<?php if ($Val_Des > 0) { ?>
	<STRIKE><?php echo formatearMillones($Prc_Uni); ?></STRIKE><BR><?php echo formatearMillones($Prc_UniDes); ?>
	<?php } else echo formatearMillones($Prc_UniDes) ?>
	</td>
	<td class="label_left_right" style="text-align: center" width="10%"><?php echo formatearMillones($Tot_Ctd * $Prc_UniDes) ?></td>
</tr>
<tr><td colspan="6" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">&nbsp;</td></tr>
</TABLE>
</TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p12s"><B>Condiciones Generales</B></TD>
	<TD width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
	Desc. Especial: <INPUT name="dfDescEsp" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo $Val_DesG ?> %" ReadOnly /></TD>
</TR>
<?php $Neto = intval($Neto - $Neto * $Val_DesG/100.0 + 0.5) ?>
<TR>
	<TD class="dato10p" width="60%" VALIGN="TOP"><LI>Precio <?php echo ($cod_pre == 1) ? "Minorista" : "Mayorista"; ?> <?php echo ($Cod_Iva == 1) ? "IVA incluido" : "mas IVA"; ?></LI></TD>
	<TD width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
	Neto: <INPUT name="dfNeto" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo formatearMillones($Neto) ?>" ReadOnly /></TD>
</TR>
<TR>
	<TD class="dato10p" width="60%" VALIGN="TOP"><LI>Cotizaci&oacute;n valida por 15 dias</LI></TD>
	<TD width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
	IVA: <INPUT name="dfIVA" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo formatearMillones(intval(($IVA * $Neto) + 0.5)); ?>" ReadOnly /></TD>
</TR>
<TR>
	<TD class="dato10p" width="60%" VALIGN="TOP"><LI>Precios sujetos a la variaci&oacute;n del dolar</LI></TD>
	<TD width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
	Total: <INPUT name="dfTotal" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo formatearMillones(intval($Neto + ($IVA * $Neto) + 0.5)); ?>" ReadOnly /></TD>
</TR>
<TR>
	<TD class="dato5p12s" width="60%" VALIGN="TOP" style="padding-top: 5px"><B>Observaciones</B></TD>
	<TD width="17%" VALIGN="TOP">&nbsp;</TD>
	<TD width="17%" VALIGN="TOP">&nbsp;</TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP">
	<textarea name="comentarios" id="comentarios" cols="80" rows="3" class="dato" ReadOnly><?php echo $obs_cot; ?></textarea></TD>
	<TD width="17%" VALIGN="TOP">&nbsp;</TD>
	<TD width="17%" VALIGN="TOP">&nbsp;</TD>
</TR>
<TR>
	<TD VALIGN="TOP" COLSPAN="3" class="avisopie" style="padding-left: 5px; padding-top: 10px">
	De conformidad con lo dispuesto en el articulo 2 bits, letra b) de la Ley N&#186; 19.496, 
	Vestmed Ltda, dispone expresamente que quienes admieran productos personalizados a trav&eacute;s de nuestro 
	canal de internet, tel&eacute;fono o por medio de cualquiera de nuestras tiendes de venta directa, 
	no tendr&aacute;n derecho a cambio o retractarse de su compra. Esta disposici&oacute;n no invalida de forma 
	alguna la responsabilidad por fallas de fabrica &#45; garant&iacute;as.</TD>
</TR>
</TABLE>
</p>
<?php formar_bottombox (); ?>

</BODY>
</HTML>
