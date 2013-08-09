<?php
	include("config.php");
	
	$caso = (isset($_GET['filter'])) ? intval(ok($_GET['filter'])) : 1;
	$flag = (isset($_GET['flag'])) ? intval(ok($_GET['filter'])) : 0;
	
	$Ctd_Prd = "";
	$Cod_Sze = "";
	foreach ($_POST as $key => $value) {
		//echo "key = $key, value = $value<BR>\n";
		if ($key == "dfCod_Sty") $Cod_Sty = $value;
		if ($key == "dfCod_Mca") $Cod_Mca = $value;
		if ($key == "dfCod_Pat") $Cod_Pat = $value;
		if ($key == "dfFec_Mov") $Fec_Mov = $value;
		if ($key == "dfNum_Odr") $Num_Odr = $value;
		if (left($key,8) == "dfSzeBuy") {
			if ($value != "") {
				$Cod_Sze = right($key,strlen($key)-8);
				$Ctd_Prd = intval($value);
				if (!isset($aListaBuy))
					$aListaBuy = array ($Cod_Sze => $Ctd_Prd);
				else
					$aListaBuy[$Cod_Sze] = $Ctd_Prd;
			}
		}
		else if (left($key,5) == "dfSze") {
			if ($value != "") {
				$Cod_Sze = right($key,strlen($key)-5);
				$Ctd_Prd = intval($value);
				if (!isset($aListaTallas))
					$aListaTallas = array ($Cod_Sze => $Ctd_Prd);
				else
					$aListaTallas[$Cod_Sze] = $Ctd_Prd;
			}
		}
		else if (($caso == 1 || $caso == 3 || $caso == 20) && left($key,6) == "TxtSze") {
			if ($value != "") {
				$Cod_Sze = right($key,strlen($key)-6);
				$Ctd_Prd = intval($value);
				if (!isset($aListaTallas))
					$aListaTallas = array ($Cod_Sze => $Ctd_Prd);
				else
					$aListaTallas[$Cod_Sze] = $Ctd_Prd;
			}
		}
	}
	
	if ($caso == 1) {
		$name_sp = "vm_odr_i";
		$pageretorno = "reponer.php?cod=$Num_Odr&fec=$Fec_Mov";
	}
	else if ($caso == 2 or $caso == 20) {
		$name_sp = "vm_odr_i_rep";
		$pageretorno = "compra.php?cod=$Num_Odr&fec=$Fec_Mov&filter=$caso";
	}
	else if ($caso == 3) {
		$name_sp = "vm_odc_i";
		$pageretorno = "detcompra.php?cod=$Num_Odr&fec=$Fec_Mov";
	}
	
	$result = mssql_query("vm_dsgsty_s '$Cod_Sty','$Cod_Mca'", $db);
	if ($row = mssql_fetch_array($result)) {
		$Cod_Dsg = $row['Cod_Dsg'];
		//echo "Cod_Dsg = $Cod_Dsg<BR>\n";
		if (isset($aListaTallas))
			foreach ($aListaTallas as $Cod_Sze => $Ctd_Prd) {
				if ($caso == 1) 
					$result = mssql_query($name_sp." '$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd,'$flag','U',NULL,NULL,$Num_Odr", $db);
				else if ($caso == 2) 
					$result = mssql_query($name_sp." $Num_Odr, '$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',0,$Ctd_Prd", $db);
				else if ($caso == 20) 
					$result = mssql_query($name_sp." $Num_Odr, '$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd,0", $db);
				else if ($caso == 3) 
					$result = mssql_query($name_sp." $Num_Odr, '$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd", $db);
			}
		if ($caso == 2)
			if (isset($aListaBuy))
				foreach ($aListaBuy as $Cod_Sze => $Ctd_Prd) 
					$result = mssql_query("vm_odr_i_buy $Num_Odr, '$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd", $db);
	}
	
	header("Location: ".$pageretorno); 	
?>
