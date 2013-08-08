<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$rut = $_POST['dfrutclt'];
$rut = str_replace(".", "", $rut);

$Num_Cot = 0;
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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Histórico Compras</title>
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        <link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
        <link href="css/itunes.css" type="text/css" rel="stylesheet" />
        <!-- Lytebox Includes //-->
        <script type="text/javascript" src="../lytebox/lytebox.js"></script>
        <link rel="stylesheet" type="text/css" href="../lytebox/lytebox.css" media="screen" />
        <!-- Lytebox Includes //-->
        
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
            <br></br>            
            
<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="99%" align="center" class="tabular">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="30%" class="tabular">Direcciones Registradas</th>
                    <th scope="column" width="15%" class="tabular">Comuna</th>
                    <th scope="column" width="15%" class="tabular">Ciudad</th>
                    <th scope="column" width="10%" class="tabular">Fono</th>
                    <th scope="column" width="10%" class="tabular">Celular</th>
                    <th scope="column" width="20%" class="tabular">Mail</th>
                </tr>
            </thead>
            <tbody>
<?php
        $result = mssql_query("vm_suc_s $cod_clt",$db);
        while (($row = mssql_fetch_array($result))) {
            $cod_suc   = $row['Cod_Suc'];
            $dir_suc   = $row['Dir_Suc'];
            $fon_ctt   = $row['Fon_Suc'];
            
            $cod_cmn   = $row['Cod_Cmn'];
            $nom_cmn   = $row['Nom_Cmn'];
            
            $cod_cdd   = $row['Cod_Cdd'];
            $nom_cdd   = $row['Nom_Cdd'];
            
            $cod_rgn   = $row['Cod_Rgn'];
            $nom_rgn   = $row['Nom_Rgn'];
            				
            $mail_ctt = "";
            
            $result2 = mssql_query("vm_ctt_s $cod_clt, $cod_suc",$db);
            while (($row2 = mssql_fetch_array($result2))) {
                $cel_ctt   = $row2['Cel_Ctt'];
                $mail_ctt  = $row2['Mail_Ctt'];
                $fon_ctt   = $row2['Fon_Ctt'];
                break;
            }
?>
<tr  class="tabular">
	<td class="tabular"><?php echo utf8_encode($dir_suc) ?></td>
	<td class="tabular"><?php echo utf8_encode($nom_cmn) ?></td>
	<td class="tabular"><?php echo utf8_encode($nom_cdd) ?></td>
	<td class="tabular"><?php echo utf8_encode($fon_ctt) ?></td>
	<td class="tabular"><?php echo utf8_encode($cel_ctt) ?></td>
	<td class="tabular"><?php echo utf8_encode($mail_ctt) ?></td>
</tr>
<?php
        }
?>
            </tbody>
