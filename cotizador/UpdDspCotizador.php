<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

$cod_cot = 0;
if (isset($_GET['cot'])) $cod_cot = intval($_GET['cot']);

if ($cod_cot > 0) {
    if (isset($_GET['trn'])) {
        $cod_trn    = intval(ok($_GET['trn']));
        $cod_sucdsp = intval(ok($_POST['dfSuc']));
        $cod_crr    = intval(ok($_POST['dfCrr']));
        $cod_crrsvc = intval(ok($_POST['dfCrrSvc']));
        $cod_clt    = intval(ok($_POST['dfCodClt']));
        $val_dsp    = ok($_POST['dfValDsp']);
        $is_dsp     = ($cod_sucdsp > 0) ? 1: 0;

        if ($cod_trn == 200) {
            //$query = "vm_u_sucdsp_cot $cod_clt, $cod_cot, $cod_sucdsp, $is_dsp, $cod_crr, $cod_crrsvc, $val_dsp";
            //echo $query;
            $result = mssql_query("vm_u_sucdsp_cot $cod_clt, $cod_cot, $cod_sucdsp, $is_dsp, $cod_crr, $cod_crrsvc, $val_dsp",$db);
        }
    }
	
    $result = mssql_query("vm_s_cothdr $cod_cot",$db);
    if (($row = mssql_fetch_array($result))) {
        $num_cot    = $row['Num_Cot'];
        $fec_cot    = $row['Fec_Cot'];
        $cod_clt    = $row['Cod_Clt'];
        $cod_pre    = $row['Cod_Pre'];
        $val_dsp    = $row['Val_Dsp'];
        $cod_pre    = $row['Cod_Pre'];
        $cod_sucdsp = 0;
        $cod_crr    = 0;
        $cod_svccrr = 0;
        $tip_cmndsp = 0;
        $peso       = $row['Val_PsoMax'];
        $is_dsp     = $row['is_dsp'];
        $condicion  = "";
        if ($is_dsp == 1) {
            $cod_sucdsp = $row['Cod_SucDsp'];
            $cod_crr    = $row['Cod_Crr'];
            $cod_svccrr = $row['Cod_SvcCrr'];
            if ($cod_pre == 1) {
                $val_dsp+=$val_dsp*$IVA;
                //$val_dsp = intval($val_dsp);
            }

            $result = mssql_query("vm_SvcCrrCmb $cod_crr, $cod_svccrr",$db);
            if (($row = mssql_fetch_array($result)))
                $condicion = utf8_encode($row['Con_SvcCrr']);

            $result = mssql_query("vm_suc_s $cod_clt, $cod_sucdsp",$db);
            if (($row = mssql_fetch_array($result)))
                $tip_cmndsp = $row['Tip_Cmn'];

        }
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script language="JavaScript" src="../Include/fngenerales.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready
    (
            //$j(":input:first").focus();

            function()
            {
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

            $j("form#detalle").submit(function(){
                            $j.post("../updcotizaciones.php",{
                                    accion: "update-ajax",
                                    sec: $j("#dfSec").val(),
                                    ctd: $j("#dfCtd").val()
                            }, function(xml) {
                                    ActualizaHidden(xml);
                            });return false;
                });
            //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
        }
		
    );

    function filterPro(obj)
    {
        //$j("#pro").val(obj.value);
        $j("#dfCrr").val(obj.value);
        $j("#dfCostoDsp").val("$ 0");
        $j("form#searchPro").submit();
    }

    function filterSvc(obj)
    {
        //$j("#esp").val(obj.value);
        $j("#dfCrrSvc").val(obj.value);
        $j("form#searchEsp").submit();
    }

    function llenarEsp(obj)
    {
        $j("#.codesp").val(obj.value);
    }

    function listLinEsp(xml)
    {
        //alert("listLinEsp");
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

    function refreshCondiciones (obj)
    {
        if (condiciones.value == "") return;
        if (obj.value == 0)
                condiciones.value = cond_original;
        else
                condiciones.value = cond_original + ". Personal de Vestmed se comunicara con Usted para indicarle la direccion del Carrier para su retiro.";
    }

    function listLinSvc(xml)
    {
        var options;
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                    //alert($j("code",filter).text()+"="+$j("value",filter).text());
                    if ($j("code",filter).text() == "condiciones") {
                            condiciones.value = $j("value",filter).text();
                            cond_original = condiciones.value;
                            if ($j("#rbTipoDsp:checked").val() == 1 && $j("#rbTipoDsp:checked").is(':visible'))
                                    condiciones.value = condiciones.value + ". Personal de Vestmed se comunicara con Usted para indicarle la direccion del Carrier para su retiro.";
                    }
                    if ($j("code",filter).text() == "costo") {
                            //alert("Costo=["+$j("value",filter).text()+"]");
                            //valordsp = parseFloat($j("value",filter).text().replace(".", ""));
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

    function ActualizaHidden(xml)
    {
        $j("filter",xml).each(
            function(id) {
	            filter=$j("filter",xml).get(id);

                    if ($j("code",filter).text() == "Peso") {
                        $j("#dfPeso").val($j("value",filter).text());
                        //alert($j("input[name='rbTipoSuc']:checked").val());
                        if ($j("input[name='rbTipoSuc']:checked").val() != "0") $j("form#searchEsp").submit();
                    }
	        }
        );
    }

    function llenarSuc(obj,campo) {
            $j("#dfSuc").val(obj.value);
            if (campo != "dfSucFct") {
                    if (obj.value == "0")
                            $j("#inf_despacho").hide("slow");
                    else
                            $j("#inf_despacho").show("slow");
                    cmbpro = document.getElementById("pro");
                    cmbesp = document.getElementById("esp");
                    if (obj.value == "0") {
                       cmbpro.disabled = true;
                       cmbesp.disabled = true;
                    }
                    else {
                       cmbpro.disabled = false;
                       cmbesp.disabled = false;
                    }
                    if ($j("#dfTipo"+obj.value).val() == 1) {
                            $j("#tipo_despacho").show("slow");
                    }
                    else {
                            $j("#tipo_despacho").hide("slow");
                    }

                    $j("#dfCostoDsp").val("$ 0");
                    if (obj.value > 0 && $j("#esp").val() != "_NONE") $j("form#searchEsp").submit();
                    else {
                            condiciones.value = "";
                            cond_original = condiciones.value;
                    }
            }
    }

    function ActualizaPadre()
    {
        parent.opener.ActualizarDsp();
        //window.close();
    }
	
    </script>
</head>

<body bgcolor="#c1f4e5" style="margin-top:5px;">
<div style="overflow:auto;">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center" style="padding-left:50px; padding-right:10px;">
<tr><td style="padding-bottom: 10px">
    <table WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
    <tr>
        <td width="100%" style="TEXT-ALIGN: left" valign="top">
            <table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
            <tr>
                    <td colspan="2" align="left" style="font-weight:bold; font-size:12px;">Direcci&oacute;n</td>
            </tr>
            <tr>
                    <td width="10px" style="TEXT-ALIGN:center">
                            <INPUT id="rbTipoSuc" name="rbTipoSuc" type="radio" style="border:none" value="0" onclick="llenarSuc(this,'dfSuc')" <?php if ($is_dsp == 0) echo "checked" ?> />
                            <INPUT type="hidden" id="dfTipo0" name="" value="<?php echo $row['Tip_Cmn']; ?>" />
                    </td>
                    <td style="TEXT-ALIGN:left; PADDING-LEFT:5px">Pick Up Tienda (Av. Vitacura 5900 Local 5)</td>
            </tr>
            <?php
                    $j = 1;
                    $iTotPrd1 = 1;
                    $result = mssql_query("vm_suc_s $cod_clt", $db);
                    while ($row = mssql_fetch_array($result)) {
            ?>
                            <tr>
                               <td style="TEXT-ALIGN: center">
                                    <INPUT id="rbTipoSuc" name="rbTipoSuc" type="radio" style="border:none" value="<?php echo $row['Cod_Suc']; ?>" onclick="llenarSuc(this,'dfSuc')" <?php if ($is_dsp == 1 and $cod_sucdsp == $row['Cod_Suc']) echo "checked"; ?> /></td>
                                    <INPUT type="hidden" id="dfTipo<?php echo $row['Cod_Suc']; ?>" name="dfTipo<?php echo $row['Cod_Suc']; ?>" value="<?php echo $row['Tip_Cmn']; ?>" />
                               <td style="TEXT-ALIGN: left; PADDING-LEFT:5px"><?php echo utf8_encode($row['Dir_Suc']); ?> (<?php echo utf8_encode($row['Nom_Cdd']); ?>)</td>
                            </tr>
            <?php
                            $j = 1 - $j;
                            $iTotPrd1++;
                    }
                    mssql_free_result($result);
            ?>
            </table>
        </td>
    </tr>
    </table>
</td></tr>
<tr><td>
    <div id="inf_despacho">
    <table WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
    <tr>
            <td class="label22" width="40%" style="TEXT-ALIGN: left">Carrier</td>
            <td class="label22" width="40%" style="TEXT-ALIGN: left">Servicio</td>
            <td class="label22" width="20%" style="TEXT-ALIGN: left">Costo(IVA Inc)</td>
    </tr>
    <tr>
            <form id="searchPro" name="searchPro">
            <td align="left">
            <select id="pro" name="pro" class="textfieldv2" onChange="filterPro(this)">
                    <option selected value="_NONE">Seleccione un Carrier</option>
                    <?php //Seleccionar los Carrier
                    $sp = mssql_query("vm_CrrCmb",$db);
                    while($row = mssql_fetch_array($sp))
                    {
                            ?>
                            <option value="<?php echo $row['Cod_Crr'] ?>"<?php if ($cod_crr == $row['Cod_Crr']) echo " selected" ?>><?php echo $row['Des_Crr'] ?></option>
                            <?php
                    }
                    ?>
            </select>
            </td>
            </form>
            <form id="searchEsp" name="searchEsp">
            <td>
            <select id="esp" name="esp" class="textfieldv2" onChange="filterSvc(this)">
                    <option selected value="_NONE">Seleccione un Servicio</option>
                    <?php //Seleccionar los Servicios
                    $sp = mssql_query("vm_SvcCrrCmb $cod_crr",$db);
                    while($row = mssql_fetch_array($sp))
                    {
                            ?>
                            <option value="<?php echo $row['Cod_SvcCrr'] ?>"<?php if ($cod_svccrr == $row['Cod_SvcCrr']) echo " selected" ?>><?php echo $row['Des_SvcCrr'] ?></option>
                            <?php
                    }
                    ?>
            </select>
            </td>
            </form>
            <td>
                    <INPUT name="dfCostoDsp" id="dfCostoDsp" size="8" maxLength="8" class="textfield_m" value="$ <?php echo number_format($val_dsp,0,',','.') ?>" ReadOnly />
                    <span id="labelPeso" class="dato"></span>
            </td>
    </tr>
    <tr><td colspan="3" style="padding-top: 5px">
    <div id="tipo_despacho">
    <table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
            <tr>
                    <td width="10px" style="TEXT-ALIGN:center" valign="top">
                            <img src="images/warning.png">
                    </td>
                    <td style="TEXT-ALIGN:left; PADDING-LEFT:5px; PADDING-BOTTOM: 5px" colspan="2" valign="top">
                    Nuestros registros indican que en el pasado han ocurrido problemas con los despachos a domicilio realizados a la comuna seleccionada.
                    Recomendamos que el despacho sea realizado a la Oficina del Carrier m&aacute;s cercana a su domicilio en donde ser&aacute; recibido y
                    almacenado para su retiro. En caso de concretar una compra, personal de Vestmed se comunicar&aacute; con usted
                    para entregarle mayor informaci&oacute;n.
                    </td>
            </tr>
            <tr>
                    <td width="10px" style="TEXT-ALIGN:center">
                            <INPUT id="rbTipoDsp" name="rbTipoDsp" type="radio" style="border:none" value="0" onclick="refreshCondiciones(this)" />
                    </td>
                    <td style="TEXT-ALIGN:left; PADDING-LEFT:5px" colspan="2">Despacho a Domicilio</td>
            </tr>
            <tr>
                    <td width="10px" style="TEXT-ALIGN:center">
                            <INPUT id="rbTipoDsp" name="rbTipoDsp" type="radio" style="border:none" value="1" onclick="refreshCondiciones(this)" />
                    </td>
                    <td style="TEXT-ALIGN:left; PADDING-LEFT:5px" colspan="2">Despacho a Sucursal del Carrier (Recomendable para localidades distantes/Rurales)</td>
            </tr>
    </table>
    </div>
    </td></tr>
    <tr>
            <td colspan="3" style="TEXT-ALIGN: left; PADDING-TOP: 10px">
               <span class="titulo_Condiciones">Condiciones del Servicio:</span><BR>
               <!--span name="texto_condicion" id="texto_condicion">Overnight: Entrega d&iacute;a Habil Siguiente antes de las 13:00 hrs</span-->
               <textarea class="textfieldv2" style="overflow: auto" name="texto_condicion" id="texto_condicion" cols="140" rows="4" ReadOnly><?php echo $condicion ?></textarea>
            </td>
    </tr>
    </table>
    </div>
</td></tr>
<tr>
        <td style="PADDING-TOP: 10px; PADDING-BOTTOM: 3px; TEXT-ALIGN: left" class="label_top">
<form ID="F1" method="post" name="F1" action="<?php echo $_SERVER['PHP_SELF'] ?>?cot=<?php echo $cod_cot; ?>&trn=200">
            <input type="hidden" id="dfSuc"    name="dfSuc" value="<?php echo $cod_sucdsp ?>" />
            <input type="hidden" id="dfCrr"    name="dfCrr" value="<?php echo $cod_crr ?>">
            <input type="hidden" id="dfCrrSvc" name="dfCrrSvc" value="<?php echo $cod_svccrr ?>">
            <input type="hidden" id="dfCodClt" name="dfCodClt" value="<?php echo $cod_clt ?>" />
            <input type="hidden" id="dfPeso"   name="dfPeso" value="<?php echo $peso; ?>" />
            <input type="hidden" id="dfValDsp" name="dfValDsp" value="<?php echo $val_dsp; ?>" />
            <input type="submit" class="btn2"  value="Actualizar Despacho" />
            <input type="button" class="btn"   value="Salir" onclick="javascript: window.close()" />
</form>
        </td>
</tr>

</table>
</div>
    <script type="text/javascript">
	var f1;

	f1 = document.F1;
	condiciones = document.getElementById("texto_condicion");

        <?php if ($is_dsp == 0) { ?>
        $j("#inf_despacho").hide();
		$j("#tipo_despacho").hide();
        <?php } ?>
        <?php if ($tip_cmndsp == 0) { ?>
	$j("#tipo_despacho").hide();
        <?php } ?>

        <?php if ($cod_trn == 200) { ?>
        ActualizaPadre();
        <?php } ?>

</script>
</body>
</html>
