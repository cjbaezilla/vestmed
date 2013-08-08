<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$rut = $_POST['dfrutclt'];
$rut = str_replace(".", "", $rut);
$fec_cot = date('d/m/Y');

$result = mssql_query("vm_s_per_tipdoc 1, '$rut'",$db);
if (($row = mssql_fetch_array($result))) {
        $nom_ctt = "";
	$cod_tipper = $row['Cod_TipPer'];
        if ($cod_tipper == 1) {
           $nom_clt = $nom_ctt = utf8_encode($row['Nom_Per']." ".$row['Pat_Per']." ".$row['Mat_Per']); 
        }
        else
           $nom_clt = utf8_encode($row['RznSoc_Per']);
	$num_doc   = $row['Num_Doc'];
        $cod_clt   = $row['Cod_Clt'];        
}

if (isset($_POST['dfParametro'])) {
    $parametro = str_replace('[', '"', str_replace(']', '"', $_POST['dfParametro']));
    $query = "vm_nvtweb_i '$parametro'";
    //echo $query;
    $result = mssql_query($query, $db) or die ('error en sql<br>');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Notas de Venta</title>
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
            function MostrarAgregar() {
                $("#IngNvt").show('slow');
            }
            function HideAgregar() {
                $("#IngNvt").hide('slow');
            }
            
            function filterCmn()
            {
                $("form#searchCmn").submit();
            } 
            
            function CallFrmUsr(codigo)
            {
                $("#dfCodNtaVtaUsr").val(codigo);
                $("form#frmIngUsr").submit();
            }

            function CallFrmKit(codigo)
            {
                $("#dfCodNtaVtaKit").val(codigo);
                $("form#frmIngKit").submit();
            }

            function PutParametro (tag, valor) {
                return " "+tag+"=["+valor+"]";
            }
            
            function ValidarDatosNta() {
                var parametro = '<parametro';
                parametro += PutParametro ('CodClt', $("#dfCodClt").val());
                if ($("#datepicker").val() == '') {
                    alert('Debe ingresar una Fecha para la Nota de Venta');
                    return false;
                }
                parametro += PutParametro ('FecNta', $("#datepicker").val());
                parametro += PutParametro ('CodCmn', $("#cmn").val());
                parametro += PutParametro ('CodCdd', $("#cdd").val());
                parametro += PutParametro ('DirNta', $("#dfDirNtaVta").val());
                parametro += PutParametro ('FonNta', $("#dfTelNtaVta").val());
                parametro += PutParametro ('VenNta', $("#dfVenNtaVta").val());
                parametro += PutParametro ('TitNta', $("#dfTitNtaVta").val());
                parametro += ' />'
                
                $("#dfParametro").val(parametro);
                $("form#IngNuevaNvt").submit();
                
                return true;
            }
    
            $.datepicker.regional['es'] = {
                    closeText: 'Cerrar',
                    prevText: '&#x3c;Ant',
                    nextText: 'Sig&#x3e;',
                    currentText: 'Hoy',
                    monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                    dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
                    dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
                    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
                    weekHeader: 'Sm',
                    dateFormat: 'dd/mm/yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ''};
            $.datepicker.setDefaults($.datepicker.regional['es']);            
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
               $("#datepicker").datepicker();
               
               $("form#searchCmn").submit(function(){
                   $.post("../ajax-search-cdd.php",{
                        search_type: "cdd",
                        param_filter: $("#cmn").val()
                   }, function(xml) {
                        listLinCdd(xml);
                   });
                   return false;
               });
               
            });
            
            function listLinCdd(xml)
            {
                options="<select id=\"cdd\" name=\"cdd\" class=\"textfield_m\">\n";
                options+="<option selected value=\"_NONE\">Seleccione una Ciudad</option>\n";
                $("filter",xml).each(
                        function(id) {
                            filter=$("filter",xml).get(id);
                            options+= "<option value=\""+$("code",filter).text()+"\">"+$("value",filter).text()+"</option>\n";
                        }
                );
                options+="</select>";
                $("#cdd").replaceWith(options);
            }
        </script>        
    </head>
    <body>
        <div align="center" style="padding-bottom: 50px">
            <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="99%" align="center">
                <tr>
                    <td width="50%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><IMG SRC="../cotizador/images/logo.gif" width="235" HEIGHT="130" /></td>
                    <td class="dato" style="text-align: right; FONT-SIZE: 2.5em; FONT-FAMILY: Arial, Verdana;" width="50%" VALIGN="bottom" COLSPAN="2"><b>Fecha:  <?php echo $fec_cot; ?></b></td>
                </tr>
                <tr>
                    <td class="dato" style="text-align: right" width="50%" VALIGN="TOP" COLSPAN="2" HEIGHT="65">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" width="50%" VALIGN="TOP" class="dato5p12s" style="padding-top: 20px;"><B>Cliente: <?php echo $nom_clt ?></B></td>
                </tr>
                <tr>
                    <td width="50%" VALIGN="TOP" class="dato5p">Rut: <?php echo formatearRut($num_doc) ?></td>
                    <td width="50%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div>
            <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="99%" align="center" class="tabular">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="5%" class="tabular">Numero</th>
                    <th scope="column" width="5%" class="tabular">Fecha</th>
                    <th scope="column" width="10%" class="tabular">Estado</th>
                    <th scope="column" width="18%" class="tabular">Vendedor</th>
                    <th scope="column" width="18%" class="tabular">Sucursal</th>
                    <th scope="column" width="30%" class="tabular">Titulo</th>
                    <th scope="column" width="10%" class="tabular">Fono</th>
                    <th scope="column" width="2%" class="tabular">&nbsp;</th>
                    <th scope="column" width="2%" class="tabular">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
