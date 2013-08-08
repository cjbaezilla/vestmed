<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("global_cot.php");

	$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";

	$result = mssql_query("vm_i_ocweb $UsrId",$db);
	if ($row = mssql_fetch_array($result)) {
		$Cod_Nvt = $row['Cod_Nvt'];
		foreach ($_POST as $key => $value) {
			//echo $key." --> ".$value."<BR>";
			if ($key == "seleccionPrd") {
				foreach ($value as $key2 => $value2) {
					echo $key2." --> ".$value2."=";
					echo $_POST["dfCtd$value2"]."/";
					echo $_POST["Neto$value2"]."<BR>";
				}
			}
		}
	}
	//header("Location: aviso.php?id=".$Cod_Per."&idmsg=4"); 	
?>
