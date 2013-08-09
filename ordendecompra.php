<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$Paso = (isset($_GET['paso'])) ? ok($_GET['paso']) : 1;
$DesPaso = split("/", "/Seleccionar Productos/Pagar/Enviar");

$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if (($row = mssql_fetch_array($result))) {
	$fec_cot   = date("d/m/Y", strtotime($row['Fec_Cot']));
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
		$peso_max   = $row['Val_PsoMax'];


		$result = mssql_query("vm_CrrCmb $cod_crr", $db);
		if (($row = mssql_fetch_array($result))) $des_crr = $row['Des_Crr'];

		$result = mssql_query("vm_SvcCrrCmb $cod_crr, $cod_svccrr", $db);
		if (($row = mssql_fetch_array($result))) $des_svccrr = $row['Des_SvcCrr'];
		
		$result = mssql_query("vm_cmn_s $cod_cmndsp", $db);
		if (($row = mssql_fetch_array($result))) $nom_cmndsp = utf8_encode($row['Nom_Cmn']);
		
		$result = mssql_query("vm_cdd_s $cod_cdddsp", $db);
		if (($row = mssql_fetch_array($result))) {
			$nom_cdddsp = utf8_encode($row['Nom_Cdd']);
			$cod_rgndsp = $row['Cod_Rgn'];
		}

		if ($cod_sucdsp > 0) {
			$result = mssql_query("vm_suc_s $cod_clt, $cod_sucdsp", $db);
			if (($row = mssql_fetch_array($result))) $nom_sucdsp = utf8_encode($row['Nom_Suc']);
		}
		else $nom_sucdsp = "Oficina Carrier";
		
		/* Calculo del Peso en base a la cotizacion original */
		$peso = 0.0;
		$result = mssql_query("vm_pvw_rescot $Cod_Cot",$db);
		while (($row = mssql_fetch_array($result))) $peso += $row["Peso_Uni"];
	}

	$result = mssql_query("vm_cmn_s $cod_cmn", $db);
	if (($row = mssql_fetch_array($result))) $nom_cmn = utf8_encode($row['Nom_Cmn']);
	
	$result = mssql_query("vm_cdd_s $cod_cdd", $db);
	if (($row = mssql_fetch_array($result))) $nom_cdd = utf8_encode($row['Nom_Cdd']);
	
	$result = mssql_query("vm_suc_s $cod_clt, $cod_suc", $db);
	if (($row = mssql_fetch_array($result))) $nom_suc = utf8_encode($row['Nom_Suc']);

	$result = mssql_query("vm_ctt_s $cod_clt, $cod_suc", $db);
	while($row = mssql_fetch_array($result))
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
	/* Consultas realizadas por el ususario a Vestmed */
	$tot_cnaclt = 0;
	$result = mssql_query("vm_totcna_totres $Cod_Cot, $cod_per");
	if (($row = mssql_fetch_array($result))) {
		$tot_cnaclt    = $row["tot_cna"];
		$tot_sinresemp = $row["tot_sinresclt"];
	}
	
	/* Consultas realizadas por el Vestmed al Usuario */
	$tot_cnaemp = 0;
	$result = mssql_query("vm_totcna_totres $Cod_Cot, 0");
	if (($row = mssql_fetch_array($result))) {
		$tot_cnaemp = $row["tot_cna"];
		$tot_sinresclt = $row["tot_sinres"];
	}	
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

/* Consultas realizadas por el Usuario a Vestmed */
$tot_cnaclt = 0;
$result = mssql_query("vm_totcna_totres $Cod_Cot, $cod_per");
if (($row = mssql_fetch_array($result))) {
	$tot_cnaclt    = $row["tot_cna"];
	$tot_sinresclt = $row["tot_sinresclt"];
	$bOkRespuesta = ($tot_sinresclt == 0) ? true : false;
}
	
/* Consultas realizadas por Vestmed al Usuario */
//$tot_cnaemp = 0;
//$result = mssql_query("vm_totcna_totres $Cod_Cot, 0");
//if (($row = mssql_fetch_array($result))) {
//	$tot_cnaemp = $row["tot_cna"];
//	$tot_sinresclt = $row["tot_sinres"];
//}


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

