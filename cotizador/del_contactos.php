<?php 
    include("../config.php");
	
	$Cod_Clt = ok($_GET['clt']);
	$Cod_Suc = ok($_GET['suc']);
	$ret = isset($_GET['ret']) ? ok($_GET['ret']) : 0;
	
	foreach ($_POST as $key => $value) 
		if ($key == "seleccionadoCtt")
			foreach ($value as $key2 => $Cod_Per)
				$result = mssql_query ("vm_ctt_d $Cod_Clt, $Cod_Suc, $Cod_Per", $db)
				            or die ("No se pudo eliminar los contactos");

	$result = mssql_query ("vm_cli_s $Cod_Clt", $db);
	if ($row = mssql_fetch_array($result)) $RutClt = $row['Num_Doc'];

	mssql_close ($db);
	
	if ($ret == 0)
		header("Location: editar.php?accion=close&clt=".$RutClt."&xis=1&suc=".$Cod_Suc); 
	else
		header("Location: escritorio_edtclt.php?clt=".$RutClt."&suc=".$Cod_Suc); 
	
?>
