<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$Flag_Print = (isset($_GET['prn'])) ? ok($_GET['prn']) : 0;

$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if (($row = mssql_fetch_array($result))) {
	$fec_cot   = date("d/m/Y", strtotime($row['Fec_Cot']));
	$Num_Cot   = $row['Num_Cot'];
	$cod_clt   = $row['Cod_Clt'];
	$cod_tipper = $row['Cod_TipPer'];
	if ($cod_tipper == 1)
		$nom_clt = $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'];
	else
		$nom_clt = $row['RznSoc_Per'];
        $nom_clt   = utf8_encode($nom_clt);
	$num_doc   = $row['Num_Doc'];
	$cod_suc   = $row['Cod_Suc'];
	$dir_suc   = utf8_encode($row['Dir_Suc']);
	$cod_cmn   = $row['Cod_Cmn'];
	$cod_cdd   = $row['Cod_Cdd'];
	$cod_per   = $row['Cod_Per'];
	$fon_ctt   = $row['Fon_Ctt'];
	$mail_ctt  = $row['Mail_Ctt'];
	$cod_pre   = $row['Cod_Pre'];
	$obs_cot   = ($row['Obs_Cot'] == "_NONE" ? "" : $row['Obs_Cot']);
        $peso_per  = $row['Cot_Peso'];
        $estatura  = $row['Cot_Estatura'];
        $is_otro   = $row['Cot_FlgTer'];
	$is_dsp    = $row['is_dsp'];
	$val_dsp   = 0;
	if ($is_dsp == 1) {
		$cod_crr    = $row['Cod_Crr'];
		$cod_svccrr = $row['Cod_SvcCrr'];
		$cod_tipsvc = $row['Cod_TipSvcCrr'];
		$cod_sucdsp = $row['Cod_SucDsp'];
		$cod_cmndsp = $row['Cod_CmnDsp'];
		$cod_cdddsp = $row['Cod_CddDsp'];
		$dir_sucdsp = utf8_encode($row['Dir_SucDsp']);
		$val_dsp    = $row['Val_Dsp'];

		$result = mssql_query("vm_CrrCmb $cod_crr", $db);
		if (($row = mssql_fetch_array($result))) $des_crr = $row['Des_Crr'];

		$result = mssql_query("vm_SvcCrrCmb $cod_crr, $cod_svccrr", $db);
		if (($row = mssql_fetch_array($result))) $des_svccrr = $row['Des_SvcCrr'];
		
		$result = mssql_query("vm_cmn_s $cod_cmndsp", $db);
		if (($row = mssql_fetch_array($result))) $nom_cmndsp = utf8_encode($row['Nom_Cmn']);
		
		$result = mssql_query("vm_cdd_s $cod_cdddsp", $db);
		if (($row = mssql_fetch_array($result))) $nom_cdddsp = utf8_encode($row['Nom_Cdd']);

		if ($cod_sucdsp > 0) {
			$result = mssql_query("vm_suc_s $cod_clt, $cod_sucdsp", $db);
			if (($row = mssql_fetch_array($result))) $nom_sucdsp = utf8_encode($row['Nom_Suc']);
		}
		else $nom_sucdsp = "Oficina Carrier";
		
		/* Calculo del Peso en base a la cotizacion original */
		$peso = 0.0;
		$result = mssql_query("vm_pvw_rescot $Cod_Cot",$db);
		while (($row = mssql_fetch_array($result))) $peso += $row["Peso_Uni"]*$row["Val_Ctd"];
	}
	
	$result = mssql_query("vm_cmn_s $cod_cmn", $db);
	if (($row = mssql_fetch_array($result))) $nom_cmn = utf8_encode($row['Nom_Cmn']);
	
	$result = mssql_query("vm_cdd_s $cod_cdd", $db);
	if (($row = mssql_fetch_array($result))) $nom_cdd = utf8_encode($row['Nom_Cdd']);
	
	$result = mssql_query("vm_suc_s $cod_clt, $cod_suc", $db);
	if (($row = mssql_fetch_array($result))) $nom_suc = utf8_encode($row['Nom_Suc']);

	$result = mssql_query("vm_ctt_s $cod_clt, $cod_suc", $db);
	while(($row = mssql_fetch_array($result)))
		if ($row['Cod_Per'] == $cod_per) $nom_ctt = utf8_encode($row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per']);
							
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
	
	/* Consultas realizadas por el Usuario a Vestmed */
	$tot_cnaclt = 0;
	$result = mssql_query("vm_totcna_totres $Cod_Cot, $cod_per");
	if (($row = mssql_fetch_array($result))) {
		$tot_cnaclt    = $row["tot_cna"];
		$tot_sinresclt = $row["tot_sinresclt"];
	}
	
	/* Consultas realizadas por Vestmed al Usuario */
	//$tot_cnaemp = 0;
	//$result = mssql_query("vm_totcna_totres $Cod_Cot, 0");
	//if (($row = mssql_fetch_array($result))) {
	//	$tot_cnaemp = $row["tot_cna"];
	//	$tot_sinresclt = $row["tot_sinres"];
	//}
	
}
$IVA = 0.0;
if ($Cod_Iva == 2) {
	$result = mssql_query("vm_getfolio_s 'IVA'",$db);
	if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;
}
else {
	$result = mssql_query("vm_getfolio_s 'IVA'",$db);
	if (($row = mssql_fetch_array($result))) $valiva = $row['Tbl_fol'] / 10000.0;
	$val_dspCIva = $val_dsp + $val_dsp * $valiva;
}

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

function DisplayTallas ($Tallas,$Stock) {
	$num_col = 0;
	$retorno = "<table BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" ALIGN=\"left\"><tr>\n";
	foreach ($Tallas as $key => $value) {
		$num_col++;
		if ($num_col > 3) {
			$retorno .= "</tr><tr>\n";
			$num_col = 1;
		}
		if ($Stock[$key] == 0) 
			$retorno .=  "<td><img src=\"./images/punto_verde.jpg\"></td><td style=\"padding-right: 7px\"><span class=\"con_stock\">".$key."(".$value.")</span></td>";
		else 
			$retorno .=  "<td><img src=\"./images/punto_rojo.jpg\"></td><td style=\"padding-right: 7px\"><span class=\"sin_stock\">".$key."(".$value.")</span></td>";
	}
	for ($i = $num_col; $i < 3; $i++) $retorno .= "<td colspan=\"2\">&nbsp;</td>";
	$retorno .=  "</tr></table>\n";
	
	return $retorno;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/tr/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/headers.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="lytebox/lytebox.css" media="screen" />
<script type="text/javascript" src="Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<!-- Lytebox Includes //-->
<script type="text/javascript">
    //new UvumiDropdown('dropdown-scliente');
    var $j = jQuery.noConflict();

    $j(document).ready
	(
            function()
            {
            $j("form#searchConsultas").submit(function(){
                $j.post("ajax-search.php",{
                        search_type: "cnamsj",
                        param_filter: "<?php echo $Cod_Cot ?>",
                        param_codper: "<?php echo $cod_per ?>"
                }, function(xml) {
                        RefrescarMensajes(xml);
                });
                return false;
            });

            //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
            }
	);

    function popwindow(ventana,altura){
       window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=800,height="+altura);
    }

    function ver_producto(cod_prd) {
            popwindow("preview_prd.php?prd="+cod_prd,400)
    }

    function ver_cotizaciones() {
            f2.action = "miscotizaciones.php";
            f2.submit();
    }

    function consultar() {
            popwindow("consultasusr.php?cot=<?php echo $Cod_Cot ?>&caso=web",540);
    }

    function imp_cotizacion(cot) {
            popwindow("preview_cot.php?cot="+cot+"&prn=1",600);
    }

    function send_cot(cot) {
            f2.action = "aviso.php?idmsg=6&cot="+cot;
            f2.submit();
    }

    function ActualizarConsultas()
    {
        $j("form#searchConsultas").submit();
    }

    function RefrescarMensajes(xml)
    {
        var tot_cnaclt=0;
        var tot_sinresclt=0;
        var total=0;

        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                tot_cnaclt    = parseInt($j("cnaclt",filter).text());
                tot_sinresemp = parseInt($j("sinresemp",filter).text());
                //tot_cnaemp    = parseInt($j("cnaemp",filter).text());
                tot_sinresclt = parseInt($j("sinresclt",filter).text());
            }
	);

        total = tot_cnaclt; // + tot_cnaemp;
        options="<table id=\"tblConsultas\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"2\" width=\"100%\" ALIGN=\"center\">\n";
	options+="<tr><td width=\"100%\" VALIGN=\"TOP\" class=\"dato5p12s\"><b>Mensaje</b></td></tr>\n";
        options+="<tr>\n";
        options+="<td width=\"100\" VALIGN=\"TOP\" class=\"dato10p\">Consultas Realizadas: "+total;
        if (total > 0)
            options+=" (<a href=\"javascript:consultar(<?php echo $Cod_Cot ?>)\">VER</a>)"
        options+="</td>\n";
        options+="</tr>\n";
        options+="<tr>\n";
        options+="<td width=\"100\" VALIGN=\"TOP\" class=\"dato10p\">Consultas Sin Leer: "+tot_sinresclt;
        if (tot_sinresclt > 0)
            options+=" (<a href=\"javascript:consultar(<?php echo $Cod_Cot ?>)\">LEER</a>)"
        options+="</td>\n";
        options+="</tr>\n";
        options+="<tr><td width=\"100\" VALIGN=\"TOP\" class=\"dato10p\">Nueva Consulta: <a href=\"javascript:consultar(<?php echo $Cod_Cot; ?>)\">Aqu&iacute;</a></td></tr>\n";
        options+="</table>\n";

        $j("#tblConsultas").replaceWith(options);
    }

