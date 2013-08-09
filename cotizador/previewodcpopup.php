<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

if (isset($_POST['cod_trn'])) {
    $cod_trn    = intval(ok($_POST['cod_trn']));
    $Cod_Cot    = intval(ok($_POST['cod_cot']));
    $Cod_Clt    = intval(ok($_POST['cod_clt']));
    $cod_sucfct = intval(ok($_POST['cod_sucfct']));
    switch ($cod_trn) {
        case 100:
            //$query = "vm_u_sucfct_cot $Cod_Clt, $Cod_Cot, $cod_sucfct";
            $result = mssql_query("vm_u_sucfct_cot $Cod_Clt, $Cod_Cot, $cod_sucfct",$db);
            break;
    }
}

$meses = split("/","/Enero/Febrero/Marzo/Abril/Mayo/Junio/Julio/Agosto/Septiembre/Octubre/Noviembre/Diciembre");
if (isset($_GET['cot']))        $Cod_Cot = intval(ok($_GET['cot']));
$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if (($row = mssql_fetch_array($result))) {
        $num_cot = $row['Num_Cot'];
	$fec_cot = $row['Fec_Cot'];
	$tip_cnl = $row['Tip_Cnl'];
	$cod_odc = $row['Cod_Odc'];
	$cod_clt = $row['Cod_Clt'];

        $dir_suc = $row['Dir_Suc'];
        $cod_cmn = $row['Com_Cmn'];
        $cod_cdd = $row['Cod_Cdd'];
        $cod_suc = $row['Cod_Suc'];

	$cod_sucfct = $row['Cod_SucFct'];
        $cod_perfct = $row['Cod_PerFct'];
        $tip_docsii = $row['Tip_DocSII'];
	$is_dsp = $row['is_dsp'];
	$cod_crr = $row['Cod_Crr'];
	$cod_svc = $row['Cod_SvcCrr'];
	$cod_sucdsp = $row['Cod_SucDsp'];
	$num_trnbco = $row['Num_trnBco'];
	$arc_adj = $row['Arc_Adj'];
	$nom_bco = utf8_encode($row['Nom_Bco']);
	$fecha = $meses[intval(substr($fec_cot, -4, 2))]." ".substr($fec_cot, -2, 2).", ".substr($fec_cot, 0, 4);
	if ($row['Cod_TipPer'] == 1)
            $nombre = $row['Nom_Per'].' '.$row['Pat_Per']." ".$row['Mat_Per'];
	else
            $nombre = $row['RznSoc_Per'];
        $nombre = utf8_encode($nombre);
	if (trim($nom_bco) != '' and (trim($arc_adj) != '' or trim($num_trnbco) != '')) {
            $labelpgo = "Ver Pago";
            $linkpgo = "javascript:verAprobacion(".$Cod_Cot.")";//"javascript:ver_comprobante('".$row['ArcFis_Adj']."')";
	}
	else {
			$labelpgo = "Ver Pago";
            $linkpgo = "javascript:verAprobacion(".$Cod_Cot.")";//"javascript:ver_comprobante('".$row['ArcFis_Adj']."')";
/*            $labelpgo = "Pago";
            $linkpgo = "#";
*/	}

	$result = mssql_query("vm_cmn_s $cod_cmn",$db);
	if (($row = mssql_fetch_array($result)))
            $nom_cmn = utf8_encode($row['Nom_Cmn']);

	$result = mssql_query("vm_cdd_s $cod_cdd",$db);
	if (($row = mssql_fetch_array($result)))
            $nom_cdd = utf8_encode($row['Nom_Cdd']);

	$result = mssql_query("vm_suc_s $cod_clt, $cod_sucfct",$db);
	if (($row = mssql_fetch_array($result))) {
		$nom_suc = utf8_encode($row['Nom_Suc']);
		$dir_suc = utf8_encode($row['Dir_Suc']);
		$nom_cmn = utf8_encode($row['Nom_Cmn']);
		$nom_cdd = utf8_encode($row['Nom_Cdd']);
	}
	if ($is_dsp == 1) {
		$result = mssql_query("vm_CrrCmb $cod_crr",$db);
		if (($row = mssql_fetch_array($result))) $nom_crr = $row['Des_Crr'];

		$result = mssql_query("vm_SvcCrrCmb $cod_crr, $cod_svc",$db);
		if (($row = mssql_fetch_array($result))) $nom_svc = $row['Des_SvcCrr'];

		$result = mssql_query("vm_suc_s $cod_clt, $cod_sucdsp",$db);
		if (($row = mssql_fetch_array($result))) {
			$nom_sucdsp = utf8_encode($row['Nom_Suc']);
			$dir_sucdsp = utf8_encode($row['Dir_Suc']);
			$nom_cmndsp = utf8_encode($row['Nom_Cmn']);
			$nom_cdddsp = utf8_encode($row['Nom_Cdd']);
		}
	}

	$result = mssql_query("vm_mtoodc $cod_odc",$db);
	if (($row = mssql_fetch_array($result))) {
            $mto_odc = $row['Mto_Nvt'] + $row['Prc_Dsp'];
            $ctd_prd = $row['Ctd_Prd'];
            $tot_dsg = $row['Tot_Dsg'];
	}

        $qty_msj    = 0;
        $qty_sinlec = 0;
        $result = mssql_query("vm_s_msj_cot $Cod_Cot");
	if (($row = mssql_fetch_array($result))) {
            $qty_msj    = $row['Qty'];
            $qty_sinlec = $row['QtySinLecCot'];
	}

}

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;