<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
</script>
<LINK href="Include/estilos.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="Include/SoloNumeros.js"></script>
<script type="text/javascript" src="Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
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

    function round(number,X) {
            // rounds number to X decimal places, defaults to 2
            X = (!X ? 2 : X);
            return Math.round(number*Math.pow(10,X))/Math.pow(10,X);
    }

    function CalcularTotal() {
            //alert("CalcularTotal");
            var pesodsp = 0;
            var neto = 0.0;
            var despacho = 0.0;
            var netocondsp = 0;
            var iva = 0;
            var total = 0;

            for (i=0; i<f2.elements.length; i++) {
                    if (f2.elements[i].name == "seleccionPrd[]")
                            if (f2.elements[i].checked) {
                                //alert(eval('f2.Neto'+f2.elements[i].value).value);
                                neto+=parseInt(eval('f2.Neto'+f2.elements[i].value).value);
                                    <?php if ($is_dsp == 1) { ?>
                                pesodsp+=(eval('f2.PesoUni'+f2.elements[i].value).value * eval('f2.dfCtd'+f2.elements[i].value).value);
                                    <?php } ?>
                            }
            }
            if (pesodsp > 0) {
                pesodsp+=0.1;
                pesodsp = round(pesodsp,2);
            }

            <?php if ($is_dsp == 1) { ?>
            
            if (neto > 0) {
                if (pesodsp <= ArrPeso[ArrPeso.length-2])
                    for (i=0; i<ArrPeso.length; i++) {
                        if (pesodsp <= ArrPeso[i]) {
                            despacho = ArrCosto[i];
                            despacho = despacho + despacho * <?php if ($Cod_Iva == 2) echo "0"; else echo $valiva; ?>;
                            f2.dfDespacho.value = Math.round(despacho);
                            f2.dfDespacho.value = formatearMillones(f2.dfDespacho.value);
                            break;
                        }
                    }
                else {
                    delta = Math.round(pesodsp - ArrPeso[ArrPeso.length-2] + 0.4);
                    despacho = ArrCosto[ArrPeso.length-2] + delta * ArrCosto[ArrPeso.length-1];
                    despacho = despacho + despacho * <?php if ($Cod_Iva == 2) echo "0"; else echo $valiva; ?>;
                    f2.dfDespacho.value = Math.round(despacho);
                    f2.dfDespacho.value = formatearMillones(f2.dfDespacho.value);
                }
            }
            else {
                despacho = 0;
                    f2.dfDespacho.value = 0;
            }
            
           
            f2.dfNeto.value = neto;
            f2.dfNeto.value = formatearMillones(f2.dfNeto.value);
            f2.dfPeso.value = pesodsp;
            //f2.dfPeso.value = f2.dfPeso.value;
            <?php } ?>


            if (neto > 0) {
                descuento = neto * <?php echo $Val_DesG / 100.0; ?>;
                neto = neto - descuento;
                netocondsp = neto + despacho;
                iva = netocondsp * <?php echo $IVA; ?>;
                //iva = Math.round(iva);
                total = netocondsp + iva;
            }

            f2.dfNetoConDsp.value = Math.round(netocondsp);
            f2.dfNetoConDsp.value = formatearMillones(f2.dfNetoConDsp.value);

            f2.dfIVA.value = Math.round(iva);
            f2.dfIVA.value = formatearMillones(f2.dfIVA.value);

            f2.dfTotal.value = Math.round(total);
            f2.dfTotal.value = formatearMillones(f2.dfTotal.value);
    }

    function DisplayAviso () {
            alert("Aviso para ser presentado en el caso de una Persona Juridica");
    }

    function ValidarOrden(form) {
            var ok=false;
    <?php if ($Paso == 1) { ?>
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
            if (!f2.rbtransferencia.checked && !f2.rbWebPay.checked) {
                    alert ("Debe indicar la Forma de Pago");
                    return false;
            }
        <?php } else if ($Paso == 2) { ?>
        for (i=0; i<f2.elements.length; i++) {
                    if (f2.elements[i].name == "rbDocumento[]")
                            if (f2.elements[i].checked) {
                               //alert(eval('f2.Neto'+f2.elements[i].value).value);
                               ok = true;
                               break;
                            }
            }
            if (!ok) {
                    alert ("Debe indicar el Tipo de Documento de Venta");
                    return false;
            }
            if (f2.Bco.value == "_NONE") {
                    alert ("Debe indicar el Banco donde realizo la transferencia");
                    return false;
            }
            //if (f2.Numtrnbco.value == "") {
            //	alert ("Debe indicar el numero de transaccion");
            //	return false;
            //}
            f2.Enviar.value = "Enviando";
        <?php } ?>
            f2.Enviar.disabled = true;
            return true;
    }

    function ver_preview (cot,paso) {
            f2.action = (paso == 1) ? "preview_cot.php?cot="+cot : "ordendecompra.php?cot="+cot;
            f2.submit();
    }

    function consultar() {
            popwindow("consultasusr.php?cot=<?php echo $Cod_Cot ?>&caso=web&paso=<?php echo $Paso; ?>",540);
    }

    function setTipDocSii(obj) {
            f2.TipDocSII.value = obj.value;
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
                            <a href="tracking-ordenes.htm">tracking de &Oacute;rdenes</a>
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
        <form ID="F1" AUTOCOMPLETE="off" method="POST" name="F1">
    	<li class="back-verde registro"><a href="registrarse.php">REGIStrARSE</a></li>
        <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
        <li class="back-verde"><a href="javascript:ValidarLogin()">ENtrAR</a></li>
        <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
        <li class="back-verde">CONtrASE&Ntilde;A</li>
        <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')"></li>
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
    <div id="work">
		<div id="back-registro3">
			<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" />
           	<div style="width:765px; height:auto; margin:0 auto 0 100px; padding-top:10px;">
<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" align="center">
<form ID="F2" method="POST" name="F2" ACTION="cotizador/enviaroc.php?cot=<?php echo $Cod_Cot ?>&paso=1" onsubmit="return ValidarOrden(this)" enctype="multipart/form-data">
<tr>
	<td width="50%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><IMG SRC="images/logo.png" width="235" HEIGHT="130"></td>
	<td class="dato" style="text-align: right; FONT-SIZE: 2.0em; FONT-FAMILY: Arial, Verdana;" width="50%" VALIGN="bottom" COLSPAN="2"><b>ORDEN DE COMPRA</b></td>
</tr>
<tr>
	<td class="dato" style="text-align: right" width="50%" VALIGN="TOP" COLSPAN="2" HEIGHT="65">
	<b>Fecha: </b><?php echo date('d/m/Y') ?><BR><b>Estado: </b>No Generada
	</td>
</tr>
<tr><td colspan="3" style="padding-top: 20px; padding-left: 5px; FONT: bold 16px 'trebuchet ms', helvetica, sans-serif; COLOR: red; ">PASO <?php echo $Paso." : ".$DesPaso[$Paso]; ?></td></tr>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="dato5p12s" style="padding-top: 10px;"><B>Informaci&oacute;n del Cliente</B></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p12s" style="padding-top: 3px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid;"><B>Cliente: <?php echo $nom_clt ?></B></td>
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
	<td width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 8px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid;">Carrier: <?php echo $des_crr ?></td>
	<?php if ($Paso == 1) { ?>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="PADDING-TOP: 8px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">Comuna: <?php echo $nom_cmndsp; ?></td>
	<?php } else { ?>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato5p" style="PADDING-TOP: 8px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">Direcci&oacute;n: <?php echo $dir_sucdsp; ?></td>
	<?php } ?>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 8px; border-left: goldenrod 1px solid;">Servicio: <?php echo $des_svccrr; ?></td>
	<?php if ($Paso == 1) { ?>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="PADDING-TOP: 8px; border-right: goldenrod 1px solid;">Ciudad: <?php echo $nom_cdddsp ?></td>
	<?php } else { ?>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato5p" style="PADDING-TOP: 8px; border-right: goldenrod 1px solid;">Comuna: <?php echo $nom_cmndsp; ?></td>
	<?php } ?>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 8px; border-left: goldenrod 1px solid;">
	Sucursal: <?php echo $nom_sucdsp ?>
	<?php 
		if ($cod_tipsvc == 0) echo " (a Domicilio)";
		if ($cod_tipsvc == 1) echo " (a Sucursal del carrier)";
	?>
	</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="PADDING-TOP: 8px; border-right: goldenrod 1px solid;">
	Peso: <INPUT id="dfPeso" name="dfPeso" size="10" maxLength="10" class="dato" style="text-align: left" value="<?php echo number_format(0,2,',','.') ; ?>" ReadOnly /> Kg
	</td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 6px; PADDING-BOTTOM: 3px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">Direcci&oacute;n: <?php echo $dir_sucdsp; ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato" style="PADDING-BOTTOM: 3px; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">&nbsp;</td>
</tr>
<?php } ?>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="dato5p12s" style="PADDING-TOP: 10px">
	<B>Lista de Productos</B> (S&oacute;lo se Desplegar&aacute;n los Productos <B>CON</B> Stock Disponible)
	</td>
