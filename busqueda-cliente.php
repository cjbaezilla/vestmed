<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("config.php");

	$EsInstitucional = false;
    $doc_id = 1;
    if (isset($_POST['dfRutClt'])) {  
	    $RutClt = ok($_POST['dfRutClt']);
	  
	    $result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutClt'", $db) or die ("No se pudo leer datos del Cliente");
        if ($row = mssql_fetch_array($result)) {
		   $xis = 1; 
		   $cod_tipper = $row['Cod_TipPer'];
		}
		else {
		   $xis = 0;
		   $cod_tipper = (ok($_POST['rbTipoClt']) == 2 ? 1 : 2);
		}
		mssql_free_result($result); 
		mssql_close ($db);
		
		if ($cod_tipper == 1)
		   header("Location: regnatural.php?clt=".$RutClt."&xis=".$xis); 
		else 
		   header("Location: regempresa.php?clt=".$RutClt."&xis=".$xis); 
	}
	mssql_close ($db);
?>
