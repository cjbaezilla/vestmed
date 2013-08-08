<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$tipo    = $_POST['tipovta'];
$pagina  = ($tipo == 1) ? "kit.php" : "productos.php";
$Cod_Nvt = isset ($_POST['dfCodNtaVta']) ? intval ($_POST['dfCodNtaVta']) : 0;
$Cod_Clt = 50001;
$Cod_Suc = 1;
$Fec_Nta = date('Ymd');
$Cod_Vta = 1;
$Tit_Vta = 'Preventa';

$query = "vm_cli_s ".$Cod_Clt;
$result = mssql_query($query, $db) or die ('error en sql (1001)');
if (($row = mssql_fetch_array($result))) $Rut_Clt = $row['Num_Doc'];

if ($Cod_Nvt == 0) {
    $query = "vm_getfolio 'NVT'";
    $result = mssql_query($query, $db) or die ('error en sql (1002)');
    if (($row = mssql_fetch_array($result))) $Cod_Nvt = $row['Tbl_fol'];
    if ($Cod_Nvt > 0) {
        $query = "vm_nvt_i ".$Cod_Nvt.",".$Cod_Clt.",".$Cod_Suc.",'".$Fec_Nta."', ".$Cod_Vta.",'".$Tit_Vta."'";
        $result = mssql_query($query, $db) or die ('error en sql (1004)');
        if (($row = mssql_fetch_array($result))) $error = $row[0];
    }
}

if (isset($_POST['dfParametro'])) {
    $parametro = str_replace('[', '"', str_replace(']', '"', $_POST['dfParametro']));
    $query = "vm_usr_nvtweb_i '$parametro'";
    $result = mssql_query($query, $db) or die ('error en sql (1003)'."<br>".$query);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Usuarios Pre Venta</title>
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
                $("#IngUsr").show('slow');
            }
            function HideAgregar() {
                $("#IngUsr").hide('slow');
            }
            
            function PutParametro (tag, valor) {
                return " "+tag+"=["+valor+"]";
            }
            
            function ContinuaPreVenta() {
                $("form#frmAgregar").attr('action', '<?php echo $pagina; ?>');
                $("form#frmAgregar").submit();
            }
    
            function DelUsrNta(NumDoc) {
                alert ('vm_usr_nvtweb_d');
            }
            
            function ValidarDatosUsr() {
                var parametro = "<" + "?" + "xml";                
                parametro += PutParametro('version', '1.0');
                parametro += PutParametro('encoding', 'UTF-8');
                parametro += "?>";
                parametro += "<parametro";
                parametro += PutParametro ('CodClt', $("#dfCodClt").val());
                parametro += PutParametro ('CodSuc', $("#dfCodSuc").val());
                parametro += PutParametro ('CodNta', $("#dfCodNtaVta").val());
                parametro += PutParametro ('RutPer', $("#dfRutUsr").val());
                parametro += PutParametro ('PatPer', $("#dfPaterno").val());
                parametro += PutParametro ('MatPer', $("#dfMaterno").val());
                parametro += PutParametro ('NomPer', $("#dfNombre").val());
                parametro += PutParametro ('SexPer', $("#sexoPer").val());
                parametro += PutParametro ('SzePer', $("#pesoPer").val());
                parametro += PutParametro ('PesPer', $("#estatura").val());
                parametro += PutParametro ('FonPer', $("#dfTelefono").val());
                parametro += PutParametro ('MailPer', $("#dfMail").val());
                parametro += ' />'
                alert('SP: vm_usr_nvtweb_i');
                $("#dfParametro").val(parametro);
                $("form#IngNuevoUsr").submit();
            }
            
        </script>
    </head>
    <body>
        <div>
            <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="99%" align="center" class="tabular">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="10%" class="tabular">Rut</th>
                    <th scope="column" width="30%" class="tabular">Nombre</th>
                    <th scope="column" width="10%" class="tabular">Estatura<br/>(cm)</th>
                    <th scope="column" width="10%" class="tabular">Peso<br/>(kg)</th>
                    <th scope="column" width="28%" class="tabular">Mail</th>
                    <th scope="column" width="10%" class="tabular">Fono<br/>Contacto</th>
                    <th scope="column" width="2%" class="tabular">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