<?php
        $bExiste = false;
        $result = mssql_query("vm_nvt_s $cod_clt",$db);
        while (($row = mssql_fetch_array($result))) {
            $bExiste = true;
            switch ($row['Est_Nvt']) {
            case 1:
                $estado = 'En proceso';
                break;
            case 2:
                $estado = 'Facturada';
                break;
            default:
                $estado = 'Cerrada';
                break;
            }
?>
<tr  class="tabular">
	<td class="tabular"><?php echo $row['Cod_Nvt'] ?></td>
        <td class="tabular"><?php echo date('d/m/Y', strtotime($row['Fec_Nvt'])) ?></td>
	<td class="tabular"><?php echo utf8_encode($estado) ?></td>
	<td class="tabular"><?php echo utf8_encode($row['Nom_Vdd']) ?></td>
	<td class="tabular"><?php echo utf8_encode($row['Nom_Suc']) ?></td>
	<td class="tabular"><?php echo utf8_encode($row['Nvt_Tlt']) ?></td>
	<td class="tabular"><?php echo utf8_encode($row['Fon_Suc']) ?></td>
        <td class="tabular" align="center">
            <?php if ($row['Est_Nvt'] == 1) { ?>
            <a href="javascript:CallFrmKit(<?php echo $row['Cod_Nvt'] ?>)"><img src="../icons/user_go.png" alt="" title="Agregar Kit" /></a>
            <?php } else { ?>
            &nbsp;
            <?php } ?>
        </td>
        <td class="tabular" align="center">
            <?php if ($row['Est_Nvt'] == 1) { ?>
            <a href="javascript:CallFrmUsr(<?php echo $row['Cod_Nvt'] ?>)"><img src="../icons/user.png" alt="" title="Agregar Usuarios" /></a>
            <?php } else { ?>
            &nbsp;
            <?php } ?>
        </td>
</tr>
<?php
        }
        if (!$bExiste) {
?>
                <tr>
                    <td colspan="8" style="padding: 5px; text-align: center">CLIENTE NO REGISTRA NOTAS DE VENTA</td>
                </tr>
<?php
        }
?>
            </tbody>
