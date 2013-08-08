<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$Cod_PerFct = (isset($_GET['per'])) ? ok($_GET['per']) : 0;
$Paso = 2;
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
    var $j = jQuery.noConflict();
    $j(document).ready
	(
            function()
            {
                $j("form#searchDirFct").submit(function(){
                    $j.post("ajax-search-per.php",{
                            search_type: "fct",
                            param_filter: $j("#dfCodClt").val(),
                            param_persona: $j("#dfCodPer").val()
                    }, function(xml) {
                            listOwnFct(xml);
                    });return false;
                });

                $j("form#searchPobDirFct").submit(function(){
                    $j.post("ajax-search-per.php",{
                            search_type: "lstfct",
                            param_filter: $j("#dfCodClt").val()
                    }, function(xml) {
                            listFct(xml);
                    });return false;
                });

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
	
    function listOwnFct(xml)
    {
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                //alert($j("code",filter).text()+"="+$j("value",filter).text());
                if ($j("code",filter).text() == "numdoc") $j("#NumDocFct").val($j("value",filter).text());
                if ($j("code",filter).text() == "nomclt") $j("#NomCltFct").val($j("value",filter).text());
                if ($j("code",filter).text() == "nomfan") $j("#NomFanFct").val($j("value",filter).text());
                if ($j("code",filter).text() == "dirfct") $j("#DirFctFct").val($j("value",filter).text());
                if ($j("code",filter).text() == "nomcmn") $j("#NomCmnFct").val($j("value",filter).text());
                if ($j("code",filter).text() == "nomcdd") $j("#NomCddFct").val($j("value",filter).text());
                if ($j("code",filter).text() == "fonfct") $j("#FonFctFct").val($j("value",filter).text());
                if ($j("code",filter).text() == "faxfct") $j("#FaxFctFct").val($j("value",filter).text());
                if ($j("code",filter).text() == "webfct") $j("#WebFctFct").val($j("value",filter).text());
            });
	}
	
    function listFct(xml)
    {
        var options;
        //alert("listFct");
        options ="<table id=\"tblDirFct\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" width=\"100%\">\n";
        options+="<tr>\n";
        options+="<td align=\"center\" width=\"20xp\"><b>Id</b></td>\n";
        options+="<td align=\"center\" width=\"80px\"><b>RUT<b></td>\n";
        options+="<td align=\"center\"><b>Raz&oacute;n Social</b></td>\n";
        options+="</tr>\n";
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                //alert($j("code",filter).text()+"="+$j("value",filter).text());
                options+= "<tr>\n";
                options+= "<td valign=\"top\" style=\"TEXT-ALIGN: center\">\n";
                options+= "<INPUT id=\"rbSucFct\" name=\"rbDirFct\" type=\"radio\" style=\"border:none\" value=\""+$j("codper",filter).text()+"\" onclick=\"GetSuc(this)\" />\n";
                options+= "</td>\n";
                options+= "<td valign=\"top\" style=\"TEXT-ALIGN: center\">"+$j("numdoc",filter).text()+"</td>\n";
                options+= "<td valign=\"top\" style=\"TEXT-ALIGN: left\">"+$j("nomclt",filter).text()+"</td>\n";
                options+= "</tr>\n";
            }
        );
        options+="</table>";

        $j("#tblDirFct").replaceWith(options);

        $j("#NumDocFct").val("");
        $j("#NomCltFct").val("");
        $j("#NomFanFct").val("");
        $j("#DirFctFct").val("");
        $j("#NomCmnFct").val("");
        $j("#NomCddFct").val("");
        $j("#FonFctFct").val("");
        $j("#FaxFctFct").val("");
        $j("#WebFctFct").val("");

    }

    function ActualizarConsultas()
    {
        $j("form#searchConsultas").submit();
    }

    function ActualizarDirFacturas()
    {
        $j("form#searchPobDirFct").submit();
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
<script type="text/javascript">
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
			for (i=0; i<ArrPeso.length; i++) {
				if (pesodsp <= ArrPeso[i]) {
					despacho = Math.round(ArrCosto[i]);
					despacho = despacho + despacho * <?php if ($Cod_Iva == 2) echo "0"; else echo $valiva; ?>;
					f2.dfDespacho.value = Math.round(despacho);
					f2.dfDespacho.value = formatearMillones(f2.dfDespacho.value);
					break;
				}
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

		if (f2.TipDocSII.value != "1" && f2.TipDocSII.value != "2") {
			alert ("Debe indicar el Tipo de Documento de Venta");
			return false;
		} 
		if (f2.TipDocSII.value == "2" && f2.Cod_PerFct.value == "0") {
			alert ("Debe indicar el RUT de facturacion");
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
		//alert("a mostrar");
		//alert(f2.montoCompra.value);
		f2.Enviar.value = "Enviando";
		f2.Enviar.disabled = true;
		return true;
	}
	
	function ver_preview (cot,paso) {
		f2.action = "ordendecompra.php?cot="+cot;
		f2.submit();
	}
	
	function consultar() {
		popwindow("consultasusr.php?cot=<?php echo $Cod_Cot ?>&caso=web&paso=<?php echo $Paso; ?>",540);
	}
	
	function setTipDocSii(obj) {
		f2.TipDocSII.value = obj.value;
		if (obj.value == 1)
			$j("#datos_factura").hide("slow");
		else
			$j("#datos_factura").show("slow");
	}
	
	function GetSuc(obj) {
	    $j("#dfCodPer").val(obj.value);
		f2.Cod_PerFct.value = obj.value;
		$j("form#searchDirFct").submit();
	}
	
	function NuevaDir() {
       	popwindow("registrar_cltfct.php?clt=<?php echo $cod_clt; ?>&cot=<?php echo $Cod_Cot; ?>&form=insert",310);
	}
	 
	function UpdateDir() {
		if (f2.Cod_PerFct.value == 0) {
			alert ("Debe seleccionar el Rut de Facturaci\u00f3n a modificar ...");
			return false;
		}
		popwindow("registrar_cltfct.php?clt=<?php echo $cod_clt; ?>&cot=<?php echo $Cod_Cot; ?>&per="+f2.Cod_PerFct.value+"&form=update",310);
	}
	
	function DeleteDir() {
		if (f2.Cod_PerFct.value == 0) {
			alert ("Debe seleccionar el Rut de Facturaci\u00f3n a eliminar ...");
			return false;
		}		
		popwindow("registrar_cltfct.php?clt=<?php echo $cod_clt; ?>&cot=<?php echo $Cod_Cot; ?>&per="+f2.Cod_PerFct.value+"&form=delete",310);
	}
	
/*	function postAction(){
		f2.action='cotizador/enviaroc.php?cot=<?php echo $Cod_Cot ?>&paso=<?php echo $Paso; ?>&valorCmp='+ f2.montoCmp.value; 
		f2.submit();
	}
*/
	
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
        <form ID="F1" method="POST" name="F1" action="">
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
			<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" alt="" />
           	<div style="width:765px; height:auto; margin:0 auto 0 100px; padding-top:10px;">
<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%">
<tr>
	<td width="50%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><img SRC="images/logo.png" width="235" HEIGHT="130" alt="" /></td>
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
	<td width="50%" VALIGN="TOP" class="dato5p" style="padding-top: 2px; border-left: goldenrod 1px solid;">Direcci&oacute;n: <?php echo $dir_suc ?></td>
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
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato5p" style="PADDING-TOP: 8px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">Direcci&oacute;n: <?php echo $dir_sucdsp; ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 8px; border-left: goldenrod 1px solid;">Servicio: <?php echo $des_svccrr; ?></td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato5p" style="PADDING-TOP: 8px; border-right: goldenrod 1px solid;">Comuna: <?php echo $nom_cmndsp; ?></td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP" class="dato5p" style="PADDING-TOP: 8px; border-left: goldenrod 1px solid;">
	Sucursal: <?php echo $nom_sucdsp ?>
	<?php 
		if ($cod_tipsvc == 0) echo " (a Domicilio)";
		if ($cod_tipsvc == 1) echo " (a Sucursal del carrier)";
	?>
	</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2" class="dato5p" style="PADDING-TOP: 8px; padding-bottom: 3px; border-right: goldenrod 1px solid;">Ciudad: <?php echo $nom_cdddsp ?></td>
</tr>
<tr>
	<td COLSPAN="3" VALIGN="TOP" class="dato5p" style="PADDING-BOTTOM: 1px; border-top: goldenrod 1px solid;">&nbsp;</td>
</tr>
<?php } ?>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="dato5p12s" style="PADDING-TOP: 10px">
	<B>
	<?php 
	switch ($Paso) {
	case 2:
	case 3:
		echo "Productos Comprados";
		break;
	}
	?>
	</B></td>
</tr>
<tr><td VALIGN="TOP" COLSPAN="3" style="padding-bottom: 10px">
<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%" ALIGN="center">
<?php 
	switch ($Paso) {
	case 2:
	case 3:
?>
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
		if (($row = mssql_fetch_array($result))) {
			$Cod_Nvt = $row["Cod_Nvt"];
			$Cod_trn = $row["Cod_Trn"];
			$bPrimerRegistro = true;
			$result = mssql_query("vm_resprd_nvt_s $Cod_Nvt, $Cod_trn",$db);
			while (($row = mssql_fetch_array($result))) {
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

					$sLinea  = "<tr><td style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">".$colCod_Sty."</td>";
					$sLinea .= "<td>".$colKey_Pat."</td>";
					$sLinea .= "<td>".str_replace("#","'",$colDes_Prd)."</td>";
					$sLinea .= "<td>".$colCtdSze_Prd."</td>";
					$sLinea .= "<td align=\"center\">".$colCtd_Prd."</td>";
					$sLinea .= "<td style=\"padding-right: 3px; text-align: right\">".number_format($colPrc_Prd,0,',','.')."</td>";
					$sLinea .= "<td style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\">".number_format($colMto_Prd,0,',','.')."</td></tr>";
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

			$sLinea  = "<tr><td style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">".$colCod_Sty."</td>";
			$sLinea .= "<td>".$colKey_Pat."</td>";
			$sLinea .= "<td>".str_replace("#","'",$colDes_Prd)."</td>";
			$sLinea .= "<td>".$colCtdSze_Prd."</td>";
			$sLinea .= "<td align=\"center\">".$colCtd_Prd."</td>";
			$sLinea .= "<td style=\"padding-right: 3px; text-align: right\">".number_format($colPrc_Prd,0,',','.')."</td>";
			$sLinea .= "<td style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\">".number_format($colMto_Prd,0,',','.')."</td></tr>";
			echo $sLinea;
			
			$dfSubTotal    = $dfSubTotal + $colMto_Prd;
			
			$result = mssql_query("vm_dsp_s $Cod_Nvt, $Cod_trn",$db);
			while (($row = mssql_fetch_array($result))) {
				$colPrc_Prd = $row["Prc_DspTrn"];
				If ($colPrc_Prd > 0) {
					$colCod_Sty = "";
					$colCod_Pat = "";
					$colCtdSze_Prd = "";
					$colDes_Prd = "Recuperaci&oacute;n de Gastos de Despacho";
					$colCtd_Prd = 1;
					$colMto_Prd = $colPrc_Prd;
					$dfSubTotal = $dfSubTotal + $colMto_Prd;

					$sLinea  = "<tr><td style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">&nbsp;</td>";
					$sLinea .= "<td>&nbsp;</td>";
					$sLinea .= "<td>".$colDes_Prd."</td>";
					$sLinea .= "<td>".$colCtdSze_Prd."</td>";
					$sLinea .= "<td align=\"center\">".$colCtd_Prd."</td>";
					$sLinea .= "<td style=\"padding-right: 3px; text-align: right\">".number_format($colPrc_Prd,0,',','.')."</td>";
					$sLinea .= "<td style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\">".number_format($colMto_Prd,0,',','.')."</td></tr>";
					echo $sLinea;
				}
			}
			
			if ($Cod_Iva == 2) {
				$sLinea  = "<tr><td colspan=\"5\" style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">&nbsp;</td>";
				$sLinea .= "<td style=\"padding-right: 3px; text-align: right\"><strong>Neto</strong></td>";
				$sLinea .= "<td style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\"><strong>".number_format($dfSubTotal,0,',','.')."</strong></td></tr>";
				echo $sLinea;
				
				$mtoiva = $IVA * $dfSubTotal;
				$sLinea  = "<tr><td colspan=\"5\" style=\"padding-left: 2px; border-left: goldenrod 1px solid;\">&nbsp;</td>";
				$sLinea .= "<td style=\"padding-right: 3px; text-align: right\"><strong>IVA</strong></td>";
				$sLinea .= "<td style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid;\"><strong>".number_format($mtoiva,0,',','.')."</strong></td></tr>";
				echo $sLinea;
			}
			$sLinea  = "<tr><td colspan=\"5\" style=\"padding-left: 2px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid;\">&nbsp;</td>";
			$sLinea .= "<td style=\"padding-right: 3px; text-align: right; border-bottom: goldenrod 1px solid;\"><strong>TOTAL</strong></td>";
			$montoTotal=$dfSubTotal+$mtoiva;
			$sLinea .= "<td style=\"padding-right: 3px; text-align: right; border-right: goldenrod 1px solid; border-bottom: goldenrod 1px solid;\"><strong>".number_format($dfSubTotal+$mtoiva,0,',','.')."</strong><input type='hidden' value='$montoTotal' id='montoNvt' name='montoNvt'></input></td></tr>";
			echo $sLinea;
		}
?>

<?php
		break;
	}
?>

</table>
</td>
</tr>
<tr>
	<td class="dato5p12s" width="60%" VALIGN="TOP" style="padding-top: 5px"><B>Documento de Venta</B></td>
	<td width="20%" VALIGN="TOP">&nbsp;</td>
	<td width="20%" VALIGN="TOP">&nbsp;</td>
</tr>
<tr>
	<td colspan="3" class="dato5p12s" style="padding-bottom: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid; border-top: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">
	<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
	<tr>
		<td class="dato" width="25%" style="text-align: right"><INPUT name="rbDocumento[]" type="radio" class="button2" value="1" onclick="setTipDocSii(this)" /></td>
		<td class="dato" width="25%" align="left">Boleta</td>
		<td class="dato" width="25%" style="text-align: right"><INPUT name="rbDocumento[]" type="radio" class="button2" value="2" onclick="setTipDocSii(this)"<?php if ($Cod_PerFct > 0) echo " checked"; ?> /></td>
		<td class="dato" width="25%" align="left">Factura</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td colspan="3">
		<div id="datos_factura">
			<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
			<tr>
				<td class="dato5p12s" width="100%" VALIGN="TOP" style="padding-top: 5px"><B>Informaci&oacute;n de Facturaci&oacute;n</B></td>
			</tr>
			<tr>
				<td colspan="3" class="dato5p12s" style="padding-top: 5px; padding-bottom: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid; border-top: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">
					<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
					<tr>
						<td class="dato" width="50%" align="left" valign="top" style="padding-top: 5px; padding-bottom: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid; border-top: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">
							<form id="searchDirFct" name="searchDirFct" action="">
							<table id="tblDirFct" BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%">
							<tr>
								<td align="center" width="20xp"><b>Id</b></td>
								<td align="center" width="80px"><b>RUT</b></td>
								<td align="center"><b>Raz&oacute;n Social</b></td>
							</tr>
					<?php
						$result = mssql_query("vm_s_rutfct $cod_clt", $db);
						while (($row = mssql_fetch_array($result))) {
					?>
							<tr>
							   <td valign="top" style="TEXT-ALIGN: center">
							   <INPUT id="rbSucFct" name="rbDirFct" type="radio" style="border:none" value="<?php echo $row['Cod_Per'] ?>"  onclick="GetSuc(this)"<?php if ($Cod_PerFct == $row['Cod_Per']) echo " checked"; ?> /></td>
							   <td valign="top" style="TEXT-ALIGN: center;"><?php echo $row['Num_Doc']; ?></td>
							   <td valign="top" style="TEXT-ALIGN: left;"><?php echo utf8_encode($row['Nom_Clt']); ?></td>
							</tr>
					<?php
							$j = 1 - $j;
							$iTotPrd1++;
						}
						mssql_free_result($result);
						
						if ($Cod_PerFct > 0) {
							$result = mssql_query("vm_s_rutfct $cod_clt, $Cod_PerFct", $db);
							if (($row = mssql_fetch_array($result))) {
								$numdoc = $row['Num_Doc'];
								$nomclt = utf8_encode($row['Nom_Clt']);
								$nomfan = utf8_encode($row['NomFan_Per']);
								$dirfct = utf8_encode($row['Dir_Fct']);
								$nomcmn = utf8_encode($row['Nom_Cmn']);
								$nomcdd = utf8_encode($row['Nom_Cdd']);
								$fonfct = $row['Fon_Fct'];
								$faxfct = $row['Fax_Fct'];
								$webfct = $row['Web_Fct'];
							}
						}
					?>
							</table>
							</form>
							<input type="hidden" value="" name="dfCodPer" id="dfCodPer" />
							<input type="hidden" value="<?php echo $cod_clt; ?>" name="dfCodClt" id="dfCodClt" />
						</td>
						<td valign="top" class="dato" width="50%" align="right" rowspan="2" style="padding-left:10px">
							<form id="searchPobDirFct" name="searchPobDirFct" action="">
							<table BORDER="0" CELLSPACING="1" CELLPADDING="1" width="100%">
								<tr>
									<td align="left" width="40%"><b>RUT:</b></td>
									<td align="left"><input name="NumDocFct" id="NumDocFct" type="text" class="dato" size="35" readonly value="<?php echo $numdoc; ?>" /></td>
								</tr>
								<tr>
									<td><b>Raz&oacute;n Social</b></td>
									<td align="left"><input name="NomCltFct" id="NomCltFct" type="text" class="dato" size="35" readonly value="<?php echo $nomclt; ?>" /></td>
								</tr>
								<tr>
									<td><b>Nombre de Fantas&iacute;a</b></td>
									<td align="left"><input name="NomFanFct" id="NomFanFct" type="text" class="dato" size="35" readonly value="<?php echo $nomfan; ?>" /></td>
								</tr>
								<tr>
									<td><b>Direcci&oacute;n Casa Matriz</b></td>
									<td align="left"><input name="DirFctFct" id="DirFctFct" type="text" class="dato" size="35" readonly value="<?php echo $dirfct; ?>" /></td>
								</tr>
								<tr>
									<td><b>Comuna</b></td>
									<td align="left"><input name="NomCmnFct" id="NomCmnFct" type="text" class="dato" size="35" readonly value="<?php echo $nomcmn; ?>" /></td>
								</tr>
								<tr>
									<td><b>Ciudad</b></td>
									<td align="left"><input name="NomCddFct" id="NomCddFct" type="text" class="dato" size="35" readonly value="<?php echo $nomcdd; ?>" /></td>
								</tr>
								<tr>
									<td><b>Tel&eacute;fono</b></td>
									<td align="left"><input name="FonFctFct" id="FonFctFct" type="text" class="dato" size="35" readonly value="<?php echo $fonfct; ?>" /></td>
								</tr>
								<tr>
									<td><b>FAX</b></td>
									<td align="left"><input name="FaxFctFct" id="FaxFctFct" type="text" class="dato" size="35" readonly value="<?php echo $faxfct; ?>" /></td>
								</tr>
								<tr>
									<td><b>P&aacute;gina Web</b></td>
									<td align="left"><input name="WebFctFct" id="WebFctFct" type="text" class="dato" size="35" readonly value="<?php echo $webfct; ?>" /></td>
								</tr>
							</table>
							</form>
						</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top: 5px">
							<input type="button" class="btn" value="Agregar" onclick="NuevaDir();" />&nbsp;
							<input type="button" class="btn" value="Modificar" onclick="UpdateDir();" />&nbsp;
							<input type="button" class="btn" value="Eliminar" onclick="DeleteDir();" />
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</div>
	</td>
</tr>
<tr>
	<td class="dato5p12s" width="60%" VALIGN="TOP" style="padding-top: 8px"><B>Informaci&oacute;n de Pago</B></td>
	<td width="20%" VALIGN="TOP">&nbsp;</td>
	<td width="20%" VALIGN="TOP">&nbsp;</td>
</tr>
<form ID="F2" method="POST" name="F2" ACTION="cotizador/enviaroc.php?cot=<?php echo $Cod_Cot;?>&paso=<?php echo $Paso;?>&montoNvt=<?php echo $montoTotal;?>" onsubmit="return ValidarOrden(this)" enctype="multipart/form-data">
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%">
	<tr>
	<td width="50%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-left: goldenrod 1px solid; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	M&eacute;todo de Pago: transferencia Bancaria
	</td>
	<td width="50%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	Selecciones su Banco: 
	<select id="Bco" name="Bco" class="textfieldv2">
	<option selected value="_NONE">Seleccione Banco</option>
	<?php //Seleccionar las ciudades
	$sp = mssql_query("vm_bco_s",$db);
	while($row = mssql_fetch_array($sp))
	{
		?>
		<option value="<?php echo utf8_encode($row['Nom_Bco']) ?>"><?php echo utf8_encode($row['Nom_Bco']) ?></option>
		<?php
	}
	?>
    </select>
	</td>
	</tr>
	<tr>
	<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	Destinatario: <BR>IMPORTADORA Y COMERCIALIZADORA VESTMED LtdA.
	</td>
	<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">
	Adjuntar Comprobante de transferencia
	</td>
	</tr>
	<tr>
	<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	RUT: 77.572.240-1
	</td>
	<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">
	Archivo <span>
				<input class="file-contacto" type="file" name="documento" id="documento" size="28" onchange="fichero.value = this.value"/>
				<input type="hidden" name="fichero"/>
			</span>
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
	<!--<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">Monto Comprobante -->
	<span>	
		<INPUT type="hidden" name="montoCmp" id="montoCmp" size="15" maxLength="10" style="text-align: left" value = "0" onKeyPress="return SoloNumeros(event)" />
	</span>
	<!--</td>-->
	<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">
	<INPUT  type="hidden" class="textfieldv2" name="Numtrnbco" id="Numtrnbco" size="15" maxLength="10" class="dato" style="text-align: left" value="" />
	</td>
	</tr>
	<tr>
	<td width="50%" style="padding-left: 5px; padding-bottom: 10px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	mail: ventas@vestmed.cl
	</td>
	<td width="50%" style="padding-left: 5px; padding-bottom: 10px; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">
    &nbsp;
	</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
  <td colspan="3" align="right" style="PADDING-TOP: 5px">
      <input type="button" name="Cerrar" value=" Volver " class="btn" onclick="javascript:ver_preview(<?php echo $Cod_Cot.",".$Paso ?>)" />
     <!-- <input type="submit" name="Enviar" id="Enviar" value=" <?php echo $DesPaso[$Paso+1] ?> " class="btn" onclick="javascript:postAction()" />-->
	 <input type="submit" name="Enviar" id="Enviar" value=" <?php echo $DesPaso[$Paso+1] ?> " class="btn"/>
	 
	  <input type="hidden" name="TipDocSII" value="<?php echo ($Cod_PerFct > 0 ? 2 : 0); ?>">
	  <input type="hidden" name="Cod_PerFct" value="<?php echo $Cod_PerFct ?>">
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
				echo $tot_cnaclt+$tot_cnaemp;
				if (($tot_cnaclt+$tot_cnaemp) > 0) echo " (<a href=\"javascript:consultar()\">VER</a>)";
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
	<div id="footer"></div>
</div>

<script type="text/javascript">
	var f1 = document.F1;	
	var f2 = document.F2;
	var ArrPeso = new Array();
	var ArrCosto = new Array();

	<?php if ($Cod_PerFct == 0) { ?>
	$j("#datos_factura").hide();
	<?php } ?>
	
	
<?php
	if ($is_dsp == 1) {
		$i = 0;
		$peso_ant = 0.0;
        $result = mssql_query("vm_SvcCrr_Prc_s ".$cod_crr.",".$cod_svccrr.",".$cod_rgndsp, $db);
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
</body>
</html>
