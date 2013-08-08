<?php 
    include("../config.php");
	
	$Cod_Clt = ok($_GET['clt']);
	$ret = isset($_GET['ret']) ? ok($_GET['ret']) : 0;
	
	foreach ($_POST as $key => $value) 
		if ($key == "seleccionadoSuc")
			foreach ($value as $key2 => $Cod_Suc)
				$result = mssql_query ("vm_suc_d $Cod_Clt, $Cod_Suc", $db)
				            or die ("No se pudo eliminar la sucursal");

	$result = mssql_query ("vm_cli_s $Cod_Clt", $db);
	if ($row = mssql_fetch_array($result)) $RutClt = $row['Num_Doc'];

	mssql_close ($db);
	
	if ($ret == 0)
		header("Location: editar.php?accion=close&clt=".$RutClt."&xis=1"); 
	else
		header("Location: escritorio_edtclt.php?clt=".$RutClt); 
	
?>
