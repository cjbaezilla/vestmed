<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$fec_cot = date('d/m/Y');

if (isset($_POST['dfCodCltKit'])) {
    $Cod_Nvt = $_POST['dfCodNtaVtaKit'];
    $Cod_Clt = $_POST['dfCodCltKit'];
    $Cod_Suc = $_POST['dfCodSucKit'];
    
    $query = "vm_cli_s ".$Cod_Clt;
    $result = mssql_query($query, $db) or die ('error en sql (1001)<br>'.$query);
    if (($row = mssql_fetch_array($result))) $Rut_Clt = $row['Num_Doc'];
}
if (isset($_POST['dfCodCltKitPrd'])) {
    $Cod_Nvt = $_POST['dfCodNtaVtaKitPrd'];
    $Cod_Clt = $_POST['dfCodCltKitPrd'];
    $Cod_Suc = $_POST['dfCodSucKitPrd'];
    
    $query = "vm_cli_s ".$Cod_Clt;
    $result = mssql_query($query, $db) or die ('error en sql (1001)<br>'.$query);
    if (($row = mssql_fetch_array($result))) $Rut_Clt = $row['Num_Doc'];
}

if (isset($_POST['dfParametro'])) {

    $parametro = str_replace('[', '"', str_replace(']', '"', $_POST['dfParametro']));
    $query = "vm_kid_i '$parametro'";
    //echo $query;
    $result = mssql_query($query, $db) or die ('error en sql (1002)'."<br>".$query);
}