</script>
</head>

<body>
<div id="body">
    <?php if ($Flag_Print == 0) { ?>
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
	<?php 
		if ($cod_per == 0) { 
	?>
    <ul id="usuario_registro">
        <form ID="F1" method="POST" name="F1" action="">
    	<li class="back-verde registro"><a href="registrarse.php">REGISTRARSE</a></li>
        <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
        <li class="back-verde"><a href="javascript:ValidarLogin()">ENTRAR</a></li>
        <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
        <li class="back-verde">CONTRASE&Ntilde;A</li>
        <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')" /></li>
        <li class="back-verde">RUT</li>
		<input type="hidden" name="dfrut" id="dfrut" />
		</form>
    </ul>
	<?php }
		  else {
	?>
    <ul id="usuario_registro">
		<?php 	echo display_login($cod_per, $cod_clt, $db); ?>
    </ul>
	
	<?php 
		}
	?>
    <?php } ?>
    <div id="work">
		<div id="<?php if ($Flag_Print == 0) echo "back-registro3"; else echo "back-registro4"; ?>">
			<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" alt="0" />
           	<div style="width:765px; height:auto; margin:0 auto 0 100px; padding-top:10px;">
<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%">
<tr>
	<td width="50%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><img SRC="images/logo.png" width="235" HEIGHT="130" alt="" /></td>
	<td class="dato" style="text-align: right; FONT-SIZE: 2.5em; FONT-FAMILY: Arial, Verdana;" width="50%" VALIGN="bottom" COLSPAN="2"><b>COTIZACI&Oacute;N <?php echo $Num_Cot; ?></b></td>
