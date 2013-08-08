<?php
ini_set('display_errors', '0');
session_start();
include("config.php");

$p_grpprd = ok($_GET['producto']);

$result = mssql_query("vm_strinv_prodinfo '".$p_grpprd."'", $db);
if (($row = mssql_fetch_array($result))) {
    $cod_sty = $row['style'];
    $cod_mca = $row['marca'];
    $cod_grppat = $row['Cod_GrpPat'];    
}

header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past    
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Transfer-Encoding: none');
header('Content-Type: application/vnd.ms-excel'); // This should work for IE & Opera
header('Content-type: application/x-msexcel'); // This should work for the rest
header('Content-Disposition: attachment; filename="nombre.xls"');
?>
<table border="0" cellpading="0" cellspacing="0" align="center" width="100%" id="productos">
    <thead>
        <tr>
            <th>Bodega</th>
            <th>StockP</th>
            <th>producto</th>
            <th>Style</th>
            <th>Pat</th>
            <th>Sze</th>
            <th>glosa</th>
            <th>StockMin</th>
            <th>StockMax</th>
            <th>Precio</th>
            <th>Unidad</th>
        </tr>
    </thead>
    <tbody>

<?php
    $hoy = date('Ymd');
    //echo "sp_getstock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy', '$p_pat','$p_sze'";
    $result = mssql_query ("sp_getstock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy', '$p_pat', '$p_sze'", $db) 
                   or die ("No pudo obtener datos del STOCK");
    while (($row = mssql_fetch_array($result))) {
?>
        <tr>
            <td align="center"><?php echo $row['Bodega'] ?></td>
            <td align="center"><?php echo number_format($row['StockP'], 0, ",", ".") ?></td>
            <td><?php echo $row['producto'] ?></td>
            <td><?php echo $row['Cod_Sty'] ?></td>
            <td><?php echo $row['Key_Pat'] ?></td>
            <td><?php echo $row['Val_Sze'] ?></td>
            <td><?php echo $row['glosa'] ?></td>
            <td><?php echo number_format($row['StockMinimo'], 0, ",", "."); ?></td>
            <td><?php echo number_format($row['StockMaximo'], 0, ",", "."); ?></td>
            <td><?php echo number_format($row['precio'], 0, ",", "."); ?></td>
            <td align="center"><?php echo $row['unidad'] ?></td>
        </tr>                
<?php                
    }
?>
    </tbody>
</table>
