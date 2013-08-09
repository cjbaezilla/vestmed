<?php
	include("config.php");
	
	if (isset($_POST['Fec_Odc']) && isset($_POST['Num_Odc'])) {
		$Fec_Odc = ok($_POST['Fec_Odc']);
		$Num_Odc = ok($_POST['Num_Odc']);
		//echo "vm_crtodc $Num_Odr, '$Fec_Odr'";
		$result = mssql_query("vm_close_odc $Num_Odc, '$Fec_Odc'", $db);
		mssql_close ($db);
		header("Location: avisoodr.php?idmsg=2&cod=$Num_Odc&fec=$Fec_Odc"); 	
	}	
	else
		echo "Error, falta identificar Numero de Orden. Para continuar pinche <a href=\"ocompra.php\">aqu&iacute;</a>";
?>
