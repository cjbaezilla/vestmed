<?php

ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$Cod_Nvt = $_POST['dfCodNtaVta'];
$Cod_Clt = $_POST['dfCodCltVta'];
$Cod_PerFct = 0;

$query = "vm_cli_s ".$Cod_Clt;
$result = mssql_query($query, $db) or die ('error en sql (1001)<br>'.$query);
if (($row = mssql_fetch_array($result))) $Rut_Clt = $row['Num_Doc'];

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if ($row = mssql_fetch_array($result)) $IVA = $row['Tbl_fol'] / 10000.0;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Usuarios Nota de Venta</title>
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        <link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
        <link href="css/itunes.css" type="text/css" rel="stylesheet" />
        
        <!-- Lytebox Includes //-->
        <script type="text/javascript" src="../lytebox/lytebox.js"></script>
        <link rel="stylesheet" type="text/css" href="../lytebox/lytebox.css" media="screen" />
        <!-- Lytebox Includes //-->
        
	<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
	<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>
        
        <script type="text/javascript">
            $(document).ready
            (
                    //$j(":input:first").focus();

                    function()
                    {
                    $("form#searchPro").submit(function(){
                        $.post("../ajax-search-svc.php",{
                            search_type: "svc",
                            param_filter: $("#pro").val()
                        }, function(xml) {
                                listLinEsp(xml);
                        });return false;
                    });

                    $("form#searchEsp").submit(function(){
                        $.post("ajax-search-svc.php",{
                                search_type: "con",
                                param_filter: $("#pro").val(),
                                param_codsvc: $("#esp").val(),
                                param_codclt: $("#dfCodClt").val(),
                                param_codsuc: $("#dfSuc").val(),
                                param_peso: $("#dfPeso").val()
                        }, function(xml) {
                                listLinSvc(xml);
                        });return false;
                    });

                    $("form#detalle").submit(function(){
                        $.post("updcotizaciones.php",{
                                accion: "update-ajax",
                                sec: $("#dfSec").val(),
                                ctd: $("#dfCtd").val()
                        }, function(xml) {
                                ActualizaHidden(xml);
                        });return false;
                    });
                    //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
                }

            );
            //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
            function filterPro(obj)
            {
                $("#pro").val(obj.value);
                $("#dfCostoDsp").val("$ 0");
                $("form#searchPro").submit();
            }
            
            function filterSvc(obj)
            {
                $("#.esp").val(obj.value);
                $("form#searchEsp").submit();
            }
    
            function listLinEsp(xml)
            {
                options="<select id=\"esp\" name=\"esp\" class=\"textfieldv2\" onChange=\"filterSvc(this)\">\n";
                options+="<option selected value=\"_NONE\">Seleccione un Servicio</option>\n";
                $("filter",xml).each(
                    function(id) {
                        filter=$("filter",xml).get(id);
                        options+= "<option value=\""+$("code",filter).text()+"\">"+$("value",filter).text()+"</option>\n";
                    }
                );
                options+="</select>";
                $("#esp").replaceWith(options);
                condiciones.value = "";
            }
    
            function listLinSvc(xml)
            {
                var options;
                $("filter",xml).each(
                    function(id) {
                        filter=$("filter",xml).get(id);
                        if ($("code",filter).text() == "condiciones") {
                            condiciones.value = $("value",filter).text();
                            cond_original = condiciones.value;
                            if ($("#rbTipoDsp:checked").val() == 1 && $("#rbTipoDsp:checked").is(':visible'))
                                condiciones.value = condiciones.value + ". Personal de Vestmed se comunicara con Usted para indicarle la direccion del Carrier para su retiro.";
                        }
                        if ($("code",filter).text() == "costo") {
                            valordsp = parseFloat($("value",filter).text());
                            valordsp += valordsp * <?php echo $IVA ?>;
                            $("#dfCostoDsp").val("$ "+FormatNumero(Math.round(valordsp).toString()));
                            if ($("value",filter).text() == "0") {
                                $("#labelPeso").replaceWith("<span id=\"labelPeso\" class=\"dato\">SIN SERVICIO</span>");
                                $("#dfCostoDspPrd").val("0");
                            }
                            else {
                                $("#labelPeso").replaceWith("<span id=\"labelPeso\" class=\"dato\"></span>");
                                $("#dfCostoDspPrd").val($("#dfCostoDsp").val());
                            }
                        }
                    }
                );
            }
    
            function AgregarUsr() {
                $("form#frmAgregar").attr('action', 'usuarios.php');
                $("form#frmAgregar").submit();
            }
            
            function SiguientePaso() {
                $("form#frmAgregar").attr('action', 'despacho.php');
                $("form#frmAgregar").submit();
            }
            
            function MostrarDespacho(obj) {
                if (obj.value == "0")
                    $("#inf_despacho").hide("slow");
                else 
                    $("#inf_despacho").show("slow");
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
                if ($("#dfTipo"+obj.value).val() == 1) 
                    $("#tipo_despacho").show("slow");
                else 
                    $("#tipo_despacho").hide("slow");

                $("#dfCostoDsp").val("$ 0");
                if (obj.value > 0 && $("#esp").val() != "_NONE") $("form#searchEsp").submit();
                else {
                    condiciones.value = "";
                    cond_original = condiciones.value;
                }
            }
        </script>
    </head>
    <body>
        <div>
            <TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
            <tr>
                    <td colspan="3" width="100%" style="TEXT-ALIGN: left" valign="top">
                    <TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
                    <TR>
                            <TD colspan="2" align="left" style="font-weight:bold; font-size:12px;">Direcci&oacute;n de Facturaci&oacute;n</TD>
                    </TR>
                    <TR>
                            <TD width="10px" style="TEXT-ALIGN:center">
                                    <INPUT id="rbTipoSuc" name="rbTipoSuc" type="radio" style="border:none" value="0" onclick="MostrarDespacho(this)" checked="true" />
                                    <INPUT type="hidden" id="dfTipo0" name="" value="<?php echo $row['Tip_Cmn']; ?>" />
                            </TD>
                            <TD style="TEXT-ALIGN:left; PADDING-LEFT:5px">Pick Up Tienda (Av. Vitacura 5900 Local 5)</TD>
                    </TR>
                    <?php
                            $j = 1;
                            $iTotPrd1 = 1;
                            $result = mssql_query("vm_suc_s $Cod_Clt", $db);
                            while ($row = mssql_fetch_array($result)) {
                    ?>
                                    <TR>
                                       <TD style="TEXT-ALIGN: center">
                                                    <INPUT id="rbTipoSuc" name="rbTipoSuc" type="radio" style="border:none" value="<?php echo $row['Cod_Suc']; ?>" onclick="MostrarDespacho(this)"></TD>
                                                    <INPUT type="hidden" id="dfTipo<?php echo $row['Cod_Suc']; ?>" name="dfTipo<?php echo $row['Cod_Suc']; ?>" value="<?php echo $row['Tip_Cmn']; ?>">
                                       <TD style="TEXT-ALIGN: left; PADDING-LEFT:5px"><?php echo utf8_encode($row['Dir_Suc']); ?> (<?php echo utf8_encode($row['Nom_Cdd']); ?>)</TD>
                                    </TR>
                    <?php
                                    $j = 1 - $j;
                                    $iTotPrd1++;
                            }
                            mssql_free_result($result);
                    ?>
                    <TR>
                            <TD colspan="2" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: left" class="label_top"><input type="button" class="btn2" value="Agregar Sucursal" onclick="NuevaSuc('<?php echo $Num_Doc; ?>');" /></TD>
                    </TR>
                    </TABLE>
                    </td>
            </tr>
            </TABLE>
        </div>
        <div id="inf_despacho">
        <TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
        <tr>
                <td class="label22" width="40%" style="TEXT-ALIGN: left">Carrier</td>
                <td class="label22" width="40%" style="TEXT-ALIGN: left">Servicio</td>
                <td class="label22" width="20%" style="TEXT-ALIGN: left">Costo(IVA Inc)</td>
        </tr>
        <tr>
                <form id="searchPro" name="searchPro">
                <TD align="left">
                <select id="pro" name="pro" class="textfieldv2" onChange="filterPro(this)">
                        <option selected value="_NONE">Seleccione un Carrier</option>
                        <?php //Seleccionar los Carrier
                        $sp = mssql_query("vm_CrrCmb",$db);
                        while($row = mssql_fetch_array($sp))
                        {
                                ?>
                                <option value="<?php echo $row['Cod_Crr'] ?>"><?php echo $row['Des_Crr'] ?></option>
                                <?php
                        }
                        ?>
                </select>
                </TD>
                </form>
                <form id="searchEsp" name="searchEsp">
                <TD>
                <select id="esp" name="esp" class="textfieldv2" onChange="filterSvc(this)">
                        <option selected value="_NONE">Seleccione un Servicio</option>
                        <?php //Seleccionar los Servicios
                        $sp = mssql_query("vm_esppro_s 0",$db);
                        while($row = mssql_fetch_array($sp))
                        {
                        ?>
                            <option value="<?php echo $row['Cod_Esp'] ?>"><?php echo $row['Nom_Esp'] ?></option>
                        <?php
                        }
                        ?>
                </select>
                </TD>
                </form>
                <td>
                        <INPUT name="dfCostoDsp" id="dfCostoDsp" size="8" maxLength="8" class="textfield_m" value="$ 0" ReadOnly />
                        <span id="labelPeso" class="dato"></span>
                </td>
        </tr>
        <tr><td colspan="3" style="padding-top: 5px">
        <div id="tipo_despacho">
        <table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
                <TR>
                        <TD width="10px" style="TEXT-ALIGN:center" valign="top">
                                <img src="images/warning.png">
                        </TD>
                        <TD style="TEXT-ALIGN:left; PADDING-LEFT:5px; PADDING-BOTTOM: 5px" colspan="2" valign="top">
                        Nuestros registros indican que en el pasado han ocurrido problemas con los despachos a domicilio realizados a la comuna seleccionada. 
                        Recomendamos que el despacho sea realizado a la Oficina del Carrier m&aacute;s cercana a su domicilio en donde ser&aacute; recibido y 
                        almacenado para su retiro. En caso de concretar una compra, personal de Vestmed se comunicar&aacute; con usted 
                        para entregarle mayor informaci&oacute;n.							
                        </TD>
                </TR>
                <TR>
                        <TD width="10px" style="TEXT-ALIGN:center">
                                <INPUT id="rbTipoDsp" name="rbTipoDsp" type="radio" style="border:none" value="0" onclick="refreshCondiciones(this)" />
                        </TD>
                        <TD style="TEXT-ALIGN:left; PADDING-LEFT:5px" colspan="2">Despacho a Domicilio</TD>
                </TR>
                <TR>
                        <TD width="10px" style="TEXT-ALIGN:center">
                                <INPUT id="rbTipoDsp" name="rbTipoDsp" type="radio" style="border:none" value="1" onclick="refreshCondiciones(this)" />
                        </TD>
                        <TD style="TEXT-ALIGN:left; PADDING-LEFT:5px" colspan="2">Despacho a Sucursal del Carrier (Recomendable para localidades distantes/Rurales)</TD>
                </TR>
        </table>
        </div>
        </td></tr>
        <tr>
                <td colspan="3" style="TEXT-ALIGN: left; PADDING-TOP: 10px">
                   <span class="titulo_Condiciones">Condiciones del Servicio:</span><BR>
                   <!--span name="texto_condicion" id="texto_condicion">Overnight: Entrega d&iacute;a Habil Siguiente antes de las 13:00 hrs</span-->
                   <textarea class="textfieldv2" style="overflow: auto" name="texto_condicion" id="texto_condicion" cols="140" rows="4" ReadOnly></textarea>
                </td> 
        </tr>
        </TABLE>				
        </div>
        <div style="padding: 50px; text-align: right">
            <form name="frmAgregar" id="frmAgregar" action="kit.php" method="POST">
                <input type="hidden" name="dfCodNtaVta" id="dfCodNtaVta" value="<?php echo $Cod_Nvt ?>" />
                <input type="hidden" name="dfCodCltVta" id="dfCodCltVta" value="<?php echo $Cod_Clt ?>" />
                <input type="button" value="Agregar Usuario" name="AgregarUsr" onclick="AgregarUsr();"/>
                <input type="button" value="Continuar" name="Continuar" onclick="SiguientePaso();"/>
                <input type="submit" value="Volver" name="volver" />
            </form>        
        </div>
        
        <script type="text/javascript">
            $("#inf_despacho").hide();
            $("#tipo_despacho").hide();
            $("#tipo_bordado").hide();
        </script>
    </body>
</html>
