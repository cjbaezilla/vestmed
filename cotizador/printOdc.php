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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 1 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;

$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if (($row = mssql_fetch_array($result))) {
    $fec_cot   = date("d/m/Y", strtotime($row['Fec_Cot']));
    $Num_Cot   = $row['Num_Cot'];
    $cod_odc = $row['Cod_Odc'];
    $cod_clt   = $row['Cod_Clt'];
    $cod_tipper = $row['Cod_TipPer'];
    if ($cod_tipper == 1)
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
    $peso      = $row['Cot_Peso'];
    $estatura  = $row['Cot_Estatura'];
    $is_otro   = $row['Cot_FlgTer'];
    $is_dsp    = $row['is_dsp'];
    if ($is_dsp == 1) {
            $cod_crr    = $row['Cod_Crr'];
            $cod_svccrr = $row['Cod_SvcCrr'];
            $cod_sucdsp = $row['Cod_SucDsp'];
            $cod_cmndsp = $row['Cod_CmnDsp'];
            $cod_cdddsp = $row['Cod_CddDsp'];
            $cod_tipsvc = $row['Cod_TipSvcCrr'];
            $dir_sucdsp = $row['Dir_SucDsp'];
            $val_dsp    = $row['Val_Dsp'];

            $result = mssql_query("vm_CrrCmb $cod_crr", $db);
            if (($row = mssql_fetch_array($result))) $des_crr = $row['Des_Crr'];

            $result = mssql_query("vm_SvcCrrCmb $cod_crr, $cod_svccrr", $db);
            if (($row = mssql_fetch_array($result))) $des_svccrr = $row['Des_SvcCrr'];

            $result = mssql_query("vm_cmn_s $cod_cmndsp", $db);
            if (($row = mssql_fetch_array($result))) $nom_cmndsp = $row['Nom_Cmn'];

            $result = mssql_query("vm_cdd_s $cod_cdddsp", $db);
            if (($row = mssql_fetch_array($result))) $nom_cdddsp = $row['Nom_Cdd'];

            if ($cod_sucdsp > 0) {
                    $result = mssql_query("vm_suc_s $cod_clt, $cod_sucdsp", $db);
                    if (($row = mssql_fetch_array($result))) $nom_sucdsp = $row['Nom_Suc'];
            }
            else $nom_sucdsp = "Oficina Carrier";
    }

    $result = mssql_query("vm_cmn_s $cod_cmn", $db);
    if (($row = mssql_fetch_array($result))) $nom_cmn = $row['Nom_Cmn'];

    $result = mssql_query("vm_cdd_s $cod_cdd", $db);
    if (($row = mssql_fetch_array($result))) $nom_cdd = $row['Nom_Cdd'];

    $result = mssql_query("vm_suc_s $cod_clt, $cod_suc", $db);
    if (($row = mssql_fetch_array($result))) $nom_suc = $row['Nom_Suc'];

    $result = mssql_query("vm_ctt_s $cod_clt, $cod_suc", $db);
    while(($row = mssql_fetch_array($result)))
            if ($row['Cod_Per'] == $cod_per) $nom_ctt = $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'];

    $result = mssql_query("vm_s_rescot $Cod_Cot, $cod_clt, $cod_per",$db);
    if (($row = mssql_fetch_array($result))) {
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
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

$result = mssql_query("vm_mtoodc $cod_odc",$db);
if (($row = mssql_fetch_array($result))) {
    $mto_nvt = $row['Mto_Nvt'];
    $mto_odc = $row['Mto_Nvt'] + $row['Prc_Dsp'] - $row['Mto_Nvt'] * $row['Val_Des'] / 100;
    $ctd_prd = $row['Ctd_Prd'];
    $tot_dsg = $row['Tot_Dsg'];
    if ($row['Cod_Iva'] == 2) $mto_odc+=($mto_odc * $IVA);
}

$result = mssql_query("ps_Nvt $cod_clt, $cod_odc, 0",$db);
if (($row = mssql_fetch_array($result))) $fec_cot = date("d/m/Y", strtotime($row['Fec_Nvt']));

$val_dspCIva = $val_dsp + $val_dsp * $IVA;

function GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Cod_LinMca, $Tallas) {
	$Des_Sze = "";
	//foreach ($Tallas as $key => $value) $Des_Sze = $Des_Sze.$key."(".$value.") ";
	echo "<table BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" width=\"100%%\" height=\"150\" ALIGN=\"center\">\n";
	echo "<tr><td width=\"20%%\" class=\"titulo_tabla5p\">Producto</td><td  class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\"><b>$Cod_Sty - $Nom_Dsg<b></td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Descripci&oacute;n</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\">$Des_GrpPrd</td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Marca</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Cod_Mca</td><td class=\"titulo_tabla5p\">Origen</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Org_Mca</td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Material</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Des_Mat</td><td class=\"titulo_tabla5p\">Linea</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px;\">$Cod_LinMca</td></tr>\n";
	//echo "<tr><td class=\"titulo_tabla5p\">Pattern</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\">$Des_Pat</td></tr>\n";
	//echo "<tr><td class=\"titulo_tabla5p\">Tallas</td><td class=\"dato5p\" colspan=\"3\">$Des_Sze</td></tr>\n";
	echo "</table>\n";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd Xhtml 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<link href="../css/headers.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<link href="../css/clearfix.css" type="text/css" rel="stylesheet" />
<!-- Lytebox Includes //-->
<script type="text/javascript" src="../lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="../lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
<script type="text/javascript">
    //new UvumiDropdown('dropdown-scliente');

	function popwindow(ventana,altura){
	   window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=800,height="+altura);
	}
	
	function ver_producto(cod_prd) {
		popwindow("preview_prd.php?prd="+cod_prd,400)
	}
</script>
</head>
<body>
<div id="body" style="width:100%">
	<!--div id="header"></div-->
    <ul id="usuario_registro">
		<?php 	echo display_mnu_odc($UsrId, $cod_tipper, $Cod_Cot, $db); ?>
    </ul>
	<div id="work">

<?php formar_topbox ("100%%","center"); ?>
<P align="center">
<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
<tr>
	<td width="50%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><IMG SRC="images/logo.gif" width="235" HEIGHT="130"></td>
	<td class="dato" style="text-align: right; FONT-SIZE: 2.0em; FONT-FAMILY: Arial, Verdana;" width="50%" VALIGN="bottom" COLSPAN="2"><b>ORDEN DE COMPRA <?php echo $cod_odc; ?></b></td>
</tr>
<tr>
	<td class="dato" style="text-align: right" width="50%" VALIGN="TOP" COLSPAN="2" HEIGHT="65"><b>Fecha: </b><?php echo $fec_cot ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p12s" style="padding-top: 20px;"><B>Cliente: <?php echo $nom_clt ?></B></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" style="padding-top: 20px">&nbsp;</td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p">Rut: <?php echo formatearRut($num_doc) ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<tr>
    <td width="50%" VALIGN="TOP" class="dato5p">Sucursal: <?php echo $nom_suc ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Contacto: <?php echo $nom_ctt ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p">Direcci&oacute;n: <?php echo $dir_suc ?></FONT></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Tel&eacute;fono: <?php echo $fon_ctt ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p">Ciudad: <?php echo $nom_cdd ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Fax: <?php echo $fon_ctt ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p">Comuna: <?php echo $nom_cmn ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Email: <?php echo $mail_ctt; ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP">&nbsp;</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<?php if ($is_otro == 0) { ?>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="dato5p12s"><B>Informaci&oacute;n Personal</B></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p">Peso: <?php echo $peso ?> Kg</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Estatura: <?php echo $estatura; ?> cm</td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP">&nbsp;</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<?php } ?>
<?php if ($is_dsp == 1) { ?>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="dato5p12s"><B>Informaci&oacute;n de Despacho</B></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p">Carrier: <?php echo $des_crr ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Direcci&oacute;n: <?php echo $dir_sucdsp; ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p">Servicio: <?php echo $des_svccrr; ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Comuna: <?php echo $nom_cmndsp; ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p">Sucursal: <?php echo $nom_sucdsp ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Ciudad: <?php echo $nom_cdddsp ?></td>
</tr>
<tr>
	<?php if ($cod_tipsvc == -1) { ?>
	<td width="50%" VALIGN="TOP" class="dato5p">&nbsp;</td>
	<?php } else { ?>
	<td width="50%" VALIGN="TOP" class="dato5p">Tipo Despacho: <?php echo ($cod_tipsvc == 0) ? "Al domicilio" : "A sucursal del carrier"; ?></td>
	<?php } ?>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Gastos Despacho:
	<?php echo number_format($val_dspCIva,0,',','.'); ?>
        </td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP">&nbsp;</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<?php } ?>
<tr><td VALIGN="TOP" COLSPAN="3" style="padding-bottom: 10px">
<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
<tr><td colspan="3"><font size="2"><b>Detalles de la Orden</b></font></td></tr>
<tr><td colspan="3"><font size="1">Total Styles: <?php echo number_format($tot_dsg,0,',','.'); ?></font></td></tr>
<tr><td colspan="3"><font size="1">Total Unidades: <?php echo number_format($ctd_prd,0,',','.'); ?></font></td></tr>
<?php
$item = 0;
$NetoTot = 0;
$Cod_Mca = "";
$Cod_GrpPrd = "";
$Cod_Dsg = "";
$Cod_Pat = "";
$Val_Sze = "";
$Prc_Uni = 0;
$Val_Des = 0;
$Tallas = "";
$bPrimero = true;
$peso = 0.1;
//print "vm_pvw_detnvt $cod_odc";
$result = mssql_query("vm_pvw_detnvt $cod_odc   ",$db);
while (($row = mssql_fetch_array($result))) {
	if ($Cod_Mca != $row['Cod_Mca'] Or $Cod_GrpPrd != $row['Cod_GrpPrd'] Or	$Cod_Dsg != $row['Cod_Dsg']) {
		if (!$bPrimero) {
			$Des_Sze = "";
			foreach ($Tallas as $key => $value) $Des_Sze = $Des_Sze.$key;
?>
                <tr>
                        <td colspan="3">
                            <table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
                            <td class="dato" width="5%" style="padding-left: 3px; border-left: goldenrod 1px solid;"><?php echo $Key_PatAnt; ?></td>
                            <td class="dato" width="30%" style="padding-left: 7px"><?php echo $Des_PatAnt; ?></td>
                            <td class="dato" width="10%" style="padding-left: 7px"><?php echo $Des_Sze; ?></td>
                            <td class="dato" width="10%" style="padding-left: 7px"><?php echo $Tot_Ctd; ?></td>
                            <td class="dato" width="10%" style="padding-left: 7px"><?php echo "B"; ?></td>
                            <td class="dato" width="25%" style="padding-left: 5px"><?php echo "Procesado"; ?></td>
                            <td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo number_format($Prc_Nto,0,',','.'); ?></td>
                            </tr></table>
                        </td>
                </tr>
<?php
			$Key_PatAnt = $row['Key_Pat'];
			$Des_PatAnt = str_replace("#","'",utf8_encode($row['Des_Pat']));
			$Cod_PatAnt = $row['Cod_Pat'];
			$Prc_UniAnt = $row['Prc_Uni'];
			$Val_Des    = $row['Val_Des'];

			$Tot_Ctd	= 0;
			$NetoTot+=$Prc_Nto;
			if (isset($Tallas)) unset($Tallas);
		}
		$Cod_Mca      = $row['Cod_Mca'];
		$Cod_LinMca   = $row['Cod_LinMca'];
		$Cod_GrpPrd   = $row['Cod_GrpPrd'];
		$Cod_Dsg      = $row['Cod_Dsg'];
		$grpprd_title = $Cod_Sty." ".utf8_encode($Nom_Dsg);
		$Prc_Nto      = $row['Prc_Nto'];
		$Org_Mca      = utf8_encode($row['Org_Mca']);
		$Val_Des      = $row['Val_Des'];
		$Cod_Sty      = $row['Cod_Sty'];
		$Nom_Dsg      = str_replace("#","'",utf8_encode($row['Nom_Dsg']));
		$Des_GrpPrd   = str_replace("#","'",utf8_encode($row['Des_GrpPrd']));
		$Des_Mat      = str_replace("#","'",utf8_encode($row['Des_Mat']));
		$Cod_Prd      = $row['Cod_Prd'];
		$Prc_UniAnt   = $row['Prc_Uni'];
		$Cod_PatAnt   = $row['Cod_Pat'];
		$Key_PatAnt   = $row['Key_Pat'];
		$Des_PatAnt   = str_replace("#","'",utf8_encode($row['Des_Pat']));
		$Val_Sze      = $row['Val_Sze'];
                $peso+=($row["Val_Ctd"]*$row["Peso_Uni"]);

		$bPrimero     = false;
		$Tot_Ct       = 0;
		if (isset($Tallas)) unset($Tallas);

		$item++;
?>
<?php if ($item > 1) { ?>
<tr><td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">&nbsp;</td></tr>
<?php } ?>
<tr>
	<td class="label_left_top" style="text-align: center" width="5%"><?php echo $item ?></td>
	<td class="label_left_top" style="text-align: center" width="10%">
	<a rev="width: 602px; height: 490px; border: 0 none; scrolling: auto;" title="<?php echo $grpprd_title ?>" rel="lyteframe[imagenes<?php echo $item ?>]" href="catalogo/imagenes-producto.php?producto=<?php echo $Cod_GrpPrd ?>"><img src="<?php echo printimg_addr("img1_grupo",$Cod_GrpPrd) ?>" width="100" class="cursor image-producto" alt="" /></a>	</td>
	<td class="label_left_right_top" valign="top" style="text-align: center" width="55%"><?php GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Cod_LinMca, $Tallas); ?></td>
	<!--td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">Cantidad</td-->
	<!--td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">P.Unitario</td-->
	<!--td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">Total</td-->
</tr>
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td class="titulo_tabla5p" width="5%">Color</td>
	<td class="titulo_tabla5p" width="30%">Descripci&oacute;n</td>
	<td class="titulo_tabla5p" width="10%">Tama&ntilde;o</td>
	<td class="titulo_tabla5p" width="10%">Cant</td>
	<td class="titulo_tabla5p" width="10%">Servicios</td>
	<td class="titulo_tabla5p" width="25%">Estado</td>
	<!--<td class="titulo_tabla5p" width="10%" style="padding-right: 3px; text-align: right">Avance</td>-->
	<td class="titulo_tabla5p" width="10%" style="padding-right: 3px; text-align: right">Precio</td>
	</tr></table>
	</td>
</tr>
<?php
	}
	if ($row['Cod_Pat'] != $Cod_PatAnt Or $row['Prc_Uni'] != $Prc_UniAnt Or $Val_Des != $row['Val_Des'] Or $Val_Sze != $row['Val_Sze']) {
		$Des_Sze = "";
		foreach ($Tallas as $key => $value) $Des_Sze = $Des_Sze.$key;
?>
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td class="dato" width="5%" style="padding-left: 3px; border-left: goldenrod 1px solid;"><?php echo $Key_PatAnt; ?></td>
	<td class="dato" width="30%" style="padding-left: 7px"><?php echo $Des_PatAnt; ?></td>
	<td class="dato" width="10%" style="padding-left: 7px"><?php echo $Des_Sze; ?></td>
	<td class="dato" width="10%" style="padding-left: 7px"><?php echo $Tot_Ctd; ?></td>
	<td class="dato" width="10%" style="padding-left: 7px"><?php echo "B"; ?></td>
	<td class="dato" width="25%" style="padding-left: 5px"><?php echo "Procesado"; ?></td>
	<!--<td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo "% 0" ?></td>-->
	<!--<td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo number_format($mto_nvt,0,',','.'); ?></td>-->
	<td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo number_format($Prc_Nto,0,',','.'); ?></td>
	</tr></table>
	</td>
</tr>
<?php
		$Key_PatAnt = $row['Key_Pat'];
		$Des_PatAnt = str_replace("#","'",$row['Des_Pat']);
		$Cod_PatAnt = $row['Cod_Pat'];
		$Prc_UniAnt = $row['Prc_Uni'];
		$Val_Des 	= $row['Val_Des'];
		$Val_Sze    = $row['Val_Sze'];

		$Tot_Ctd	= 0;
		if (isset($Tallas)) unset($Tallas);
	}
	if (!isset($Tallas)) $Tallas = array($row['Val_Sze'] => $row['Val_Ctd']);
	else $Tallas[$row['Val_Sze']] = $Tallas[$row['Val_Sze']] + $row['Val_Ctd'];
	$Tot_Ctd += $row['Val_Ctd'];
}
$Des_Sze = "";
foreach ($Tallas as $key => $value) $Des_Sze = $Des_Sze.$key;
?>
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td class="dato" width="5%" style="padding-left: 3px; border-left: goldenrod 1px solid;"><?php echo $Key_PatAnt; ?></td>
	<td class="dato" width="30%" style="padding-left: 7px"><?php echo $Des_PatAnt; ?></td>
	<td class="dato" width="10%" style="padding-left: 7px"><?php echo $Des_Sze; ?></td>
	<td class="dato" width="10%" style="padding-left: 7px"><?php echo $Tot_Ctd; ?></td>
	<td class="dato" width="10%" style="padding-left: 7px"><?php echo "B"; ?></td>
	<td class="dato" width="25%" style="padding-left: 5px"><?php echo "Procesado"; ?></td>
	<!--<td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo "% 0" ?></td>-->
	<td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo number_format($Prc_Nto,0,',','.'); ?></td>
	<?php $NetoTot+=$Prc_Nto; ?>
	</tr></table>
	</td>
</tr>
<tr><td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">
        <input type="hidden" name="dfPeso" id="dfPeso" value="<?php echo $peso; ?>" />&nbsp;
</td></tr>
</table>
</td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p12s"><B>Condiciones Generales</B></td>
	<td class="dato10p" width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px; text-align: right">
	<?php if ($Val_DesG > 0) { ?>
            <b>Desc. Especial:</b> <?php echo $Val_DesG ?>
	<?php } else { ?>
	&nbsp;
	<?php } ?>
	</td>
</tr>
<?php 
	$NetoTot = intval($NetoTot - $NetoTot * $Val_DesG/100.0 + 0.5);
	if ($Cod_Iva == 1) $IVA = 0.0;
?>
<tr>
	<td class="dato10p" width="60%" VALIGN="TOP"><li>Precio <?php echo ($cod_pre == 1) ? "Minorista" : "Mayorista"; ?> <?php echo ($Cod_Iva == 1) ? "IVA incluido" : "mas IVA"; ?></li></td>
	<td class="dato10p" width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px; text-align: right">
	<?php if ($Cod_Iva == 2) { ?>
            <b>Neto:</b> <?php echo number_format($NetoTot,0,',','.') ?>
	<?php } else { ?>
	&nbsp;
	<?php } ?>
	</td>
</tr>
<tr>
	<td class="dato10p" width="60%" VALIGN="TOP">&nbsp;</td>
	<td class="dato10p" width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px; text-align: right">
	<?php if ($Cod_Iva == 2) { ?>
            <b>IVA:</b> <?php echo number_format($IVA * $NetoTot,0,',','.'); ?>
	<?php } else { ?>
	&nbsp;
	<?php } ?>
	</td>
</tr>
<tr>
    <td class="dato10p" width="60%" valign="TOP">&nbsp;</td>
	<td class="dato10p" width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px; text-align: right">
            <b>Total:</b> <?php echo number_format(($NetoTot + ($IVA * $NetoTot)),0,',','.'); ?></td>
</tr>
<?php if ($is_dsp == 1) { ?>
<tr>
<?php if ($bXisSinStock) { ?>
<td VALIGN="TOP" class="dato10p" width="60%" style="color: red">&nbsp;</td>
<?php } else { ?>
	<td class="dato10p" width="60%" VALIGN="TOP">&nbsp;</td>
<?php } ?>
	<td class="dato10p" width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px; text-align: right">
            <b>Total + Despacho:</b> <?php echo number_format(($NetoTot + ($IVA * $NetoTot) + $val_dspCIva),0,',','.'); ?></td>
</tr>
<?php } else { ?>
<?php } ?>
<tr>
	<td class="dato5p12s" width="60%" VALIGN="TOP" style="padding-top: 5px"><B>Observaciones</B></td>
	<td width="20%" VALIGN="TOP">&nbsp;</td>
	<td width="20%" VALIGN="TOP">&nbsp;</td>
</tr>
<tr>
	<td VALIGN="TOP" class="dato10p" colspan="3">
	<?php 
		$result = mssql_query("vm_count_cotcna $Cod_Cot, $cod_clt, '".($UsrId == "cotizador" ? "Tienda" : "Cliente")."'", $db);
		if (($row = mssql_fetch_array($result))) $Qty_Cna = $row['Qty_Cna'];
		if ($Qty_Cna == 0) echo "No existen consultas pendientes de leer";
		else echo "Tiene ".$Qty_Cna." consulta(s) sin leer";
    ?>
    </td>
</tr>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="avisopie" style="padding-left: 5px; padding-top: 10px">
	De conformidad con lo dispuesto en el articulo 2 bits, letra b) de la Ley N&#186; 19.496,
	Vestmed Ltda, dispone expresamente que quienes admieran productos personalizados a trav&eacute;s de nuestro
	canal de internet, tel&eacute;fono o por medio de cualquiera de nuestras tiendes de venta directa,
	no tendr&aacute;n derecho a cambio o retractarse de su compra. Esta disposici&oacute;n no invalida de forma
	alguna la responsabilidad por fallas de fabrica &#45; garant&iacute;as.</td>
</tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <!--div id="footer"></div-->
</div>
<?php echo "<!-- CodIva=".$Cod_Iva." -->\n"; ?>
</body>
</html>