</table>
    
    
        </div>
        <table class="tabular" id="TblInforme"  border="0" style="width:99%;" align="center">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="5%" class="tabular">Fuente</th>
                    <th scope="column" width="5%" class="tabular">Tipo<br/>Documento</th>
                    <th scope="column" width="5%" class="tabular">N&uacute;mero<br/>Documento</th>
                    <th scope="column" width="5%" style="text-align: center" class="tabular">Fecha</th>
                    <th scope="column" width="5%" class="tabular">Marca</th>
                    <!--th scope="column" width="20%" class="tabular">Nombre</th-->
                    <th scope="column" class="tabular">Glosa</th>
                    <th scope="column" width="7%" class="tabular">Style</th>
                    <th scope="column" width="7%" class="tabular">Patron</th>
                    <th scope="column" width="3%" class="tabular">Talla</th>
                    <th scope="column" width="5%" style="text-align: center" class="tabular">Ctd</th>
                    <th scope="column" width="3%" style="text-align: center" class="tabular">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                $result = mssql_query("vm_pvw_hiscmp '$rut'",$db) or die ("error sql");
                while (($row = mssql_fetch_array($result))) {                
                    $i++;
                ?>
                <tr class="tabular">
                    <td class="tabular"><?php echo utf8_encode($row['TipCom']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['TipDoc']) ?></td>
                    <td class="tabular" style="text-align: center"><?php echo utf8_encode($row['Numero']) ?></td>
                    <td class="tabular" style="text-align: center"><?php echo utf8_encode($row['Fecha']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Cod_Mca']) ?></td>
                    <!--td class="tabular"><?php echo utf8_encode(str_replace("#","'",$row['Nom_Dsg'])) ?></td-->
                    <td class="tabular"><?php echo utf8_encode(str_replace("#","'",$row['Glosa'])) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Cod_Sty']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Key_Pat']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Val_Sze']) ?></td>
                    <td class="tabular" style="text-align: center"><?php echo intval($row['Val_Ctd']) ?></td>
                    <td class="tabular" style="text-align: center">
                        <?php if ($row['Cod_GrpPrd'] > 0) { ?>
                    <a rev="width: 630px; height: 490px; border: 0 none; scrolling: auto;" rel="lyteframe[imagenes<?php echo $i; ?>]" href="comprashis.php?producto=<?php echo $row['Cod_GrpPrd']; ?>">
                        <img src="../images/info.png" alt="" title="Ver Detalle" />
                    </a>
                        <?php } else { ?>
                        &nbsp;
                        <?php } ?>
                    </td>
                </tr>
                <?php
                } 
                if ($i == 0) {
                ?>
                <tr class="tabular">
                    <td class="tabular" colspan="12" style="padding-top: 10px; padding-bottom: 10px; text-align: center">CLIENTE NO TIENE COMPRAS REGISTRADAS EN EL SISTEMA</td>
                </tr>                
                <?php
                }
                ?>
            </tbody>
        </table>
        <br/>
        <?php
            $i = 0;
            $RutAsociados = "";
            $result = mssql_query("vm_get_emp_erp '$rut'",$db) or die ("error sql");
            while (($row = mssql_fetch_array($result))) {                
                $RutAsociados[$i] = $row['CodLegal'];
                if ($i++ == 0) {
        ?>
        <div style="text-align: center; width: 1200">
        <h2>Empresas Asociadas</h2>
        <table class="tabular" id="TblInforme"  border="0" style="width:99%;" align="center">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="10%" class="tabular">RUT</th>
                    <th scope="column" width="50%" class="tabular">Razon Social</th>
                    <th scope="column" width="30%" class="tabular">Direcci&oacute;n</th>
                    <th scope="column" width="10%" class="tabular">Comuna</th>
                </tr>
            </thead>
            <tbody>        
        <?php
                }
        ?>
                <tr class="tabular">
                    <td class="tabular"><?php echo formatearRut($row['CodLegal']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['RazonSocial']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Dir_Suc']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Nom_Cmn']) ?></td>
                </tr>
        <?php
            }
            if ($i > 0) {
        ?>
            </tbody>
        </table>
        </div>
        <div style="text-align: center; width: 1200">
        <?php
            for ($j = 0; $j < $i; $j++) {
                $rutemp = $RutAsociados[$j];
                $nom_clt = "?????";
                $result = mssql_query("vm_s_per_tipdoc 1, '$rutemp'",$db);
                if (($row = mssql_fetch_array($result))) {
                    $cod_tipper = $row['Cod_TipPer'];
                    if ($cod_tipper == 1)
                       $nom_clt = $nom_ctt = $row['Nom_Per']." ".$row['Pat_Per']." ".$row['Mat_Per']; 
                    else
                       $nom_clt = $row['RznSoc_Per'];
                }
        ?>
            <h2>Compras asociadas a <?php echo $nom_clt; ?></h2>
        <table class="tabular" id="TblInforme"  border="0" style="width:99%;" align="center">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="5%" class="tabular">Fuente</th>
                    <th scope="column" width="5%" class="tabular">Tipo<br/>Documento</th>
                    <th scope="column" width="5%" class="tabular">N&uacute;mero<br/>Documento</th>
                    <th scope="column" width="5%" style="text-align: center" class="tabular">Fecha</th>
                    <th scope="column" width="5%" class="tabular">Marca</th>
                    <!--th scope="column" width="20%" class="tabular">Nombre</th-->
                    <th scope="column" class="tabular">Glosa</th>
                    <th scope="column" width="7%" class="tabular">Style</th>
                    <th scope="column" width="7%" class="tabular">Patron</th>
                    <th scope="column" width="3%" class="tabular">Talla</th>
                    <th scope="column" width="5%" style="text-align: center" class="tabular">Ctd</th>
                    <th scope="column" width="3%" style="text-align: center" class="tabular">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $k = 0;
                $result = mssql_query("vm_pvw_hiscmp '$rutemp'",$db) or die ("error sql");
                while (($row = mssql_fetch_array($result))) {                
                    $k++;
                ?>
                <tr class="tabular">
                    <td class="tabular"><?php echo utf8_encode($row['TipCom']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['TipDoc']) ?></td>
                    <td class="tabular" style="text-align: center"><?php echo utf8_encode($row['Numero']) ?></td>
                    <td class="tabular" style="text-align: center"><?php echo utf8_encode($row['Fecha']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Cod_Mca']) ?></td>
                    <!--td class="tabular"><?php echo utf8_encode(str_replace("#","'",$row['Nom_Dsg'])) ?></td-->
                    <td class="tabular"><?php echo utf8_encode(str_replace("#","'",$row['Glosa'])) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Cod_Sty']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Key_Pat']) ?></td>
                    <td class="tabular"><?php echo utf8_encode($row['Val_Sze']) ?></td>
                    <td class="tabular" style="text-align: center"><?php echo intval($row['Val_Ctd']) ?></td>
                    <td class="tabular" style="text-align: center">
                        <?php if ($row['Cod_GrpPrd'] > 0) { ?>
                    <a rev="width: 630px; height: 490px; border: 0 none; scrolling: auto;" rel="lyteframe[imagenes<?php echo $i; ?>]" href="comprashis.php?producto=<?php echo $row['Cod_GrpPrd']; ?>">
                        <img src="../images/info.png" alt="" title="Ver Detalle" />
                    </a>
                        <?php } else { ?>
                        &nbsp;
                        <?php } ?>
                    </td>
                </tr>
                <?php
                } 
                if ($k == 0) {
                ?>
                <tr class="tabular">
                    <td class="tabular" colspan="12" style="padding-top: 10px; padding-bottom: 10px; text-align: center">CLIENTE NO TIENE COMPRAS REGISTRADAS EN EL SISTEMA</td>
                </tr>                
                <?php
                }
                ?>
            </tbody>
        </table>
        <?php
            }
        ?>
        </div>
        <?php 
            } 
        ?>
        <div width="99%" align="center" style="padding-top: 50px">
            <form name="consulta" action="principal.php" method="POST">
            <input type="submit" value="Volver" name="volver" />
            <input type="button" value="Imprimir" name="Imprimir" onclick="window.print();"/>
            </form>
        </div>

        <?php
        // put your code here
        ?>
    </body>
</html>