</tr>
<tr><td VALIGN="TOP" COLSPAN="3" style="padding-bottom: 10px">
<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center">
<?php 
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

		$result = mssql_query("vm_pvw_rescot $Cod_Cot, 1",$db);
		while (($row = mssql_fetch_array($result))) {
			if ($Cod_Mca != $row['Cod_Mca'] Or $Cod_GrpPrd != $row['Cod_GrpPrd'] Or	$Cod_Dsg != $row['Cod_Dsg']) {
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
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td class="titulo_tabla5p" width="10%">Color</td>
	<td class="titulo_tabla5p" width="30%">Descripci&oacute;n</td>
	<td class="titulo_tabla5p" width="15%">Tama&ntilde;o</td>
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
	//$NetoTot+=$Total;
?>
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center"><tr>
	<td class="dato" width="10%" style="padding-left: 3px; border-left: goldenrod 1px solid;"><?php echo $row['Key_Pat']; ?></td>
	<td class="dato" width="30%" style="padding-left: 3px"><?php echo $row['Des_Pat']; ?></td>
	<td class="dato" width="15%" style="padding-left: 3px"><?php echo $row['Val_Sze']; ?></td>
	<td class="dato" width="10%" style="padding-left: 3px"><INPUT name="dfCtd<?php echo $row["Cod_Prd"]; ?>" size="5" onchange="CalcularMonto(this,<?php echo $row['Prc_Uni']; ?>)" maxLength="5" class="dato" style="text-align: left" value="<?php echo $row['Val_Ctd']; ?>" /></td>
	<td class="dato" width="10%" style="padding-left: 3px"><?php echo number_format($row['Prc_Uni'],0,',','.'); ?></td>
	<td class="dato" width="10%" style="padding-left: 3px"><?php echo $row['Val_Des']."%"; ?></td>
	<td class="dato" width="10%" style="padding-right: 3px; text-align: right;"><INPUT name="dfMto<?php echo $row["Cod_Prd"]; ?>" size="10" maxLength="10" style="TEXT-ALIGN: right" value="<?php echo number_format($Total,0,',','.'); ?>" class="dato" ReadOnly /></td>
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
} // fin while
?>
<tr><td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">&nbsp;</td></tr>
</table>
</td>
</tr>
<tr><td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="1" width="100%" ALIGN="right">
		<tr>
			<td width="50%" align="left" valign="top" style="border-left: goldenrod 1px solid; border-top: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">
				<table BORDER="0" CELLSPACING="0" CELLPADDING="1" width="100%" ALIGN="right">
					<?php if ($Val_DesG > 0) { ?>
					<tr>
					<td width="100%" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
					Desc. Especial: <INPUT name="dfDescEsp" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo $Val_DesG ?> %" ReadOnly />
					</td>
					</tr>
					<?php } ?>

					<?php if ($is_dsp > 0) { ?>
					<tr>
					<td width="100%" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
					<?php if ($Cod_Iva == 2) { ?>
					Subtotal Productos: <INPUT name="dfNeto" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($NetoTot,0,',','.') ?>" ReadOnly />
					<?php } else { ?>
					Subtotal Productos: <INPUT name="dfNeto" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($NetoTot,0,',','.') ?>" ReadOnly />
					<?php } ?>
					</td>
					</tr>

					
					<tr>
					<td width="100%" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
					<?php if ($Cod_Iva == 2) { ?>
					Gastos Despacho: <INPUT name="dfDespacho" id="dfDespacho" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format(0,0,',','.') ?>" ReadOnly />
					<?php } else { ?>
					Gastos Despacho: <INPUT name="dfDespacho" id="dfDespacho" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format(0,0,',','.') ?>" ReadOnly />
					<?php } ?>
					</td>
					</tr>
					<?php } ?>

					<tr>
					<td width="100%" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
					<?php if ($Cod_Iva == 2) { ?>
					Neto: <INPUT name="dfNetoConDsp" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($NetoTot+$val_dsp,0,',','.') ?>" ReadOnly />
					<?php } else { ?>
					&nbsp;<INPUT name="dfNetoConDsp" type="hidden" value="<?php echo number_format($NetoTot+$val_dsp,0,',','.') ?>" ReadOnly />
					<?php } ?>
					</td>
					</tr>
					
					<tr>
					<td width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
					<?php if ($Cod_Iva == 2) { ?>
					IVA: <INPUT name="dfIVA" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($IVA * ($NetoTot+$val_dsp),0,',','.'); ?>" ReadOnly />
					<?php } else { ?>
					&nbsp;<INPUT name="dfIVA" type="hidden" value="<?php echo number_format($IVA * ($NetoTot+$val_dsp),0,',','.'); ?>" ReadOnly />					
					<?php } ?>
					</td>
					</tr>
					
					<tr>
					<td width="40%" colspan="2" VALIGN="TOP" align="right" style="padding-right: 5px; padding-bottom: 5px">
					Total: <INPUT name="dfTotal" size="15" maxLength="10" class="dato" style="text-align: right" value="<?php echo number_format($NetoTot+$val_dsp+($IVA*($NetoTot+$val_dsp)),0,',','.'); ?>" ReadOnly />
					</td>
					</tr>
				</table>
			</td>
			<td width="50%" align="rigth" valign="top" style="border-left: goldenrod 1px solid; border-top: goldenrod 1px solid; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">
				<table BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%" ALIGN="center">
					<tr><td colspan="2" width="100%" VALIGN="TOP" class="dato5p12s"><B>Forma de Pago</B></td></tr>
					<tr>
					<td width="5%" class="dato5p"><INPUT id="rbtransferencia" name="rbtransferencia" type="radio" class="button2" value="0" /></td>
					<td class="dato" style="padding-top: 5px;">transferencia Bancaria</td>
					</tr>
					<tr>
					<td width="5%" class="dato5p"><INPUT id="rbWebPay" name="rbWebPay" type="radio" class="button2" value="0" DISABLED /></td>
					<td class="dato" style="padding-top: 5px;">WebPay - transbank (pr&oacute;ximamente)</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</td></tr>
<tr>
  <td colspan="3" align="right" style="PADDING-TOP: 5px">
      <input type="button" name="Cerrar" value=" Volver " class="btn" onclick="javascript:ver_preview(<?php echo $Cod_Cot.",".$Paso ?>)" />
      <input type="submit" name="Enviar" id="Enviar" value=" <?php echo $DesPaso[$Paso+1] ?> " class="btn" />
  </td>
</tr>
</form>
<tr>
	<td VALIGN="TOP" class="dato10p" colspan="3">
        <form id="searchConsultas" action="">
	<table id="tblConsultas" BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%" ALIGN="center">
		<tr><td width="100%" VALIGN="TOP" class="dato5p12s"><B>Mensaje</B></td></tr>
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
</tr>
<tr>
        <form id="CalcularDespacho" action="">
	<td VALIGN="TOP" COLSPAN="3" class="avisopie" style="padding-left: 5px; padding-top: 10px">
	De conformidad con lo dispuesto en el articulo 2 bits, letra b) de la Ley N&#186; 19.496,
	Vestmed Ltda, dispone expresamente que quienes admieran productos personalizados a trav&eacute;s de nuestro
	canal de internet, tel&eacute;fono o por medio de cualquiera de nuestras tiendes de venta directa,
	no tendr&aacute;n derecho a cambio o retractarse de su compra. Esta disposici&oacute;n no invalida de forma
	alguna la responsabilidad por fallas de fabrica &#45; garant&iacute;as.
        </td>
        </form>
