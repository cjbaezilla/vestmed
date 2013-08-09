<?php
	include("config.php");
	
	$caso 	 = (isset($_GET['filter'])) ? intval(ok($_GET['filter'])) : 1;
	$flag 	 = 0;
	
	$Ctd_Prd = "";
	$Cod_Sze = "";
	switch ($caso) {
	case 7:
		$Num_Lst = 0;
		foreach ($_POST as $key => $value) {
			//echo "key = $key, value = $value<BR>\n";
			if ($key == "dfListOdr") {
				$ArrOdr = split(";", $value);
				foreach ($ArrOdr as $index => $Num_Odr) {
					if ($Num_Odr != "") {
						$result = mssql_query("vm_i_lstodr $Num_Odr, $Num_Lst", $db);
						if ($row = mssql_fetch_array($result)) $Num_Lst = $row['Num_Lst'];
					}
				}
				mssql_close ($db);
				header("Location: listado.php?cod=$Num_Lst"); 	
			}
		}
		break;
		
	case 6:
		$result = mssql_query ("vm_crtodc", $db);
		if ($row = mssql_fetch_array($result)) {
			$Num_Odc = $row['Num_Odc'];
			$Fec_Mov = $row['Fec_Odc'];
			mssql_close ($db);
			header("Location: detcompra.php?cod=$Num_Odc&fec=$Fec_Mov"); 	
		}
		break;
		
	case 5:
		$result = mssql_query ("vm_crtodr", $db);
		if ($row = mssql_fetch_array($result)) {
			$Num_Odr = $row['Num_Odr'];
			$Fec_Mov = $row['Fec_Odr'];
			mssql_close ($db);
			header("Location: reponer.php?cod=$Num_Odr&fec=$Fec_Mov"); 	
		}
		break;
		
	case 4:
		$result = mssql_query ("vm_crtodr", $db);
		if ($row = mssql_fetch_array($result)) {
			$Num_Odr = $row['Num_Odr'];
			$Fec_Mov = $row['Fec_Odr'];
			
			$result = mssql_query ("vm_odrhdr_u_est $Num_Odr, '$Fec_Mov', '2'", $db);
			mssql_close ($db);
			header("Location: compra.php?cod=$Num_Odr&fec=$Fec_Mov"); 	
		}
		break;
		
	default:
		foreach ($_POST as $key => $value) {
			//echo "key = $key, value = $value<BR>\n";
			if ($key == "dfCod_Sty") $Cod_Sty = $value;
			if ($key == "dfCod_Mca") $Cod_Mca = $value;
			if ($key == "dfCod_Pat") $Cod_Pat = $value;
			if ($key == "dfCod_Sze") $Cod_Sze = $value;
			if ($key == "dfFec_Mov") $Fec_Mov = $value;
			if ($key == "dfNum_Odr") $Num_Odr = $value;
			if ($key == "dfCod_Qty") $Ctd_Prd = $value;
			if ($key == "dfNum_Odc") $Num_Odr = $value;
			if ($key == "seleccionadof") {
				$Fec_Mov = date("Ymd");
				foreach ($value as $index => $value2) {
					$aValues = split("-", $value2);
					$Cod_Prd = $aValues[0];
					$Num_Lin = $aValues[1];
					$result = mssql_query("vm_prd_s '$Cod_Prd'", $db);
					if ($row = mssql_fetch_array($result)) {
						$Cod_Dsg = $row['Cod_Dsg'];
						$Cod_Pat = $row['Cod_Pat'];
						$Cod_Sze = $row['Cod_Sze'];
						$Cod_Mca = $row['Cod_Mca'];
						$Ctd_Prd = $_POST["dfCtd_Prd".$Num_Lin];
						$Cod_Nvt = $_POST["dfCod_Nvt".$Num_Lin];
						$Flg_Rep = isset($_POST["dfRsv".$Num_Lin]) ? "1" : "0";
						if ($caso == 1) {
							$result = mssql_query("vm_odr_i '$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd,'$Flg_Rep','A',$Cod_Nvt,$Num_Lin", $db);
							if ($row = mssql_fetch_array($result)) $Num_Odr = $row['Num_Odr'];
						}
					}
				}
				mssql_close ($db);
				//$fechafmt = right($fechain, 2)."/".substr($fechain, 4, 2)."/".left($fechain,4);
				header("Location: reponer.php?cod=$Num_Odr&fec=$Fec_Mov"); 	
				exit(0);
			}
		}
		//echo $Ctd_Prd." - ".$Cod_Sze."<BR>";
		$result = mssql_query("vm_dsgsty_s '$Cod_Sty','$Cod_Mca'", $db);
		if ($row = mssql_fetch_array($result)) {
			$Cod_Dsg = $row['Cod_Dsg'];
			if ($Ctd_Prd  != "" && $Cod_Sze != "") {
				$result = mssql_query("vm_bus_prd '$Cod_Dsg','$Cod_Pat','$Cod_Sze'", $db);
				if ($row = mssql_fetch_array($result)) {
					$Cod_Prd = $row['Cod_Prd'];
					if ($caso == 1) {
						$result = mssql_query("vm_odr_i '$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd,'$flag','U'", $db);
						mssql_close ($db);
						//$fecfmt = fechafmt($Fec_Mov);
						header("Location: reponer.php?cod=$Num_Odr&fec=$Fec_Mov"); 	
					}
					else if ($caso == 2) {
						//echo "vm_odr_i_rep $Num_Odr,'$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd";
						$result = mssql_query("vm_odr_i_rep $Num_Odr,'$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd", $db);
						mssql_close ($db);
						header("Location: compra.php?cod=$Num_Odr&fec=$Fec_Mov"); 				
					}
					else if ($caso == 3) {
						//echo "vm_odr_i_rep $Num_Odr,'$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd";
						$result = mssql_query("vm_odc_i $Num_Odr,'$Fec_Mov','$Cod_Dsg','$Cod_Pat','$Cod_Sze',$Ctd_Prd", $db);
						mssql_close ($db);
						header("Location: detcompra.php?cod=$Num_Odr&fec=$Fec_Mov"); 				
					}
					exit(0);
				}
			}
			else
				header("Location: grilla.php?dsg=$Cod_Dsg&pat=$Cod_Pat&fec=$Fec_Mov&filter=$caso&flag=$flag&cod=$Num_Odr");
		}
	}
?>
