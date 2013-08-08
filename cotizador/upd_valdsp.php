<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$cod_trn    = isset($_GET['trn']) ? intval($_GET['trn']) : 0;

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

if ($cod_trn == 100) {
    $Cod_Crr    = intval(ok($_POST['crr']));
    $Cod_SvrCrr = intval(ok($_POST['svc']));
    $Cod_Rgn    = intval(ok($_POST['rgn']));
    $monto1     = floatval(ok($_POST['tramo1'])) / (1.0 + $IVA);
    $monto2     = floatval(ok($_POST['tramo2'])) / (1.0 + $IVA);
    $monto3     = floatval(ok($_POST['tramo3'])) / (1.0 + $IVA);
    $monto4     = floatval(ok($_POST['tramo4'])) / (1.0 + $IVA);
    $monto5     = floatval(ok($_POST['tramo5'])) / (1.0 + $IVA);
    $adicional  = floatval(ok($_POST['adicional'])) / (1.0 + $IVA);
    
    //$sql = "vm_setvaldsp $Cod_Crr, $Cod_SvrCrr, $Cod_Rgn, $monto1, $monto2, $monto3, $monto4, $monto5, $adicional";
    $result = mssql_query("vm_setvaldsp $Cod_Crr, $Cod_SvrCrr, $Cod_Rgn, $monto1, $monto2, $monto3, $monto4, $monto5, $adicional",$db);
}
else {
    $Cod_Crr    = intval(ok($_GET['crr']));
    $Cod_SvrCrr = intval(ok($_GET['svc']));
    $Cod_Rgn    = intval(ok($_GET['rgn']));
    
    $result = mssql_query("vm_getvaldsp $Cod_Crr, $Cod_SvrCrr, $Cod_Rgn",$db);
    while (($row = mssql_fetch_array($result))) {
        if ($row['Pes_Max'] == 1.5)  $tramo1 = $row['Prc_Dsp'] * (1 + $IVA);
        if ($row['Pes_Max'] == 3.0)  $tramo2 = $row['Prc_Dsp'] * (1 + $IVA);
        if ($row['Pes_Max'] == 6.0)  $tramo3 = $row['Prc_Dsp'] * (1 + $IVA);
        if ($row['Pes_Max'] == 10.0) $tramo4 = $row['Prc_Dsp'] * (1 + $IVA);
        if ($row['Pes_Max'] == 15.0) $tramo5 = $row['Prc_Dsp'] * (1 + $IVA);
        if ($row['Pes_Max'] == 9999) $adicional = $row['Prc_Dsp'] * (1 + $IVA);
        if (!isset($Nom_Rgn)) {
            $Nom_Rgn = utf8_encode($row['Nom_Rgn']);
            $Des_Crr = utf8_encode($row['Des_Crr']);
            $Des_SvcCrr = utf8_encode($row['Des_SvcCrr']);
        }
    } 
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
<link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
    function ActualizaPadre()
    {
        parent.opener.ActualizarDsp();
        window.close();
    }
</script>
</head>

<body>
<div id="body" style="width: 480px">
    <div id="work" style="width: 480px">

<?php formar_topbox ("100%%","center"); ?>
<p align="center">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
    <form ID="F1" method="post" name="F1" action="<?php echo $_SERVER['PHP_SELF'] ?>?trn=100">    
    <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="90%" ALIGN="center">
        <tr>
                <td class="titulo-producto" colspan="2" style="padding-bottom: 30px">Modificar Valores Despacho</td>
        </tr>
        <tr>
            <td width="200px" class=""><b>Carrier</b></td><td><?php echo $Des_Crr; ?></td>
        </tr>
        <tr>
            <td><b>Servicio</b></td><td><?php echo $Des_SvcCrr ?></td>
        </tr>
        <tr>
            <td><b>Regi&oacute;n</b></td><td><?php echo $Nom_Rgn; ?></td>
        </tr>
        <tr>
            <td><b>Precio hasta 1.5 Kg</b></td><td><input name="tramo1" type="text" onKeyPress="javascript:return SoloNumeros(event)" value="<?php echo number_format($tramo1,0,',',''); ?>" style="width: 100px" /></td>
        </tr>
        <tr>
            <td><b>Precio hasta 3.0 Kg</b></td><td><input name="tramo2" type="text" onKeyPress="javascript:return SoloNumeros(event)" value="<?php echo number_format($tramo2,0,',',''); ?>" style="width: 100px" /></td>
        </tr>
        <tr>
            <td><b>Precio hasta 6.0 Kg</b></td><td><input name="tramo3" type="text" onKeyPress="javascript:return SoloNumeros(event)" value="<?php echo number_format($tramo3,0,',',''); ?>" style="width: 100px" /></td>
        </tr>
        <tr>
            <td><b>Precio hasta 10.0 Kg</b></td><td><input name="tramo4" type="text" onKeyPress="javascript:return SoloNumeros(event)" value="<?php echo number_format($tramo4,0,',',''); ?>" style="width: 100px" /></td>
        </tr>
        <tr>
            <td><b>Precio hasta 15.0 Kg</b></td><td><input name="tramo5" type="text" onKeyPress="javascript:return SoloNumeros(event)" value="<?php echo number_format($tramo5,0,',',''); ?>" style="width: 100px" /></td>
        </tr>
        <tr>
            <td><b>Precio Kg Adicional</b></td><td><input name="adicional" type="text" onKeyPress="javascript:return SoloNumeros(event)" value="<?php echo number_format($adicional,0,',',''); ?>" style="width: 100px" /></td>
        </tr>
        <tr>
                <td style="text-align: right; padding-top: 30px" colspan="2">
                    <input type="submit" value="Guardar" style="width: 100px;"/>&nbsp;
                    <input type="button" value="Cerrar" onclick="window.close()" style="width: 100px;" />
                    <input type="hidden" name="crr" value="<?php echo $Cod_Crr; ?>" />
                    <input type="hidden" name="svc" value="<?php echo $Cod_SvrCrr; ?>" />
                    <input type="hidden" name="rgn" value="<?php echo $Cod_Rgn; ?>" />
                </td>
        </tr>
    </table>
    </form>
</td>
</tr>
</table>
    </p>
<?php formar_bottombox (); ?>
    </div>
</div>
<script type="text/javascript">
    <?php if ($cod_trn == 100) { ?>
    ActualizaPadre();
    <?php } ?>
</script>
</body>
</html>