</table>
            <form id="frmIngUsr" method="POST" action="usuarios.php">
                <input type="hidden" id="dfRutCltUsr" name="dfRutCltUsr" value="<?php echo $rut ?>" />
                <input type="hidden" id="dfCodNtaVtaUsr" name="dfCodNtaVta" value="" />
            </form>                    
            <form id="frmIngKit" method="POST" action="kit.php">
                <input type="hidden" id="dfRutCltKit" name="dfRutCltKit" value="<?php echo $rut ?>" />
                <input type="hidden" id="dfCodNtaVtaKit" name="dfCodNtaVtaKit" value="" />
            </form>                    
        </div>
        <div id="IngNvt">
            <fieldset class="label_left_right_top_bottom" style="width: 50%">
                <legend>Datos Generales</legend>
                <table align="center" cellpadding="2" cellspacing="2" width="100%">
                    <tr>
                        <td align="right">Numero:</td><td align="left">Por Asignar</td>
                    </tr>
                    <tr>
                        <td align="right">Fecha:</td>
                        <td align="left">
                            <input type="text" name="datepicker" id="datepicker" readonly="readonly" size="12" class="textfield_m"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Vendedor:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfVenNtaVta" id="dfVenNtaVta" value="" size="40" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Titulo:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfTitNtaVta" id="dfTitNtaVta" value="" size="50" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="label_left_right_top_bottom" style="width: 50%">
                <legend>Informaci&oacute;n de Locación para la Nota de Venta</legend>
                <table align="center" cellpadding="2" cellspacing="2" width="100%">
                    <tr>
                        <td align="right">Comuna:</td>
                        <td align="left">
                            <form id="searchCmn">
                            <select id="cmn" name="cmn" class="textfield_m" onChange="filterCmn()">
                                    <option selected="true" value="_NONE">Seleccione una Comuna</option>
                                    <?php //Seleccionar las ciudades
                                    $sp = mssql_query("vm_cmn_s",$db);
                                    while($row = mssql_fetch_array($sp))
                                    {
                                            ?>
                                    <option value="<?php echo $row['Cod_Cmn'] ?>"><?php echo utf8_encode($row['Nom_Cmn']) ?></option>
                                            <?php
                                    }
                                    ?>
                            </select>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Ciudad:</td>
                        <td align="left">
                            <form id="searchCdd" name="searchCdd">
                            <select id="cdd" name="cdd" class="textfield_m" onChange="llenarCdd(this)">
                                <option selected="true" value="_NONE">Seleccione una Ciudad</option>
                                <?php //Seleccionar las ciudades
                                $sp = mssql_query("vm_cddcmn_s NULL, 0", $db);
                                while($row = mssql_fetch_array($sp))
                                {
                                        ?>
                                        <option value="<?php echo $row['Cod_Cdd'] ?>"><?php echo utf8_encode($row['Nom_Cdd']) ?></option>
                                        <?php
                                }
                                ?>
                            </select>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Direcci&oacute;n:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfDirNtaVta" id="dfDirNtaVta" value="" size="50" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Tel&eacute;fono:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfTelNtaVta" id="dfTelNtaVta" value="" size="50" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 5px; text-align: right">
                            <form id="IngNuevaNvt" action="nventas.php" method="POST" onsubmit="return ValidarDatosNta()">
                            <input type="hidden" name="dfParametro" id="dfParametro" value="" />
                            <input type="hidden" name="dfrutclt" id="dfrutclt" value="<?php echo $rut ?>" />
                            <input type="submit" value="Agregar" name="Agregar" />
                            <input type="button" value="Cancelar" name="Cancelar" onclick="HideAgregar();"/>
                            </form>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <div width="99%" align="center" style="padding-top: 10px; text-align: right">
            <form name="frmAgregar" id="frmAgregar" action="principal.php" method="POST">
                <input type="hidden" name="dfCodClt" id="dfCodClt" value="<?php echo $cod_clt; ?>" />
                <input type="button" value="Agregar Nota de Venta" name="Agregar" onclick="MostrarAgregar();"/>
                <input type="submit" value="Volver" name="volver" />
            </form>
        </div>
        
        <script type="text/javascript">
            $("#IngNvt").hide();
        </script>
        
    </body>
</html>