</tr>
<tr>
	<td class="dato" style="text-align: right" width="50%" VALIGN="TOP" COLSPAN="2" HEIGHT="65">
	<b>Fecha: </b><?php echo $fec_cot ?><br /><b>Estado: </b>Publicada
	</td>
</tr>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="dato5p12s" style="padding-top: 10px;"><b>Informaci&oacute;n del Cliente</b></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p12s" style="padding-top: 3px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid;"><b>Cliente: <?php echo $nom_clt ?></b></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" style="padding-top: 3px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">&nbsp;</td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Rut: <?php echo formatearRut($num_doc) ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" style="padding-top: 2px; border-right: goldenrod 1px solid;">&nbsp;</td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Sucursal: <?php echo $nom_suc ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; border-right: goldenrod 1px solid;">Contacto: <?php echo $nom_ctt ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Direcci&oacute;n: <?php echo $dir_suc ?></FONT></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; border-right: goldenrod 1px solid;">Tel&eacute;fono: <?php echo $fon_ctt ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Ciudad: <?php echo $nom_cdd ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; border-right: goldenrod 1px solid;">Fax: <?php echo $fon_ctt ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; padding-bottom: 3px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">Comuna: <?php echo $nom_cmn ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; padding-bottom: 3px; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">Email: <?php echo $mail_ctt; ?></td>
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
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 3px; padding-bottom: 3px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">Peso: <?php echo $peso_per ?> Kg</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 3px; padding-bottom: 3px; border-right: goldenrod 1px solid; border-top: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">Estatura: <?php echo $estatura; ?> cm</td>
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
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 3px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid;">Carrier: <?php echo $des_crr ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 3px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">Comuna: <?php echo $nom_cmndsp; ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Servicio: <?php echo $des_svccrr; ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; border-right: goldenrod 1px solid;">Ciudad: <?php echo $nom_cdddsp ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">
	Sucursal: <?php echo $nom_sucdsp ?>
	<?php 
		if ($cod_tipsvc == 0) echo " (a Domicilio)";
		if ($cod_tipsvc == 1) echo " (a Sucursal del carrier)";
	?>
	</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; border-right: goldenrod 1px solid;">
	Gastos Despacho: <?php echo ($Cod_Iva == 1) ? number_format($val_dspCIva,0,',','.') : number_format($val_dsp,0,',','.'); ?>
	</td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; padding-bottom: 3px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">Direcci&oacute;n: <?php echo $dir_sucdsp; ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="padding-top: 2px; padding-bottom: 3px; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">Peso: <?php echo number_format($peso,2,',','.') ; ?> Kg</td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP">&nbsp;</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<?php } ?>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="dato5p12s"><B>Productos Seleccionados</B></td>
