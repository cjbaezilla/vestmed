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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 9 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

if (isset($_GET['opc'])) $_SESSION['opcion'] = $_GET['opc'];
$accion = (isset($_GET['accion'])) ? ok($_GET['accion']) : 0;
$idmsg = (isset($_GET['id'])) ? intval(ok($_GET['id'])) : 0;
$Tip_Bus = (isset($_POST['tipo_bus']) ? ok($_POST['tipo_bus']) : 'P');
$Cod_Per = (isset($_POST['cod_per']) ? ok($_POST['cod_per']) : 0);
if ($Cod_Per == "") $Cod_Per = 0;

$result = mssql_query ("vm_cna_sin_res_ctt_cot", $db)
						or die ("No se pudo leer datos del cliente");
if (($row = mssql_fetch_array($result))) $tot_cnactt = $row["tot_cna"];
mssql_free_result($result); 

$result = mssql_query ("vm_cna_sin_res_cot", $db)
						or die ("No se pudo obtener datos de los mensajes");
if (($row = mssql_fetch_array($result))) $tot_cna = $row["tot_cna"];
mssql_free_result($result); 

if ($accion == 21 or $accion == 22) {
    if (isset($_GET['cot'])) {
        $Cod_Cot = ok($_GET['cot']);
        if ($Cod_Cot == 0) $Cod_Cot = ok($_POST['numcot']);
        $cod_per = 0; // la hace vestmed
        $result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
        if (($row = mssql_fetch_array($result))) {
            $cod_clt = $row['Cod_Clt'];
            $mail_ctt = $row['Mail_Ctt'];
        }
        $consulta = str_replace("\'", "''", utf8_decode($_POST['consulta']));
        $result = mssql_query("vm_i_cna $Cod_Cot, $cod_clt, $cod_per, '$consulta'",$db);
        $casomail = 0;
        include("avisonewmensaje.php");
        $asunto       = "Vestmed Ltda.| Tienes un Nuevo Mensaje Pendiente";
        enviar_mail ($mail_ctt, $asunto, $cuerpo_mail, "HTML");
        $accion = 12;
    } else if (isset($_GET['folctt'])) {
        $folctt = ok($_GET['folctt']);
        if ($folctt > 0) {
            $result = mssql_query("vm_cttweb_s $folctt", $db);
            if (($row = mssql_fetch_array($result))) {
                $cod_per = $row['Cod_Per'];
                $mail_ctt = $row['email'];
            }
            $consulta = str_replace("\'", "''", utf8_decode($_POST['consulta']));
            //echo "vm_i_cnactt $folctt, $cod_per, '$consulta'";
            $result = mssql_query("vm_i_cnactt $folctt, $cod_per, '$consulta'", $db);
            $accion = 11;
        }
        else {
            $cod_clt = ok($_POST['cod_clt']);
            $Cod_Suc = ok($_POST['cod_suc']);
            $tip_cna = ok($_POST['tip_cna']);
            $consulta = str_replace("\'", "''", utf8_decode($_POST['consulta']));
            $archivo = '';
            //$query = "vm_i_cttweb_cot $cod_clt, $Cod_Suc, $Cod_Per, 1, $tip_cna, '$consulta', '$archivo'";
            $result = mssql_query("vm_i_cttweb_cot $cod_clt, $Cod_Suc, $Cod_Per, 1, $tip_cna, '$consulta', '$archivo'",$db) or die ("No se pudo insertar registro en Contactos Web");
            if (($row = mssql_fetch_array($result))) {
                $folctt = $row['fol_cttweb'];
                $result = mssql_query("vm_cttweb_s $folctt", $db);
                if (($row = mssql_fetch_array($result))) {
                    $cod_per = $row['Cod_Per'];
                    $mail_ctt = $row['email'];
                }
            }
            $accion = 11;
        }
        $casomail = 1;
        include("avisonewmensaje.php");
        $asunto       = "Vestmed Ltda.| Tienes un Nuevo Mensaje Pendiente";
        enviar_mail ($mail_ctt, $asunto, $cuerpo_mail, "HTML");
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
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
<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="../Include/SoloNumeros.js"></script>
<script language="JavaScript" src="../Include/validarRut.js"></script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script type="text/javascript" src="js/AccionesMenu.js"></script>
<script type="text/javascript">
    new UvumiDropdown('dropdown-scliente');

    function MarcarTodos(form,nombrecheckbox) {
       for (i=0; i<form.elements.length; i++) {
            if (form.elements[i].name == nombrecheckbox)
                    form.elements[i].checked = true;
       }
    }

    function DesMarcarTodos(form,nombrecheckbox) {
       for (i=0; i<form.elements.length; i++) {
            if (form.elements[i].name == nombrecheckbox)
                    form.elements[i].checked = false;
       }
    }
</script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    //*************************************
    var $j = jQuery.noConflict();
    $j(document).ready
    (
            //$j(":input:first").focus();

            function()
            {
            $j("form#searchMsgCot").submit(function(){
                    $j.post("../ajax-search.php",{
                            search_type: "msgcotves",
                            param_clt: $j("#cod_clt").val(),
                            param_cot: $j("#last_cot").val(),
                            param_bus: $j("#tipo_bus_cot").val(),
                            param_per: $j("#cod_per").val(),
                            param_ord: $j("#orden").val()
                    }, function(xml) {
                            listMsgCot(xml);
                    });
                    return false;
            });

            $j("form#searchMsgCtt").submit(function(){
                    $j.post("../ajax-search.php",{
                            search_type: "msgcttves",
                            param_clt: $j("#cod_clt").val(),
                            param_fol: $j("#last_folio").val(),
                            param_bus: $j("#tipo_bus_folio").val(),
                            param_per: $j("#cod_per").val(),
                            param_ord: $j("#ordenfolio").val()
                    }, function(xml) {
                            listMsgCtt(xml);
                    });
                    return false;
            });

            $j("form#frmBusqueda").submit(function(){
                    $j.post("../ajax-search.php",{
                            search_type: "findper",
                            param_filter: "",
                            param_pat: $j("#dfPatPer").val(),
                            param_mat: $j("#dfManPer").val(),
                            param_nom: $j("#dfNomPer").val(),
                            param_clt: $j("#dfNomClt").val(),
                            param_rut: $j("#dfRutSinFmt").val()
                    }, function(xml) {
                            listdataPer(xml);
                    });
                    return false;
            });

            $j("form#searchDatosPer").submit(function(){
                    $j.post("../ajax-search.php",{
                            search_type: "getper",
                            param_filter: $j("#cod_per").val()
                    }, function(xml) {
                            PutdataPer(xml);
                    });
                    return false;
            });

            $j("form#searchPer").submit(function(){
                $j.post("../ajax-search-per.php",{
                        search_type: "clt",
                        param_filter: $j("#dfRut").val()
                }, function(xml) {
                        listLinPer(xml);
                });return false;
            });
            
            $j("form#searchSuc").submit(function(){
                $j.post("../ajax-search-per.php",{
                        search_type: "suc",
                        param_cliente: $j("#cod_clt").val(),
                        param_filter: $j("#suc").val()
                }, function(xml) {
                        listLinSuc(xml);
                });return false;
            });           
            
            $j("form#searchCtt").submit(function(){
                $j.post("../ajax-search-per.php",{
                        search_type: "ctt",
                        param_cliente: $j("#cod_clt").val(),
                        param_sucursal: $j("#suc").val(),
                        param_filter: $j("#ctt").val()
                }, function(xml) {
                        listLinCtt(xml);
                });return false;
            });

        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
        }

    );
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
    function listLinPer(xml)
    {
        var	xisper = false;
        var     xissuc = false;

        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                //alert($j("code",filter).text() + "=" + $j("value",filter).text());
                if ($j("code",filter).text() == "rutfmt") $j("#dfRut").val($j("value",filter).text());
                if ($j("code",filter).text() == "rut")    numdoc = $j("value",filter).text();
                if ($j("code",filter).text() == "nombre") $j("#dfNomClt").val($j("value",filter).text());
                if ($j("code",filter).text() == "codclt") $j("#cod_clt").val($j("value",filter).text());
                if ($j("code",filter).text() == "codper") $j("#cod_per").val($j("value",filter).text());
                xisper = true;
            }
        );

        if (xisper) {
            codsuc = 0;
            options="<select id=\"suc\" name=\"suc\" class=\"textfield\" onChange=\"filterSuc(this)\">\n";
            $j("sucursal",xml).each(
                function(id) {
                    sucursal=$j("sucursal",xml).get(id);
                    options+= "<option value=\""+$j("code",sucursal).text()+"\">"+$j("value",sucursal).text()+"</option>\n";
                    if (codsuc == 0) {
                        codsuc = $j("code",sucursal).text();
                        $j("#cod_suc").val(codsuc);
                    }
                    xissuc = true;
                }
            );
            if (!xissuc) options+= "<option selected value=\"_NONE\">Seleccione una Sucursal</option>\n";
            //options+= "<option value=\"NewSuc\">Nueva Sucursal</option>\n";
            options+= "</select>";
            $j("#suc").replaceWith(options);
            if (SeleccionarEnCombo(codsuc,"suc")) {
                filterSuc($j("#suc"));
            }

            options="<input type=\"button\" id=\"Nuevo\" name=\"Nuevo\" value=\"Editar\" class=\"button2\" onclick=\"EditarCliente('";
            options+=numdoc;
            options+="')\">";
            $j("#Nuevo").replaceWith(options);
        }
        else {
            $j("#dfNomClt").val("");
            $j("#cod_clt").val("");
            $j("#cod_per").val("");
            $j("#dfDir").val("");
            $j("#dfemail").val("");
            $j("#dfFono").val("");
            $j("#dfCmn").val("");
            $j("#dfCdd").val("");

            options="<select id=\"suc\" name=\"suc\" class=\"textfield\" onChange=\"filterSuc(this)\">\n";
            options+="<option selected value=\"_NONE\">Seleccione una Sucursal</option>\n";
            options+="</select>";
            $j("#suc").replaceWith(options);

            options="<select id=\"ctt\" name=\"ctt\" class=\"textfield\" onChange=\"filterCtt(this)\">\n";
            options+="<option selected value=\"_NONE\">Seleccione un Contacto</option>\n";
            options+="</select>";
            $j("#ctt").replaceWith(options);

            //options="<select id=\"cdd\" name=\"cdd\" class=\"textfield\" onChange=\"llenarCdd(this)\">\n";
            //options+="<option selected value=\"_NONE\">Seleccione una Ciudad</option>\n";
            //options+="</select>";
            //$j("#cdd").replaceWith(options);

            //SeleccionarEnCombo("_NONE",'cmn');

            options="<input type=\"button\" id=\"Nuevo\" name=\"Nuevo\" value=\"Nuevo\" class=\"button2\" onclick=\"NuevoCliente()\">";
            $j("#Nuevo").replaceWith(options);

            if (confirm("El rut ingresado NO existe.\nDesea crearlo en estos momentos ?"))
                    popwindow("registrarse.php?clt="+$j("#dfRut").val()+"&xis=0",600)
        }
    }

    function listLinSuc(xml)
    {
        var xisctt = false;
        var numdoc = "";

        $j("filter",xml).each(
            function(id) {
	            filter=$j("filter",xml).get(id);
                    //alert($j("code",filter).text() + "=" + $j("value",filter).text());
	            if ($j("code",filter).text() == "dirsuc") $j("#dfDir").val($j("value",filter).text());
	            if ($j("code",filter).text() == "fonsuc") $j("#dfFono").val($j("value",filter).text());
	            if ($j("code",filter).text() == "nomcmn") $j("#dfCmn").val($j("value",filter).text());
	            if ($j("code",filter).text() == "nomcdd") $j("#dfCdd").val($j("value",filter).text());
	            if ($j("code",filter).text() == "numdoc") numdoc = $j("value",filter).text();
	        }
            );
            if (numdoc != "") NuevaSuc (numdoc);
            else {
                codctt = 0;
                options="<select id=\"ctt\" name=\"ctt\" class=\"textfield\" onChange=\"filterCtt(this)\">\n";
                //options+="<option selected value=\"_NONE\">Seleccione un Contacto</option>\n";
                $j("contacto",xml).each(
                    function(id) {
                        contacto=$j("contacto",xml).get(id);
                        options+= "<option value=\""+$j("code",contacto).text()+"\">"+$j("value",contacto).text()+"</option>\n";
                        if (codctt == 0) codctt = $j("code",contacto).text();
                        xisctt = true;
                    }
                );
                if (!xisctt) options+= "<option selected value=\"_NONE\">Seleccione un Contacto</option>\n";
                options+= "<option value=\"NewCtt\">Nuevo Contacto</option>\n";
                options+= "</select>";
                $j("#ctt").replaceWith(options);
                if (SeleccionarEnCombo(codctt,"ctt")) filterCtt($j("#ctt"));
            }
    }

    function listLinCtt(xml)
    {
        var codsuc = 0;
        var numdoc = "";
        //alert("listLinCtt");
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                if ($j("code",filter).text() == "mail") $j("#dfemail").val($j("value",filter).text());
                if ($j("code",filter).text() == "fonctt") $j("#dfFono").val($j("value",filter).text());
                if ($j("code",filter).text() == "numdoc") numdoc = $j("value",filter).text();
                if ($j("code",filter).text() == "codsuc") codsuc = $j("value",filter).text();
            }
            );
            if (numdoc != "") NuevoCtt (numdoc,codsuc);
    }

    function filterSuc(obj)
    {
        if (obj.value != "NewSuc") {
            $j("#cod_suc").val(obj.value);
            $j("form#searchSuc").submit();
        }
    }

    function filterCtt(obj)
    {
        if (obj.value != "_NONE") $j("form#searchCtt").submit();
    }


    function SeleccionarEnCombo(codigo,obj) {
        var i=0;
        var combo = document.getElementById(obj);
        var cantidad = combo.length;

        for (i = 0; i < cantidad; i++) {
            if (combo[i].value == codigo) {
                combo[i].selected = true;
                return true;
            }
        }
            return false;
    }

    function PutdataPer (xml)
    {
        var i = 0;
        options="<table id=\"tblDatosPer\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"2\" width=\"500\" ALIGN=\"left\">\n";
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                if ($j("tipper",filter).text() == "1") {
                    options+="<tr><td width=\"120\"><b>RUT</b></td>"
                    options+="<td align=\"left\"  valign=\"top\" style=\"PADDING-RIGHT: 5px\">"+$j("numdocfmt",filter).text()+"</td><tr>\n";
                    options+="<tr><td width=\"120\"><b>Nombre</b></td>"
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("nombre",filter).text()+"</td></tr>\n";
                    options+="<tr><td width=\"120\"><b>Profesi&oacute;n</b></td>"
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("nompro",filter).text()+"</td></tr>\n";
                    options+="<tr><td width=\"120\"><b>Especialidad</b></td>"
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("nomesp",filter).text()+"</td></tr>\n";
                    options+="<tr><td width=\"120\"><b>Sexo</b></td>"
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+($j("sexo",filter).text() == "2" ? "Hombre" : "Mujer")+"</td></tr>\n";
                }
                else {
                    options+="<tr><td width=\"120\"><b>RUT</b></td>"
                    options+="<td align=\"right\" valign=\"top\" style=\"PADDING-RIGHT: 5px\">"+$j("numdocfmt",filter).text()+"</td></tr>\n";
                    options+="<tr><td width=\"120\"><b>Nombre</b></td>"
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("nombre",filter).text()+"</td></tr>\n";
                    options+="<tr><td width=\"120\"><b>Nombre</b></td>"
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("groper",filter).text()+"</td></tr>\n";
                }
                i++;
            }
        );
        options+="</table>";
        $j("#tblDatosPer").replaceWith(options);
        $j("#datos_persona").show("slow");
    }


    function listdataPer (xml)
    {
        var i = 0;
        //alert("listdataPer");
        options="<table WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" ALIGN=\"center\" id=\"tblResultado\">\n";
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                if ($j("tipper",filter).text() == "1") {
                    if (i == 0) {
                        options+="<tr>\n";
                        options+="<td class=\"titulo_tabla\" width=\"15%\" align=\"middle\">Rut</td>\n";
                        options+="<td class=\"titulo_tabla\" width=\"40%\" align=\"middle\">Nombre</td>\n";
                        options+="<td class=\"titulo_tabla\" width=\"15%\" align=\"middle\">Profesion</td>\n";
                        options+="<td class=\"titulo_tabla\" width=\"15%\" align=\"middle\">Especialidad</td>\n";
                        options+="<td class=\"titulo_tabla\" width=\"15%\" align=\"middle\">mail</td>\n";
                        options+="</tr>\n";
                    }
                    options+="<tr>\n";
                    options+="<td align=\"right\" valign=\"top\" style=\"PADDING-RIGHT: 5px\"><a href=\"javascript:listarCotPer('"+$j("codper",filter).text()+"')\">"+$j("numdocfmt",filter).text()+"</a></td>\n";
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("nombre",filter).text()+"</td>\n";
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("nompro",filter).text()+"</td>\n";
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("nomesp",filter).text()+"</td>\n";
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("mailctt",filter).text()+"</td>\n";
                    options+="</tr>\n";
                }
                else {
                    if (i == 0) {
                        options+="<tr>\n";
                        options+="<td class=\"titulo_tabla\" width=\"15%\" align=\"middle\">Rut</td>\n";
                        options+="<td class=\"titulo_tabla\" width=\"65%\" align=\"middle\">Raz&oacute;n Social</td>\n";
                        options+="<td class=\"titulo_tabla\" width=\"20%\" align=\"middle\">Giro</td>\n";
                        options+="</tr>\n";
                    }
                    options+="<tr>\n";
                    options+="<td align=\"right\" valign=\"top\" style=\"PADDING-RIGHT: 5px\"><a href=\"javascript:listarCotPer('"+$j("codper",filter).text()+"')\">"+$j("numdocfmt",filter).text()+"</a></td>\n";
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("nombre",filter).text()+"</td>\n";
                    options+="<td align=\"left\"  valign=\"top\" style=\"padding-left:3px\">"+$j("groper",filter).text()+"</td>\n";
                    options+="</tr>\n";
                }
                i++;
            });
        options+="</table>";
        $j("#tblResultado").replaceWith(options);
    }

    function listMsgCot (xml)
    {
        var tot_filas = 0;
        var atrascot = 0;

        options="<table BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" width=\"100%\" ALIGN=\"center\" id=\"tblMsgCot\">\n";
        if ($j("#tipo_bus_cot").val() == "P")
           options+="<tr><td colspan=\"7\" align=\"right\"><a href=\"javascript:listarCot('T')\">TODOS</a> | <a href=\"javascript:listarCot('A')\">ABIERTOS</a> | PENDIENTES</td></tr>\n";
        else if ($j("#tipo_bus_cot").val() == "T")
           options+="<tr><td colspan=\"7\" align=\"right\">TODOS | <a href=\"javascript:listarCot('A')\">ABIERTOS</a> | <a href=\"javascript:listarCot('P')\">PENDIENTES</a></td></tr>\n";
        else
           options+="<tr><td colspan=\"7\" align=\"right\"><a href=\"javascript:listarCot('T')\">TODOS</a> | ABIERTOS | <a href=\"javascript:listarCot('P')\">PENDIENTES</a></td></tr>\n";
        options+="<tr>\n";
        options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Fecha</td>\n";
        options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Grupo</td>\n";
        options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"left\" class=\"titulo_tabla\">#Cot</td>\n";
        options+="<td width=\"15%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">RUT</td>\n";
        options+="<td width=\"40%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Nombre</td>\n";
        options+="<td width=\"5%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Msg</td>\n";
        //options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Historial</td>\n";
        options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Ver</td>\n";
        //options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Nuevo</td>\n";

        options+="</tr>\n";
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                //alert ("row="+$j("row",filter).text());
                options+= "<tr>\n";
                options+= "<td align=\"center\" valign=\"top\">"+$j("fecfmtcot",filter).text()+"</td>\n";
                options+= "<td align=\"center\" valign=\"top\">"+$j("codcot",filter).text()+"</td>\n";
                options+= "<td align=\"left\" valign=\"top\">"+$j("numcot",filter).text()+"</td>\n";
                options+= "<td align=\"right\" valign=\"top\" style=\"padding-right: 5px\">"+$j("numdoc",filter).text()+"</td>\n";
                options+= "<td align=\"left\" valign=\"top\">"+$j("nomclt",filter).text()+"</td>\n";
                options+= "<td align=\"center\" valign=\"top\">"+$j("ctd",filter).text()+"</td>\n";

                //lite = "<td align=\"center\" valign=\"top\">\n";
                //lite+= "<a href=\"javascript:popwindow('historico_msjcot.php?cot="+$j("codcot",filter).text()+"',400)\">\n";
                //lite+= "<img src=\"../images/001_38.gif\" width=\"16px\" height=\"16px\"></a>\n";
                //lite+= "</td>\n";
                //options+=lite;

                options+="<td align=\"center\" valign=\"top\">";
                if ($j("tnepdt",filter).text() == "S") {
                   options+="<table cellpadding=\"0\" cellspacing=\"0\"><tr>";
                   options+="<td><a href=\"javascript:respondercot("+$j("codcot",filter).text()+")\"><img src=\"../images/mail.png\" width=\"24px\" height=\"16px\"></a></td>";
                   options+="<td>&nbsp;("+$j("ctdsinlec",filter).text()+")</td>";
                   options+="</tr></table>";
                }
                else
                   options+="<a href=\"javascript:respondercot("+$j("codcot",filter).text()+")\"><img src=\"../images/001_38.gif\" width=\"16px\" height=\"16px\" alt=\"\"></a>\n";

                options+="</td>\n";

                //options+="<td align=\"center\" valign=\"top\">";
                //options+="<a href=\"javascript:Nuevo_Msg(2,"+$j("codcot",filter).text()+")\"><img src=\"../images/folder_feed.png\" width=\"16px\" height=\"16px\"></a>";
                //options+="</td>\n";

                options+= "</tr>\n";
                fila = $j("row",filter).text();
                if (atrascot == 0) atrascot = fila;
                if (tot_filas == 0) $j("#primera_cot").val($j("row",filter).text());
                //if ($j("#primera_cot").val() == "0") $j("#primera_cot").val($j("row",filter).text());
                tot_filas++;
            }
        );

        options+="<td style=\"padding-top: 5px\" colspan=\"7\" align=\"right\">\n";
        if (tot_filas >= 18) {
            options+="<input type=\"hidden\" id=\"last_cot\" value=\""+fila+"\">\n"
        }
        else {
            options+="<input type=\"hidden\" id=\"last_cot\" value=\"_NONE\">\n"
        }

        options+="</table>";
        $j("#tblMsgCot").replaceWith(options);
        $j("#atras_cot").val(atrascot);
    }

    function listMsgCtt (xml)
    {
        var tot_filas = 0;
        var atrasfol = 0;
        var arrTipCna = ["","Informaci\u00f3n del Producto","Reclamos","Contacto Comercial","Solicitud de Catalogos","Informaci\u00f3n de sus Ordenes","Otro"];

        options="<table BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" width=\"100%\" ALIGN=\"center\" id=\"tblMsgCtt\">\n";
        if ($j("#tipo_bus_folio").val() == "P")
           options+="<tr><td colspan=\"7\" align=\"right\"><a href=\"javascript:listarCtt('T')\">TODOS</a> | PENDIENTES</td></tr>\n";
        else
           options+="<tr><td colspan=\"7\" align=\"right\">TODOS | <a href=\"javascript:listarCtt('P')\">PENDIENTES</a></td></tr>\n";
        options+="<tr>\n";
        options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Fecha</td>\n";
        options+="<td width=\"5%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Grupo</td>\n";
        options+="<td width=\"23%\" VALIGN=\"TOP\" ALIGN=\"left\" class=\"titulo_tabla\">#Caso</td>\n";
        options+="<td width=\"12%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">RUT</td>\n";
        options+="<td width=\"30%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Nombre</td>\n";
        options+="<td width=\"5%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Msg</td>\n";
        //options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Historial</td>\n";
        options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Ver</td>\n";
        //options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Nuevo</td>\n";
        options+="</tr>\n";
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                //alert ($j("code",filter).text()+"="+$j("value",filter).text());
                options+= "<tr>\n";
                options+= "<td align=\"center\" valign=\"top\">"+$j("feccna",filter).text()+"</td>\n";
                options+= "<td align=\"center\" valign=\"top\">"+$j("folctt",filter).text()+"</td>\n";
                options+= "<td align=\"left\" valign=\"top\">"+arrTipCna[$j("tipcna",filter).text()]+"</td>\n";
                options+= "<td align=\"right\" valign=\"top\" style=\"padding-right: 5px\">"+$j("numdocfmt",filter).text()+"</td>\n";
                options+= "<td align=\"left\" valign=\"top\" title=\""+$j("nomclt",filter).text()+"\">"+$j("nomcltmax",filter).text()+"</td>\n";
                options+= "<td align=\"center\" valign=\"top\">"+$j("ctd",filter).text()+"</td>\n";

                //lite = "<td align=\"center\" valign=\"top\">\n";
                //lite+= "<a href=\"javascript:popwindow('historico_msjcot.php?folctt="+$j("folctt",filter).text()+"&per="+$j("numdoc",filter).text()+"',400)\">\n";
                //lite+= "<img src=\"../images/001_38.gif\" width=\"16px\" height=\"16px\"></a>\n";
                //lite+= "</td>\n";
                //options+=lite;

                options+="<td align=\"center\" valign=\"top\">";
                if ($j("tnepdt",filter).text() == "S") {
                   options+="<table cellpadding=\"0\" cellspacing=\"0\"><tr>";
                   options+="<td><a href=\"javascript:responderctt("+$j("folctt",filter).text()+")\"><img src=\"../images/mail.png\" width=\"24px\" height=\"16px\"></a></td>";
                   options+="<td>&nbsp;("+$j("ctdsinlec",filter).text()+")</td>";
                   options+="</tr></table>";
                }
                else
                   options+="<a href=\"javascript:responderctt("+$j("folctt",filter).text()+")\"><img src=\"../images/001_38.gif\" width=\"16px\" height=\"16px\" alt=\"\"></a>\n";
                options+="</td>\n";

                //options+="<td align=\"center\" valign=\"top\">";
                //options+="<a href=\"javascript:Nuevo_Msg(1,"+$j("folctt",filter).text()+")\"><img src=\"../images/folder_feed.png\" width=\"16px\" height=\"16px\"></a>";
                //options+="</td>\n";


                options+= "</tr>\n";
                fila = $j("row",filter).text();
                if (atrasfol == 0) atrasfol = fila;
                if (tot_filas == 0) $j("#primer_folio").val($j("row",filter).text());
                //if ($j("#primer_folio").val() == "0") $j("#primer_folio").val($j("row",filter).text());

                tot_filas++;
            }
        );

        options+="<td style=\"padding-top: 5px\" colspan=\"7\" align=\"right\">\n";
        options+="<input type=\"hidden\" id=\"cod_clt\" value=\"\">\n";
        if (tot_filas >= 18) {
                options+="<input type=\"hidden\" id=\"last_folio\" value=\""+fila+"\">\n"
        }
        else {
                options+="<input type=\"hidden\" id=\"last_folio\" value=\"_NONE\">\n"
        }

        options+="</table>";
        $j("#tblMsgCtt").replaceWith(options);
        $j("#atras_folio").val(atrasfol);
    }

    function Next_MsgCtt() {
        $j("#ordenfolio").val("1");
        if ($j("#last_folio").val() == "_NONE")
            alert ("No existen mas mensajes que mostrar");
        else
            $j("form#searchMsgCtt").submit();
    }

    function Previus_MsgCtt() {
        $j("#ordenfolio").val("2");
        if ($j("#primer_folio").val() == "1")
                alert ("No existen mas mensajes que mostrar");
        else {
            $j("#last_folio").val(parseInt($j("#primer_folio").val())-1);
            $j("form#searchMsgCtt").submit();
        }
    }

    function Next_MsgCot() {
        $j("#orden").val("1");
        if ($j("#last_cot").val() == "_NONE")
            alert ("No existen mas mensajes que mostrar");
        else
            $j("form#searchMsgCot").submit();
    }

    function Previus_MsgCot() {
        $j("#orden").val("2");
        if ($j("#primera_cot").val() == "1")
                alert ("No existen mas mensajes que mostrar");
        else {
            $j("#last_cot").val(parseInt($j("#primera_cot").val())-1);
            $j("form#searchMsgCot").submit();
        }
    }

    function listarCot(caso) {
        $j("#orden").val("1");
        $j("#last_cot").val("0");
        $j("#tipo_bus_cot").val(caso);
        $j("#tipo_bus").val(caso);
        $j("#primera_cot").val("0");
        $j("form#searchMsgCot").submit();
    }

    function listarCtt(caso) {
        $j("#ordenfolio").val("1");
        $j("#last_folio").val("0");
        $j("#tipo_bus_folio").val(caso);
        $j("#tipo_bus").val(caso);
        $j("#primer_folio").val("0");
        $j("form#searchMsgCtt").submit();
    }

    function listarCotPer(codper) {
        $j("#cod_per").val(codper);
        listarCot('P');
        listarCtt('P');
        Show_Cotizaciones();
        traerDatosPer();
    }

    function Show_Cotizaciones ()
    {
        $j("#busqueda").hide();
        $j("#datos_persona").hide();
        $j("#historial").show("slow");
        $j("#formulario_contacto").hide();
        $j("#formulario_cotizaciones").show("slow");
        $j("#btnCotizaciones").removeClass("btn3");
        $j("#btnCotizaciones").addClass("btn4");
        $j("#btnContactos").removeClass("btn4");
        $j("#btnContactos").addClass("btn3");
        $j("#btnBuscar").removeClass("btn4");
        $j("#btnBuscar").addClass("btn3");
    }

    function Show_Contactos ()
    {
        $j("#busqueda").hide();
        $j("#historial").show("slow");
        $j("#formulario_cotizaciones").hide();
        $j("#formulario_contacto").show("slow");
        $j("#btnCotizaciones").removeClass("btn4");
        $j("#btnCotizaciones").addClass("btn3");
        $j("#btnContactos").removeClass("btn3");
        $j("#btnContactos").addClass("btn4");
        $j("#btnBuscar").removeClass("btn4");
        $j("#btnBuscar").addClass("btn3");
    }

    function Show_Busqueda ()
    {
        options="<table WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" ALIGN=\"center\" id=\"tblResultado\">\n";
        options+="<tr><td>&nbsp;</td></tr>\n";
        options+="</table>\n";
        $j("#tblResultado").replaceWith(options);

        $j("#dfPatPer").val("");
        $j("#dfMatPer").val("");
        $j("#dfNomPer").val("");
        $j("#dfNomClt").val("");
        $j("#dfRutPer").val("");

        $j("#historial").hide();
        $j("#btnBuscar").removeClass("btn3");
        $j("#btnBuscar").addClass("btn4");
        $j("#busqueda").show("slow");
    }

    function Busqueda ()
    {
        options="<table WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" ALIGN=\"center\" id=\"tblResultado\">\n";
        options+="<tr><td><img src=\"../lytebox/images/loading.gif\"/></td></tr>\n";
        options+="</table>\n";
        $j("#tblResultado").replaceWith(options);

        if ($j("#dfPatPer").val() == "" && $j("#dfMatPer").val() == "" && $j("#dfNomPer").val() == "" && $j("#dfNomClt").val() == "" && $j("#dfRutPer").val() == "")
            alert("Debe ingresar un par\u00e1metro de b\u00fasqueda");
        else
            $j("form#frmBusqueda").submit();
    }

    function traerDatosPer ()
    {
        $j("form#searchDatosPer").submit();
    }

    //*************************************

    function Nuevo_Msg(caso,id) {
        f2.action = "mensajes.php?accion="+caso+"&id="+id;
        f2.submit();
    }


    function respondercot(cot) {
        f2.action = "mensajes2.php?cot="+cot;
        f2.submit();
    }

    function responderctt(folio) {
        f2.action = "mensajes2.php?folctt="+folio;
        f2.submit();
    }

    function Salir(caso) {
        f2.action = "mensajes.php?accion="+caso;
        f2.submit();
    }

    function filterPro(obj)
    {
        f2.codpro.value = obj.value;
        $j("form#searchPro").submit();
    }
	
    function llenarEsp(obj)
    {
        f2.codesp.value = obj.value;
    }
	
    function ResetMsgCot()
    {
        $j("#datos_persona").hide();
        $j("#cod_per").val(0);
        $j("#orden").val("1");
        $j("#last_cot").val("0");
        $j("#primera_cot").val("0");
        $j("form#searchMsgCot").submit();
    }

    function ResetMsgCtt()
    {
        $j("#datos_persona").hide();
        $j("#cod_per").val(0);
        $j("#ordenfolio").val("1");
        $j("#last_folio").val("0");
        $j("#primer_folio").val("0");
        $j("form#searchMsgCtt").submit();
    }
	
    function filterPer(obj)
    {
        if (obj.value != "") {
            if (validarRutCompleto('dfRut')) $j("form#searchPer").submit();
            else {
              alert("Rut invalido. Intente nuevamente");
              $('dfRut').focus();
            }
        }
    }

    function checkDataFormCtt (form)
    {
        if (form.cod_per.value == "0") {
              alert("Debe indicar una persona a quien se le enviar\u00e1 el mensaje");
              return false;
        }
        if (form.cod_clt.value == "") {
              alert("La persona seleccionada no es cliente. No se le puede enviar un mensaje");
              return false;
        }
        if (form.tip_cna.value == "") {
              alert("Debe seleccionar un Tipo de Consulta");
              return false;
        }
        if (form.consulta.value == "") {
              alert("Debe indicar una consulta a enviar");
              return false;
        }

        return true;
    }

    function checkDataFormCot (form)
    {
        if (form.numcot.value == "0") {
              alert("Debe indicar una cotizaci\u00f3n");
              return false;
        }
        if (form.consulta.value == "") {
              alert("Debe indicar una consulta a enviar");
              return false;
        }

        return true;
    }