if (isset($_POST['dfParametroPrd'])) {
    $parametro = str_replace('[', '"', str_replace(']', '"', $_POST['dfParametroPrd']));
    $query = "vm_prdkid_i '$parametro'";
    //echo $query;
    $result = mssql_query($query, $db) or die ('error en sql (1003)'."<br>".$query);
}

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
            $(document).ready(function() {
                $("#datepicker1").datepicker();
                $("#datepicker2").datepicker();
               
                $("form#detprdkit").submit(function(){
                    alert ('vm_kitprd_prd_s');
                    $.post("ajax-empresa.php",{
                            search_type: "emp",
                            id_clt: $("#dfCodCltKit").val(),
                            id_nvt: $("#dfCodNtaVtaKit").val(),
                            id_kit: $("#cod_kit").val()
                        }, function(xml) {
                        listaPrdKit(xml);
                    });
                    return false;
                });
               
                $("form#marca").submit(function(){
                    $.post("ajax-empresa.php",{
                            search_type: "sty",
                            id_mca: $("#dfCodMca").val()
                        }, function(xml) {
                        listaStyMca(xml);
                    });
                    return false;
                });
                
                $("form#style").submit(function(){
                    $.post("ajax-empresa.php",{
                            search_type: "pat",
                            id_grpprd: $("#dfCodStyle").val()
                        }, function(xml) {
                        listaPatSty(xml);
                    });
                    return false;
                });
                
                $("form#color").submit(function(){
                    $.post("ajax-empresa.php",{
                            search_type: "sze",
                            id_pat: $("#dfKeyPat").val(),
                            id_grpprd: $("#dfCodStyle").val()
                        }, function(xml) {
                        listaSzePat(xml);
                    });
                    return false;
                });
                
                $("form#IngPrcPrd").submit(function(){
                    $.post("ajax-empresa.php",{
                            search_type: "sze",
                            id_pat: $("#dfCodPatPrc").val(),
                            id_grpprd: $("#dfCodGrpPrc").val()
                        }, function(xml) {
                        listaTblPrc(xml);
                    });
                    return false;
                });
                
            });
            
            function listaPrdKit (xml) {
                var tabla = "";
                
                tabla += "<table id=\"tblPrdKit\" BORDER=\"0\" CELLSPACING=\"2\" CELLPADDING=\"2\" width=\"80%\" align=\"center\" class=\"tabular\">";
                tabla += "<thead>";
                tabla += "<tr class=\"tabular\">";
                tabla += "<th scope=\"column\" width=\"15%\" class=\"tabular\">Marca</th>";
                tabla += "<th scope=\"column\" width=\"15%\" align=\"center\" class=\"tabular\">Diseño</th>";
                tabla += "<th scope=\"column\" width=\"15%\" align=\"center\" class=\"tabular\">Color</th>";
                tabla += "<th scope=\"column\" width=\"50%\" align=\"center\" class=\"tabular\">Descripci&oacute;n</th>";
                tabla += "<th scope=\"column\" width=\"5%\" align=\"center\" class=\"tabular\">Precio</th>";
                tabla += "</tr>";
                tabla += "</thead>";
                tabla += "<tbody>";
                $("filter",xml).each(
                    function(id) {
                        filter = $("filter",xml).get(id);
                        tabla += "<tr class=\"tabular\"><td>"+$("mca",filter).text()+"</td>";
                        tabla += "<td class=\"tabular\">"+$("sty",filter).text()+"</td>";
                        tabla += "<td class=\"tabular\">"+$("key",filter).text()+"</td>";
                        tabla += "<td class=\"tabular\">"+$("des",filter).text()+"</td>";
                        tabla += "<td class=\"tabular\" align=\"center\">"+"<a href=\"javascript:GetPrecio('" + $("pat",filter).text() + "',";
                        tabla += "'" + $("dsg",filter).text() +"',";
                        tabla += "'" + $("prd",filter).text() +"')";
                        tabla += "\"><img src=\"icons/page_white_edit.png\" alt=\"\" title=\"Definir Precios\" />"+"</td></tr>";
                    }
                );
                tabla += "</tbody>";
                tabla += "</table>";
                
                $("#dfCodKitPrd").val($("#cod_kit").val());
                $("#tblPrdKit").replaceWith(tabla);
                $("#PrdKit").show('slow');
                $("#btnAgregarPrd").show('slow');
            }
            
            function listaStyMca (xml) {
                var availableSty = [];
                
                $("filter",xml).each(
                    function(id) {
                        filter = $("filter",xml).get(id);
                        var objeto = {
                            label: $("sty",filter).text(),
                            value: $("grp",filter).text()
                        }
                        availableSty.push(objeto);
                    }
                );
                
                $( "#dfCodStyle" ).autocomplete( 
                    {source: availableSty} ,
                    {change: function( event, ui ) {
                         $("#dfValSze").val();
                         $("form#style").submit();                
                    }}
                );
            }
            
            function listaPatSty (xml) {
                var availableSty = [];
                $("filter",xml).each(
                    function(id) {
                        filter = $("filter",xml).get(id);
                        var objeto = {
                            label: $("des",filter).text(),
                            value: $("key",filter).text()
                        }
                        availableSty.push(objeto);
                    }
                );
                
                $( "#dfKeyPat" ).autocomplete( 
                    {source: availableSty} ,
                    {change: function( event, ui ) {
                         //$("form#color").submit();         
                    }}
                );
            }
            
            function listaSzePat (xml) {
                var availableSty = [];
                $("filter",xml).each(
                    function(id) {
                        filter = $("filter",xml).get(id);
                        var objeto = {
                            label: $("val",filter).text(),
                            value: $("sze",filter).text()
                        }
                        availableSty.push(objeto);
                    }
                );
                
                $( "#dfValSze" ).autocomplete( 
                    {source: availableSty} ,
                    {change: function( event, ui ) {
                            
                    }}
                );
            }
            
            function listaTblPrc (xml) {
                var tabla = "<table id=\"tblIngPrc\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" width=\"40%\" class=\"tabular\">";
                tabla += "<thead>";
                tabla += "    <tr class=\"tabular\">";
                tabla += "        <th scope=\"column\" width=\"50%\" align=\"center\" class=\"tabular\">Talla</th>";
                tabla += "        <th scope=\"column\" width=\"25%\" align=\"center\" class=\"tabular\">Precio<br>Lista</th>";
                tabla += "        <th scope=\"column\" width=\"25%\" align=\"center\" class=\"tabular\">Precio<br>Kit</th>";
                tabla += "    </tr>";
                tabla += "</thead>";
                tabla += "<tbody>";
                $("filter",xml).each(
                    function(id) {
                        filter = $("filter",xml).get(id);
                        tabla += "<tr>";
                        tabla += "<td class=\"tabular\" align=\"center\">" + $("val",filter).text() + "</td>";
                        tabla += "<td class=\"tabular\" align=\"center\">" + $("prc",filter).text() + "</td>";
                        tabla += "<td class=\"tabular\" align=\"center\"><input size=\"5\" class=\"textfield_m\" type=\"text\" name=\"campo\" value=\"" + $("prc",filter).text() + "\" /></td>";
                        tabla += "</tr>";
                    }
                );
                tabla += "</tbody>";
                tabla += "</tabla>";
                
                $("#tblIngPrc").replaceWith(tabla);
                $("#IngPrcKit").show('slow');
            }
            
            function MostrarAgregar() {
                $("#IngUsr").show('slow');
            }
            
            function HideAgregar() {
                $("#IngUsr").hide('slow');
            }
            
            function MostrarAgregarPrd() {
                $("#IngDetKit").show('slow');
            }
            
            function HideAgregarPrd() {
                $("#IngDetKit").hide('slow');
            }
            
            function PutParametro (tag, valor) {
                return " "+tag+"=["+valor+"]";
            }
            
            function MostrarPrdKit (codigo) {
                $("#cod_kit").val(codigo);
                $("form#detprdkit").submit();
            }
            
            function GetPrecio(pat, dsg, prd) {
                alert ("vm_strinv_szepat");
                $("#dfCodPatPrc").val(pat);
                $("#dfCodGrpPrc").val(prd);                
                $("form#IngPrcPrd").submit();  
            }
            
            function ValidarDatosKit() {
                alert('vm_kid_i');
                var parametro = "<" + "?" + "xml";                
                parametro += PutParametro('version', '1.0');
                parametro += PutParametro('encoding', 'UTF-8');
                parametro += "?>";
                parametro += "<parametro";
                parametro += PutParametro ('CodClt', $("#dfCodCltKit").val());
                parametro += PutParametro ('CodNta', $("#dfCodNtaVtaKit").val());
                parametro += PutParametro ('NomKit', $("#dfNomKit").val());
                parametro += PutParametro ('FecDes', $("#datepicker1").val());
                parametro += PutParametro ('FecHas', $("#datepicker2").val());
                parametro += ' />';
                $("#dfParametro").val(parametro);
                $("form#IngNuevoKit").submit();  
            }
    
            function ValidarDatosKitPrd() {
                alert('vm_prdkid_i');
                var arrDsg = $("#dfCodStyle").val().split('-');
                
                var parametro = "<" + "?" + "xml";                
                parametro += PutParametro('version', '1.0');
                parametro += PutParametro('encoding', 'UTF-8');
                parametro += "?>";
                parametro += "<parametro";
                parametro += PutParametro ('CodClt', $("#dfCodCltKitPrd").val());
                parametro += PutParametro ('CodNta', $("#dfCodNtaVtaKitPrd").val());
                parametro += PutParametro ('CodKit', $("#dfCodKitPrd").val());
                parametro += PutParametro ('CodPat', $("#dfKeyPat").val());
                parametro += PutParametro ('CodDsg', arrDsg[0]);
                parametro += ' />';
                $("#dfParametroPrd").val(parametro);
                $("form#IngNuevoKitPrd").submit();                
            }
         
            function SiguientePaso () {
                $("form#frmAgregar").attr('action', 'catalogo.php');
                $("form#frmAgregar").submit();
            }
            
            function HidePrcKit() {
                $("#IngPrcKit").hide('slow');
            }
        </script>
        <script type="text/javascript">
                $(function() {
                 var availableMca = [
                                <?php
                                    $i = 0;
                                    $result = mssql_query("vm_mca_cmb_full",$db) or die ('error en sql (1005)');
                                    while (($row = mssql_fetch_array($result)))
                                        if ($row['Total'] > 0) {
                                ?>
                                <?php if ($i++ == 0) echo '"'.$row['Cod_Mca'].'"'; else echo ',"'.$row['Cod_Mca'].'"'; ?>                               <?php
                                        }
                                ?>
                            ]; 
                  $( "#dfCodMca" ).autocomplete( 
                    {source: availableMca} ,
                    {change: function( event, ui ) {
                         $("#dfCodStyle").val();
                         $("#dfKeyPat").val();
                         $("#dfValSze").val();
                         $("form#marca").submit();                
                    }}
                  );
               } );               
        </script>    
    </head>
    <body>
        <div>
            <table BORDER="0" CELLSPACING="2" CELLPADDING="2" width="50%" align="center" class="tabular">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="58%" class="tabular">Nombre Kit</th>
                    <th scope="column" width="20%" align="center" class="tabular">V&aacute;lido<br/>Desde</th>
                    <th scope="column" width="20%" align="center" class="tabular">V&aacute;lido<br/>Hasta</th>
                    <th scope="column" width="2%" align="center" class="tabular">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
