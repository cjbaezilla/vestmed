<?php
	include("config.php");


	$result = mssql_query ("vm_crtodc", $db);
	if ($row = mssql_fetch_array($result)) {
		$Num_Odc = $row['Num_Odc'];
		$Fec_Mov = $row['Fec_Odc'];
		foreach ($_POST as $key => $value) 
			if ($key == "seleccionadof") {
				foreach ($value as $index => $value2) {
					$aValues = split("-", $value2);
					$Num_Odr = $aValues[0];
					$Num_Lin = $aValues[1];
					$Ctd_Rsv = 0;
					if (isset($_POST['dfCtd_Rsv'.$Num_Lin])) {
						$Ctd_Rsv = ok($_POST['dfCtd_Rsv'.$Num_Lin]);
						$Ctd_Buy = $Ctd_Rsv;
					}
					else
						$Ctd_Buy = ok($_POST['dfCtd_Buy'.$Num_Lin]);
					$result = mssql_query ("vm_odcdet_i $Num_Odc, '$Fec_Mov', $Num_Odr, $Num_Lin, $Ctd_Buy, $Ctd_Rsv", $db);
				}
			}
		mssql_close ($db);
		header("Location: detcompra.php?cod=$Num_Odc&fec=$Fec_Mov"); 	
	}
?>
