<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$resultado="";
if (isset($_POST['cot'])){
	$Cod_Cot = $_POST['cot'];
	try {
            //$query = "vm_u_anulaventa $Cod_Cot";
            $result = mssql_query("vm_u_anulaventa $Cod_Cot");
            $resultado ="success";
            $mensaje=$result;
	} catch (Exception $e) {
            $resultado="NOK_BD";
            $mensaje="Error en base de datos";
	}
}else{
	$resultado="NOK_DATA";
	$mensaje="Data enviada erroneamente";
}

echo '{"result":"'.$resultado.'","msg":"'.$mensaje.'"}';
?>