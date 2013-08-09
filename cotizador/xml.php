<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '1');
session_start();
include("global_cot.php");

if (isset($_GET['cod_cot'])){
	$Cod_Cot = intval(ok($_GET['cod_cot']));
}

header("Content-type: text/xml");
header("Cache-Control: no-cache");

echo "<?xml version=\"1.0\"?>\n";
echo "<rows>\n"; 
echo "<page>1</page>\n"; 
echo "<total>1</total>\n"; 

$tot_reg = 0;

$query = "vm_s_pgoodc $Cod_Cot";
//echo $query;
// cot=1386
//$sp = mssql_query("vm_s_pgoodc $Cod_Cot", $db);
$result = mssql_query("sp_s_pgoodc ".$Cod_Cot, $db);
//if (mssql_num_rows($result)==0) {
//    echo "<records>0</records>";
//} else {
//	echo "<records>".mssql_num_rows($result)."</records>"; 

while ($row = mssql_fetch_array($result)) {
        echo "<row id='".$row['Sec_Pgo']."'>"; 
        if ($row['ArcFis_Adj'] != ' '){
                echo "<cell>Ver Pago</cell>";
        } else {
                echo "<cell>Ingresar Comprobante</cell>"; 
        }

        echo "<cell>".$row['Mto_Nvt']."</cell>"; 
        echo "<cell>".$row['Mto_Cpt']."</cell>"; 
        echo "<cell>".$row['Fec_Pgo']."</cell>"; 
        if ($row['Est_Pgo'] == -1) {
                echo "<cell>No Validado</cell>";
        } else {
                echo "<cell>Aprobado</cell>";
        }

        echo "<cell>".$row['difer']."</cell>"; 
        if ($row['ArcFis_Adj'] != ' '){
                echo "<cell>".$row['ArcFis_Adj']."</cell>"; 
        }else{
                echo "<cell>IngPagoCot.php</cell>"; 
        }
        echo "</row>";
        $tot_reg++;
}


echo "<records>".$tot_reg."</records>\n"; 

//}

echo "</rows>";


?>