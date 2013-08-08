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
            function AgregarUsr() {
                $("form#frmAgregar").attr('action', 'usuarios.php');
                $("form#frmAgregar").submit();
            }
            
            function SiguientePaso() {
                $("form#frmAgregar").attr('action', 'despacho.php');
                $("form#frmAgregar").submit();
            }
            
            function setTipDocSii(obj) {
                    if (obj.value == 1)
                        $("#datos_factura").hide("slow");
                    else
                        $("#datos_factura").show("slow");
            }
        </script>
    </head>
    <body>
        <div style="padding: 10px; text-align: center">
            <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
            <tr>
                    <td class="dato" width="25%" style="text-align: right"><INPUT name="rbDocumento[]" type="radio" class="button2" value="1" onclick="setTipDocSii(this)" /></td>
                    <td class="dato" width="25%" align="left">Boleta</td>
                    <td class="dato" width="25%" style="text-align: right"><INPUT name="rbDocumento[]" type="radio" class="button2" value="2" onclick="setTipDocSii(this)" /></td>
                    <td class="dato" width="25%" align="left">Factura</td>
            </tr>
            </table>
        </div>
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
                        $result = mssql_query("vm_s_rutfct $Cod_Clt", $db);
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
                                $result = mssql_query("vm_s_rutfct $Cod_Clt, $Cod_PerFct", $db);
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
                                                <td align="left"><input name="NumDocFct" id="NumDocFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $numdoc; ?>" /></td>
                                        </tr>
                                        <tr>
                                                <td><b>Raz&oacute;n Social</b></td>
                                                <td align="left"><input name="NomCltFct" id="NomCltFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $nomclt; ?>" /></td>
                                        </tr>
                                        <tr>
                                                <td><b>Nombre de Fantas&iacute;a</b></td>
                                                <td align="left"><input name="NomFanFct" id="NomFanFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $nomfan; ?>" /></td>
                                        </tr>
                                        <tr>
                                                <td><b>Direcci&oacute;n Casa Matriz</b></td>
                                                <td align="left"><input name="DirFctFct" id="DirFctFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $dirfct; ?>" /></td>
                                        </tr>
                                        <tr>
                                                <td><b>Comuna</b></td>
                                                <td align="left"><input name="NomCmnFct" id="NomCmnFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $nomcmn; ?>" /></td>
                                        </tr>
                                        <tr>
                                                <td><b>Ciudad</b></td>
                                                <td align="left"><input name="NomCddFct" id="NomCddFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $nomcdd; ?>" /></td>
                                        </tr>
                                        <tr>
                                                <td><b>Tel&eacute;fono</b></td>
                                                <td align="left"><input name="FonFctFct" id="FonFctFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $fonfct; ?>" /></td>
                                        </tr>
                                        <tr>
                                                <td><b>FAX</b></td>
                                                <td align="left"><input name="FaxFctFct" id="FaxFctFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $faxfct; ?>" /></td>
                                        </tr>
                                        <tr>
                                                <td><b>P&aacute;gina Web</b></td>
                                                <td align="left"><input name="WebFctFct" id="WebFctFct" type="text" class="dato" size="35" readonly="true" value="<?php echo $webfct; ?>" /></td>
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
            $("#datos_factura").hide();
        </script>
    </body>
</html>
