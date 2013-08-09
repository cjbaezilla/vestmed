<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("../config.php");

	$EsInstitucional = false;
    $doc_id = 1;
    if (isset($_POST['dfRutClt'])) {  
	    $RutClt = ok($_POST['dfRutClt']);
		$contexto = isset($_GET['contexto']) ? $_GET['contexto'] : "";
	  
	    $result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutClt'", $db) or die ("No se pudo leer datos del Cliente");
        if ($row = mssql_fetch_array($result)) $xis = 1; else $xis = 0;
		mssql_free_result($result); 
		mssql_close ($db);
		
		if ($xis == 0) 
			header("Location: registrarse.php?clt=".$RutClt."&xis=".$xis); 
		else 
		    if ($contexto == "mnu")
				header("Location: registrarse.php?accion=closeedt&clt=".$RutClt."&xis=1"); 
			else
				header("Location: registrarse.php?accion=close&clt=".$RutClt."&xis=1"); 
	}
	mssql_close ($db);
?>
