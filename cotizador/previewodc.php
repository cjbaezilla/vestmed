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

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

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
if (isset($_GET['cot'])) $Cod_Cot = intval(ok($_GET['cot']));

$canales = Array("I" => "Web", "M" => "Vestmed", "A" => "Asociado");

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
        $flg_ter = $row['Cot_FlgTer'];
        $peso = $row['Cot_Peso'];
        $talla = $row['Cot_Estatura'];

	$cod_sucfct = $row['Cod_SucFct'];
        $cod_perfct = $row['Cod_PerFct'];
        $tip_docsii = $row['Tip_DocSII'];
	$is_dsp = $row['is_dsp'];
	$val_dsp = $row['Val_Dsp'] + ($row['Val_Dsp'] * $IVA);
	$cod_crr = $row['Cod_Crr'];
	$cod_svc = $row['Cod_SvcCrr'];
	$cod_sucdsp = $row['Cod_SucDsp'];
	$num_trnbco = $row['Num_trnBco'];
	$arc_adj = $row['Arc_Adj'];
	$arc_adjfis = $row['ArcFis_Adj'];
	$nom_bco = utf8_encode($row['Nom_Bco']);
	$fecha = $meses[intval(substr($fec_cot, -4, 2))]." ".substr($fec_cot, -2, 2).", ".substr($fec_cot, 0, 4);
	if ($row['Cod_TipPer'] == 1)
            $nombre = $row['Nom_Per'].' '.$row['Pat_Per']." ".$row['Mat_Per'];
	else
            $nombre = $row['RznSoc_Per'];
        $nombre = utf8_encode($nombre);
	if (trim($nom_bco) != '' and (trim($arc_adj) != '' or trim($num_trnbco) != '')) {
            $labelpgo = "Ver Pago";
            $linkpgo = "javascript:ver_comprobante('".$row['ArcFis_Adj']."')";
	}
	else {
            $labelpgo = "Pago";
            $linkpgo = "#";
	}
        //$linkpgo = "javascript:verAprobacion('".$arc_adj."')";
        //$linkpgo = "javascript:verAprobacion(".$Cod_Cot.")";
        $linkpgo = "javascript:popwindow('validaPago.php?cod_odc=".$cod_odc."',90,70,800,420)";
        $linkpgoajax = "javascript:popwindow(\'validaPago.php?cod_odc=".$cod_odc."\',90,70,800,420)";
        //$linkpgo = "javascript:ver_comprobante('".$row['ArcFis_Adj']."')";

	//print "sp_s_balance_pgoodc $Cod_Cot";
	$result = mssql_query("sp_s_balance_pgoodc $cod_odc",$db);
	if (($row = mssql_fetch_array($result))) {
		$mto_odc+=($mto_odc * $IVA);
		$tot_pagado = $row['pagado'];
		$tot_x_pagar = $row['por_pagar'];
		$balance = $row['balance'];
		
		//$tot_pagado =$tot_pagado;
		//$tot_x_pagar +=($tot_x_pagar * $IVA); 
		//$balance +=($balance * $IVA);
	}
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
	
	//print "vm_mtoodc $cod_odc";
	$result = mssql_query("vm_mtoodc $cod_odc",$db);
	if (($row = mssql_fetch_array($result))) {
            $mto_nvt = $row['Mto_Nvt'];
            $mto_odc = $row['Mto_Nvt'] + $row['Prc_Dsp'] - $row['Mto_Nvt'] * $row['Val_Des'] / 100;
            $val_dsp = $row['Prc_Dsp'];
            $ctd_prd = $row['Ctd_Prd'];
            $tot_dsg = $row['Tot_Dsg'];
            if ($row['Cod_Iva'] == 2) $mto_odc+=($mto_odc * $IVA);
	}

        $qty_msj    = 0;
        $qty_sinlec = 0;
        $result = mssql_query("vm_s_msj_cot $Cod_Cot");
	if (($row = mssql_fetch_array($result))) {
            $qty_msj    = $row['Qty'];
            $qty_sinlec = $row['QtySinLecCot'];
	}

}


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
            function()
            {
	        $j("form#F2").submit(function(){
	            $j.post("../ajax-search.php",{
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

	        $j("form#searchPro").submit(function(){
                    $j.post("../ajax-search-svc.php",{
                            search_type: "svc",
                            param_filter: $j("#pro").val()
                    }, function(xml) {
                            listLinEsp(xml);
                    });return false;
		    });

	        $j("form#searchEsp").submit(function(){
                    $j.post("../ajax-search-svc.php",{
                            search_type: "con",
                            param_filter: $j("#pro").val(),
                            param_codsvc: $j("#esp").val(),
                            param_codclt: $j("#dfCodClt").val(),
                            param_codsuc: $j("#dfSuc").val(),
                            param_peso: $j("#dfPeso").val()
                    }, function(xml) {
                            listLinSvc(xml);
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

                $j("form#frmCuadratura").submit(function(){
                    $j.post("../ajax-search.php",{
                            search_type: "pago",
                            param_filter: $j("#cod_odc").val(),
                            param_mtoodc: $j("#mto_odc").val(),
                            param_valdsp: $j("#val_dsp").val()
                    }, function(xml) {
                            tablaCuadratura(xml);
                    });return false;
                });		
                    //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
            }

	);

	function tablaCuadratura(xml){
            $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                mto_odc = $j("mto_odc",filter).text();
                tot_pagado = $j("tot_pagado",filter).text();
                tot_x_pagar = $j("tot_x_pagar",filter).text();
                balancefmt = $j("balancefmt",filter).text();
                balance = $j("balance",filter).text();
                });
                NewHTML='<table id="tblpagos" name ="tblpagos">';
                            NewHTML+='<tr>';
                            NewHTML+='<td><font color="#a9a9a9" size="1">Total Orden:</font> <font size="1"><b><span id="mtopago">$ '+mto_odc+'</span></b></font>&nbsp;';
                            NewHTML+='(<span id="linkpago"><a href="<?php echo $linkpgoajax; ?>"><?php echo $labelpgo ?></a></span>)';
                            NewHTML+='</td></tr><tr>';
                            NewHTML+='<td><font color="#a9a9a9" size="1">Total Pagado:</font> <font size="1"><b><span id="bal_mtopago">$ '+tot_pagado+'</span></b></font>&nbsp;';
                            NewHTML+='</td></tr><tr>';
                            NewHTML+='<td><font color="#a9a9a9" size="1">Total Por Pagar:</font> <font size="1"><b><span id="bal_por_pagar">$ '+tot_x_pagar+'</span></b></font>&nbsp;';
                            NewHTML+='</td></tr><tr>';
                            if (balance >= 0)
                                NewHTML+='<td><font color="#a9a9a9" size="1">Balance:</font> <font size="1"><b><span id="balance">$ '+balancefmt+'</span></b></font>&nbsp;';
                            else
                                NewHTML+='<td><font color="#a9a9a9" size="1">Balance:</font> <font size="1" color="red" ><b><span id="balance">$ '+balancefmt+'</span></b></font>&nbsp;';
                            NewHTML+='</td></tr></table>';
                            $j("#tblpagos").replaceWith(NewHTML);
            }
	
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
                    NewHTML+='<td style="padding-left: 40px"><font size="1"><b>BOLETA'
            } else {
                    NewHTML+='<td style="padding-left: 40px"><font size="1"><b>FACTURA'
                    NewHTML+='</b></font>'
                    NewHTML+='</td>'
                    NewHTML+='</tr>'
                    NewHTML+='<tr>'
                    NewHTML+='<td style="padding-left: 40px"><font size="1">' + Num_DocFct + '</font>'
                    NewHTML+='</td>'
                    NewHTML+='</tr>'
                    NewHTML+='<tr>'
                    NewHTML+='<td style="padding-left: 40px"><font size="1">' + Nom_CltFct + '</font></td>'
                    NewHTML+='</tr>'
                    NewHTML+='<tr>'
                    NewHTML+='<td style="padding-left: 40px"><font size="1">'+ Dir_Fct + '</font></td>'
                    NewHTML+='</tr>'
            }
            NewHTML+='</table>';
            $j("#tbl_formapago").replaceWith(NewHTML);
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
				valDsp = parseFloat($j("val_dsp",filter).text());
                mtoodc = "<span id=\"mtopago\">$" + $j("mtoodc",filter).text() + "</span>";
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
                tbl += "<td style=\"padding-left: 40px\"><font size=\"1\">Carrier: " + nomcrr + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 40px\"><font size=\"1\">Servicio: " + nomsvc + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 40px\"><font size=\"1\">Valor: " + FormatNumero(Math.round(valDsp).toString()) + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                //tbl += "<td style=\"padding-top: 30px\"><font color=\"#a9a9a9\" size=\"2\">Direccion de Despacho:</font>&nbsp;&nbsp;(<a href=\"javascript:MostrarDespacho()\" title=\"Modifica metodo de entrega\">Modificar</a>)</td>";
                tbl += "<td style=\"padding-top: 30px\"><font color=\"#a9a9a9\" size=\"2\">Direccion de Despacho:</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 40px\"><b><font size=\"1\">" + nomsucdsp + "</font></b></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 40px\"><font size=\"1\">" + dirsucdsp + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 40px\"><font size=\"1\">" + nomcmndsp + "</font></td>";
                tbl += "</tr>";
                tbl += "<tr>";
                tbl += "<td style=\"padding-left: 40px\"><font size=\"1\">" + nomcdddsp + "</font></td>";
                tbl += "</tr>";
            }
            tbl += "</table>";

            $j("#desdsp").replaceWith(tbl);
            $j("#mtopago").replaceWith(mtoodc);
            
            actualizar_Pago();
        }
        if (codtrn == 300) {
            $j("#linkpago").replaceWith(arcadj);
        }
    }

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
                options+= "<input id=\"rbSucFct\" name=\"rbDirFct\" type=\"radio\" style=\"border:none\" value=\""+$j("codper",filter).text()+"\" onclick=\"GetSuc(this)\" />\n";
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

    function filterPro(obj)
    {
        $j("#pro").val(obj.value);
        $j("#dfCostoDsp").val("$ 0");
        $j("form#searchPro").submit();
    }

    function filterSvc(obj)
    {
        $j("#esp").val(obj.value);
        $j("form#searchEsp").submit();
    }

    function ActualizarDirFacturas()
    {
        $j("form#searchPobDirFct").submit();
    }

    function popwindow(ventana,left,right,ancho,alto){
       popupActive = window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left='+left+',top='+right+',width='+ancho+',height='+alto)
    }

    function popupcorreos(ventana, left, right, ancho, alto){
            popupActive = window.open(ventana,"new_correo",'toolbar=0,location=0,scrollbars=yes,resizable=1,left='+left+',top='+right+',width='+ancho+',height='+alto)
    }

    function redactar_nuevoCorreo(accion,cod_cot){
            popupcorreos('mensajes2.php?accion='+accion+'&cot='+cod_cot,200,100,960,560);
    }
	
    function ver_comprobante(comprobante) {
        popwindow('../<?php echo $pathadjuntos; ?>'+comprobante,300,100,600,400);
    }

    function MostrarDirFac() {
        alert("Opci\u00f3n no implementada");
        return;
        $j("#productos").hide();
        $j("#inf_despacho").hide("");
        $j("#suc_facturacion").hide("");
        $j("#dir_despacho").hide("");
        $j("#tipo_doc").show("slow");
        <?php if ($tip_docsii == 1) { ?>
        $j('input:radio[name=rbDocumento]')[0].checked = true;
        $j("#datos_factura").hide("");
        <?php } else { ?>
        $j('input:radio[name=rbDocumento]')[1].checked = true;
        $j("#datos_factura").show("");
        <?php } ?>
    }

    function MostrarDetalle() {
        alert("Opci\u00f3n no implementada");
        return;
        $j("#datos_factura").hide("");
        $j("#tipo_doc").hide("");
        $j("#inf_despacho").hide("");
        $j("#suc_facturacion").hide("");
        $j("#dir_despacho").hide("");
        $j("#productos").show("slow");
    }

    function MostrarDespacho() {
        popwindow('UpdDspCotizador.php?cot=<?php echo $Cod_Cot ?>',350,100,820,450);
    }

    function MostrarSucFct() {
        popwindow('../UpdDirFct.php?cot=<?php echo $Cod_Cot ?>',500,200,600,150);
    }

    function MostrarDirDespacho() {
        popwindow('<?php echo $pathadjuntos; ?>',800,600);
    }

    function setTipDocSii(obj) {
        if (obj.value == 1)
            $j("#datos_factura").hide("slow");
        else
            $j("#datos_factura").show("slow");
    }

    function GetSuc(obj) {
        $j("#dfCodPer").val(obj.value);
        $j("form#searchDirFct").submit();
    }

    function UpdateSuc() {
        $j("#cod_sucfct").val($j("#rbSucFct:checked").val());
        $j("#cod_trn").val("100");
        f2.submit();
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


    function listLinEsp(xml)
    {
        options="<select id=\"esp\" name=\"esp\" class=\"textfieldv2\" onChange=\"filterSvc(this)\">\n";
        options+="<option selected value=\"_NONE\">Seleccione un Servicio</option>\n";
        $j("filter",xml).each(
                function(id) {
	            filter=$j("filter",xml).get(id);
	            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
	        }
	);
        options+="</select>";
        $j("#esp").replaceWith(options);
        condiciones.value = "";
    }

    function listLinSvc(xml)
    {
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                //alert($j("code",filter).text());
                if ($j("code",filter).text() == "condiciones") {
                    condiciones.value = $j("value",filter).text();
                    cond_original = condiciones.value;
                    if ($j("#rbTipoDsp:checked").val() == 1 && $j("#rbTipoDsp:checked").is(':visible'))
                        condiciones.value = condiciones.value + ". Personal de Vestmed se comunicara con Usted para indicarle la direccion del Carrier para su retiro.";
                }
                if ($j("code",filter).text() == "costo") {
                    //valordsp = parseFloat($j("value",filter).text().replace(".", ""));
                    alert($j("value",filter).text());
                    valordsp = parseFloat($j("value",filter).text());
                    valordsp += valordsp * <?php echo $IVA ?>;
                    $j("#dfCostoDsp").val("$ "+FormatNumero(Math.round(valordsp).toString()));
                    $j("#dfValDsp").val(Math.round(valordsp).toString());
                    if ($j("value",filter).text() == "0") {
                        $j("#labelPeso").replaceWith("<span id=\"labelPeso\" class=\"dato\">POR COTIZAR</span>");
                    }
                    else {
                        $j("#labelPeso").replaceWith("<span id=\"labelPeso\" class=\"dato\"></span>");
                    }
                }
            }
        );
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
                qty_sinlect = $j("qty_sinleccot",filter).text();
            	}
            );

            NewHTML='<table id=\"tbl_msg\">';
            NewHTML+='<tr>';
            NewHTML+='<td style=\"padding-top: 30px\"><font size=\"2\"><b>Mensajes</b></font></td>';
            NewHTML+='</tr>';
            NewHTML+='<tr>';
            NewHTML+='<td><hr color=\"#c0c0c0\" /></td>';
            NewHTML+='</tr>';
            NewHTML+='<tr>';
            NewHTML+='<td><font color=\"#a9a9a9\" size=\"2\">Mensajes:</font></td>';
            NewHTML+='</tr>';
            NewHTML+='<tr>';
            NewHTML+='<td style=\"padding-left: 40px\"><font size=\"1\"><b>Ver Historial</b></font></td>';
            NewHTML+='</tr>';
            NewHTML+='<tr>';
            NewHTML+='<td style=\"padding-left: 40px\">';
            NewHTML+='<font size=\"1\">';
            NewHTML+=qty_msj + ' mensajes(s)';
            if (parseInt(qty_sinlect) > 0) {
                NewHTML+='/<a href=\"javascript:redactar_nuevoCorreo(411,<?php echo $Cod_Cot; ?>)\">' + qty_sinlect + ' no leido(s)</a>';
        }
        NewHTML+='</font>';
        NewHTML+='</td>';
        NewHTML+='</tr>';
        NewHTML+='<tr>';
        NewHTML+='<td style="padding-left: 40px"><font size="1"><a href="javascript:redactar_nuevoCorreo(411,<?php echo $Cod_Cot;?>)">Redactar Nuevo</a></font></td>';
        NewHTML+='</tr>';
        NewHTML+='</table>';
        $j("#tbl_msg").replaceWith(NewHTML);
    }
    
    function popup_formapago(vcod_cot){
            popwindow('../vistas/lyt_formapago.php?cot=' + vcod_cot + "&per="+$j("#cod_perfct").val(),300,200,900,550);
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
    
    function actualizar_TipDoc(){
		$j("form#frm_formapago").submit();
    }

    function actualizar_Pago(){
            $j("form#frmCuadratura").submit();
    }
	
    function verAprobacion(cod_cot) {
        //popwindow('validaPago.php?cod_cot='+cod_cot,90,70,1010,440);
	//window.open('validaPago.php?cod_cot='+cod_cot);
        popwindow('../<?php echo $pathadjuntos.$arc_adjfis; ?>',300,100,600,400);
    }
	
    function puede_borrar(cod_cot){
        if (confirm('¿Esta seguro de anular la venta?')) {
            $j.post("anular_venta.php",
                        {cot: cod_cot}, 
                        function(json){
                                if(json.result=='success'){
                                        $j("#estado").replaceWith("<span id =\"estado\">ANULADA</span>")
                                        return true;
                                }else{
                                        alert('Anulación no pudo ser efectuada.');
                                        return false
                                }
                        },
                        'json'
                );
        }else{
                //return false;	
        }
    }
    
    function actualizar_qtymsg(){
            $j("form#msg").submit();
    }

</script>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, "Ventas", $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
    	<div>
                <form ID="F2" method="POST" name="F2" action="ordenes.php">
                    <input type="hidden" id="cod_trn" name="cod_trn" value="" />
                    <input type="hidden" id="cod_clt" name="cod_clt" value="<?php echo $cod_clt; ?>" />
                    <input type="hidden" id="cod_cot" name="cod_cot" value="<?php echo $Cod_Cot; ?>" />
                    <input type="hidden" id="cod_odc" name="cod_odc" value="<?php echo $cod_odc; ?>" />
                    <input type="hidden" id="val_dsp" name="val_dsp" value="<?php echo $val_dsp; ?>" />
                </form>
		<table border="0" width="100%" CELLSPACING="0" CELLPADDING="1">
			<tr>
                            <td align="left"><a href="ventas.php">Volver</a></td>
                            <td align="right" style="padding-right: 10px">Vista Resumen | <a href="#">Vista Detallada</a></td>
			</tr>
			<tr>
                           <td valign="top" style="padding-right: 10px; padding-left: 10px" colspan="2">
				<table border="0" width="100%" CELLSPACING="0" CELLPADDING="1" >
			  	<tr>
			  		<td width="255px">
						
						<table border="0" width="100%" CELLSPACING="0" CELLPADDING="1" >
						<tr>
					   		<td><h3><?php echo $fecha; ?></h3></td>
					   	</tr>
					   	<tr>
                                                        <td><font color="#0066ff" size="2">Origen: </font><font size="2"><?php echo $canales[$tip_cnl]; ?></font></td>
					   	</tr>
					   	<!--tr>
					   		<td><font color="#0066ff" size="2">Vendedor: </font><font size="2">XXXX</font></td>
					   	</tr-->
					   	<tr>
                                                        <td><font color="#0066ff" size="2">Cotizaci&oacute;n #: </font><font color="#ff0000" size="2"><?php echo $num_cot; ?></font></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#0066ff" size="2">Orden #: </font><font color="#ff0000" size="2"><?php echo $cod_odc ?></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-top: 30px"><font size="2"><b>Informaci&oacute;n de Cliente</b></font></td>
					   	</tr>
					   	<tr>
					   		<td><hr color="#c0c0c0" /></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#a9a9a9" size="1">CLIENTE:</font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><b><font size="1"><?php echo $nombre; ?></font></b></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-top: 30px"><font color="#a9a9a9" size="1">DIRECCI&Oacute;N DE FACTURACI&Oacute;N:</font>&nbsp;&nbsp;<font size="1">(<a href="javascript:MostrarSucFct()" title="Modifica direccion de facturacion">Modificar</a>)</font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><b><font size="1"><span id="nomsuc"><?php echo $nom_suc ?></span></font></b></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1"><span id="dirsuc"><?php echo $dir_suc ?></span></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1"><span id="nomcmn"><?php echo $nom_cmn ?></span></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1"><span id="nomcdd"><?php echo $nom_cdd ?></span></font></td>
					   	</tr>
						
				<tr><td>
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
						<td style="padding-left: 40px"><font size="1"><b>
							<?php
							if ($tip_docsii == 1)
								echo "BOLETA";
							else
								echo "FACTURA";
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
						<td style="padding-left: 40px"><font size="1"><?php echo formatearRut($Num_DocFct); ?></font>
						</td>
					</tr>
					<tr>
						<td style="padding-left: 40px"><font size="1"><?php echo $Nom_CltFct; ?></font></td>
					</tr>
					<tr>
						<td style="padding-left: 40px"><font size="1"><?php echo $Dir_Fct; ?></font></td>
					</tr>
					<?php } ?>
					<tr>
						<td style="padding-top: 30px"><font color="#a9a9a9" size="2">Informaci&oacute;n Personal</font></td>
					</tr>
                                        <?php if ($flg_ter == 1) {?>
					<tr>
						<td style="padding-left: 40px"><font size="1">Producto para Terceros</font></td>
					</tr>
                                        <?php } else { ?>
					<tr>
                                            <td style="padding-left: 40px"><font size="1"><b>Peso: </b><?php echo $peso; ?> Kg</font></td>
					</tr>
					<tr>
                                            <td style="padding-left: 40px"><font size="1"><b>Talla: </b><?php echo $talla; ?> cms</font></td>
					</tr>
                                        <?php } ?>

				</table>
			</form>
            </td></tr>
                                                <tr><td>
			<form id="msg">
				<input type="hidden" id="cod_cot" name="cod_cot" value="<?php echo $Cod_Cot; ?>" />
				<table id="tbl_msg">
				   	<tr>
				   		<td style="padding-top: 30px"><font size="2"><b>Mensajes</b></font></td>
				   	</tr>
				   	<tr>
				   		<td><hr color="#c0c0c0" /></td>
				   	</tr>
				   	<tr>
				   		<td><font color="#a9a9a9" size="2">Mensajes:</font></td>
				   	</tr>
				   	<tr>
				   		<td style="padding-left: 40px"><font size="1"><b>Ver Historial</b></font></td>
				   	</tr>
				   	<tr>
				   		<td style="padding-left: 40px">
                            <font size="1">
                               <?php
                                 echo $qty_msj." mensajes(s)";
                                 if ($qty_sinlec > 0) {
                                    echo " / <a href=\"redactar_nuevoCorreo(411,".$Cod_Cot.")\">".$qty_sinlec." no leido(s)</a>";
                                 }
                               ?>
                           </font>
						 </td>
				   	</tr>
				   	<tr>