<?php
        $bExiste = false;
        $result = mssql_query("vm_kidprd_nvt_s $Cod_Clt, $Cod_Nvt",$db) or die ('error en sql (1003)');
        while (($row = mssql_fetch_array($result))) {
            $bExiste = true;
?>
<tr  class="tabular">
	<td class="tabular"><?php echo utf8_encode($row['Des_KitNvt']) ?></td>
        <td class="tabular" align="center"><?php echo date('d/m/Y', strtotime($row['FecDes'])) ?></td>
        <td class="tabular" align="center"><?php echo date('d/m/Y', strtotime($row['FecHas'])) ?></td>
        <td align="center"><a href="javascript:MostrarPrdKit(<?php echo $row['Cod_KitNvt'] ?>)"><img src="icons/page_white_edit.png" alt="" title="Agregar Kit" /></a></td>
</tr>
<?php
        }
        if (!$bExiste) {
?>
                <tr>
                    <td colspan="8" style="padding: 5px; text-align: center">FAVOR INGRESE KIT</td>
                </tr>
<?php
        }
?>
            </tbody>
</table>
                    
        </div>
        <div id="PrdKit" style="padding-top: 20px">
            <table id="tblPrdKit" BORDER="0" CELLSPACING="2" CELLPADDING="2" width="80%" align="center" class="tabular">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="15%" class="tabular">Marca</th>
                    <th scope="column" width="15%" align="center" class="tabular">Diseño</th>
                    <th scope="column" width="15%" align="center" class="tabular">Color</th>
                    <th scope="column" width="40%" align="center" class="tabular">Descripci&oacute;n</th>
                    <th scope="column" width="15%" align="center" class="tabular">Precio</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            </table>
            <form id="detprdkit" name="detprdkit" method="post">
                <input type="hidden" id="cod_kit" name="cod_kit"  value="" />
            </form>
        </div>
        <div id="IngUsr">
            <fieldset class="label_left_right_top_bottom" style="width: 50%">
                <legend>Datos Nuevo KIT</legend>
                <table align="center" cellpadding="2" cellspacing="2" width="100%">
                    <tr>
                        <td align="right">Fecha Inicio:</td>
                        <td align="left">
                            <input type="text" name="datepicker1" id="datepicker1" readonly="readonly" size="12" class="textfield_m"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Fecha Inicio:</td>
                        <td align="left">
                            <input type="text" name="datepicker2" id="datepicker2" readonly="readonly" size="12" class="textfield_m"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Nombre:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfNomKit" id="dfNomKit" value="" size="40" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 5px; text-align: right">
                            <form id="IngNuevoKit" action="kit.php" method="POST">
                            <input type="hidden" name="dfParametro" id="dfParametro" value="" />
                            <input type="hidden" name="dfCodCltKit" id="dfCodCltKit" value="<?php echo $Cod_Clt ?>" />
                            <input type="hidden" name="dfCodSucKit" id="dfCodSucKit" value="<?php echo $Cod_Suc ?>" />
                            <input type="hidden" name="dfCodNtaVtaKit" id="dfCodNtaVtaKit" value="<?php echo $Cod_Nvt ?>" />
                            <input type="button" value="Agregar" name="Agregar" onclick="ValidarDatosKit()" />
                            <input type="button" value="Cancelar" name="Cancelar" onclick="HideAgregar();"/>
                            </form>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <div id="IngDetKit">
            <fieldset class="label_left_right_top_bottom" style="width: 50%">
                <legend>Productos del KIT</legend>
                <table align="center" cellpadding="2" cellspacing="2" width="100%">
                    <tr>
                        <td align="right">Marca:</td>
                        <td align="left">
                            <form id="marca" name="marca" method="post">
                            <input class="textfield_m" type="text" name="dfCodMca" id="dfCodMca" value="" size="15" />
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Style:</td>
                        <td align="left">
                            <form id="style" name="style" method="post">
                            <input class="textfield_m" type="text" name="dfCodStyle" id="dfCodStyle" value="" size="15" />
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Color:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfKeyPat" id="dfKeyPat" value="" size="15" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 5px; text-align: right">
                            <form id="IngNuevoKitPrd" action="kit.php" method="POST">
                            <input type="hidden" name="dfParametroPrd" id="dfParametroPrd" value="" />
                            <input type="hidden" name="dfCodKitPrd" id="dfCodKitPrd" value="" />
                            <input type="hidden" name="dfCodCltKitPrd" id="dfCodCltKitPrd" value="<?php echo $Cod_Clt ?>" />
                            <input type="hidden" name="dfCodSucKitPrd" id="dfCodSucKitPrd" value="<?php echo $Cod_Suc ?>" />
                            <input type="hidden" name="dfCodNtaVtaKitPrd" id="dfCodNtaVtaKitPrd" value="<?php echo $Cod_Nvt ?>" />
                            <input type="button" value="Agregar" name="AgregarPrd" onclick="ValidarDatosKitPrd();"/>
                            <input type="button" value="Cancelar" name="CancelarPrd" onclick="HideAgregarPrd();"/>
                            </form>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <div id="IngPrcKit">
            <fieldset class="label_left_right_top_bottom" style="width: 50%">
                <legend>Precio Kit</legend>
                <form id="IngPrcPrd" method="POST">
                <table id="tblIngPrc" align="center" cellpadding="2" cellspacing="2" width="100%" class="tabular">
                <thead>
                    <tr class="tabular">
                        <th scope="column" width="20%" class="tabular">Marca</th>
                        <th scope="column" width="15%" align="center" class="tabular">Diseño</th>
                        <th scope="column" width="15%" align="center" class="tabular">Style</th>
                        <th scope="column" width="15%" align="center" class="tabular">Color</th>
                        <th scope="column" width="15%" align="center" class="tabular">Talla</th>
                        <th scope="column" width="20%" align="center" class="tabular">Precio</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                </table>
                <input type="hidden" id="dfCodPatPrc" value="" />
                <input type="hidden" id="dfCodGrpPrc" value="" />
                <input type="button" value="Guardar" name="AgregarPrd" onclick="AgregarPrcKit();"/>
                <input type="button" value="Cancelar" name="CancelarPrc" onclick="HidePrcKit();"/>
                </form>
            </fieldset>
        </div>
        <div width="99%" align="center" style="padding-top: 10px; text-align: right">
            <form name="frmAgregar" id="frmAgregar" action="usuarios.php" method="POST">
                <input type="hidden" name="dfCodNtaVta" id="dfCodNtaVta" value="<?php echo $Cod_Nvt ?>" />
                <input type="hidden" name="dfCodCltVta" id="dfCodCltVta" value="<?php echo $Cod_Clt ?>" />
                <input type="hidden" name="tipovta" id="tipovta" value="1" />
                <input type="button" value="Agregar Productos" name="btnAgregarPrd" id="btnAgregarPrd" onclick="MostrarAgregarPrd();"/>
                <input type="button" value="Agregar Kit" name="Agregar" onclick="MostrarAgregar();"/>
                <input type="button" value="Continuar" name="Continuar" onclick="SiguientePaso();"/>
                <input type="submit" value="Volver" name="volver" />
            </form>
        </div>
        
        <script type="text/javascript">
            $("#IngDetKit").hide();
            $("#btnAgregarPrd").hide();
            $("#IngUsr").hide();
            $("#PrdKit").hide();
            $("#IngPrcKit").hide();
        </script>
        
    </body>
</html>
