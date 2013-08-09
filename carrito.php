<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("config.php");

	$cod_per = 0;
	$cod_clt = 0;
	$cod_cot = 0;
	if (isset($_SESSION['CodPer'])) $cod_per = intval($_SESSION['CodPer']);
	if (isset($_SESSION['CodClt'])) $cod_clt = intval($_SESSION['CodClt']);
	if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);
	
	foreach ($_POST as $key => $value) {
            //echo $key." --> ".$value."<BR>";
            if ($key == "cantidad")	$cantidad  = intval($value);
            if ($key == "dfDsg")	$cod_dsg   = $value;
            if ($key == "dfSze")	$val_sze   = $value;
            if ($key == "dfPat")	$cod_pat   = $value;
            if ($key == "dfPrd")	$cod_prd   = $value;
            if ($key == "dfTitle")	$cod_title = $value;
	}


        $IVA = 0.0;
        $result = mssql_query("vm_getfolio_s 'IVA'",$db);
        if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;
        
        $prc_prd = 0.0;
        $result = mssql_query ("vm_s_dsg '$cod_dsg'", $db) or die ("No pudo obtener datos de DSG");
	if (($row = mssql_fetch_array($result))) {
            $cod_mca = $row["Cod_Mca"];
            $cod_sty = $row["Cod_Sty"];

            $result = mssql_query ("vm_s_pat '$cod_dsg', '$cod_pat'", $db) or die ("No pudo obtener datos de PAT");
            if (($row = mssql_fetch_array($result))) {
                $cod_grppat = $row["Cod_GrpPat"];
                $hoy = date('Ymd');
                $result = mssql_query ("BDFlexline..sp_stock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy'", $db) 
                               or die ("No pudo obtener datos del STOCK");
                
                if (($row = mssql_fetch_array($result))) $prc_prd = $row["precio"] / (1 + $IVA);
            }
        }
        
	if ($cod_cot > 0)
            $result = mssql_query ("vm_i_cotweb $cod_per, $cod_clt, $cantidad, '$cod_dsg', '$val_sze', '$cod_pat', '$cod_prd', $prc_prd, $cod_cot", $db)
                                   or die ("No se pudo agregar a la cotizacion<BR>"."vm_i_cotweb $cod_per, $cod_clt, $cantidad, '$cod_dsg', '$val_sze', '$cod_pat', '$cod_prd', $prc_prd, $cod_cot");
	else
            $result = mssql_query ("vm_i_cotweb $cod_per, $cod_clt, $cantidad, '$cod_dsg', '$val_sze', '$cod_pat', '$cod_prd', $prc_prd", $db)
                                   or die ("No se pudo insertar la cotizacion<BR>"."vm_i_cotweb $cod_per, $cod_clt, $cantidad, '$cod_dsg', '$val_sze', '$cod_pat', '$cod_prd', $prc_prd");
		
	if (($row = mssql_fetch_array($result)))
		$cod_cot = $row["cod_cot"];
	
	if (!isset($_SESSION['CodCot'])) $_SESSION['CodCot'] = $cod_cot;     
	
	eliminarLastLevelSession();
	header("Location: detalle-producto.php?producto=".$cod_prd."&title=".$cod_title);
?>