function GenTblCenter ($Cod_Mca, $Org_Mca, $Cod_Sty, $Nom_Dsg, $Des_GrpPrd, $Des_Mat, $Des_Pat, $Val_Des, $Tallas) {
	$Des_Sze = "";
	foreach ($Tallas as $key => $value) $Des_Sze = $Des_Sze.$key."(".$value.") ";
	echo "<table BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" width=\"100%%\" height=\"150\" ALIGN=\"center\">\n";
	echo "<tr><td width=\"20%%\" class=\"titulo_tabla5p\">Producto</td><td  class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\"><b>$Cod_Sty - $Nom_Dsg<b></td></tr>\n";
	echo "<tr><td valign=\"top\" class=\"titulo_tabla5p\">Descripci&oacute;n</td><td  class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\">$Des_GrpPrd</td></tr>\n";
	echo "<tr><td class=\"titulo_tabla5p\">Marca</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Cod_Mca</td><td class=\"titulo_tabla5p\">Origen</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Org_Mca</td></tr>\n";
	echo "<tr><td valign=\"top\" class=\"titulo_tabla5p\">Material</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\">$Des_Mat</td><td valign=\"top\" class=\"titulo_tabla5p\">Descuento</td><td valign=\"top\" class=\"label_bottom\" style=\"text-align: left; padding-left: 5px; padding-top: 3px\">$Val_Des</td></tr>\n";
	//echo "<tr><td class=\"titulo_tabla5p\">Pattern</td><td class=\"label_bottom\" style=\"text-align: left; padding-left: 5px\" colspan=\"3\">$Des_Pat</td></tr>\n";
	//echo "<tr><td class=\"titulo_tabla5p\">Tallas</td><td class=\"dato5p\" colspan=\"3\">$Des_Sze</td></tr>\n";
	echo "</table>\n";
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="../dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript" src="../Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="../Include/SoloNumeros.js"></script>
<script type="text/javascript" src="../Include/validarRut.js"></script>
<script type="text/javascript" src="../Include/fngenerales.js"></script>
<link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script type="text/javascript" src="js/AccionesMenu.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    new UvumiDropdown('dropdown-scliente');
    $j(document).ready
	(
		//$j(":input:first").focus();

		function()
		{
            $j("form#F2").submit(function(){
                $j.post("ajax-search.php",{
                        search_type: "dirfct",
                        param_filter: $j("#cod_trn").val(),
                        param_clt: $j("#cod_clt").val(),
                        param_cot: $j("#cod_cot").val()
                }, function(xml) {
                        listDirFct(xml);
                });return false;
		    });

            $j("form#msg").submit(function(){
                $j.post("../ajax-search.php",{
                        search_type: "popupmsg",
						param_filter: "",
						param_cot: $j("#cod_cot").val()
                }, function(xml) {
                        listMessage(xml);
                });return false;
		    });

            $j("form#frm_formapago").submit(function(){
                $j.post("../ajax-search.php",{
                        search_type: "formapago",
						param_filter: "",
						param_clt: $j("#cod_clt").val(),
						param_perfct: $j("#cod_perfct").val(),
						param_docsii: $j("#tip_docsii").val()
                }, function(xml) {
                        listUpdTipoPago(xml);
                });return false;
		    });			
	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }

	);

	function listUpdTipoPago(xml){
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                Num_DocFct = $j("Num_DocFct",filter).text();
                Nom_CltFct = $j("Nom_CltFct",filter).text();
				Dir_Fct = $j("Dir_Fct",filter).text();
				tip_docsii = $j("tip_docsii",filter).text();
            	}
			);
		NewHTML='<table id=\"tbl_formapago\">';
		NewHTML+='<tr>'
		NewHTML+='<td style="padding-top: 30px"><font color="#a9a9a9" size="2">Tipo de Documento</font>&nbsp;&nbsp;(<a href="javascript:popup_formapago(<?php echo $Cod_Cot;?>)">Modificar</a>)</td>'
		NewHTML+='</tr>'
		NewHTML+='<tr>'
		if (tip_docsii == 1){
			NewHTML+='<td style="padding-left: 70px"><font size="2"><b>Boleta'
		} else {
			NewHTML+='<td style="padding-left: 70px"><font size="2"><b>Factura'
			NewHTML+='</b></font>'
			NewHTML+='</td>'
			NewHTML+='</tr>'
			NewHTML+='<tr>'
			NewHTML+='<td style="padding-left: 70px"><font size="2">' + Num_DocFct + '</font>'
			NewHTML+='</td>'
			NewHTML+='</tr>'
			NewHTML+='<tr>'
			NewHTML+='<td style="padding-left: 70px"><font size="2">' + Nom_CltFct + '</font></td>'
			NewHTML+='</tr>'
			NewHTML+='<tr>'
			NewHTML+='<td style="padding-left: 70px"><font size="2">'+ Dir_Fct + '</font></td>'
			NewHTML+='</tr>'
		}
		NewHTML+='</table>';
		$j("#tbl_formapago").replaceWith(NewHTML);
	}

    function listMessage(xml)
    {
        var filter;
		var qty_msj=0;
		var qty_sinlect=0;
		//alert(xml);
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                qty_msj = $j("qty_msj",filter).text();
                qty_sinlect = $j("qty_sinlec",filter).text();
            	}
			);

		NewHTML='<table id=\"tbl_msg\">';
		NewHTML+='<tr>';
		NewHTML+='<td style=\"padding-top: 30px\"><b>Mensajes</b></td>';
		NewHTML+='</tr>';
		NewHTML+='<tr>';
		NewHTML+='<td><hr color=\"#c0c0c0\" /></td>';
		NewHTML+='</tr>';
		NewHTML+='<tr>';
		NewHTML+='<td><font color=\"#a9a9a9\" size=\"2\">Mensajes:</font></td>';
		NewHTML+='</tr>';
		NewHTML+='<tr>';
		NewHTML+='<td style=\"padding-left: 70px\"><font size=\"2\"><b>Ver Historial</b></font></td>';
		NewHTML+='</tr>';
		NewHTML+='<tr>';
		NewHTML+='<td style=\"padding-left: 70px\">';
		NewHTML+='<font size=\"2\">';
		NewHTML+=qty_msj + ' mensajes(s)';
		if (parseInt(qty_sinlect) > 0) {
		    NewHTML+='/<a href=\"verdetallemensajes.php?cot=$Cod_Cot&pag=0\">"' + qty_sinlect + ' no leido(s)</a>';
        }
		NewHTML+='</font>';
		NewHTML+='</td>';
		NewHTML+='</tr>';
		NewHTML+='<tr>';
		NewHTML+='<td style="padding-left: 70px"><font size="2"><a href="javascript:ver_mensaje(<?php echo $Cod_Cot;?>)">Redactar Nuevo</a></font></td>';
		NewHTML+='</tr>';
		NewHTML+='</table>';
		$j("#tbl_msg").replaceWith(NewHTML);
    }
	
    function listDirFct(xml)
    {
        var filter;

        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                codtrn = parseInt($j("codtrn",filter).text());
                nomsuc = "<span id=\"nomsuc\">"+$j("nomsuc",filter).text()+"</span>";
                dirsuc = "<span id=\"dirsuc\">"+$j("dirsuc",filter).text()+"</span>";
                nomcmn = "<span id=\"nomcmn\">"+$j("nomcmn",filter).text()+"</span>";
                nomcdd = "<span id=\"nomcdd\">"+$j("nomcdd",filter).text()+"</span>";
                nomcrr = $j("nomcrr",filter).text();
                nomsvc = $j("nomsvccrr",filter).text();
                nomsucdsp = $j("nomsucdsp",filter).text();
                dirsucdsp = $j("dirsucdsp",filter).text();
                nomcmndsp = $j("nomcmndsp",filter).text();
                nomcdddsp = $j("nomcdddsp",filter).text();
                isdsp = parseInt($j("isdsp",filter).text());
                arcadj = "<span id=\"linkpago\"><a href=\"javascript:ver_comprobante('"+$j("arcadj",filter).text()+"')\">Ver Pago</a></span>";
            }
	);
        if (codtrn == 100) {
            $j("#nomsuc").replaceWith(nomsuc);
            $j("#dirsuc").replaceWith(dirsuc);
            $j("#nomcmn").replaceWith(nomcmn);
            $j("#nomcdd").replaceWith(nomcdd);
        }
        if (codtrn == 200) {
            if (isdsp == 0)
                labeldsp = "<span id=\"tipdsp\">Retiro en Tienda</span>";
            else
                labeldsp = "<span id=\"tipdsp\">Despacho</span>";
            $j("#tipdsp").replaceWith(labeldsp);

            tbl  = "<table id=\"desdsp\">";
            if (isdsp != 0) {
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 70px\"><font size=\"2\">Carrier: " + nomcrr + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 70px\"><font size=\"2\">Servicio: " + nomsvc + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-top: 30px\"><font color=\"#a9a9a9\" size=\"2\">Direccion de Despacho:</font>&nbsp;&nbsp;(<a href=\"javascript:MostrarDespacho()\" title=\"Modifica metodo de entrega\">Modificar</a>)</td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 70px\"><b><font size=\"2\">" + nomsucdsp + "</font></b></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 70px\"><font size=\"2\">" + dirsucdsp + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 70px\"><font size=\"2\">" + nomcmndsp + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 70px\"><font size=\"2\">" + nomcdddsp + "</font></td>";
                tbl += "</tr>";
            }
            tbl += "</table>";

            $j("#desdsp").replaceWith(tbl);
        }
        if (codtrn == 300) {
            $j("#linkpago").replaceWith(arcadj);
        }
    }

    function popwindow(ventana,left,right,ancho,alto){
       window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left='+left+',top='+right+',width='+ancho+',height='+alto)
    }

    function ver_comprobante(comprobante) {
        popwindow('<?php echo $pathadjuntos; ?>'+comprobante,140,140,600,480);
    }

    function verAprobacion(cod_cot) {
        //popwindow('validaPago.php?cod_cot='+cod_cot,90,70,1010,410);
		window.open('validaPago.php?cod_cot='+cod_cot);
    }
	
    function MostrarSucFct() {
        popwindow('UpdDirFctCotizador.php?cot=<?php echo $Cod_Cot ?>',500,200,600,150);
    }

    function MostrarDirFac() {
        popwindow('<?php echo $pathadjuntos; ?>',800,600);
    }

    function MostrarDespacho() {
        popwindow('UpdDspCotizador.php?cot=<?php echo $Cod_Cot ?>',350,100,820,450);
    }

    function MostrarDirDespacho() {
        popwindow('<?php echo $pathadjuntos; ?>',800,600);
    }

    function ing_comprobante() {
        popwindow('IngPago.php?cot=<?php echo $Cod_Cot ?>',350,300,620,150);
    }

    function ActualizarDirFac()
    {
        $j("#cod_trn").val("100");
        $j("form#F2").submit();
    }

    function ActualizarDsp()
    {
        $j("#cod_trn").val("200");
        $j("form#F2").submit();
    }

    function ActualizarPago()
    {
        $j("#cod_trn").val("300");
        $j("form#F2").submit();
    }

    function ver_mensaje(vcod_cot) {
        popwindow('../vistas/lyt_vermensajes.php?accion=111&cot=' + vcod_cot,300,200,900,550);
    }	

	function actualizar_qtymsg(){
		$j("form#msg").submit();
	}

	function popup_formapago(vcod_cot){
		popwindow('../vistas/lyt_formapago.php?cot=' + vcod_cot + "&per="+$j("#cod_perfct").val(),300,200,900,550);
	}	

	function actualizar_TipDoc(){
		$j("form#frm_formapago").submit();
	}
