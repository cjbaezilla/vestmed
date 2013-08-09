<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("../config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$save = (isset($_GET['save']));
$Paso = 2;
$DesPaso = split("/", "/Seleccionar Productos/Pagar/Enviar");
$Cod_PerFct = (isset($_GET['per'])) ? ok($_GET['per']) : 0;

if ($save=='true'){
	try {
		$Cod_DocSII = (isset($_GET['docsii'])) ? ok($_GET['docsii']) : 0;
		$Cod_Clt = (isset($_GET['clt'])) ? ok($_GET['clt']) : 0;
		
		//echo "vm_u_cot_cltweb $Cod_Cot, $Cod_DocSII, $Cod_PerFct"."<BR>";
		//$store_proc = "vm_u_cot_cltweb $Cod_Cot, $Cod_DocSII, $Cod_PerFct";
		$result = mssql_query("vm_u_cot_cltweb $Cod_Cot, $Cod_DocSII, $Cod_PerFct",$db);
		//mssql_execute($store_proc);
		
		mssql_free_statement($store_proc);
		echo "Acción realizada.";
	} catch (Exception $ex){
		echo $ex;
	}
	
}

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
<title>Forma de Pago</title>
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="../dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
</script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="../Include/SoloNumeros.js"></script>
<script type="text/javascript" src="../Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="../Include/fngenerales.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready
	(
            function()
            {
                $j("form#searchDirFct").submit(function(){
                    $j.post("../ajax-search-per.php",{
                            search_type: "fct",
                            param_filter: $j("#dfCodClt").val(),
                            param_persona: $j("#dfCodPer").val()
                    }, function(xml) {
                            listOwnFct(xml);
                    });return false;
                });

                $j("form#searchPobDirFct").submit(function(){
                    $j.post("../ajax-search-per.php",{
                            search_type: "lstfct",
                            param_filter: $j("#dfCodClt").val()
                    }, function(xml) {
                            listFct(xml);
                    });return false;
                });

                $j("form#searchConsultas").submit(function(){
                    $j.post("../ajax-search.php",{
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

	function popwindow2(ventana,altura){
	   window.open(ventana,"NewDir","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=800,height="+altura);
	}
	
	function ver_producto(cod_prd) {
		popwindow("../preview_prd.php?prd="+cod_prd,400)
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

		f2.Enviar.value = "Enviando";
		f2.Enviar.disabled = true;
		return true;
	}
	
	function ver_preview (cot,paso) {
		f2.action = "../ordendecompra.php?cot="+cot;
		f2.submit();
	}
	
	function consultar() {
		popwindow("../consultasusr.php?cot=<?php echo $Cod_Cot ?>&caso=web&paso=<?php echo $Paso; ?>",540);
	}
	
	function setTipDocSii(obj) {
		f2.TipDocSII.value = obj.value;
		if (obj.value == 1){
			$j("#datos_factura").hide("slow");
			f2.Cod_PerFct.value=0;
		} else {
			$j("#datos_factura").show("slow");
		}
	}
	
	function GetSuc(obj) {
	    $j("#dfCodPer").val(obj.value);
		f2.Cod_PerFct.value = obj.value;
		$j("form#searchDirFct").submit();
	}
	
	function NuevaDir() {
       	popwindow2("../registrar_cltfct.php?clt=<?php echo $cod_clt; ?>&cot=<?php echo $Cod_Cot; ?>&form=insert",310);
	}
	 
	function UpdateDir() {
		if (f2.Cod_PerFct.value == 0) {
			alert ("Debe seleccionar el Rut de Facturaci\u00f3n a modificar ...");
			return false;
		}
		popwindow("../registrar_cltfct.php?clt=<?php echo $cod_clt; ?>&cot=<?php echo $Cod_Cot; ?>&per="+f2.Cod_PerFct.value+"&form=update",310);
	}
	
	function DeleteDir() {
		if (f2.Cod_PerFct.value == 0) {
			alert ("Debe seleccionar el Rut de Facturaci\u00f3n a eliminar ...");
			return false;
		}		
		popwindow("../registrar_cltfct.php?clt=<?php echo $cod_clt; ?>&cot=<?php echo $Cod_Cot; ?>&per="+f2.Cod_PerFct.value+"&form=delete",310);
	}
	
	function ActualizaTipDoc(cod_cot)
    {
		//alert (dfCodClt.value + " " + f2.Cod_PerFct.value + " " + f2.TipDocSII.value)
		parent.opener.document.frm_formapago.cod_clt.value= dfCodClt.value;
		if (f2.TipDocSII.value==2){
			parent.opener.document.frm_formapago.cod_perfct.value= f2.Cod_PerFct.value;	
			parent.opener.document.frm_formapago.tip_docsii.value= 2;
		}else {
			parent.opener.document.frm_formapago.cod_perfct.value= 0;
			parent.opener.document.frm_formapago.tip_docsii.value= 1;
		}
		
		//parent.opener.document.frm_formapago.tip_docsii.value= f2.TipDocSII.value; // Por algun motivo, al ser boleta envia con valor en CERO. DAG - 20110505
		
		parent.opener.actualizar_TipDoc();
		window.close();
    }
	
	function ActualizaPago(f,cod_cot){
		if (f2.Cod_PerFct.value > 0){
			f.action = "lyt_formapago.php?cot="+cod_cot+"&per="+f2.Cod_PerFct.value+"&docsii=2&clt=" + jQuery("#dfCodClt").val() + "&save='true'";
		}else{
			f.action = "lyt_formapago.php?cot="+cod_cot+"&per=0&docsii=1&clt=" + jQuery("#dfCodClt").val() + "&save='true'";
		}
		
		return true;
		
	}
		
</script>
</head>
<body bgcolor="#c1f4e5" style="margin-top:5px;">
<div style="overflow:auto;">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center" style="padding-left:50px; padding-right:10px;">

<tr>
	<td width="50%" VALIGN="TOP">&nbsp;</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<tr>
	<td width="50%" VALIGN="TOP">&nbsp;</td>
	<td width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
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
		<td class="dato" width="25%" style="text-align: right"><INPUT id="rbBol" name="rbDocumento[]" type="radio" class="button2" value="1" onclick="setTipDocSii(this)" checked="true" /></td>
		<td class="dato" width="25%" align="left">Boleta</td>
		<td class="dato" width="25%" style="text-align: right"><INPUT name="rbDocumento[]" type="radio" class="button2" value="2" onclick="setTipDocSii(this)"<?php if ($Cod_PerFct > 0) echo " checked='true'"; ?> /></td>
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
<!--<form ID="F2" method="POST" name="F2" ACTION="../cotizador/enviaroc.php?cot=<?php echo $Cod_Cot ?>&paso=<?php echo $Paso; ?>" onsubmit="return ValidarOrden(this)" enctype="multipart/form-data">-->
<form ID="F2" method="POST" name="F2" ACTION="" onsubmit="return ActualizaPago(this,<?php echo $Cod_Cot ?>)">
<tr>
	<td colspan="3">
	<table BORDER="0" CELLSPACING="0" CELLPADDING="0" width="100%">
	<tr>
	<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	</td>
	<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">
	</td>
	</tr>
	<tr>
	<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	</td>
	<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">
	</td>
	</tr>
	<tr>
	<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	</td>
	<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">&nbsp;</td>
	</tr>
	<tr>
	<td width="50%" style="padding-left: 5px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	</td>
	<td width="50%" style="padding-left: 5px; border-right: goldenrod 1px solid;">
	</td>
	</tr>
	<tr>
	<td width="50%" style="padding-left: 5px; padding-bottom: 10px; border-left: goldenrod 1px solid; border-bottom: goldenrod 1px solid; border-right: goldenrod 1px solid;">
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
	  <input type="hidden" name="TipDocSII" value="<?php echo ($Cod_PerFct > 0 ? 2 : 0); ?>">
	  <input type="hidden" name="Cod_PerFct" value="<?php echo $Cod_PerFct ?>">
      <input type="button" name="Cerrar" value=" Volver " class="btn" onclick="javascript:window.close()" />
	  <input type="submit" name="Enviar" id="Enviar" value="<?php echo $DesPaso[$Paso+1] ?>" class="btn"/>
  </td>
</tr>
</form>
<tr>
	<td VALIGN="TOP" class="dato10p" colspan="3">
        <form id="searchConsultas" action="">
        </form>
	</td>
</tr>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="avisopie" style="padding-left: 5px; padding-top: 10px">
</td>
</tr>
</table>
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
	if ($save==true){
		?>
		ActualizaTipDoc(<?php echo $Cod_Cot;?>)
		<?php
	}
?>

//	CalcularTotal();
</script>
</body>
</html>
