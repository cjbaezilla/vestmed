<?php
	include("config.php");
	
	if (isset($_POST['Fec_Odr']) && isset($_POST['Num_Odr'])) {
		$Fec_Odr = ok($_POST['Fec_Odr']);
		$Num_Odr = ok($_POST['Num_Odr']);
		//echo "vm_crtodc $Num_Odr, '$Fec_Odr'";
		//$result = mssql_query("vm_crtodc $Num_Odr, '$Fec_Odr'", $db);
		$result = mssql_query("vm_odrhdr_s $Num_Odr, '$Fec_Odr'", $db);
		if ($row = mssql_fetch_array($result)) {
			$Estado = intval($row['Est_Odr']);
			$Estado+=1;
			$result = mssql_query("vm_odrhdr_u_est $Num_Odr, '$Fec_Odr', '$Estado'", $db);
		}
		mssql_close ($db);
		header("Location: avisoodr.php?idmsg=1&cod=$Num_Odr&fec=$Fec_Odr"); 	
	}	
	else
		echo "Error, falta identificar Numero de Order. Pra continuar pinche <a href=\"oreponer.php\">aqu&iacute;</a>";
?>
