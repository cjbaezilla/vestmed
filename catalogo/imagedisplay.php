<?php
include("../config.php");

$p_name = ok($_GET['name']);
$p_filter = ok($_GET['filter']);

//SELECTION
//$sp_query = mssql_init("vm_strimg_get", $db);
//$query = "SELECT DATALENGTH(Lgo_Mca) FROM Mca";
//mssql_bind($sp_query, "@p_name", &$p_name, SQLVARCHAR);
//mssql_bind($sp_query, "@p_filter", &$p_filter, SQLVARCHAR);
//$result = mssql_execute($sp_query);
$result = mssql_query("vm_strimg_get '".$p_name."','".$p_filter."'", $db);
header("Content-type: image/jpeg");
if (@mssql_result($result, 0, 0)!=null)
    echo @mssql_result($result, 0, 0);
else
    //Muestra una imagen en blanco si es que no existe en la BD
    echo file_get_contents("../images/blank.jpg");

?>
