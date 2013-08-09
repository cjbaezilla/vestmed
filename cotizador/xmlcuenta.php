<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '1');
session_start();
include("global_cot.php");

if (isset($_GET['cod_odc'])){
    $Cod_Odc = intval(ok($_GET['cod_odc']));
}

header("Content-type: text/xml");
header("Cache-Control: no-cache");

echo "<?xml version=\"1.0\"?>\n";
echo "<rows>\n"; 
echo "<page>1</page>\n"; 
echo "<total>1</total>\n"; 

$tot_reg = 0;
$saldo = 0;
//$query = "vm_s_ctaodc $Cod_Odc";
$result = mssql_query("vm_s_ctaodc $Cod_Odc", $db);

while ($row = mssql_fetch_array($result)) {
        echo "<row id='".($tot_reg+1)."'>"; 
        //if ($row['ArcFis_Adj'] != ' '){
        //        echo "<cell>Ver Pago</cell>";
        //} else {
        //        echo "<cell>Ingresar Comprobante</cell>"; 
        //}

        echo "<cell>".$row['FecMov']."</cell>"; 
        if ($row['TipMov'] == 'D') {
            echo "<cell>".number_format($row['MtoMov'],0,',','.')."</cell>"; 
            $saldo-=$row['MtoMov'];
        }
        else
            echo "<cell></cell>";
        
        if ($row['TipMov'] == 'H') {
            echo "<cell>".number_format($row['MtoMov'],0,',','.')."</cell>"; 
            $saldo+=$row['MtoMov'];
        }
        else
            echo "<cell></cell>";
        
        echo "<cell>".number_format($saldo,0,',','.')."</cell>"; 
        
        //if ($row['Est_Pgo'] == -1) {
        //        echo "<cell>No Validado</cell>";
        //} else {
        //        echo "<cell>Aprobado</cell>";
        //}
        //echo "<cell>".$row['difer']."</cell>"; 
        
        if ($row['ArcFis_Adj'] != ' '){
                echo "<cell>".$row['ArcFis_Adj']."</cell>"; 
        }else{
                echo "<cell></cell>"; 
        }
        echo "</row>";
        $tot_reg++;
}


echo "<records>".$tot_reg."</records>\n"; 

echo "</rows>";

?>