</tr>
<tr>
<td VALIGN="TOP" COLSPAN="3" style="padding-bottom: 1px">
<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
<?php
$item = 0;
$NetoTot = 0;
$Cod_Mca = "";
$Cod_GrpPrd = "";
$Cod_Dsg = "";
$Cod_Pat = "";
$Prc_Uni = 0;
$Val_Des = 0;
$Tallas = "";
$bPrimero = true;
$bXisSinStock = false;
$result = mssql_query("vm_pvw_rescot $Cod_Cot",$db);
while (($row = mssql_fetch_array($result))) {
	if ($Cod_Mca != $row['Cod_Mca'] Or $Cod_GrpPrd != $row['Cod_GrpPrd'] Or	$Cod_Dsg != $row['Cod_Dsg']) {
		if (!$bPrimero) {
			//$Des_Sze = "";
			//foreach ($Tallas as $key => $value) 
			//	if ($Stock[$key] == 0) $Des_Sze = "<span class=\"sin_stock\">".$Des_Sze.$key."(".$value.")</span>&nbsp;";
			//	else $Des_Sze = "<span class=\"con_stock\">".$Des_Sze.$key."(".$value.")</span>&nbsp;";
			$Des_Sze = DisplayTallas($Tallas,$Stock);
			$Prc_UniDes = intval($Prc_UniAnt - $Prc_UniAnt*$Val_Des/100.0 + 0.5);
			$Neto = $Tot_Ctd * $Prc_UniDes; // * (1 - $Flg_SinInv);
			
			if ($Flg_SinInv == 0) {
				$class = "dato"; 
				$punto = "&nbsp;";
			}
			else {
				$class="dato";
				$punto = "&nbsp;";
				$bXisSinStock = true;
			}
?>
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px; border-left: goldenrod 1px solid;"><?php echo $Key_PatAnt; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="23%" style="padding-left: 3px"><?php echo $Des_PatAnt; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="30%" style="padding-left: 3px"><?php echo $Des_Sze; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="2%" style="padding-left: 3px"><?php echo $punto; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px"><?php echo $Tot_Ctd; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px"><?php echo number_format($Prc_UniAnt,0,',','.'); ?></td>
	<td valign="top" class="<?php echo $class ?>" width="5%" style="padding-left: 3px"><?php echo $Val_Des."%"; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo number_format($Neto,0,',','.'); ?></td>
	</tr></table>
	</td>
</tr>
<?php
			$Key_PatAnt = $row['Key_Pat'];
			$Des_PatAnt = str_replace("#","'",utf8_encode($row['Des_Pat']));
			$Cod_PatAnt = $row['Cod_Pat'];
			$Prc_UniAnt = $row['Prc_Uni'];
			$Val_Des 	= $row['Val_Des'];
			$Flg_SinInv = $row['Flg_SinInv'];
			
			$Tot_Ctd	= 0;
			$NetoTot+=$Neto;
			if (isset($Tallas)) unset($Tallas);
			if (isset($Stock)) unset($Stock);
		}
		$Cod_Mca      = $row['Cod_Mca'];
		$Cod_LinMca   = $row['Cod_LinMca'];
		$Cod_GrpPrd   = $row['Cod_GrpPrd'];
		$Cod_Dsg 	  = $row['Cod_Dsg'];
		$grpprd_title = $Cod_Sty." ".utf8_encode($Nom_Dsg);
		$Prc_Nto 	  = $row['Prc_Nto'];
		$Org_Mca      = utf8_encode($row['Org_Mca']);
		$Val_Des 	  = $row['Val_Des'];
		$Cod_Sty	  = $row['Cod_Sty'];
		$Nom_Dsg	  = str_replace("#","'",utf8_encode($row['Nom_Dsg']));
		$Des_GrpPrd   = str_replace("#","'",utf8_encode($row['Des_GrpPrd']));
		$Des_Mat	  = str_replace("#","'",utf8_encode($row['Des_Mat']));
		$Cod_Prd	  = $row['Cod_Prd'];
		$Prc_UniAnt   = $row['Prc_Uni'];
		$Cod_PatAnt   = $row['Cod_Pat'];
		$Key_PatAnt   = $row['Key_Pat'];
		$Des_PatAnt	  = str_replace("#","'",utf8_encode($row['Des_Pat']));
		$Flg_SinInv   = $row['Flg_SinInv'];
		$bPrimero     = false;
		$Tot_Ctd	  = 0;
		if (isset($Tallas)) unset($Tallas);
		if (isset($Stock)) unset($Stock);
		
		$item++;
?>
<?php if ($item > 1) { ?>
<tr><td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">&nbsp;</td></tr>
<?php } ?>
<tr>
	<td class="label_left_top" style="text-align: center" width="5%"><?php echo $item ?></td>
	<td class="label_left_top" style="text-align: center" width="10%">
	<a rev="width: 602px; height: 490px; border: 0 none; scrolling: auto;" title="<?php echo $grpprd_title ?>" rel="lyteframe[imagenes<?php echo $item ?>]" href="catalogo/imagenes-producto.php?producto=<?php echo $Cod_GrpPrd ?>"><img src="<?php echo printimg_addr("img1_grupo",$Cod_GrpPrd) ?>" width="100" class="cursor image-producto" /></a>	</td>
	<td class="label_left_right_top" valign="top" style="text-align: center" width="55%"><?php GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Cod_LinMca, $Tallas); ?></td>
</tr>
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td class="titulo_tabla5p" width="10%">Color</td>
	<td class="titulo_tabla5p" width="23%">Descripci&oacute;n</td>
	<td class="titulo_tabla5p" width="30%">Tama&ntilde;o(Cantidad)</td>
	<td class="titulo_tabla5p" width="2%">&nbsp;</td>
	<td class="titulo_tabla5p" width="10%">Total</td>
	<td class="titulo_tabla5p" width="10%">Precio<br>Unitario</td>
	<td class="titulo_tabla5p" width="5%">Desc</td>
	<td class="titulo_tabla5p" width="10%" style="padding-right: 3px; text-align: right">Monto<br>Total</td>
	</tr></table>
	</td>
</tr>
<?php 
	}
	//if ($row['Cod_Pat'] != $Cod_PatAnt Or $row['Flg_SinInv'] != $Flg_SinInv Or $row['Prc_Uni'] != $Prc_UniAnt Or $Val_Des != $row['Val_Des']) {
	if ($row['Cod_Pat'] != $Cod_PatAnt Or $row['Prc_Uni'] != $Prc_UniAnt Or $Val_Des != $row['Val_Des']) {
		//$Des_Sze = "";
		//foreach ($Tallas as $key => $value) 
		//	if ($Stock[$key] == 0) $Des_Sze = "<span class=\"sin_stock\">".$Des_Sze.$key."(".$value.")</span>&nbsp;";
		//	else $Des_Sze = "<span class=\"con_stock\">".$Des_Sze.$key."(".$value.")</span>&nbsp;";
		$Des_Sze = DisplayTallas($Tallas,$Stock);
		$Prc_UniDes = intval($Prc_UniAnt - $Prc_UniAnt*$Val_Des/100.0 + 0.5);
		$Neto = $Tot_Ctd * $Prc_UniDes; // * (1 - $Flg_SinInv);
		
		if ($Flg_SinInv == 0) {
			$class = "dato"; 
			$punto = "&nbsp;";
		}
		else {
			$class="dato";
			$punto = "&nbsp;";
			$bXisSinStock = true;
		}
		
?>
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px; border-left: goldenrod 1px solid;"><?php echo $Key_PatAnt; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="23%" style="padding-left: 3px"><?php echo $Des_PatAnt; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="30%" style="padding-left: 3px"><?php echo $Des_Sze; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="2%" style="padding-left: 3px"><?php echo $punto; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px"><?php echo $Tot_Ctd; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px"><?php echo number_format($Prc_UniAnt,0,',','.'); ?></td>
	<td valign="top" class="<?php echo $class ?>" width="5%" style="padding-left: 3px"><?php echo $Val_Des."%"; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo number_format($Neto,0,',','.'); ?></td>
	</tr></table>
	</td>
</tr>
<?php
		$Key_PatAnt = $row['Key_Pat'];
		$Des_PatAnt = str_replace("#","'",$row['Des_Pat']);
		$Cod_PatAnt = $row['Cod_Pat'];
		$Prc_UniAnt = $row['Prc_Uni'];
		$Flg_SinInv = $row['Flg_SinInv'];
		$Val_Des 	= $row['Val_Des'];
		
		$Tot_Ctd	= 0;
		$NetoTot+=$Neto;
		if (isset($Tallas)) unset($Tallas);
		if (isset($Stock)) unset($Stock);
	}
	if (!isset($Tallas)) $Tallas = array($row['Val_Sze'] => $row['Val_Ctd']);
	else $Tallas[$row['Val_Sze']] = $Tallas[$row['Val_Sze']] + $row['Val_Ctd'];
	if (!isset($Stock)) $Stock = array($row['Val_Sze'] => $row['Flg_SinInv']);
	else $Stock[$row['Val_Sze']] = $Stock[$row['Val_Sze']] + $row['Flg_SinInv'];
	$Tot_Ctd += $row['Val_Ctd'];
}
//$Des_Sze = "";
//foreach ($Tallas as $key => $value) 
//	if ($Stock[$key] == 0) $Des_Sze = "<span class=\"sin_stock\">".$Des_Sze.$key."(".$value.")</span>&nbsp;";
//	else $Des_Sze = "<span class=\"con_stock\">".$Des_Sze.$key."(".$value.")</span>&nbsp;";
$Des_Sze = DisplayTallas($Tallas,$Stock);	
$Prc_UniDes = intval($Prc_UniAnt - $Prc_UniAnt*$Val_Des/100.0 + 0.5);
$Neto = $Tot_Ctd * $Prc_UniDes; // * (1 - $Flg_SinInv);
$NetoTot+=$Neto;

