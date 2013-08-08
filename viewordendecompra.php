<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$DesPaso = split("/", "/Seleccionar Productos/Pagar/Enviar");

$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if ($row = mssql_fetch_array($result)) {
	$fec_cot   = date("d/m/Y", strtotime($row['Fec_Cot']));
	$cod_clt   = $row['Cod_Clt'];
	$cod_tipper = $row['Cod_TipPer'];
	if ($cod_tipper == 1)
		$nom_clt = $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'];
	else
		$nom_clt = $row['RznSoc_Per'];
		
	$cod_Odc = $row['Cod_Odc'];
    $tip_docsii = $row['Tip_DocSII'];
	$nom_bco = $row['Nom_Bco'];
	$num_trn = (trim($row['Num_TrnBco']) == "" ? "No Indicado" : $row['Num_TrnBco']);
	$arc_adj = (trim($row['Arc_Adj']) == "" ? "No Adjuntado" : $row['Arc_Adj']);
	
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
	$val_dsp   = 0;
	if ($is_dsp == 1) {
		$cod_crr    = $row['Cod_Crr'];
		$cod_svccrr = $row['Cod_SvcCrr'];
		$cod_sucdsp = $row['Cod_SucDsp'];
		$cod_cmndsp = $row['Cod_CmnDsp'];
		$cod_cdddsp = $row['Cod_CddDsp'];
		$dir_sucdsp = $row['Dir_SucDsp'];
		$val_dsp    = $row['Val_Dsp']; 

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
		
		/* Calculo del Peso en base a la cotizacion original */
		$peso = 0.0;
		$result = mssql_query("vm_pvw_rescot $Cod_Cot",$db);
		while ($row = mssql_fetch_array($result)) $peso += $row["Peso_Uni"];
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
	/* Consultas realizadas por el ususario a Vestmed */
	$tot_cnaclt = 0;
	$result = mssql_query("vm_totcna_totres $Cod_Cot, $cod_per");
	if ($row = mssql_fetch_array($result)) {
		$tot_cnaclt    = $row["tot_cna"];
		$tot_sinresemp = $row["tot_sinres"];
	}
	
	/* Consultas realizadas por el Vestmed al Usuario */
	$tot_cnaemp = 0;
	$result = mssql_query("vm_totcna_totres $Cod_Cot, 0");
	if ($row = mssql_fetch_array($result)) {
		$tot_cnaemp = $row["tot_cna"];
		$tot_sinresclt = $row["tot_sinres"];
	}	
}
$IVA = 0.0;
if ($Cod_Iva == 2) {
	$result = mssql_query("vm_getfolio_s 'IVA'",$db);
	if ($row = mssql_fetch_array($result)) $IVA = $row['Tbl_fol'] / 10000.0;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<LINK href="Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/headers.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox/lytebox.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>
<link rel="stylesheet" type="text/css" href="lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
</head>

<body>
<div id="body">
    <?php if ($Perfil == 0) { ?>
	<div id="header"></div>
    <div class="menu"id="menu-noselect">
    	<a id="home" href="index.htm">home</a>
    	<a id="empresa" href="empresa.htm">empresa</a>
        <a id="marcas" href="marcas.htm">marcas</a>
        <a id="telas" href="telas.htm">telas</a>
        <a id="bordados" href="bordados.htm">bordados</a>
        <a id="despachos" href="despachos.htm">despachos</a>
        <a id="clientes" href="clientes.htm">clientes</a>
         <div id="servicio-cliente-selected" style="z-index:1000;padding-top:0px;" class="selected">
        <ul id="dropdown-scliente" class="dropdown">
                <li>
                    <a class="normal" href="servicio-cliente.htm">servicio al cliente</a>
                    <ul>
                       <li>
                            <a href="faq.htm">Faq</a>
                        </li>
                        <li>
                            <a href="como-tomar-medidas.htm">C&oacute;mo Tomar Medidas</a>
                        </li>
                        <li>
                            <a href="despachos.htm">Despachos</a>
                        </li>
                        <li>
                            <a href="clean-care.htm">Clean & Care</a>
                        </li>
                        <li>
                            <a href="tracking-ordenes.htm">Tracking de &Oacute;rdenes</a>
                        </li>
                        <li>
                            <a href="como-cotizar.htm">C&oacute;mo Cotizar</a>
                        </li>
                       
                        <li>
                            <a href="politicas-privacidad.htm">Pol&iacute;ticas de Privacidad</a>
                        </li>
                    </ul>
                </li>
            </ul>		
        </div>
        <a id="catalogo" href="catalogo.php">catalogo</a>
        <a id="contacto" href="contacto.htm">contacto</a>
  
  	</div>
    <ul id="usuario_registro">
		<?php 	echo display_login($cod_per, $cod_clt, $db); ?>
    </ul>
	<?php } ?>
    <div id="work">
		<div id="back-registro3">
			<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" />
           	<div style="width:765px; height:auto; margin:0 auto 0 100px; padding-top:10px;">
<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="tracking.php" />
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%">
<TR>
	<TD width="50%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><IMG SRC="images/logo.png" width="235" HEIGHT="130"></TD>
	<TD class="dato" style="text-align: right; FONT-SIZE: 2.0em; FONT-FAMILY: Arial, Verdana;" width="50%" VALIGN="bottom" COLSPAN="2"><b>ORDEN DE COMPRA <?php echo $cod_Odc; ?></b></TD>
</TR>
<TR>
	<TD class="dato" style="text-align: right" width="50%" VALIGN="TOP" COLSPAN="2" HEIGHT="65">
	<b>Fecha: </b><?php echo date('d/m/Y') ?><BR><b>Estado: </b>Enviada
	</TD>
</TR>
<TR>
	<TD VALIGN="TOP" COLSPAN="3" class="dato5p12s" style="padding-top: 10px;"><B>Informaci&oacute;n del Cliente</B></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p12s" style="padding-top: 3px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid;"><B>Cliente: <?php echo $nom_clt ?></B></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" style="padding-top: 3px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">&nbsp;</TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Rut: <?php echo formatearRut($num_doc) ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" style="padding-top: 2px; border-right: goldenrod 1px solid;">&nbsp;</TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Sucursal: <?php echo $nom_suc ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; border-right: goldenrod 1px solid;">Contacto: <?php echo $nom_ctt ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Direcci&oacute;n: <?php echo $dir_suc ?></FONT></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; border-right: goldenrod 1px solid;">Tel&eacute;fono: <?php echo $fon_ctt ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Ciudad: <?php echo $nom_cdd ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; border-right: goldenrod 1px solid;">Fax: <?php echo $fon_ctt ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; padding-bottom: 3px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">Comuna: <?php echo $nom_cmn ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; padding-bottom: 3px; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">Email: <?php echo $mail_ctt; ?></TD>
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
	<TD width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 8px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid;">Carrier: <?php echo $des_crr ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato5p" style="PADDING-TOP: 8px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">Direcci&oacute;n: <?php echo $dir_sucdsp; ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 8px; border-left: goldenrod 1px solid;">Servicio: <?php echo $des_svccrr; ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato5p" style="PADDING-TOP: 8px; border-right: goldenrod 1px solid;">Comuna: <?php echo $nom_cmndsp; ?></TD>
</TR>
<TR>
	<TD width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 8px; border-left: goldenrod 1px solid;">Sucursal: <?php echo $nom_sucdsp ?></TD>
	<TD width="50%" VALIGN="TOP" COLSPAN="2" class="dato5p" style="PADDING-TOP: 8px; padding-bottom: 3px; border-right: goldenrod 1px solid;">Ciudad: <?php echo $nom_cdddsp ?></TD>
</TR>
<TR>
	<TD COLSPAN="3" VALIGN="TOP" class="dato5p" style="PADDING-BOTTOM: 1px; border-top: goldenrod 1px solid;">&nbsp;</TD>
</TR>
<?php } ?>
<TR>
	<TD VALIGN="TOP" COLSPAN="3" class="dato5p12s" style="PADDING-TOP: 10px">
	<B>Productos Comprados</B></TD>
</TR>
<TR><TD VALIGN="TOP" COLSPAN="3" style="padding-bottom: 10px">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center">
<tr>
	<td class="titulo_tabla5p" width="8%">Style</td>
	<td class="titulo_tabla5p" width="7%">Color</td>
	<td class="titulo_tabla5p" width="40%">Descripci&oacute;n</td>
	<td class="titulo_tabla5p" width="20%">Cantidad | Tama&ntilde;o</td>
	<td class="titulo_tabla5p" width="5%">Total</td>
	<td class="titulo_tabla5p" width="10%" style="padding-right: 3px; text-align: right">Precio<br>Unitario</td>
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

					$sLinea  = "<TR><TD style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">".$colCod_Sty."</TD>";
					$sLinea .= "<TD>".$colKey_Pat."</TD>";
					$sLinea .= "<TD>".str_replace("#","'",$colDes_Prd)."</TD>";
					$sLinea .= "<TD>".$colCtdSze_Prd."</TD>";
					$sLinea .= "<TD align=\"center\">".$colCtd_Prd."</TD>";
					$sLinea .= "<TD style=\"padding-right: 3px; text-align: right\">".number_format($colPrc_Prd,0,',','.')."</TD>";
					$sLinea .= "<TD style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\">".number_format($colMto_Prd,0,',','.')."</TD></TR>";
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
			$colMto_Prd    = $nTotCtd_Prd * $colPrc_Prd;

			$sLinea  = "<TR><TD style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">".$colCod_Sty."</TD>";
			$sLinea .= "<TD>".$colKey_Pat."</TD>";
			$sLinea .= "<TD>".str_replace("#","'",$colDes_Prd)."</TD>";
			$sLinea .= "<TD>".$colCtdSze_Prd."</TD>";
			$sLinea .= "<TD align=\"center\">".$colCtd_Prd."</TD>";
			$sLinea .= "<TD style=\"padding-right: 3px; text-align: right\">".number_format($colPrc_Prd,0,',','.')."</TD>";
			$sLinea .= "<TD style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\">".number_format($colMto_Prd,0,',','.')."</TD></TR>";
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

					$sLinea  = "<TR><TD style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">&nbsp;</TD>";
					$sLinea .= "<TD>&nbsp;</TD>";
					$sLinea .= "<TD>".$colDes_Prd."</TD>";
					$sLinea .= "<TD>".$colCtdSze_Prd."</TD>";
					$sLinea .= "<TD align=\"center\">".$colCtd_Prd."</TD>";
					$sLinea .= "<TD style=\"padding-right: 3px; text-align: right\">".number_format($colPrc_Prd,0,',','.')."</TD>";
					$sLinea .= "<TD style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\">".number_format($colMto_Prd,0,',','.')."</TD></TR>";
					echo $sLinea;
				}
			}
			
			if ($Cod_Iva == 2) {
				$sLinea  = "<TR><TD colspan=\"5\" style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">&nbsp;</TD>";
				$sLinea .= "<TD style=\"padding-right: 3px; text-align: right\"><strong>Neto</strong></TD>";
				$sLinea .= "<TD style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\"><strong>".number_format($dfSubTotal,0,',','.')."</strong></TD></TR>";
				echo $sLinea;
				
				$mtoiva = $IVA * $dfSubTotal;
				$sLinea  = "<TR><TD colspan=\"5\" style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">&nbsp;</TD>";
				$sLinea .= "<TD style=\"padding-right: 3px; text-align: right\"><strong>IVA</strong></TD>";
				$sLinea .= "<TD style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\"><strong>".number_format($mtoiva,0,',','.')."</strong></TD></TR>";
				echo $sLinea;
			}
			$sLinea  = "<TR><TD colspan=\"5\" style=\"padding-left: 2px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid;\">&nbsp;</TD>";
			$sLinea .= "<TD style=\"padding-right: 3px; text-align: right; border-bottom: goldenrod 1px solid;\"><strong>TOTAL</strong></TD>";
			$sLinea .= "<TD style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid; border-bottom: goldenrod 1px solid;\"><strong>".number_format($dfSubTotal+$mtoiva,0,',','.')."</strong></TD></TR>";
			echo $sLinea;
		}
