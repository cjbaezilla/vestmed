<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$Cod_Nvt = $_POST['dfCodNtaVtaKit'];
$Cod_Clt = $_POST['dfCodCltKit'];
$Cod_Suc = $_POST['dfCodSucKit'];
$fec_cot = date('d/m/Y');

$query = "vm_cli_s ".$Cod_Clt;
$result = mssql_query($query, $db) or die ('error en sql (1001)');
if (($row = mssql_fetch_array($result))) $Rut_Clt = $row['Num_Doc'];

if (isset($_POST['dfParametro'])) {
    $parametro = str_replace('[', '"', str_replace(']', '"', $_POST['dfParametro']));
    $query = "vm_kid_i '$parametro'";
    //echo $query;
    $result = mssql_query($query, $db) or die ('error en sql (1002)'."<br>".$query);
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
                    $.post("ajax-empresa.php",{
                            id_clt: $("#dfCodCltKit").val(),
                            id_nvt: $("#dfCodNtaVtaKit").val(),
                            id_kit: $("#cod_kit").val()
                        }, function(xml) {
                        listaPrdKit(xml);
                    });
                    return false;
                });
               
            });
            
            function MostrarAgregar() {
                $("#IngUsr").show('slow');
            }
            
            function HideAgregar() {
                $("#IngUsr").hide('slow');
            }
            
            function PutParametro (tag, valor) {
                return " "+tag+"=["+valor+"]";
            }
            
            function MostrarPrdKit (codigo) {
                $("#cod_kit").val(codigo);
                $("form#detprdkit").submit();
            }
            
            function ValidarDatosKit() {
                var parametro = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><parametro";
                parametro += PutParametro ('CodClt', $("#dfCodCltKit").val());
                parametro += PutParametro ('CodNta', $("#dfCodNtaVtaKit").val());
                parametro += PutParametro ('NomKit', $("#dfNomKit").val());
                parametro += PutParametro ('FecDes', $("#datepicker1").val());
                parametro += PutParametro ('FecHas', $("#datepicker2").val());
                parametro += ' />';
                
                $("#dfParametro").val(parametro);
                $("form#IngNuevoKit").submit();                
            }
    
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
        <td align="center"><a href="javascript:MostrarPrdKit(<?php echo $row['Cod_KitNvt'] ?>)"><img src="../icons/page_white_edit.png" alt="" title="Agregar Kit" /></a></td>
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
        <div id="PrdKit">
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
                            <form id="IngNuevoKit" action="kit.php" method="POST" onsubmit="return ValidarDatosKit()" enctype="multipart/form-data">
                            <input type="hidden" name="dfParametro" id="dfParametro" value="" />
                            <input type="hidden" name="dfCodCltKit" id="dfCodCltKit" value="<?php echo $Cod_Clt ?>" />
                            <input type="hidden" name="dfCodSucKit" id="dfCodSucKit" value="<?php echo $Cod_Suc ?>" />
                            <input type="hidden" name="dfCodNtaVtaKit" id="dfCodNtaVtaKit" value="<?php echo $Cod_Nvt ?>" />
                            <input type="submit" value="Agregar" name="Agregar" />
                            <input type="button" value="Cancelar" name="Cancelar" onclick="HideAgregar();"/>
                            </form>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <div width="99%" align="center" style="padding-top: 10px; text-align: right">
            <form name="frmAgregar" id="frmAgregar" action="usuarios.php" method="POST">
                <input type="hidden" name="dfCodNtaVta" id="dfCodNtaVta" value="<?php echo $Cod_Nvt ?>" />
                <input type="hidden" name="tipovta" id="tipovta" value="1" />
                <input type="button" value="Agregar Kit" name="Agregar" onclick="MostrarAgregar();"/>
                <input type="submit" value="Volver" name="volver" />
            </form>
        </div>
        
        <script type="text/javascript">
            $("#IngUsr").hide();
            $("#PrdKit").hide();
        </script>
        
    </body>
</html>