</script>
</head>

<body>
<div id="body">
    <div id="header"></div>
    <div class="menu" id="menu-noselect">
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
                            <a href="como-tomar-medidas.htm">Cómo Tomar Medidas</a>
                        </li>
                        <li>
                            <a href="despachos.htm">Despachos</a>
                        </li>
                        <li>
                            <a href="clean-care.htm">Clean & Care</a>
                        </li>
                        <li>
                            <a href="tracking-ordenes.htm">tracking de Órdenes</a>
                        </li>
                        <li>
                            <a href="como-cotizar.htm">Cómo Cotizar</a>
                        </li>
                       
                        <li>
                            <a href="politicas-privacidad.htm">Políticas de Privacidad</a>
                        </li>
                    </ul>
                </li>
            </ul>		
        </div>
        <a id="catalogo" href="catalogo.php">catalogo</a>
        <a id="contacto" href="contacto.htm">contacto</a>
  
    </div>
	<?php 
		if ($Cod_Per == 0) { 
	?>
    <ul id="usuario_registro">
        <form ID="F1" method="POST" name="F1" action="">
    	<li class="back-verde registro"><a href="registrarse.php">REGISTRARSE</a></li>
        <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
        <li class="back-verde"><a href="javascript:ValidarLogin()">ENTRAR</a></li>
        <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
        <li class="back-verde">CONTRASEÑA</li>
        <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')" /></li>
        <li class="back-verde">RUT</li>
        <input type="hidden" name="dfrut" id="dfrut" />
        </form>
    </ul>
	<?php }
		  else {
	?>
    <ul id="usuario_registro">
		<?php 	echo display_login($Cod_Per, $Cod_Clt, $db); ?>
    </ul>
	
	<?php 
		}
	?>
    <div id="work">
    	<div id="back-registro4">
                <form ID="F2" name="F2" method="POST" action="">
                    <input type="hidden" id="cod_trn" name="cod_trn" value="" />
                    <input type="hidden" id="cod_clt" name="cod_clt" value="<?php echo $cod_clt; ?>" />
                    <input type="hidden" id="cod_cot" name="cod_cot" value="<?php echo $Cod_Cot; ?>" />
                </form>
		<table border="0" width="100%" CELLSPACING="0" CELLPADDING="1">
			<tr>
                            <td align="right" style="padding-right: 10px"><a href="javascript:MostrarDetalle()">Vista Resumen</a> | <a href="#">Vista Detallada</a></td>
			</tr>
			<tr>
                            <td valign="top" style="padding-right: 10px; padding-left: 10px">
				<table border="0" width="100%" CELLSPACING="0" CELLPADDING="1" >
			  	<tr>
			  		<td width="280px">
						<table border="0" width="100%" CELLSPACING="0" CELLPADDING="1">
					   	<tr>
					   		<td><h2><?php echo $fecha ?></h2></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#0066ff">Origen: </font><?php echo ($tip_cnl == "I" ? "Web" : "Vestmed"); ?></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#0066ff">Vendedor: </font> Victoria Cofre</td>
					   	</tr>
					   	<tr>
                                                    <td><font color="#0066ff">Cotizaci&oacute;n #: </font><font color="#ff0000"><?php echo $num_cot; ?></font></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#0066ff">Orden #: </font><font color="#ff0000"><?php echo $cod_odc ?></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-top: 30px"><b>Informaci&oacute;n de Cliente</b></td>
					   	</tr>
					   	<tr>
					   		<td><hr color="#c0c0c0" /></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#a9a9a9" size="2">Cliente:</font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 70px"><b><font size="2"><?php echo $nombre; ?></font></b></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-top: 30px"><font color="#a9a9a9" size="2">Direcci&oacute;n de Facturaci&oacute;n:</font>&nbsp;&nbsp;(<a href="javascript:MostrarSucFct()" title="Modifica direccion de facturacion">Modificar</a>)</td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 70px"><b><font size="2"><span id="nomsuc"><?php echo $nom_suc ?></span></font></b></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 70px"><font size="2"><span id="dirsuc"><?php echo $dir_suc ?></span></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 70px"><font size="2"><span id="nomcmn"><?php echo $nom_cmn ?></span></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 70px"><font size="2"><span id="nomcdd"><?php echo $nom_cdd ?></span></font></td>
					   	</tr>