if ($Flg_SinInv == 0) {
	$class = "dato"; 
	$punto = "&nbsp;";
}
else {
	$class="dato";
	$punto = "&nbsp;";
	$bXisSinStock = true;
}

?>
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px; border-left: goldenrod 1px solid;"><?php echo $Key_PatAnt; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="23%" style="padding-left: 3px"><?php echo $Des_PatAnt; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="30%" style="padding-left: 3px"><?php echo $Des_Sze; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="2%" style="padding-left: 3px"><?php echo $punto; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px"><?php echo $Tot_Ctd; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-left: 3px"><?php echo number_format($Prc_UniAnt,0,',','.'); ?></td>
	<td valign="top" class="<?php echo $class ?>" width="5%" style="padding-left: 3px"><?php echo $Val_Des."%"; ?></td>
	<td valign="top" class="<?php echo $class ?>" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo number_format($Neto,0,',','.'); ?></td>
	</tr></table>
	</td>
</tr>
<tr><td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid; height: 5px">&nbsp;</td></tr>
</table>
</td>
</tr>
<tr>
	<td width="100%" align="left" valign="top" colspan="3" class="dato" style="padding-bottom: 10px">
		<table BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%" ALIGN="left">
		<tr><td width="20px"><img src="./images/punto_verde.jpg" alt="0" /></td><td>Productos CON Stock Disponible</td></tr>
		<tr><td width="20px"><img src="./images/punto_rojo.jpg" alt="0" /></td><td>Productos SIN Stock</td></tr>
		</table>
	</td>
