<?php

//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();

if (!isset($_SESSION['usuario'])) {
    if (!isset($_POST["usuario"])) header("Location: ../index.php");
    $_SESSION['usuario'] = $_POST["usuario"];     
}
$UsrId = (isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : "";

include("global_cot.php");

$OkOpc = false;
$sp = mssql_query("vm_seg_usr_opcmodweb '$UsrId'",$db) or die ("error sql, vm_seg_usr_opcmodweb '$UsrId'");
while (($row = mssql_fetch_array($sp))) 
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 12 && $row['CodUsr'] == $UsrId) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

$Cod_TipPer = ok($_GET['tipper']);

header("Content-Type: application/vnd.ms-excel");

header("Expires: 0");

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("content-disposition: attachment;filename=informe.xls");

echo "<table border=\"0\">";
echo "<tr>";
echo "<td>Tipo</td>";
echo "<td>Appellidos</td>";
echo "<td>Nombres</td>";
if ($Cod_TipPer == 2) echo "<td>Empresa</td>";
echo "<td>Mail</td>";
echo "<td>Sexo</td>";
echo "</tr>";

$sp = mssql_query("vm_listado_emailing $Cod_TipPer",$db) or die ("error sql, vm_listado_emailing $Cod_TipPer");
while (($row = mssql_fetch_array($sp))) {
    echo "<tr>";
    echo "<td>".$row["Tipo"]."</td>";
    echo "<td>".$row["AppPer"]."</td>";
    echo "<td>".$row["NomPer"]."</td>";
    if ($Cod_TipPer == 2) echo "<td>".$row["RznSoc_Per"]."</td>";
    echo "<td>".$row["Mail_Ctt"]."</td>";
    echo "<td>".$row["SexPer"]."</td>";
    echo "</tr>";
}

echo "</table>";

?>