<!--					   	<tr>
					   		<td style="padding-top: 30px"><font color="#a9a9a9" size="2">Tipo de Documento</font>&nbsp;&nbsp;(<a href="javascript:MostrarDirFac()" title="Modifica direccion de facturacion">Modificar</a>)</td>
					   	</tr>-->
			<form id="frm_formapago" name="frm_formapago">
				<input type="hidden" id = "cod_clt" name = "cod_clt" value = "<?php echo $cod_clt; ?>" />
				<input type="hidden" id = "cod_cot" name = "cod_cot" value = "<?php echo $Cod_Cot; ?>" />
				<input type="hidden" id = "tip_docsii" name = "tip_docsii" value = "<?php echo $tip_docsii; ?>" />
				<input type="hidden" id = "cod_perfct" name = "cod_perfct" value = "<?php echo $cod_perfct; ?>" />
				<input type="hidden" id = "num_DocFct" name = "num_DocFct" value = "<?php echo $Num_DocFct; ?>" />
				<input type="hidden" id = "nom_CltFct" name = "nom_CltFct" value = "<?php echo $Nom_CltFct; ?>" />
				<input type="hidden" id = "dir_Fct" name = "dir_Fct" value = "<?php echo $Dir_Fct; ?>" />
				<table id="tbl_formapago">
					<tr>
						<td style="padding-top: 30px"><font color="#a9a9a9" size="2">Tipo de Documento</font>&nbsp;&nbsp;(<a href="javascript:popup_formapago(<?php echo $Cod_Cot;?>)">Modificar</a>)</td>
					</tr>
					<tr>
						<td style="padding-left: 70px"><font size="2"><b>
							<?php
							if ($tip_docsii == 1)
								echo "Boleta";
							else
								echo "Factura";
							?>
							</b></font>
						</td>
					</tr>
						<?php
						if ($tip_docsii == 2) {
							$result = mssql_query("vm_s_rutfct $cod_clt, $cod_perfct",$db);
							if (($row = mssql_fetch_array($result))) {
								$Num_DocFct = $row['Num_Doc'];
								$Nom_CltFct = utf8_encode($row['Nom_Clt']);
								$Dir_Fct = utf8_encode($row['Dir_Fct']);
						}
						?>
					<tr>
						<td style="padding-left: 70px"><font size="2"><?php echo formatearRut($Num_DocFct); ?></font>
						</td>
					</tr>
					<tr>
						<td style="padding-left: 70px"><font size="2"><?php echo $Nom_CltFct; ?></font></td>
					</tr>
					<tr>
						<td style="padding-left: 70px"><font size="2"><?php echo $Dir_Fct; ?></font></td>
					</tr>
					<?php } ?>

				</table>
			</form>
			<form id="msg">
				<input type="hidden" id="cod_cot" name="cod_cot" value="<?php echo $Cod_Cot; ?>" />
				<table id="tbl_msg">
				   	<tr>
				   		<td style="padding-top: 30px"><b>Mensajes</b></td>
				   	</tr>
				   	<tr>
				   		<td><hr color="#c0c0c0" /></td>
				   	</tr>
				   	<tr>
				   		<td><font color="#a9a9a9" size="2">Mensajes:</font></td>
				   	</tr>
				   	<tr>
				   		<td style="padding-left: 70px"><font size="2"><b>Ver Historial</b></font></td>
				   	</tr>
				   	<tr>
				   		<td style="padding-left: 70px">
                            <font size="2">
                               <?php
                                 echo $qty_msj." mensajes(s)";
                                 if ($qty_sinlec > 0) {
                                    echo " / <a href=\"verdetallemensajes.php?cot=$Cod_Cot&pag=0\">".$qty_sinlec." no leido(s)</a>";
                                 }
                               ?>
                           </font>
						 </td>
				   	</tr>
				   	<tr>