</tr>
<tr>
<td width="50%" align="left" valign="top">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%" ALIGN="center">
		<tr><td width="100%" VALIGN="TOP" class="dato5p12s"><B>Condiciones Generales</B></td></tr>
		<tr><td class="dato10p" width="60%" VALIGN="TOP"><LI>Precio <?php echo ($cod_pre == 1) ? "Minorista" : "Mayorista"; ?> <?php echo ($Cod_Iva == 1) ? "IVA incluido" : "mas IVA"; ?></LI></td></tr>
		<tr><td class="dato10p" width="60%" VALIGN="TOP"><LI>Cotizaci&oacute;n valida por 15 dias</LI></td></tr>
		<tr><td class="dato10p" width="60%" VALIGN="TOP"><LI>Precios sujetos a la variaci&oacute;n del dolar</LI></td></tr>
<?php if ($bXisSinStock) { ?>
<tr><td VALIGN="TOP" class="dato10p"><LI>Esta cotizaci&oacute;n incluye productos sin stock</LI></td></tr>
<?php } ?>		
	</table>
</td>
<td width="50%" align="rigth" valign="top" colspan="2">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="1" width="100%" ALIGN="right">
		<?php if ($Val_DesG > 0) { ?>
		<tr>
		<td width="100%" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
		Desc. Especial: <input name="dfDescEsp" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo $Val_DesG ?> %" ReadOnly />
		</td>
		</tr>
		<?php } ?>

		<?php if ($is_dsp > 0) { ?>
		<tr>
		<td width="100%" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
		Subtotal Productos: <INPUT name="dfDespacho" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($NetoTot,0,',','.') ?>" ReadOnly />
		</td>
		</tr>

		
		<tr>
		<td width="100%" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
		<?php if ($Cod_Iva == 2) { ?>
		Gastos Despacho: <INPUT name="dfNeto" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($val_dsp,0,',','.') ?>" ReadOnly />
		<?php } else { ?>
		Gastos Despacho: <INPUT name="dfNeto" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($val_dspCIva,0,',','.') ?>" ReadOnly />
		<?php } ?>
		</td>
		</tr>
		<?php } ?>

		<tr>
		<td width="100%" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
		<?php if ($Cod_Iva == 2) { ?>
		Neto: <INPUT name="dfNeto" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($NetoTot-$NetoTot*$Val_DesG/100.0+$val_dsp,0,',','.') ?>" ReadOnly />
		<?php } else { ?>
		&nbsp;
		<?php } ?>
		</td>
		</tr>
		
		<tr>
		<td width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
		<?php if ($Cod_Iva == 2) { ?>
		IVA: <INPUT name="dfIVA" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($IVA * ($NetoTot-$NetoTot*$Val_DesG/100.0+$val_dsp),0,',','.'); ?>" ReadOnly />
		<?php } else { ?>
		&nbsp;
		<?php } ?>
		</td>
		</tr>
		
		<tr>
		<td width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
		<?php if ($Cod_Iva == 2) { ?>
		Total: <INPUT name="dfTotal" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($NetoTot-$NetoTot*$Val_DesG/100.0+$val_dsp+($IVA*($NetoTot-$NetoTot*$Val_DesG/100.0+$val_dsp)),0,',','.'); ?>" ReadOnly />
		<?php } else { ?>
		Total: <INPUT name="dfTotal" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($NetoTot-$NetoTot*$Val_DesG/100.0+$val_dspCIva,0,',','.'); ?>" ReadOnly />
		<?php } ?>
		</td>
		</tr>
		
		
	</table>