</script>

</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, "Mensajes", $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="left"><strong>Escritorio</strong></P>
<P align="center">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="170px" align="left" valign="top">
	<?php echo display_mnuizq($UsrId, $db); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
	<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
	<tr><td>
		<?php if ($accion == 0 or $accion == 11 or $accion == 12) { ?>
		<H1>Nuevos Mensajes Recibidos</H1>
		<table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="650" ALIGN="center">
		<tr>
                    <td width="70%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px">Msg Formulario Contacto: (<a href="javascript:listarCtt('P');Show_Contactos();"><?php echo $tot_cnactt ?></a>)</td>
                    <td rowspan="2" valign="middle">
                    <input type="button" name="btnBuscar" id="btnBuscar" value=" Busqueda " class="btn3" onclick="Show_Busqueda()">
                    </td>
		</tr>
		<tr>
                    <td width="70%" VALIGN="TOP" class="dato">Msg Cotizaciones: (<a href="javascript:listarCot('P');Show_Cotizaciones();"><?php echo $tot_cna; ?></a>)</td>
		</tr>
		</table>
		
		<BR>
		<div id="historial">
			<H3>Historial de Mensajes</H3>
			<div id="datos_persona" style="position:relative; overflow:auto; left: 30px; top: 0px;" >
			<form ID="searchDatosPer" name="searchDatosPer" action="">
			<table id="tblDatosPer" BORDER="0" CELLSPACING="1" CELLPADDING="2" width="500" ALIGN="left">
			<?php if ($Cod_Per > 0) { 
			        $sp = mssql_query("vm_per_s $Cod_Per", $db);
                                if (($row = mssql_fetch_array($sp))) {
                                    $tip_per = $row['Cod_TipPer'];
                                    $rut_per = formatearRut($row['Num_Doc']);
                                    $nom_per = utf8_encode($row['Nom_Clt']);
                                    if ($tip_per == 1) {
                                        $nom_pro = $row['Nom_Pro'];
                                        $nom_esp = $row['Nom_Esp'];
                                        $sexo = $row['Sex'];
                                    }
                                    else
                                        $gro_per = $row['Gro_Per'];
                                }
			?>
                                <tr><td width="120"><b>RUT</b></td>
                                <td align="left"  valign="top" style="PADDING-RIGHT: 5px"><?php echo $rut_per; ?></td><tr>
                                <tr><td width="120"><b>Nombre</b></td>
                                <td align="left"  valign="top" style="padding-left:3px"><?php echo $nom_per; ?></td></tr>
                                <?php if ($tip_per == 1) { ?>
                                <tr><td width="120"><b>Profesi&oacute;n</b></td>
                                <td align="left"  valign="top" style="padding-left:3px"><?php echo $nom_pro; ?></td></tr>
                                <tr><td width="120"><b>Especialidad</b></td>
                                <td align="left"  valign="top" style="padding-left:3px"><?php echo $nom_esp; ?></td></tr>
                                <tr><td width="120"><b>Sexo</b></td>
                                <td align="left"  valign="top" style="padding-left:3px"><?php echo ($sexo == 2 ? "Hombre" : "Mujer"); ?></td></tr>
                                <?php } else { ?>
                                <tr><td width="120"><b>Giro</b></td>"
                                <td align="left"  valign="top" style="padding-left:3px"><?php echo $gro_per; ?></td></tr>
                                <?php } ?>
			
			<?php } ?>
			</table>
			</form>
			</div>
			<table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="500" ALIGN="center">
			<tr>
                            <td width="50%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px; TEXT-ALIGN: center">
                            <input type="button" name="btnCotizaciones" id="btnCotizaciones" value=" Formulario Cotizaciones " class="<?php echo ($accion == 12) ? "btn4" : "btn3"; ?>" onclick="Show_Cotizaciones()">
                            </td>
                            <td width="50%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px; TEXT-ALIGN: center">
                            <input type="button" name="btnContactos" id="btnContactos" value=" Formulario Contacto " class="<?php echo ($accion == 11) ? "btn4" : "btn3"; ?>" onclick="Show_Contactos()">
                            </td>
			</tr>
			</table>
			<div style="position:relative;width:730px;height:490px; overflow:auto; left: 20px; top: 0px;">
				<div id="formulario_contacto">
                                    <H3>Msg. Formulario Contacto</H3>
                                    <form ID="searchMsgCtt" name="searchMsgCtt" action="">
                                    <div style="position:relative;width:730px;height:390px; overflow:auto; left: 0px; top: 0px;">
                                    <table BORDER="0" CELLSPACING="1" CELLPADDING="1" width="100%" ALIGN="center" id="tblMsgCtt">
                                    <tr><td colspan="7" align="right">
                                    <?php if ($Tip_Bus != "T") { ?><a href="javascript:listarCtt('T')">TODOS</a><?php } else { ?>TODOS<?php } ?> |
                                    <?php if ($Tip_Bus != "P") { ?><a href="javascript:listarCtt('P')">PENDIENTES</a><?php } else { ?>PENDIENTES<?php } ?>

                                    </td></tr>
                                    <tr>
                                            <td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Fecha</td>
                                            <td width="5%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Grupo</td>
                                            <td width="23%" VALIGN="TOP" ALIGN="left"   class="titulo_tabla">#Caso</td>
                                            <td width="12%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">RUT</td>
                                            <td width="30%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Nombre</td>
                                            <td width="5%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Msg</td>
                                            <!--td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Historial</td-->
                                            <td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Ver</td>
                                            <!--td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Nuevo</td-->
                                    </tr>
                                    <?php
                                        $result = mssql_query("vm_msgctt_cot 1, '$Tip_Bus', NULL, $Cod_Per", $db);
                                        //echo "vm_msgctt_cot 1, '$Tip_Bus', NULL, $Cod_Per";
                                        $totfilas = 0;
                                        $primer_folio = 0;
                                        $tipo = split ("/", "/Informaci&oacute;n del Producto/Reclamos/Contacto Comercial/Solicitud de Catalogos/Informaci&oacute;n de sus Ordenes/Otro");
                                        while (($row = mssql_fetch_array($result))) {
                                            $totfilas++;
                                            $last_folio = $row['Row'];
                                            if ($primer_folio == 0) $primer_folio = $last_folio;
                                    ?>
                                            <tr>
                                                <td align="center" valign="top"><?php echo $row['Fec_Ctt']; ?></td>
                                                <td align="center" valign="top"><?php echo $row['Fol_CttWeb']; ?></td>
                                                <td align="left" valign="top"><?php echo $tipo[$row['Tip_Cna']]; ?></td>
                                                <td align="right" valign="top" style="padding-right: 5px"><?php echo formatearRut($row['Num_Doc']); ?></td>
                                                <td align="left" valign="top" title="<?php echo utf8_encode($row['Nom_Clt']); ?>"><?php echo utf8_encode($row['Nom_Clt']); ?></td>
                                                <td align="center" valign="top"><?php echo $row['Ctd'] ?></td>
                                                <!--td align="center" valign="top">
                                                <a href="javascript:popwindow('historico_msjcot.php?folctt=<?php echo $row['Fol_CttWeb']; ?>&per=<?php echo $row['Num_Doc'] ?>',400)"><img src="../images/001_38.gif" width="16px" height="16px" alt=""></a>
                                                </td-->
                                                <td align="center" valign="top">
                                                <?php if ($row['Tne_Pen'] == 'S') { ?>
                                                <table cellpadding="0" cellspacing="0"><tr><td><a href="javascript:responderctt(<?php echo $row['Fol_CttWeb']; ?>)"><img src="../images/mail.png" width="24px" height="16px" alt=""></a></td><td>&nbsp;(<?php echo $row['CtdSinLec']; ?>)</td></tr></table>
                                                <?php } else { ?>
                                                <a href="javascript:responderctt(<?php echo $row['Fol_CttWeb']; ?>)"><img src="../images/001_38.gif" width="16px" height="16px" alt=""></a>
                                                <?php } ?>
                                                </td>
                                                <!--td align="center" valign="top">
                                                <a href="javascript:Nuevo_Msg(1,<?php echo $row['Fol_CttWeb']; ?>)"><img src="../images/folder_feed.png" width="16px" height="16px" alt=""></a>
                                                </td-->
                                            </tr>
                                    <?php
                                        }
                                    ?>
                                    <tr>
                                    <td style="padding-top: 5px" colspan="7" align="right">
                                    <input type="hidden" id="last_folio" value="<?php echo $last_folio; ?>">
                                    </td>
                                    </tr>
                                    </table>
                                    </div>
                                    <div style="text-align: right; position:relative;width:670px; overflow:auto; left: 0px; top: 0px;">
                                            <table border="0" CELLSPACING="1" CELLPADDING="1" width="100%" ALIGN="center">
                                            <tr>
                                            <td width="50%" align="left">
                                            <input type="button" name="btnNuevaCtt" id="btnNuevaCtt" value=" Nuevo Caso " class="btn" onclick="javascript:Nuevo_Msg(1,0)">&nbsp;
                                            <input type="button" name="btnResetCtt" id="btnResetCtt" value=" Todos los Mensajes " class="btn2" onclick="javascript:ResetMsgCtt()">&nbsp;
                                            <input type="hidden" id="primer_folio" value="<?php echo $primer_folio; ?>">
                                            <input type="hidden" id="atras_folio" value="<?php echo $primer_folio; ?>">
                                            <input type="hidden" id="ordenfolio" value="">
                                            </td>
                                            <td width="50%" align="right">
                                            <a href="javascript:Previus_MsgCtt()"><img src="../images/arrow1_w.gif" alt=""></a> &nbsp; <a href="javascript:Next_MsgCtt()"><img src="../images/arrow1_e.gif" alt=""></a>
                                            </td>
                                            </tr>
                                            </table>
                                    </div>
                                    </form>
				</div>
				<div id="formulario_cotizaciones">
                                    <H3>Msg. Cotizaciones</H3>
                                    <form ID="searchMsgCot" name="searchMsgCot" action="">
                                    <div style="position:relative;width:730px;height:390px; overflow:auto; left: 0px; top: 0px;">
                                    <table BORDER="0" CELLSPACING="1" CELLPADDING="1" width="100%" ALIGN="center" id="tblMsgCot">
                                    <tr><td colspan="7" align="right">
                                    <?php if ($Tip_Bus != "T") { ?><a href="javascript:listarCot('T')">TODOS</a><?php } else { ?>TODOS<?php } ?> |
                                    <?php if ($Tip_Bus != "A") { ?><a href="javascript:listarCot('A')">ABIERTOS</a><?php } else { ?>ABIERTOS<?php } ?> |
                                    <?php if ($Tip_Bus != "P") { ?><a href="javascript:listarCot('P')">PENDIENTES</a><?php } else { ?>PENDIENTES<?php } ?>
                                    </td></tr>
                                    <tr>
                                        <td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Fecha</td>
                                        <td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Grupo</td>
                                        <td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">#Cot</td>
                                        <td width="15%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">RUT</td>
                                        <td width="40%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Nombre</td>
                                        <td width="5%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Msg</td>
                                        <!--td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Historial</td-->
                                        <td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Ver</td>
                                        <!--td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Nuevo</td-->
                                    </tr>
                                        <?php
                                            $result = mssql_query("vm_newmsg_cot 1, '$Tip_Bus', NULL, $Cod_Per", $db);
                                            $bOkRespuesta = false;
                                            $totfilas = 0;
                                            $primera_cot = 0;
                                            while (($row = mssql_fetch_array($result))) {
                                                $last_cot = $row['Row'];
                                                if ($primera_cot == 0) $primera_cot = $last_cot;
                                                $totfilas++;
                                        ?>
                                            <tr>
                                                <td align="center" valign="top"><?php echo $row['FecFmt_Cot']; ?></td>
                                                <td align="center" valign="top"><?php echo $row['Cod_Cot']; ?></td>
                                                <td align="center" valign="top"><?php echo $row['Num_Cot']; ?></td>
                                                <td align="right" valign="top" style="padding-right: 5px"><?php echo formatearRut($row['Num_Doc']); ?></td>
                                                <td align="left" valign="top"><?php echo utf8_encode($row['Nom_Clt']); ?></td>
                                                <td align="center" valign="top"><?php echo $row['Ctd']; ?></td>
                                                <!--td align="center" valign="top">
                                                <a href="javascript:popwindow('historico_msjcot.php?cot=<?php echo $row['Cod_Cot']; ?>', 400)"><img src="../images/001_38.gif" width="16px" height="16px" alt=""></a>
                                                </td-->
                                                <td align="center" valign="top">
                                                <?php if ($row['Tne_Pen'] == 'S') { ?>
                                                <table cellpadding="0" cellspacing="0"><tr><td><a href="javascript:respondercot(<?php echo $row['Cod_Cot']; ?>)"><img src="../images/mail.png" width="24px" height="16px" alt="" /></a></td><td>&nbsp;(<?php echo $row['CtdSinLec']; ?>)</td></tr></table>
                                                <?php } else { ?>
                                                <a href="javascript:respondercot(<?php echo $row['Cod_Cot']; ?>)"><img src="../images/001_38.gif" width="16px" height="16px" alt=""></a>
                                                <?php } ?>
                                                </td>
                                                <!--td align="center" valign="top">
                                                <a href="javascript:Nuevo_Msg(2,<?php echo $row['Cod_Cot']; ?>)"><img src="../images/folder_feed.png" width="16px" height="16px" alt="" /></a>
                                                </td-->
                                            </tr>
                                        <?php
                                            }
                                        ?>
                                    <tr>
                                    <td style="padding-top: 5px" colspan="7" align="right">
                                    <?php if ($last_cot < 18) { ?>
                                    <input type="hidden" id="last_cot" value="_NONE">
                                    <?php } else { ?>
                                    <input type="hidden" id="last_cot" value="<?php echo $last_cot; ?>">
                                    <?php } ?>
                                    </td>
                                    </tr>
                                    </table>
                                    </div>
                                    <div style="text-align: right; position:relative;width:670px; overflow:auto; left: 0px; top: 0px;">
                                        <table border="0" CELLSPACING="1" CELLPADDING="1" width="100%" ALIGN="center">
                                        <tr>
                                        <td width="50%" align="left">
                                        <input type="button" name="btnNuevaCot" id="btnNuevaCot" value=" Nuevo Caso " class="btn" onclick="javascript:Nuevo_Msg(2,0)">
                                        <input type="button" name="btnResetCot" id="btnResetCot" value=" Todos los Mensajes " class="btn2" onclick="javascript:ResetMsgCot()">&nbsp;
                                        <input type="hidden" id="atras_cot" value="<?php echo $primera_cot; ?>">
                                        <input type="hidden" id="orden" value="">
                                        </td>
                                        <td width="50%" align="right">
                                        <a href="javascript:Previus_MsgCot()"><img src="../images/arrow1_w.gif" alt="" /></a> &nbsp; <a href="javascript:Next_MsgCot()"><img src="../images/arrow1_e.gif" alt="" /></a>
                                        </td>
                                        </tr>
                                        </table>
                                    </div>
                                    </form>
                                    <form ID="F2" method="post" name="F2" ACTION="mismensajes.php?accion=1">
                                        <input type="hidden" id="primera_cot" name="primera_cot" value="<?php echo $primera_cot; ?>">
                                        <input type="hidden" id="tipo_bus_cot" name="tipo_bus_cot" value="<?php echo $Tip_Bus ?>">
                                        <input type="hidden" id="tipo_bus_folio" name="tipo_bus_folio" value="<?php echo $Tip_Bus ?>">
                                        <input type="hidden" id="tipo_bus" name="tipo_bus" value="<?php echo $Tip_Bus ?>">
                                        <input type="hidden" id="cod_per" name="cod_per" value="<?php echo $Cod_Per ?>">
                                    </form>
				</div>
			</div>
		</div>
		<div id="busqueda" style="position:relative;height:490px; overflow:auto; left: 0px; top: 0px;">
			<H3>B&uacute;squeda</H3>
			<table WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
			<tr>
                            <td width="90" class="dato">&nbsp;</td>
                            <td align="left" colspan="2">
                                <table border="0" width="100%"><tr>
                                <td width="198px"><b>Apellido Paterno</b></td>
                                <td width="198px"><b>Apellido Materno</b></td>
                                <td><b>Nombres</b></td></tr>
                                </table>
                            </td>
			</tr>
			<tr>
                            <td width="90" class="dato">Persona Natural</td>
                            <td align="left" colspan="2">
                            <INPUT name="dfPatPer" id="dfPatPer" size="35" maxLength="80" class="textfield_m" value="" style="TEXT-trANSFORM: uppercase" />
                            <INPUT name="dfManPer" id="dfManPer" size="35" maxLength="80" class="textfield_m" value="" style="TEXT-trANSFORM: uppercase" />
                            <INPUT name="dfNomPer" id="dfNomPer" size="35" maxLength="80" class="textfield_m" value="" style="TEXT-trANSFORM: uppercase" />
                            </td>
			</tr>
			<tr>
                            <td width="90" class="dato">Persona Juridica</td>
                            <td colspan="2" align="left"><INPUT name="dfNomClt" id="dfNomClt" size="80" maxLength="120" value="" class="textfield_m" style="TEXT-trANSFORM: uppercase" /></td>
			</tr>
			<form ID="frmBusqueda" method="POST" name="frmBusqueda" action="">
			<tr>
                            <td class="dato">Rut</td>
                            <td align="left">
                                <INPUT name="dfRutPer" id="dfRutPer" size="15" maxLength="10" class="textfield_m" value="" style="TEXT-trANSFORM: uppercase" onKeyPress="javascript:return soloRUT(event)" onblur="formatearRut('dfRutPer','dfRutSinFmt')" />
                                <input type="hidden" name="dfRutSinFmt" id="dfRutSinFmt">
                            </td>
                            <td align="right">
                                <input type="button" name="Cerrar" value="Cerrar" class="btn" onclick="javascript:Show_Cotizaciones()">&nbsp;
                                <input type="button" name="Buscar"  value="Buscar" class="btn" onclick="javascript:Busqueda()">
                            </td>
			</tr>
			<tr><td colspan="3" STYLE="TEXT-ALIGN: center">
			<fieldset class="label_left_right_top_bottom">
			<legend>Resultado Busqueda</legend>
			<div style="position:relative;height:300px; overflow:auto; left: 0px; top: 0px;">
			<table WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="1" ALIGN="gcenter" id="tblResultado">
			<tr><td>&nbsp;</td></tr>
			</table>
			</div>
			</fieldset>
			</td>
			</tr>
			</form>
			</table>
		</div>
		<?php } elseif ($accion == 1) {
                        if ($idmsg > 0) {
                            $result = mssql_query("vm_cttweb_s $idmsg", $db);
                            if (($row = mssql_fetch_array($result))) {
                                $num_doc = $row['rut_ctt'];
                                $nom_clt = $row['nom_ctt'];
                                $fon_ctt = $row['fon_ctt'];
                                $email   = $row['email'];
                                $Cod_Clt = $row['Cod_Clt'];
                                $Nom_Cmn = "";
                                $Nom_Cdd = "";
                                if ($cod_clt > 0) {
                                    $cod_suc = $row['Cod_Suc'];
                                    $cod_cmn = $row['Cod_Cmn'];
                                    $cod_cdd = $row['Cod_Cdd'];
                                    $dir_suc = $row['Dir_Suc'];
                                    $result = mssql_query("vm_cmn_s $cod_cmn", $db);
                                    if (($row = mssql_fetch_array($result))) $Nom_Cmn = $row['Nom_Cmn'];
                                    $result = mssql_query("vm_cdd_s $cod_cdd", $db);
                                    if (($row = mssql_fetch_array($result))) $Nom_Cdd = $row['Nom_Cdd'];
                                }
                            }
                        }
                        else if ($Cod_Per > 0) {
                            $result = mssql_query("vm_per_s $Cod_Per", $db);
                            if (($row = mssql_fetch_array($result))) {
                                $num_doc = $row['Num_Doc'];
                                $nom_clt = $row['Nom_Clt'];
                                $Cod_Clt = $row['Cod_Clt'];
                                if ($Cod_Clt > 0) {
                                    $result = mssql_query("vm_usrweb_ctt_s $Cod_Per, $Cod_Per", $db);
                                    if (($row = mssql_fetch_array($result))) {
                                        $cod_suc = $row['Cod_Suc'];
                                        $dir_suc = $row['Dir_Suc'];
                                        $fon_ctt = $row['Fon_Ctt'];
                                        $email   = $row['Mail_Ctt'];
                                        $cod_cmn = $row['Cod_Cmn'];
                                        $cod_cdd = $row['Cod_Cdd'];

                                        $result = mssql_query("vm_cmn_s $cod_cmn", $db);
                                        if (($row = mssql_fetch_array($result))) $Nom_Cmn = $row['Nom_Cmn'];

                                        $result = mssql_query("vm_cdd_s $cod_cdd", $db);
                                        if (($row = mssql_fetch_array($result))) $Nom_Cdd = $row['Nom_Cdd'];
                                    }
                                }
                            }
                        }
		?>
		<H1>Nuevo Mensaje</H1>
		<table BORDER="0" CELLSPACING="1" CELLPADDING="5" width="100%" ALIGN="center">
		<tr>
			<td colspan="2">
                        <fieldset class="label_left_right_top_bottom">
                                <legend>Datos Cliente</legend>
                                <table WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
                                    <form ID="searchPer" action="">
                                    <tr>
                                        <td width="5%" align="right">RUT</td>
                                        <td width="40%" align="left" STYLE="PADDING-LEFT: 3px" colspan="3">
                                        <input name="dfRut" id="dfRut" size="12" maxLength="12" onblur="filterPer(this)" class="textfield" onKeyPress="javascript:return soloRUT(event)" <?php echo (($idmsg == 0 and $Cod_Per == 0) ? "value=\"\"" : "value=\"".formatearRut($num_doc)."\" ReadOnly"); ?> />&nbsp;
                                        <?php if (false) { ?>
                                        <input type="button" name="Buscar" value="Buscar" class="button2" onclick="BuscarCliente('new')" />&nbsp;
                                        <?php    if ($Rut_Per == "") { ?>
                                        <input type="button" id="Nuevo" name="Nuevo" value="Nuevo" class="button2" onclick="NuevoCliente()" />
                                        <?php    } else { ?>
                                        <input type="button" id="Nuevo" name="Nuevo" value="Editar" class="button2" onclick="EditarCliente('<?php echo $Rut_Per; ?>')" />
                                        <?php    } ?>
                                        <?php } ?>
                                        </td>
                                        <!--td width="10%">&nbsp;</td-->
                                        <td width="10%" align="right">Direcci&oacute;n</td>
                                        <td width="35%" align="left" STYLE="PADDING-LEFT: 3px"><input name="dfDir" id="dfDir" size="50" maxLength="80" class="textfield" <?php echo (($idmsg == 0 and $Cod_Per == 0) ? "value=\"\"" : "value=\"".$dir_suc."\""); ?> ReadOnly /></td>
                                    </tr>
                                    </form>
                                    <tr>
                                        <td width="5%" align="right">Cliente</td>
                                        <td width="40%" align="left" STYLE="PADDING-LEFT: 3px" colspan="3"><input name="dfNomClt" id="dfNomClt" size="40" maxLength="120" class="textfield" <?php echo (($idmsg == 0 and $Cod_Per == 0) ? "value=\"\"" : "value=\"".$nom_clt."\""); ?> ReadOnly /></td>
                                        <!--td width="10%">&nbsp;</td-->
                                        <td width="10%" align="right">Comuna</td>
                                        <td width="35%" align="left" STYLE="PADDING-LEFT: 3px">
                                            <input name="dfCmn" id="dfCmn" size="20" maxLength="20" class="textfield" value="<?php echo $Nom_Cmn; ?>" ReadOnly />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%" align="right">Sucursal</td>
                                        <td width="40%" align="left" STYLE="PADDING-LEFT: 3px" colspan="3">
                                            <?php //Seleccionar las comuna
                                            if ($idmsg == 0 and $Cod_Per == 0) {
                                            ?>
                                                <form id="searchSuc" action="">
                                                <select id="suc" name="suc" class="textfield" onChange="filterSuc(this)">
                                                     <option selected value="_NONE">Seleccione una Sucursal</option>
                                                </select>
                                                </form>
                                            <?php
                                            }
                                            else {
                                            ?>
                                                <!--input name="dfSuc" size="30" maxLength="30" class="textfield" <?php echo "value=\"".$nom_suc."\" ReadOnly"; ?> /-->
                                                <form id="searchSuc" action="">
                                                <select id="suc" name="suc" class="textfield" onChange="filterSuc(this)"<?php if($idmsg > 0) echo " DISABLED"; ?>>
                                                <?php
                                                $sp = mssql_query("vm_suc_s $Cod_Clt", $db);
                                                $xissuc = false;
                                                while ($row = mssql_fetch_array($sp)) {
                                                    $selected = "";
                                                    if ($row['Cod_Suc'] == $cod_suc) {
                                                        $selected = " selected";
                                                    }
                                                    echo "<option value=\"".$row['Cod_Suc']."\"".$selected.">".$row['Nom_Suc']."</option>\n";
                                                    $xissuc = true;
                                                }
                                                ?>
                                                <?php if (!$xissuc) { ?>
                                                <option selected value="_NONE">Seleccione una Sucursal</option>
                                                <?php } ?>
                                                </select>
                                                </form>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                        <!--td width="10%">&nbsp;</td-->
                                        <td width="10%" align="right">Ciudad</td>
                                        <td width="35%" align="left" STYLE="PADDING-LEFT: 3px">
                                            <input name="dfCdd" id="dfCdd" size="20" maxLength="20" class="textfield" value="<?php echo $Nom_Cdd; ?>" ReadOnly />
                                        </td>
                                    </tr>
                                    <tr>
                                            <td width="5%" align="right">Contacto</td>
                                            <td width="40%" align="left" STYLE="PADDING-LEFT: 3px" colspan="3">
                                                <?php //Seleccionar contacto
                                                if ($idmsg == 0 and $Cod_Per == 0) {
                                                ?>
                                                    <form id="searchCtt" action="">
                                                    <select id="ctt" name="ctt" class="textfield" onChange="filterCtt(this)">
                                                         <option selected value="_NONE">Seleccione un Contacto</option>
                                                    </select>
                                                    </form>
                                                <?php
                                                }
                                                else {
                                                ?>
                                                    <!--input name="dfCtt" size="40" maxLength="40" class="textfield" <?php echo "value=\"".$nom_ctt."\" ReadOnly"; ?> /-->
                                                    <form id="searchCtt" action="">
                                                    <select id="ctt" name="ctt" class="textfield" onChange="filterCtt(this)"<?php if($idmsg > 0) echo " DISABLED"; ?>>
                                                <?php
                                                    $sp = mssql_query("vm_ctt_s $Cod_Clt, $cod_suc", $db);
                                                    $xisctt = false;
                                                    $nom_ctt = $nom_clt;
                                                    while($row = mssql_fetch_array($sp))
                                                    {
                                                            $nom_ctt = trim($row['Pat_Per'])." ".trim($row['Mat_Per']).", ".trim($row['Nom_Per']);
                                                            $flagdefault = ($row['Cod_Per'] == $Cod_Per ? " selected" : "");
                                                            $xisctt = true;
                                                ?>
                                                        <option value="<?php echo $row['Cod_Per'] ?>"<?php echo $flagdefault; ?>><?php echo $nom_ctt; ?></option>
                                                <?php
                                                    }
                                                ?>
                                                <?php if (!$xisctt) { ?>
                                                    <option selected value="_NONE">Seleccione un Contacto</option>
                                                <?php } ?>
                                                    </select>
                                                    </form>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <!--td width="10%">&nbsp;</td-->
                                            <td width="10%" align="right">Tel&eacute;fono</td>
                                            <td width="35%" align="left" STYLE="PADDING-LEFT: 3px"><input name="dfFono" id="dfFono" size="30" maxLength="10" class="textfield" <?php echo (($idmsg == 0 and $Cod_Per == 0) ? "value=\"\"" : "value=\"".$fon_ctt."\""); ?> ReadOnly /></td>
                                    </tr>
                                    <tr>
                                            <td colspan="4" align="right">&nbsp;</td>
                                            <td width="10%" align="right">e-mail</td>
                                            <td width="35%" align="left" STYLE="PADDING-LEFT: 3px"><input name="dfemail" id="dfemail" size="50" maxLength="50" class="textfield"  <?php echo (($idmsg == 0 and $Cod_Per == 0) ? "value=\"\"" : "value=\"".$email."\""); ?> ReadOnly /></td>
                                    </tr>
                                </table>
                        </fieldset>
			</td>
		</tr>
		<tr>
                    <td colspan="2">
                        <table border="0" CELLSPACING="1" CELLPADDING="1" width="100%">
                        <tr>
                            <td width="170px" VALIGN="TOP" class="dato"><b>Grupo Mensaje</b></td>
                            <td VALIGN="TOP" class="dato">
                            <?php if ($idmsg == 0) { ?>
                                    Nuevo
                            <?php } else {
                                    $result = mssql_query("vm_s_cttweb $idmsg", $db);
                                    if (($row = mssql_fetch_array($result))) {
                                        echo $idmsg;
                                        $cod_tipcna = $row['Tip_Cna'];
                                    }
                                  }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="170px" VALIGN="TOP" class="dato"><b>Tipo Consulta</b></td>
                            <td VALIGN="TOP" class="dato">
                            <?php if ($idmsg == 0) { ?>
                            <select class="select-contacto" name="tipcna" onChange="$j('#tip_cna').val(this.value)">
                            <option selected value="_NONE">Seleccione tipo de Consulta</option>
                            <option value="1">Informaci&oacute;n del Producto</option>
                            <option value="2">Reclamos</option>
                            <option value="3">Contacto Comercial</option>
                            <option value="4">Solicitud de Catalogos</option>
                            <option value="5">Informaci&oacute;n de sus Ordenes</option>
                            <option value="6">Otro</option>
                            </select>
                            <?php } else {
                                    $aTipCna   = array(1 => "Informaci&oacute;n del Producto",
                                                                       2 =>  "Reclamos",
                                                                       3 =>  "Contacto Comercial",
                                                                       4 =>  "Solicitud de Catalogos",
                                                                       5 =>  "Informaci&oacute;n de sus Ordenes",
                                                                       6 =>  "Otro");
                                    echo $aTipCna[$cod_tipcna];
                            ?>
                                    <input type="hidden" name="tipcna" value="<?php echo $cod_tipcna ?>">
                            <?php
                                 }
                            ?>
                            </td>
                        </tr>
                        </table>
                    </td>
		</tr>
		<tr>
                    <td colspan="2">
                        <form ID="F2" method="post" name="F2" ACTION="mensajes.php?accion=21&folctt=<?php echo $idmsg; ?>" onsubmit="return checkDataFormCtt(this)" enctype="multipart/form-data">
                        <table border="0" CELLSPACING="1" CELLPADDING="1" width="100%">
                        <tr>
                            <td width="30%" VALIGN="TOP" class="dato"><b>Nuevo Mensaje</b></td>
                            <td width="70%" VALIGN="TOP" class="dato">
                            <textarea class="textfieldv2" rows="5" cols="100" name="consulta"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" VALIGN="TOP" class="dato" colspan="3" style="text-align: right">
                            <input type="button" name="Volver" value=" Volver " class="btn" onclick="Salir(11)">&nbsp;&nbsp;
                            <input type="submit" name="Enviar" value=" Enviar " class="btn">
                            <input type="hidden" name="tipo_bus" value="<?php echo $Tip_Bus; ?>">
                            <input type="hidden" id="cod_per" name="cod_per" value="<?php echo $Cod_Per ?>">
                            <input type="hidden" id="cod_clt" name="cod_clt" value="<?php echo $Cod_Clt ?>">
                            <input type="hidden" id="cod_suc" name="cod_suc" value="<?php echo $cod_suc ?>">
                            <input type="hidden" id="tip_cna" name="tip_cna" value="">
                            </td>
                        </tr>
                        </table>
                        </form>
                    </td>
		</tr>
		</table>
		<?php } elseif ($accion == 2) { ?>
		<H1>Nuevo Mensaje</H1>
		<form ID="F2" method="post" name="F2" ACTION="mensajes.php?accion=22&cot=<?php echo $idmsg; ?>" onsubmit="return checkDataFormCot(this)" enctype="multipart/form-data" >
		<table BORDER="0" CELLSPACING="1" CELLPADDING="5" width="650" height="400" ALIGN="center">
		<tr>
			<td width="30%" VALIGN="TOP" class="dato" style="PADDING-TOP: 20px"><b>Tipo Mensaje</b></td>
			<td width="30%" VALIGN="TOP" class="dato" style="PADDING-TOP: 20px" colspan="2">Msg. Cotizaciones</td>
		</tr>
		<tr>
			<td width="30%" VALIGN="TOP" class="dato"><b>Cotizaci&oacute;n</b></td>
			<td width="30%" VALIGN="TOP" class="dato">
			<?php if ($idmsg == 0) { ?>
			<select class="select-contacto" name="numcot">
			<option selected value="_NONE">Seleccione Cotizaci&oacute;n</option>
			<?php
				$result = mssql_query("vm_cot_sinmsj", $db);
				while (($row = mssql_fetch_array($result))) echo "<option value=\"".$row['cod_cot']."\">".$row['num_cot']."</option>\n";
			?>
			</select>
			<?php } else { 
                                    $result = mssql_query("vm_s_cothdr $idmsg",$db);
                                    if (($row = mssql_fetch_array($result))) echo $row['Num_Cot'];
				  } 
			?>
			</td>
			<td width="40%" VALIGN="TOP" class="dato">&nbsp;</td>
		</tr>
		<tr>
                    <td width="30%" VALIGN="TOP" class="dato"><b>Nuevo Mensaje</b></td>
                    <td width="70%" VALIGN="TOP" class="dato" colspan="2">
                        <textarea class="textfieldv2" rows="5" cols="100" name="consulta"></textarea>
                    </td>
		</tr>
		<tr>
                    <td width="100%" VALIGN="TOP" class="dato" colspan="3" style="text-align: right">
                        <input type="button" name="Volver" value=" Volver " class="btn" onclick="Salir(12)">&nbsp;&nbsp;
                        <input type="submit" name="Enviar" value=" Enviar " class="btn">
                        <input type="hidden" name="tipo_bus" value="<?php echo $Tip_Bus; ?>">
                        <input type="hidden" id="cod_clt" name="cod_clt" value="<?php echo $Cod_Clt ?>">
                        <input type="hidden" id="cod_per" name="cod_per" value="<?php echo $Cod_Per ?>">
                    </td>
		</tr>
		</table>
		</form>

		<?php } ?>
	</td></tr>
	</table>
</td>
</tr>
</table>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;
	var f2;
	var f3;
	f1 = document.F1;
	f2 = document.F2;
	f3 = document.F3;
	
    <?php if ($accion == 0) { ?>	
	Show_Cotizaciones();
	<?php } elseif ($accion == 12) { ?>
	$j("#formulario_cotizaciones").show();
	$j("#formulario_contacto").hide();
	$j("#busqueda").hide();
	<?php } elseif ($accion == 11) { ?>
	$j("#formulario_cotizaciones").hide();
	$j("#formulario_contacto").show();
	$j("#busqueda").hide();
	<?php } ?>
	
</script>


</body>
</html>