?>
</TABLE>
</TD>
</TR>
<TR>
	<TD class="dato5p12s" width="60%" VALIGN="TOP" style="padding-top: 5px"><B>Documento de Venta</B></TD>
	<TD width="20%" VALIGN="TOP">&nbsp;</TD>
	<TD width="20%" VALIGN="TOP">&nbsp;</TD>
</TR>
<TR>
	<TD colspan="3" class="dato5p12s" style="padding-bottom: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid; border-top: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
	<tr>
		<td class="dato" width="25%" style="text-align: right"><INPUT name="rbDocumento[]" type="radio" class="button2" value="1" <?php if ($tip_docsii == 1) echo "checked "; ?> DISABLED/></td>
		<td class="dato" width="25%" align="left">Boleta</td>
		<td class="dato" width="25%" style="text-align: right"><INPUT name="rbDocumento[]" type="radio" class="button2" value="2" <?php if ($tip_docsii == 2) echo "checked "; ?> DISABLED/></td>
		<td class="dato" width="25%" align="left">Factura</td>
	</tr>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD class="dato5p12s" width="60%" VALIGN="TOP" style="padding-top: 8px"><B>Informaci&oacute;n de Pago</B></TD>
	<TD width="20%" VALIGN="TOP">&nbsp;</TD>
	<TD width="20%" VALIGN="TOP">&nbsp;</TD>