<!--					   		<td style="padding-left: 70px"><font size="2"><a href="vermensajes.php?accion=111&cot=<?php echo $Cod_Cot; ?>">Redactar Nuevo</a></font></td>
-->					   		<td style="padding-left: 40px"><font size="1"><a href="javascript:redactar_nuevoCorreo(411,<?php echo $Cod_Cot;?>)">Redactar Nuevo</a></font></td>

				   	</tr>
			</table>
			</form>
                                                    </td></tr>
                                                
                                                
					   	<tr>
					   		<td style="padding-top: 30px"><font size="2"><b>M&eacute;todo de Entrega</b></font></td>
					   	</tr>
					   	<tr>
					   		<td><hr color="#c0c0c0" /></td>
					   	</tr>
					   	<tr>
					   		<td><font color="#a9a9a9" size="1">M&eacute;todo:</font>&nbsp;&nbsp;(<a href="javascript:MostrarDespacho()" title="Modifica metodo de entrega">Modificar</a>)</td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1"><b><span id="tipdsp"><?php echo ($is_dsp == 0 ? "Retiro en Tienda" : "Despacho") ?></span></b></font></td>
					   	</tr>
						<?php if ($is_dsp == 1) { ?>
					   	<tr><td><table id="desdsp">
                                                <tr>
					   		<td style="padding-left: 40px"><font size="1">Carrier: <?php echo $nom_crr ?></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1">Servicio: <?php echo $nom_svc ?></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1">Valor: <?php echo number_format($val_dsp,0,',','.');?></font></td>
					   	</tr>
					   	<tr>
					   		<!--<td style="padding-top: 30px"><font color="#a9a9a9" size="1">Direccion de Despacho:</font>&nbsp;&nbsp;(<a href="javascript:MostrarDespacho()" title="Modifica metodo de entrega">Modificar</a>)</td>-->
							<td style="padding-top: 30px"><font color="#a9a9a9" size="1">Direccion de Despacho:</font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><b><font size="1"><?php echo $nom_sucdsp ?></font></b></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1"><?php echo $dir_sucdsp ?></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1"><?php echo $nom_cmndsp ?></font></td>
					   	</tr>
					   	<tr>
					   		<td style="padding-left: 40px"><font size="1"><?php echo $nom_cdddsp ?></font></td>
					   	</tr>
                                                </table></td></tr>
						<?php } else { ?>
                                                <tr><td><table id="desdsp">
                                                </table></td></tr>
						<?php } ?>
					   	<tr>
					   		<td style="padding-top: 30px"><font size="2"><b>Forma de Pago</b></font></td>
					   	</tr>
					   	<tr>
					   		<td><hr color="#c0c0c0" /></td>
					   	</tr>
					   	<tr>
						<td>
						<form id="frmCuadratura" name="frmCuadratura">
							<input type="hidden" id = "cod_cot" name = "cod_cot" value = "<?php echo $cod_cot; ?>" />
							<input type="hidden" id = "mto_odc" name = "mto_odc" value = "<?php echo $mto_odc; ?>" />
							<input type="hidden" id = "val_dsp" name = "val_dsp" value = "<?php echo $val_dsp; ?>" />
							
							<table id="tblpagos" name ="tblpagos">
								<tr>
							   		<td><font color="#a9a9a9" size="1">Total Orden:</font> <font size="1"><b><span id="mtopago">$<?php echo number_format($mto_odc,0,',','.'); ?></span></b></font>&nbsp;
									(<span id="linkpago"><a href="<?php echo $linkpgo; ?>"><?php echo $labelpgo ?></a></span>)
									</td>

								</tr>
								<tr>
									<td><font color="#a9a9a9" size="1">Total Pagado:</font> <font size="1"><b><span id="bal_mtopago">$ <?php echo number_format($tot_pagado,0,',','.');?></span></b></font>&nbsp;
									</td>
								</tr>
								<tr>
									<td><font color="#a9a9a9" size="1">Total Por Pagar:</font> <font size="1"><b><span id="bal_por_pagar">$ <?php echo number_format($tot_x_pagar,0,',','.');?></span></b></font>&nbsp;
									</td>
								</tr>
								<tr>
									<td><font color="#a9a9a9" size="1">Balance:</font> <font size="1" color="<?php echo ($balance < 0) ? "red" : "black"; ?>"><b><span id="balance">$ <?php echo number_format($balance,0,',','.');?></span></b></font>&nbsp;
									</td>
							   	</tr>

							</table>
						</form>
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
                                            <td>
                                                <font size="+1">Estado Global: <b><span id ="estado">En Proceso</span></b></font>
                                            </td>
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
                                                <td style="padding-top: 10px">
                                                    <font size="2">Acciones: 
                                                        <a href="javascript:popwindow('validaPago.php?cod_odc=<?php echo $cod_odc;?>',90,70,800,420)">Pagar</a>, 
                                                        <a href="javascript:redactar_nuevoCorreo(411,<?php echo $Cod_Cot;?>)">Mensajes</a>, 
                                                        <a href="#">Despachos</a>, 
                                                        <a href="#">Devoluciones</a>, 
                                                        <a href="#">LOG</a>, 
                                                        <a href="javascript:popwindow('printOdc.php?cot=<?php echo $Cod_Cot; ?>',90,70,800,420)">Imprimir</a>, 
                                                        <a href="javascript:puede_borrar(<?php echo $Cod_Cot;?>);">Anular Venta</a>,
                                                    </font>
                                                </td>
                                            </tr>
                                        <tr>
                                            <td><hr size="2px" color="#c0c0c0" /></td>
                                        </tr>
					<tr><td>