</td>
</tr>

<tr>
<td width="50%" align="left" valign="top">
        <form id="searchConsultas" action="">
	<table id="tblConsultas" BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%" ALIGN="center">
		<tr><td width="100%" VALIGN="TOP" class="dato5p12s"><b>Mensaje</b></td></tr>
		<tr>
		    <td width="100" VALIGN="TOP" class="dato10p">Consultas Realizadas:
			<?php 
				echo $tot_cnaclt;
				if (($tot_cnaclt) > 0) echo " (<a href=\"javascript:consultar()\">VER</a>)";
			?> 
			</td>
		</tr>
		<tr>
		    <td width="100" VALIGN="TOP" class="dato10p">Consultas Sin Leer:
			<?php 
				echo $tot_sinresclt;
				if ($tot_sinresclt > 0) echo " (<a href=\"javascript:consultar()\">LEER</a>)";
			?> 
			</td>
		</tr>
		<tr><td width="100" VALIGN="TOP" class="dato10p">Nueva Consulta: <a href="javascript:consultar()">Aqu&iacute;</a></td></tr>
	</table>
        </form>
</td>
<td width="50%" align="left" valign="top">
    <?php if ($Flag_Print == 0) { ?>
        <form ID="F2" method="POST" name="F2" ACTION="ordendecompra.php?cot=<?php echo $Cod_Cot ?>">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%" ALIGN="center">
	    <td align="right" style="PADDING-TOP: 5px; PADDING-BOTTOM: 5px">
		    <input type="button" name="Cerrar" value=" Volver " class="btn" onclick="javascript:ver_cotizaciones()" />
		    <input type="button" name="Imprimir" value=" Imprimir " class="btn" onclick="javascript:imp_cotizacion(<?php echo $Cod_Cot; ?>)" />
			<?php if ($cod_clt == 50002 or $cod_clt == 50001 or 1 == 1) { ?>
		    <input type="submit" name="Enviar" value=" Comprar " class="btn" />
			<?php } else { ?>
		    <input type="button" name="Enviar" value=" Comprar " class="btn" onclick="javascript:send_cot(<?php echo $Cod_Cot; ?>)" />
			<?php } ?>
	    </td>
	</table>
        </form>
    <?php } else { ?>
    &nbsp;	
    <?php } ?>
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
			</div>
		</div>
	</div>
    <?php if ($Flag_Print == 0) { ?>
	<div id="footer"></div>
    <?php } ?>	
<script language="javascript">
	var f1 = document.F1;	
	var f2 = document.F2;
    <?php if ($Flag_Print == 1) { ?>
	window.print();
	window.close();
	<?php } ?>
</script>
</body>
</html>