</tr>
</table>
			</div>
		</div>
	</div>
	<div id="footer"></div>

</body>
<script language="javascript">
	var f1 = document.F1;	
	var f2 = document.F2;
	var ArrPeso = new Array();
	var ArrCosto = new Array();
	
<?php
	if ($is_dsp == 1) {
		$i = 0;
		$peso_ant = 0.0;
        $result = mssql_query("vm_SvcCrr_Prc_s ".$cod_crr.",".$cod_svccrr.",".$cod_rgndsp.",-1", $db);
		while (($row = mssql_fetch_array($result))) {
			echo "\tArrPeso[$i] = ".$row['Pes_Max'].";\n";
			echo "\tArrCosto[$i] = ".$row['Prc_Dsp'].";\n";
			if ($row['Prc_Dsp'] == 0 and $peso_max > $peso_ant ) {
				$peso_ant = $row['Pes_Max'];
				echo "\tArrPeso[$i] = $peso_max;\n";
				echo "\tArrCosto[$i] = ".$val_dsp.";\n";
				$i++;
				echo "\tArrPeso[$i] = $peso_ant;\n";
				echo "\tArrCosto[$i] = 0;\n";
			}
			else {
				$i++;
				$peso_ant = $row['Pes_Max'];
			}
		}
	}
?>
	CalcularTotal();
</script>
</html>