<!--					   		<td style="padding-left: 70px"><font size="2"><a href="vermensajes.php?accion=111&cot=<?php echo $Cod_Cot; ?>">Redactar Nuevo</a></font></td>
-->					   		<td style="padding-left: 70px"><font size="2"><a href="javascript:ver_mensaje(<?php echo $Cod_Cot;?>)">Redactar Nuevo</a></font></td>

				   	</tr>
			</table>
			</form>

					   	<tr>
					   		<td style="padding-top: 30px"><b>M&eacute;todo de Entrega</b></td>
					   	</tr>
					   	<tr>
					   		<td><hr color="#c0c0c0" /></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#a9a9a9" size="2">M&eacute;todo:</font>&nbsp;&nbsp;(<a href="javascript:MostrarDespacho()" title="Modifica metodo de entrega">Modificar</a>)</td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 70px"><font size="2"><b><span id="tipdsp"><?php echo ($is_dsp == 0 ? "Retiro en Tienda" : "Despacho") ?></span></b></font></td>
					   	</tr>
						<?php if ($is_dsp == 1) { ?>
                                                <tr><td><table id="desdsp">
                                                    <tr>
                                                            <td style="padding-left: 70px"><font size="2">Carrier: <?php echo $nom_crr ?></font></td>
                                                    </tr>
                                                    <tr>
                                                            <td style="padding-left: 70px"><font size="2">Servicio: <?php echo $nom_svc ?></font></td>
                                                    </tr>
                                                    <tr>
                                                            <td style="padding-top: 30px"><font color="#a9a9a9" size="2">Direccion de Despacho:</font>&nbsp;&nbsp;(<a href="javascript:MostrarDespacho()" title="Modifica metodo de entrega">Modificar</a>)</td>
                                                    </tr>
                                                    <tr>
                                                            <td style="padding-left: 70px"><b><font size="2"><?php echo $nom_sucdsp ?></font></b></td>
                                                    </tr>
                                                    <tr>
                                                            <td style="padding-left: 70px"><font size="2"><?php echo $dir_sucdsp ?></font></td>
                                                    </tr>
                                                    <tr>
                                                            <td style="padding-left: 70px"><font size="2"><?php echo $nom_cmndsp ?></font></td>
                                                    </tr>
                                                    <tr>
                                                            <td style="padding-left: 70px"><font size="2"><?php echo $nom_cdddsp ?></font></td>
                                                    </tr>
                                                </table></td></tr>
						<?php } else { ?>
                                                <tr><td><table id="desdsp">
                                                </table></td></tr>
						<?php } ?>
					   	<tr>
					   		<td style="padding-top: 30px"><b>Forma de Pago</b></td>
					   	</tr>

					   	<tr>
					   		<td><hr color="#c0c0c0" /></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#a9a9a9" size="2">Total Orden:</font> <b>$<?php echo number_format($mto_odc,0,',','.'); ?></b>&nbsp;
							(<span id="linkpago"><a href="<?php echo $linkpgo; ?>"><?php echo $labelpgo ?></a></span>)
							</td>
					   	</tr>
					   </table>
					</td>
					<td width="1px" style="padding-left: 5px; border-right: #c0c0c0 1px solid;"><br /></td>
			  		<td valign="top" style="padding-left: 10px">
					<table border="0" width="100%" CELLSPACING="0" CELLPADDING="1">
                                        <tr>
                                            <td><hr size="2px" color="#c0c0c0" /></td>
                                        </tr>
                                        <tr>
                                            <td><font size="+1">Estado Global: <b>En Proceso</b></font></td>
                                        </tr>
                                        <tr>
                                            <td><font size="+1" color="#a9a9a9">% Global Avance: </font><font size="+1" color="#808080">0%</font></td>
                                        </tr>
                                        <tr>
                                            <td><font size="2" color="#c0c0c0">Despacho Estimado: </font><font size="2" color="#808080">Enero 10, 2010</font></td>
                                        </tr>
                                        <tr>
                                            <td><font size="2" color="#c0c0c0">Cantidad Despachada: </font><font size="2" color="#808080">0 de 10</font></td>
                                        </tr>
                                            <tr>
                                                <td style="padding-top: 10px"><font size="2">Acciones: <a href="#">Pagar</a>, <a href="#">Mensajes</a>, <a href="#">Despachos</a>, <a href="#">Devoluciones</a>, <a href="#">LOG</a>, <a href="#">Imprimir</a></font></td>
                                            </tr>
                                        <tr>
                                            <td><hr size="2px" color="#c0c0c0" /></td>
                                        </tr>
					<tr><td>