</TR>
<TR>
	<TD colspan="3">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%">
	<tr>
		<td width="50%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">
		M&eacute;todo de Pago: Transferencia Bancaria
		</td>
		<td width="50%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">
		Banco: <?php echo $nom_bco; ?>
		</td>
	</tr>
	<tr>
		<td width="50%" style="padding-left: 5px; 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
		Destinatario: <BR>IMPORTADORA Y COMERCIALIZADORA VESTMED LTDA.
		</td>
		<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">
		&nbsp;
		</td>
	</tr>
	<tr>
		<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
		RUT: 77.772.240-1
		</td>
		<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">
		# Transacci&oacute;n : <?php echo $num_trn ?>  
		</td>
	</tr>
	<tr>
		<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
		Cuenta #: 29293847
		</td>
		<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">&nbsp;</td>
	</tr>
	<tr>
		<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
		Banco: BCI
		</td>
		<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">&nbsp;</td>
	</tr>
	<tr>
		<td width="50%" style="padding-left: 5px; padding-bottom: 10px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">
		mail: ventas@vestmed.cl
		</td>
		<td width="50%" style="padding-left: 5px; padding-bottom: 10px; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">
		Comprobante Transferencia : <?php echo $arc_adj; ?>
		</td>
	</tr>
	</TABLE>
	</TD>
</TR>
<TR>
  <TD colspan="3" align="right" style="PADDING-TOP: 5px">
      <input type="submit" name="Cerrar" value=" Volver " class="btn" />
  </TD>
</TR>
<TR>
	<TD VALIGN="TOP" class="dato10p" colspan="3">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%" ALIGN="center">
			<tr><TD width="100%" VALIGN="TOP" class="dato5p12s"><B>Mensaje</B></TD></tr>
			<TR><TD width="100" VALIGN="TOP" class="dato10p">Consultas realizadas: 
				<?php 
					echo $tot_cnaemp;
					if ($tot_sinresclt > 0) echo " (<a href=\"javascript:consultar()\">".$tot_sinresclt." sin responder</a>)";
				?> 
				</TD>
			</TR>
			<TR><TD width="100" VALIGN="TOP" class="dato10p">Nueva Consulta: <a href="javascript:consultar()">Aqu&iacute;</a></TD></TR>
		</TABLE>
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
</TABLE>
</form>
			</div>
		</div>
	</div>
    <?php if ($Perfil == 0) { ?>
	<div id="footer"></div>
    <?php } ?>
</body>
<script language="javascript">
	var f1 = document.F1;	
	var f2 = document.F2;
</script>
</html>
