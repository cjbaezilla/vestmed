<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$UsrId = (isset($_SESSION['UsrIntra'])) ? $UsrId = $_SESSION['UsrIntra'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$Paso = (isset($_GET['paso'])) ? ok($_GET['paso']) : 1;
$DesPaso = split("/", "/Seleccionar Productos/Pagar/Confirmar Orden");


$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if ($row = mssql_fetch_array($result)) {
	$fec_cot   = date("d/m/Y", strtotime($row['Fec_Cot']));
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
	$is_dsp    = $row['is_dsp'];
	if ($is_dsp == 1) {
		$cod_crr    = $row['Cod_Crr'];
		$cod_svccrr = $row['Cod_SvcCrr'];
		$cod_sucdsp = $row['Cod_SucDsp'];
		$cod_cmndsp = $row['Cod_CmnDsp'];
		$cod_cdddsp = $row['Cod_CddDsp'];
		$dir_sucdsp = $row['Dir_SucDsp'];
		$val_dsp    = ($row['Val_Dsp'] == null ? 0 : $row['Val_Dsp']); 

		$result = mssql_query("vm_CrrCmb $cod_crr", $db);
		if ($row = mssql_fetch_array($result)) $des_crr = $row['Des_Crr'];

		$result = mssql_query("vm_SvcCrrCmb $cod_crr, $cod_svccrr", $db);
		if ($row = mssql_fetch_array($result)) $des_svccrr = $row['Des_SvcCrr'];
		
		$result = mssql_query("vm_cmn_s $cod_cmndsp", $db);
		if ($row = mssql_fetch_array($result)) $nom_cmndsp = $row['Nom_Cmn'];
		
		$result = mssql_query("vm_cdd_s $cod_cdddsp", $db);
		if ($row = mssql_fetch_array($result)) {
			$nom_cdddsp = $row['Nom_Cdd'];
			$cod_rgndsp = $row['Cod_Rgn'];
		}

		if ($cod_sucdsp > 0) {
			$result = mssql_query("vm_suc_s $cod_clt, $cod_sucdsp", $db);
			if ($row = mssql_fetch_array($result)) $nom_sucdsp = $row['Nom_Suc'];
		}
		else $nom_sucdsp = "Oficina Carrier";
		

	}

	$result = mssql_query("vm_cmn_s $cod_cmn", $db);
	if ($row = mssql_fetch_array($result)) $nom_cmn = $row['Nom_Cmn'];
	
	$result = mssql_query("vm_cdd_s $cod_cdd", $db);
	if ($row = mssql_fetch_array($result)) $nom_cdd = $row['Nom_Cdd'];
	
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

function GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Cod_LinMca, $Tallas) {
	$Des_Sze = "";
	//foreach ($Tallas as $key => $value) $Des_Sze = $Des_Sze.$key."(".$value.") ";
	echo "<TABLE BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" width=\"100%%\" height=\"150\" ALIGN=\"center\">\n";
	echo "<tr><td width=\"20%%\" class=\"titulo_tabla5p\">Producto</td><td  class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\"><b>$Cod_Sty - $Nom_Dsg<b></td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Descripci&oacute;n</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\">$Des_GrpPrd</td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Marca</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Cod_Mca</td><td class=\"titulo_tabla5p\">Origen</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Org_Mca</td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Material</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Des_Mat</td><td class=\"titulo_tabla5p\">Linea</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px;\">$Cod_LinMca</td></tr>\n";
	//echo "<tr><td class=\"titulo_tabla5p\">Pattern</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\">$Des_Pat</td></tr>\n";
	//echo "<tr><td class=\"titulo_tabla5p\">Tallas</td><td class=\"dato5p\" colspan=\"3\">$Des_Sze</td></tr>\n";
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
<!-- Lytebox Includes //-->
<script type="text/javascript" src="../lytebox/lytebox.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
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
	
	function formatearMillones(nNmb){
		var sRes = "";
		for (var j, i = nNmb.length - 1, j = 0; i >= 0; i--, j++)
		 sRes = nNmb.charAt(i) + ((j > 0) && (j % 3 == 0)? ".": "") + sRes;
		return sRes;
	}
	
	function CalcularMonto(obj,precio) {
		//alert(obj.value);
		//alert(obj.name.substr(5));
		
		eval('f2.Neto'+obj.name.substr(5)).value = parseInt(obj.value) * precio;
		eval('f2.dfMto'+obj.name.substr(5)).value = formatearMillones(eval('f2.Neto'+obj.name.substr(5)).value);
		
		CalcularTotal();
	}
	
	function CalcularTotal() {
		//alert("CalcularTotal");
		var pesodsp = 0.0;
		var neto = 0;
		var iva = 0;
	    for (i=0; i<f2.elements.length; i++) {
			if (f2.elements[i].name == "seleccionPrd[]")
				if (f2.elements[i].checked) {
				   //alert(eval('f2.Neto'+f2.elements[i].value).value);
				   neto+=parseInt(eval('f2.Neto'+f2.elements[i].value).value);
				   pesodsp+=(eval('f2.PesoUni'+f2.elements[i].value).value * eval('f2.dfCtd'+f2.elements[i].value).value);
				}
		}
		if (pesodsp > 0) pesodsp+=0.1;
		
		descuento = neto * <?php echo $Val_DesG / 100.0; ?>;
		neto = neto - Math.round(descuento);
		
		iva = neto * <?php echo $IVA; ?>;
		iva = Math.round(iva);
		total = neto + iva;
		
		f2.dfNeto.value = neto;
		f2.dfNeto.value = formatearMillones(f2.dfNeto.value);
		
		f2.dfIVA.value = iva;
		f2.dfIVA.value = formatearMillones(f2.dfIVA.value);
		
		f2.dfTotal.value = total;
		f2.dfTotal.value = formatearMillones(f2.dfTotal.value);
		
		<?php if ($is_dsp == 1) { ?>
		if (total > 0) {
			for (i=0; i<ArrPeso.length; i++) {
				if (pesodsp < ArrPeso[i]) {
					f2.dfDespacho.value = ArrCosto[i];
					f2.dfTotalConDsp.value = total + ArrCosto[i];
					f2.dfDespacho.value = formatearMillones(f2.dfDespacho.value);
					break;
				}
			}
		}
		else {
			f2.dfDespacho.value = 0;
			f2.dfTotalConDsp.value = 0;
		}
		f2.dfTotalConDsp.value = formatearMillones(f2.dfTotalConDsp.value);
		<?php } ?>
	}
	
	function DisplayAviso () {
		alert("Aviso para ser presentado en el caso de una Persona Juridica");
	}
	
	function ValidarOrden(form) {
		var ok=false;
	    for (i=0; i<f2.elements.length; i++) {
			if (f2.elements[i].name == "seleccionPrd[]")
				if (f2.elements[i].checked) {
				   //alert(eval('f2.Neto'+f2.elements[i].value).value);
				   ok = true;
				   break;
				}
		}
		if (!ok) {
			alert ("Debe indicar los productos que desea comprar");
			return false;
		}
		if (!f2.rbTransferencia.checked && !f2.rbWebPay.checked) {
			alert ("Debe indicar la Forma de Pago");
			return false;
		}
		return true;
	}
</script>
</head>
<BODY>
<div id="body" style="width:100%">
	<!--div id="header"></div-->
    <ul id="usuario_registro">
		<?php 	echo display_mnu($UsrId, $cod_tipper, $Cod_Cot, $db); ?>
    </ul>
	<div id="work">

<?php formar_topbox ("100%%","center"); ?>
<P align="center">
<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="enviaroc.php?cot=<?php echo $Cod_Cot ?>&paso=1" onsubmit="return ValidarOrden(this)" enctype="multipart/form-data" />
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
<TR>
	<TD width="50%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><IMG SRC="logo.gif" width="235" HEIGHT="130"></TD>
	<TD class="dato" style="text-align: right; FONT-SIZE: 2.0em; FONT-FAMILY: Arial, Verdana;" width="50%" VALIGN="bottom" COLSPAN="2"><b>ORDEN DE COMPRA</b></TD>
</TR>
<TR>
	<TD class="dato" style="text-align: right" width="50%" VALIGN="TOP" COLSPAN="2" HEIGHT="65"><b>Fecha: </b><?php echo date('d/m/Y'); ?></TD>
</TR>
<TR><TD colspan="3" style="padding-top: 20px; padding-left: 5px; FONT: bold 16px 'trebuchet ms', helvetica, sans-serif; COLOR: red; ">PASO <?php echo $Paso." : ".$DesPaso[$Paso]; ?></TD></TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p12s" style="padding-top: 10px;"><B>Cliente: <?php echo $nom_clt ?></B></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" style="padding-top: 20px">&nbsp;</TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">Rut: <?php echo formatearRut($num_doc) ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">Sucursal: <?php echo $nom_suc ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Contacto: <?php echo $nom_ctt ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">Direcci&oacute;n: <?php echo $dir_suc ?></FONT></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Tel&eacute;fono: <?php echo $fon_ctt ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">Ciudad: <?php echo $nom_cdd ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Fax: <?php echo $fon_ctt ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">Comuna: <?php echo $nom_cmn ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Email: <?php echo $mail_ctt; ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP">&nbsp;</TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</TD>
</TR>
<?php if ($is_dsp == 1) { ?>
<TR>
	<TD VALIGN="TOP" COLSPAN="3" class="dato5p12s"><B>Informaci&oacute;n de Despacho</B></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">Carrier: <?php echo $des_crr ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Direcci&oacute;n: <?php echo $dir_sucdsp; ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">Servicio: <?php echo $des_svccrr; ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Comuna: <?php echo $nom_cmndsp; ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">Sucursal: <?php echo $nom_sucdsp ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Ciudad: <?php echo $nom_cdddsp ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p">&nbsp;</TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato">Gastos Despacho: 
	<INPUT id="dfDespacho" name="dfDespacho" size="15" maxLength="10" class="dato" style="text-align: left" value="<?php echo  formatearMillones($Val_Dsp); ?>" ReadOnly />
</TR>
<TR>
	<TD width="50%" VALIGN="TOP">&nbsp;</TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</TD>
</TR>
<?php } ?>
<TR>
	<TD VALIGN="TOP" COLSPAN="3" class="dato5p12s">
	<B>
	<?php 
	switch ($Paso) {
	case 1:
		echo "Productos Seleccionados";
		break;
	case 2:
	case 3:
		echo "Productos Comprados";
		break;
	}
	?>
	</B></TD>
</TR>
<TR><TD VALIGN="TOP" COLSPAN="3" style="padding-bottom: 10px">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
<?php 
	switch ($Paso) {
	case 1:
		$item        = 0;
		$NetoTot     = 0;
		$Cod_Mca     = "";
		$Cod_GrpPrd  = "";
		$Cod_Dsg     = "";
		$Cod_Pat     = "";
		$Prc_Uni     = 0;
		$Val_Des     = 0;
		$Tallas      = "";
		$bPrimero    = true;

		$result = mssql_query("vm_pvw_rescot $Cod_Cot",$db);
		while ($row = mssql_fetch_array($result)) {
			if ($Cod_Mca != $row['Cod_Mca'] Or $Cod_GrpPrd != $row['Cod_GrpPrd'] Or	$Cod_Dsg != $row['Cod_Dsg']) {
				$Cod_Mca      = $row['Cod_Mca'];
				$Cod_LinMca   = $row['Cod_LinMca'];
				$Cod_GrpPrd   = $row['Cod_GrpPrd'];
				$Cod_Dsg 	  = $row['Cod_Dsg'];
				$grpprd_title = $Cod_Sty." ".$Nom_Dsg;
				$Prc_Nto 	  = $row['Prc_Nto'];
				$Org_Mca      = $row['Org_Mca'];
				$Val_Des 	  = $row['Val_Des'];
				$Cod_Sty	  = $row['Cod_Sty'];
				$Nom_Dsg	  = str_replace("#","'",$row['Nom_Dsg']);
				$Des_GrpPrd   = str_replace("#","'",$row['Des_GrpPrd']);
				$Des_Mat	  = str_replace("#","'",$row['Des_Mat']);
				$Cod_Prd	  = $row['Cod_Prd'];
				$item++;
		?>
		<?php if ($item > 1) { ?>
		<tr><td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">&nbsp;</td></tr>
		<?php } ?>
<tr>
	<td class="label_left_top" style="text-align: center" width="5%"><?php echo $item ?></td>
	<td class="label_left_top" style="text-align: center" width="10%">
	<a rev="width: 602px; height: 490px; border: 0 none; scrolling: auto;" title="<?php echo $grpprd_title ?>" rel="lyteframe[imagenes<?php echo $item ?>]" href="../catalogo/imagenes-producto.php?producto=<?php echo $Cod_GrpPrd ?>"><img src="<?php echo printimg_addr("img1_grupo",$Cod_GrpPrd) ?>" width="100" class="cursor image-producto" /></a>	</td>
	<td class="label_left_right_top" valign="top" style="text-align: center" width="55%"><?php GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Cod_LinMca, $Tallas); ?></td>
	<!--td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">Cantidad</td-->
	<!--td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">P.Unitario</td-->
	<!--td class="titulo_tabla" valign="top" style="text-align: center" width="10%" height="15">Total</td-->
</tr>
<tr>
	<td colspan="3">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td class="titulo_tabla5p" width="10%">Color</td>
	<td class="titulo_tabla5p" width="25%">Descripci&oacute;n</td>
	<td class="titulo_tabla5p" width="20%">Tama&ntilde;o</td>
	<td class="titulo_tabla5p" width="10%">Total</td>
	<td class="titulo_tabla5p" width="10%">Precio<br>Unitario</td>
	<td class="titulo_tabla5p" width="10%">Desc</td>
	<td class="titulo_tabla5p" width="10%" style="padding-right: 3px; text-align: right">Monto<br>Total</td>
	<td class="titulo_tabla5p" width="5%" style="text-align: center">Sel</td>
	</tr></table>
	</td>
</tr>
<?php 
	}
	$Val_Des = $row['Val_Des'];
	$Neto = $row['Val_Ctd'] * $row['Prc_Uni'];
	$Total = intval($Neto - $Neto*$Val_Des/100.0 + 0.5);
	$NetoTot+=$Total;
?>
<tr>
	<td colspan="3">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td class="dato" width="10%" style="padding-left: 3px; border-left: goldenrod 1px solid;"><?php echo $row['Key_Pat']; ?></td>
	<td class="dato" width="25%" style="padding-left: 3px"><?php echo $row['Des_Pat']; ?></td>
	<td class="dato" width="20%" style="padding-left: 3px"><?php echo $row['Val_Sze']; ?></td>
	<td class="dato" width="10%" style="padding-left: 3px"><INPUT name="dfCtd<?php echo $row["Cod_Prd"]; ?>" size="5" onchange="CalcularMonto(this,<?php echo $row['Prc_Uni']; ?>)" maxLength="5" class="dato" style="text-align: left" value="<?php echo $row['Val_Ctd']; ?>" /></td>
	<td class="dato" width="10%" style="padding-left: 3px"><?php echo formatearMillones($row['Prc_Uni']); ?></td>
	<td class="dato" width="10%" style="padding-left: 3px"><?php echo $row['Val_Des']."%"; ?></td>
	<td class="dato" width="10%" style="padding-right: 3px; text-align: right;"><INPUT name="dfMto<?php echo $row["Cod_Prd"]; ?>" size="10" maxLength="10" style="TEXT-ALIGN: right" value="<?php echo formatearMillones($Total); ?>" class="textfieldRO3" ReadOnly /></td>
	<td class="dato" width="5%"  style="border-right: goldenrod 1px solid; text-align: center;">
	<INPUT type="checkbox" class="dato" style="height: 14px" onclick="CalcularTotal()" name="seleccionPrd[]" value="<?php echo $row["Cod_Prd"]; ?>" />
	<INPUT type="hidden" name="Neto<?php echo $row["Cod_Prd"]; ?>" value="<?php echo $Total; ?>">
	<INPUT type="hidden" name="CodSec<?php echo $row["Cod_Prd"]; ?>" value="<?php echo $row['Cod_Sec']; ?>">
	<INPUT type="hidden" name="PesoUni<?php echo $row["Cod_Prd"]; ?>" value="<?php echo $row['Peso_Uni']; ?>">
	</td>
	</tr></table>
	</td>
</tr>
<?php
}
?>
<tr><td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">&nbsp;</td></tr>
<?php 
		break;
	
	case 2:
	case 3:
?>
<tr>
	<td class="titulo_tabla5p" width="10%">Style</td>
	<td class="titulo_tabla5p" width="10%">Color</td>
	<td class="titulo_tabla5p" width="25%">Descripci&oacute;n</td>
	<td class="titulo_tabla5p" width="25%">Cantidad | Tama&ntilde;o</td>
	<td class="titulo_tabla5p" width="10%">Total</td>
	<td class="titulo_tabla5p" width="10%">Precio<br>Unitario</td>
	<td class="titulo_tabla5p" width="10%" style="padding-right: 3px; text-align: right">Monto<br>Total</td>
</tr>
<?php
		$result = mssql_query("vm_s_cotnvt $Cod_Cot", $db);
		if ($row = mssql_fetch_array($result)) {
			$Cod_Nvt = $row["Cod_Nvt"];
			$Cod_Trn = $row["Cod_Trn"];
			$bPrimerRegistro = true;
			$result = mssql_query("vm_resprd_nvt_s $Cod_Nvt, $Cod_Trn",$db);
			while ($row = mssql_fetch_array($result)) {
				$sCod_Dsg = $row["Cod_Dsg"];
				$sCod_Sty = $row["Cod_Sty"];
				$sCod_Pat = $row["Cod_Pat"];
				$sKey_Pat = $row["Key_Pat"];
				$sCod_Sze = $row["Cod_Sze"];
				$sVal_Sze = $row["Val_Sze"];
				$nPrc_Prd = $row["Prc_Prd"];
				$nCtd_Prd = $row["Ctd_Prd"];
				$sDes_Prd = $row["Des_Prd"];
				If ($bPrimerRegistro) {
					$colCod_Dsg  = $sCod_Dsg;
					$colCod_Pat  = $sCod_Pat;
					$colDes_Prd  = $sDes_Prd;
					$nPrc_PrdAnt = $nPrc_Prd;
					$colCod_Sty  =  $sCod_Sty;
					$colKey_Pat  =  $sKey_Pat;
					$nTotCtd_Prd = 0;
					$sCtdSze_Prd = "";
					$sCod_SzeAnt =  $sVal_Sze;
					$nCtd_Sze    = 0;
					$bPrimerRegistro = false;
				}
				if ($sCod_Dsg != $colCod_Dsg Or $sCod_Pat != $colCod_Pat Or $nPrc_Prd != $nPrc_PrdAnt) {
					$colPrc_Prd = $nPrc_PrdAnt;
					$sCtdSze_Prd .= $nCtd_Sze."|".$sCod_SzeAnt." ";
					$colCtdSze_Prd = $sCtdSze_Prd;
					$colCtd_Prd = $nTotCtd_Prd;
					$colMto_Prd = $nTotCtd_Prd * $colPrc_Prd;
					$dfSubTotal = $dfSubTotal + $colMto_Prd;

					$sLinea  = "<TR><TD>".$colCod_Sty."</TD>";
					$sLinea .= "<TD>".$colKey_Pat."</TD>";
					$sLinea .= "<TD>".$colDes_Prd."</TD>";
					$sLinea .= "<TD>".$colCtdSze_Prd."</TD>";
					$sLinea .= "<TD>".$colCtd_Prd."</TD>";
					$sLinea .= "<TD>".formatearMillones($colPrc_Prd)."</TD>";
					$sLinea .= "<TD>".formatearMillones($colMto_Prd)."</TD></TR>";
					echo $sLinea;
					
					$colCod_Dsg  = $sCod_Dsg;
					$colCod_Pat  = $sCod_Pat;
					$colDes_Prd  = $sDes_Prd;
					$nPrc_PrdAnt = $nPrc_Prd;
					$colCod_Sty  = $sCod_Sty;
					$colKey_Pat  = $sKey_Pat;
					$nTotCtd_Prd = $nCtd_Prd;
					$sCtdSze_Prd = "";
					$sCod_SzeAnt = $sVal_Sze;
					$nCtd_Sze    = $nCtd_Prd;
				}
				else {
					$nTotCtd_Prd = $nTotCtd_Prd + $nCtd_Prd;
					if ($sVal_Sze == $sCod_SzeAnt)
						$nCtd_Sze = $nCtd_Sze + $nCtd_Prd;
					else {
						$sCtdSze_Prd .= $nCtd_Sze."|".$sCod_SzeAnt." ";
						$sCod_SzeAnt = $sVal_Sze;
						$nCtd_Sze = $nCtd_Prd;
					}
				}
			}
			$colPrc_Prd   = $nPrc_PrdAnt;
			$sCtdSze_Prd .= $nCtd_Sze."|".$sCod_SzeAnt." ";
			
			$colCtdSze_Prd = $sCtdSze_Prd;
			$colCtd_Prd    = $nTotCtd_Prd;
			$colMto_Prd    = $nTotCtd_Prd * colPrc_Prd;

			$sLinea  = "<TR><TD>".$colCod_Sty."</TD>";
			$sLinea .= "<TD>".$colKey_Pat."</TD>";
			$sLinea .= "<TD>".$colDes_Prd."</TD>";
			$sLinea .= "<TD>".$colCtdSze_Prd."</TD>";
			$sLinea .= "<TD>".$colCtd_Prd."</TD>";
			$sLinea .= "<TD>".formatearMillones($colPrc_Prd)."</TD>";
			$sLinea .= "<TD>".formatearMillones($colMto_Prd)."</TD></TR>";
			echo $sLinea;
			
			$dfSubTotal    = $dfSubTotal + $colMto_Prd;
			
			$result = mssql_query("vm_dsp_s $Cod_Nvt, $Cod_Trn",$db);
			while ($row = mssql_fetch_array($result)) {
				$colPrc_Prd = $row["Prc_DspTrn"];
				If ($colPrc_Prd > 0) {
					$colCod_Sty = "";
					$colCod_Pat = "";
					$colCtdSze_Prd = "";
					$colDes_Prd = "Recuperaci&oacute;n de Gastos de Despacho";
					$colCtd_Prd = 1;
					$colMto_Prd = $colPrc_Prd;
					$dfSubTotal = $dfSubTotal + $colMto_Prd;

					$sLinea  = "<TR><TD>".$colCod_Sty."</TD>";
					$sLinea .= "<TD>".$colKey_Pat."</TD>";
					$sLinea .= "<TD>".$colDes_Prd."</TD>";
					$sLinea .= "<TD>".$colCtdSze_Prd."</TD>";
					$sLinea .= "<TD>".$colCtd_Prd."</TD>";
					$sLinea .= "<TD>".formatearMillones($colPrc_Prd)."</TD>";
					$sLinea .= "<TD>".formatearMillones($colMto_Prd)."</TD></TR>";
					echo $sLinea;
				}
			}
		}
?>

<?php
		break;
	}
?>
</TABLE>
</TD>
</TR>
<tr>
	<td colspan="3">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center">
	<tr>
		<td colspan="2" width="50%" class="dato5p12s" style="border-left: goldenrod 1px solid; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">Resumen</td>
		<td colspan="2" width="50%" class="dato5p12s" style="border-right: goldenrod 1px solid; border-top: goldenrod 1px solid;">Forma de Pago</td>
	</tr>
	<?php if ($Val_DesG > 0) { ?>
	<tr>
	<td align="Left" class="dato" >Desc. Especial:</td>
	<td align="Left"><INPUT name="dfDescEsp" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo $Val_DesG ?> %" ReadOnly /></td>
	<td colspan="2">&nbsp;</td>
	</tr>
	<?php } ?>
	<tr>
		<?php if ($Cod_Iva == 2) { ?>
		<td align="Left" class="dato5p" style="border-left: goldenrod 1px solid; padding-top: 5px;">Neto:</td>
		<td align="Left" style="border-right: goldenrod 1px solid; padding-top: 5px;">
		<INPUT name="dfNeto" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo formatearMillones($NetoTot) ?>" ReadOnly />
		</td>
		<?php } else { ?>
		<td colspan="2" class="dato5p" style="border-left: goldenrod 1px solid; padding-top: 5px;">&nbsp;</td>
		<?php } ?>
		<td width="5%" class="dato5p"><INPUT id="rbTransferencia" name="rbTransferencia" type="radio" class="button2" value="0" /></td>
		<td class="dato" style="border-right: goldenrod 1px solid; padding-top: 5px;">Transferencia Bancaria</td>
	</tr>
	<tr>
		<?php if ($Cod_Iva == 2) { ?>
		<td align="Left" class="dato5p" style="border-left: goldenrod 1px solid; padding-top: 5px;">IVA:</td>
		<td align="Left" style="border-right: goldenrod 1px solid; padding-top: 5px;">
		<INPUT name="dfIVA" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo formatearMillones(intval(($IVA * $NetoTot) + 0.5)); ?>" ReadOnly />
		</td>
		<?php } else { ?>
		<td colspan="2" class="dato" style="border-left: goldenrod 1px solid; padding-top: 5px;">&nbsp;</td>
		<?php } ?>
		<td width="5%" class="dato5p"><INPUT id="rbWebPay" name="rbWebPay" type="radio" class="button2" value="0" DISABLED /></td>
		<td class="dato" style="border-right: goldenrod 1px solid; padding-top: 5px;">WebPay - Transbank (pr&oacute;ximamente)</td>
	</tr>
	<tr>
		<td align="Left" class="dato5p" style="border-left: goldenrod 1px solid; padding-top: 5px;">Total:</td>
		<td align="Left" style="border-right: goldenrod 1px solid; padding-top: 5px;">
		<INPUT name="dfTotal" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo formatearMillones(intval($NetoTot + ($IVA * $NetoTot) + 0.5)); ?>" ReadOnly />
		</td>
		<td colspan="2" class="dato5p" style="border-right: goldenrod 1px solid; padding-top: 5px;">&nbsp;</td>
	</tr>
	<tr>
		<?php if ($is_dsp == 1) { ?>
		<td align="Left" class="dato5p" style="border-left: goldenrod 1px solid; padding-top: 5px;">Total + Despacho:</td>
		<td align="Left" style="border-right: goldenrod 1px solid; padding-top: 5px;">
		<INPUT name="dfTotalConDsp" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo formatearMillones(intval($NetoTot + ($IVA * $NetoTot) + 0.5)); ?>" ReadOnly />
		</td>
		<?php } else { ?>
		<td colspan="2" align="Left" class="dato5p" style="border-left: goldenrod 1px solid; padding-top: 5px; border-right: goldenrod 1px solid;">&nbsp;</td>
		<?php } ?>
		<td colspan="2" class="dato5p" style="border-right: goldenrod 1px solid; padding-top: 5px;">
		<!--input class="file-contacto" type="file" name="documento" id="documento" size="28" onchange="fichero.value = this.value"/-->
		<!--input type="hidden" name="fichero"/-->
		&nbsp;
		</td>
	</tr>
	<tr><td colspan="4" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">&nbsp;</td></tr>
	</table>
	</td>
</tr>
<TR>
	<TD class="dato5p12s" width="60%" VALIGN="TOP" style="padding-top: 5px"><B>Observaciones</B></TD>
	<TD width="20%" VALIGN="TOP">&nbsp;</TD>
	<TD width="20%" VALIGN="TOP">&nbsp;</TD>
</TR>
<TR>
	<TD VALIGN="TOP" class="dato10p" colspan="3">
	<!--textarea name="comentarios" id="comentarios" cols="80" rows="3" class="dato" ReadOnly><?php echo $obs_cot; ?></textarea></TD-->
	<!--table width="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
	<?php 
		$result = mssql_query("vm_s_cotcna $Cod_Cot, $cod_clt", $db);
		$bOkRespuesta = false;
		$totfilas = 0;
		while ($row = mssql_fetch_array($result)) {
			if ($row['Tip_Cna'] == "R") $bOkRespuesta = true;
			if (strlen($row['Det_Cna']) > 80) $mensaje = substr($row['Det_Cna'],0,50)."...";
			else $mensaje = $row['Det_Cna'];
	?>
		<tr>
			<td width="70px"><?php echo $row['Fec_Dis']; ?></td>
			<td align="left" title="<?php echo $row['Det_Cna']; ?>"><?php echo $mensaje; ?></td>
		</tr>
	<?php 
		$totfilas++;
		}
		if ($totfilas == 0) $bOkRespuesta = true;
		for ($i=$totfilas; $i < 5; $i++) {
	?>
		<tr><td colspan="2">&nbsp;</td></tr>
	<?php } ?>
	</table-->
	<?php 
		$result = mssql_query("vm_count_cotcna $Cod_Cot, $cod_clt, '".($UsrId == "cotizador" ? "Tienda" : "Cliente")."'", $db);
		if ($row = mssql_fetch_array($result)) $Qty_Cna = $row['Qty_Cna'];
		if ($Qty_Cna == 0) echo "No existen consultas pendientes de responder";
		else echo "Tiene ".$Qty_Cna." consulta(s) sin responder";
    ?>	
	</TD>
</TR>
<TR>
	<TD VALIGN="TOP" COLSPAN="3" class="avisopie" style="padding-left: 5px; padding-top: 10px">
	De conformidad con lo dispuesto en el articulo 2 bits, letra b) de la Ley N&#186; 19.496, 
	Vestmed Ltda, dispone expresamente que quienes admieran productos personalizados a trav&eacute;s de nuestro 
	canal de internet, tel&eacute;fono o por medio de cualquiera de nuestras tiendes de venta directa, 
	no tendr&aacute;n derecho a cambio o retractarse de su compra. Esta disposici&oacute;n no invalida de forma 
	alguna la responsabilidad por fallas de fabrica &#45; garant&iacute;as.</TD>
</TR>
<TR>
  <TD colspan="3" align="right" style="PADDING-TOP: 5px">
      <input type="submit" name="Enviar" value=" Paso 2: Pagar " class="bnt" />
  </TD>
</TR>
</TABLE>
</form>
</p>
<?php formar_bottombox (); ?>
    </div>
    <!--div id="footer"></div-->
</div>
<script language="javascript">
	var f1 = document.F1;	
	var f2 = document.F2;
	var ArrPeso = new Array();
	var ArrCosto = new Array();
	
<?php
	if ($is_dsp == 1) {
		$i = 0;
        $result = mssql_query("vm_SvcCrr_Prc_s ".$cod_crr.",".$cod_svccrr.",".$cod_rgndsp, $db);
		while ($row = mssql_fetch_array($result)) {
			echo "\tArrPeso[$i] = ".$row['Pes_Max'].";\n";
			echo "\tArrCosto[$i] = ".$row['Prc_Dsp'].";\n";
			$i++;
		}
	}
?>
	CalcularTotal();
</script>
</BODY>
</HTML>