<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
<tr><td colspan="3"><font size="2"><b>Detalles de la Orden</b></font></td></tr>
<tr><td colspan="3">Total Styles: <?php echo number_format($tot_dsg,0,',','.'); ?></td></tr>
<tr><td colspan="3">Total Unidades: <?php echo number_format($ctd_prd,0,',','.'); ?></td></tr>

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
                            <td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo "% 0" ?></td>
                            </tr></table>
                        </td>
                </tr>
<?php
			$Key_PatAnt = $row['Key_Pat'];
			$Des_PatAnt = str_replace("#","'",utf8_encode($row['Des_Pat']));
			$Cod_PatAnt = $row['Cod_Pat'];
			$Prc_UniAnt = $row['Prc_Uni'];
			$Val_Des 	= $row['Val_Des'];
			
			$Tot_Ctd	= 0;
			$NetoTot+=$Neto;
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
	<td class="titulo_tabla5p" width="10%" style="padding-right: 3px; text-align: right">Avance</td>
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
	<td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo "% 0" ?></td>
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
	<td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo "% 0" ?></td>
	</tr></table>
	</td>
</tr>
<tr><td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">
        <input type="hidden" name="dfPeso" id="dfPeso" value="<?php echo $peso; ?>" />&nbsp;
</td></tr>
</table>

					</td></tr>
			        </table>
					</td>
			  	</tr>
			    </table>
		    	</td>
			</tr>
		</table>
		</div>
	</div>
	<div id="footer"></div>
</div>
<script type="text/javascript">
    var f1;
    
    f1 = document.F1;
</script>
</body>
</html>