<div id="productos">
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
			$Val_Des 	= $row['Val_Des'];

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
	<!--<td class="dato" width="10%" style="padding-right: 3px; border-right: goldenrod 1px solid; text-align: right;"><?php echo formatearMillones($mto_nvt); ?></td>-->
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
<tr>
    <td colspan="3" class="label_top" style="BORDER-TOP: goldenrod 1px solid;">
        <input type="hidden" name="dfPeso" id="dfPeso" value="<?php echo $peso; ?>" />&nbsp;
    </td>
</tr>
<tr>
    <td colspan="3" style="padding-top: 5px; text-align: right"><b>Total:</b> <?php echo number_format($NetoTot,0,',','.'); ?>  </td>
</tr>
<tr>
    <td colspan="3" style="padding-top: 5px; text-align: right"><b>Total + Despacho:</b> <?php echo number_format($NetoTot+$val_dsp,0,',','.'); ?>  </td>
</tr>
</table>
</div>
<div id="bottom">
<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
    <tr>
        <td style="padding-top: 10px; padding-right: 5px; text-align: right">
            <font size="2">
                <a href="javascript:puede_borrar(<?php echo $Cod_Cot;?>);">Anular Venta</a>,
                <a href="javascript:popwindow('printOdc.php?cot=<?php echo $Cod_Cot; ?>',90,70,800,420)">Imprimir</a>, 
                <a href="#">Despachos</a>, 
                <a href="#">Devoluciones</a>
            </font>
        </td>
    </tr>
</table>
</div>
					</td></tr>
			        </table>
					</td>
			  	</tr>
			    </table>
		    	</td>
			</tr>
		</table>
        </div>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script type="text/javascript">
    var f1;
    var f2;
    var condiciones;

    f1 = document.F1;
    f2 = document.F2;

    $j("#tipo_doc").hide("");
    $j("#datos_factura").hide("");
    $j("#inf_despacho").hide("");
    $j("#tipo_despacho").hide("");
    $j("#suc_facturacion").hide("");
    $j("#dir_despacho").hide("");

    condiciones = document.getElementById("texto_condicion");
</script>


</body>
</html>