<?php
        $bExiste = false;
        $query = "vm_usr_prevta $Cod_Clt, $Cod_Suc, $Cod_Nvt";
        $result = mssql_query($query,$db) or die ('error en sql (1004)');
        while (($row = mssql_fetch_array($result))) {
            $bExiste = true;
            $nombre = $row['Pat_Per']." ".$row['Mat_Per']." ".$row['Nom_Per'];
?>
<tr  class="tabular">
	<td class="tabular"><?php echo $row['Num_Doc'] ?></td>
        <td class="tabular"><?php echo utf8_encode($nombre) ?></td>
	<td class="tabular"><?php echo $row['Pso_Per'] ?></td>
	<td class="tabular"><?php echo $row['Tal_Per'] ?></td>
	<td class="tabular"><?php echo $row['Mail_Ctt'] ?></td>
	<td class="tabular"><?php echo $row['Fon_Ctt'] ?></td>        
        <td><a href="javascript:DelUsrNta('<?php echo $row['Num_Doc'] ?>')"><img src="../icons/delete.png" alt="" title="Eliminar Usuario" /></a></td>
</tr>
<?php
        }
        if (!$bExiste) {
?>
                <tr>
                    <td colspan="8" style="padding: 5px; text-align: center">FAVOR INGRESE LOS USUARIOS DE LA NOTA DE VENTA</td>
                </tr>
<?php
        }
?>
            </tbody>
</table>
                    
        </div>
        <div id="IngUsr">
            <fieldset class="label_left_right_top_bottom" style="width: 50%">
                <legend>Datos Nuevo Usuario</legend>
                <table align="center" cellpadding="2" cellspacing="2" width="100%">
                    <tr>
                        <td align="right">Rut:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfRutUsr" id="dfRutUsr" value="" size="12" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Apellido Paterno:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfPaterno" id="dfPaterno" value="" size="40" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Apellido Materno:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfMaterno" id="dfMaterno" value="" size="40" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Nombre:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfNombre" id="dfNombre" value="" size="40" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Sexo:</td>
                        <td align="left">
                                <select id="sexoPer" name="sexoPer" class="textfield_m">
                                <option value="_NONE">_NONE</option>
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Peso (Kg):</td>
                        <td align="left">
                                <select id="pesoPer" name="pesoPer" class="textfield_m">
                                <option value="0">_NONE</option>
                                <?php 
                                        for ($pesoPer = 40; $pesoPer <= 120; $pesoPer++) {
                                ?>
                                <option value="<?php echo $pesoPer ?>"><?php echo $pesoPer ?></option>
                                <?php
                                        }
                                ?>
                                </select>
                        </td>
                    </tr>
                    <tr>
                            <td align="right">Estatura (cm):</td>
                            <td align="left">
                                    <select id="estatura" name="estatura" class="textfield_m">
                                    <option value="0">_NONE</option>
                                    <?php 
                                            for ($estatura = 140; $estatura <= 200; $estatura++) {
                                    ?>
                                    <option value="<?php echo $estatura ?>"><?php echo $estatura ?></option>
                                    <?php
                                            }
                                    ?>
                                    </select>
                            </td>
                    </tr>
                    <tr>
                        <td align="right">Tel&eacute;fono:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfTelefono" id="dfTelefono" value="" size="10" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Mail:</td>
                        <td align="left">
                            <input class="textfield_m" type="text" name="dfMail" id="dfMail" value="" size="50" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 5px; text-align: right">
                            <form id="IngNuevoUsr" action="usuarios.php" method="POST">
                            <input type="hidden" name="dfParametro" id="dfParametro" value="" />
                            <input type="hidden" name="dfCodClt" id="dfCodClt" value="<?php echo $Cod_Clt ?>" />
                            <input type="hidden" name="dfCodSuc" id="dfCodSuc" value="<?php echo $Cod_Suc ?>" />
                            <input type="hidden" name="dfCodNtaVta" id="dfCodNtaVta" value="<?php echo $Cod_Nvt ?>" />
                            <input type="hidden" name="tipovta" id="tipovta" value="<?php echo $tipo ?>" />
                            <input type="button" value="Agregar" name="Agregar" onclick="ValidarDatosUsr()"/>
                            <input type="button" value="Cancelar" name="Cancelar" onclick="HideAgregar();"/>
                            </form>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <div width="99%" align="center" style="padding-top: 10px; text-align: right">
            <form name="frmAgregar" id="frmAgregar" action="principal.php" method="POST">
                <input type="hidden" name="dfrutclt" id="dfrutclt" value="<?php echo $Rut_Clt ?>" />
                Importar Usuarios (<a href="#">Ver Formato</a>): 
                <input type="file" name="documento" id="documento" onchange="fichero.value = this.value"/>
                <input type="hidden" name="fichero"/>
                <input type="button" value="Agregar Usuario" name="Agregar" onclick="MostrarAgregar();"/>
                <input type="button" value="Continuar" name="Continuar" onclick="ContinuaPreVenta();"/>
                <input type="submit" value="Volver" name="volver" />
                <input type="hidden" name="dfCodCltKit" id="dfCodCltKit" value="<?php echo $Cod_Clt ?>" />
                <input type="hidden" name="dfCodSucKit" id="dfCodSucKit" value="<?php echo $Cod_Suc ?>" />
                <input type="hidden" name="dfCodNtaVtaKit" id="dfCodNtaVtaKit" value="<?php echo $Cod_Nvt ?>" />
            </form>
        </div>
        
        <script type="text/javascript">
            $("#IngUsr").hide();
        </script>
        
    </body>
</html>
