<?php
	include("config.php");
	
	$Ctd_Prd = "";
	$Cod_Sze = "";
	foreach ($_POST as $key => $value) {
		//echo "key = $key, value = $value<BR>\n";
		if ($key == "dfCod_Sty") $Cod_Sty = $value;
		if ($key == "dfCod_Mca") $Cod_Mca = $value;
		if ($key == "dfCod_Pat") $Cod_Pat = $value;
		if ($key == "dfFec_Mov") $Fec_Mov = $value;
		if (left($key,5) == "dfSze")
			if ($value != "") {
				$Cod_Sze = right($key,strlen($key)-5);
				$Ctd_Prd = intval($value);
				if (!isset($aListaTallas))
					$aListaTallas = array ($Cod_Sze => $Ctd_Prd);
				else
					$aListaTallas[$Cod_Sze] = $Ctd_Prd;
			}
	}
	$result = mssql_query("vm_dsgsty_s '$Cod_Sty','$Cod_Mca'", $db);
	if ($row = mssql_fetch_array($result)) {
		$Cod_Dsg = $row['Cod_Dsg'];
		//echo "Cod_Dsg = $Cod_Dsg<BR>\n";
		foreach ($aListaTallas as $Cod_Sze => $Ctd_Prd) {
			//echo "Cod_Sze = $Cod_Sze<BR>\n";
			//echo "Ctd_Prd = $Ctd_Prd<BR>\n";
			$result = mssql_query("vm_bus_prd '$Cod_Dsg','$Cod_Pat','$Cod_Sze'", $db);
			if ($row = mssql_fetch_array($result)) {
				$Cod_Prd = $row['Cod_Prd'];
				$result = mssql_query("vm_odc_i '$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze','$Cod_Prd','$Cod_Mca',$Ctd_Prd", $db);
			}
		}
	}
	
	$fecfmt = fechafmt($Fec_Mov);
	header("Location: ocompra.php?fec=$fecfmt"); 	
?>
