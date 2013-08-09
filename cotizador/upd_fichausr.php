<?php 
    include("../config.php");

	$ret = isset($_GET['ret']) ? ok($_GET['ret']) : 0;
	$suc = isset($_GET['suc']) ? ok($_GET['suc']) : 0;
    if (isset($_POST['dfRutClt']) && trim($_POST['dfRutClt']) != "") {  
	    $RutClt = strtoupper($_POST['dfRutClt']);
		$doc_id = 1;
		
	    $result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutClt'", $db)
				            or die ("No se pudo leer datos del Cliente");
        if ($row = mssql_fetch_array($result)) {
			$Cod_Per 	= $row['Cod_Per'];
			$Cod_TipPer = $row['Cod_TipPer'];
			$Gro_Per    = $row['Gro_Per'];
			$Cod_Pro	= null;
			$Cod_Esp	= null;
			
			foreach ($_POST as $key => $value) {
				//echo $key." --> ".$value."<BR>";
				if ($key == "dfAppPat") 	 	$AppPat    = str_replace("'", "&#39;", strtoupper($value));
				if ($key == "dfAppMat") 	 	$AppMat    = str_replace("'", "&#39;", strtoupper($value));
				if ($key == "dfNomPer") 	 	$NomPer    = str_replace("'", "&#39;", strtoupper($value));
				if ($key == "codpro") 		 	$Cod_Pro   = intval($value);
				if ($key == "codesp") 		 	$Cod_Esp   = intval($value);
				if ($key == "rbSexo") 		 	$nSex   	= intval($value);
				
				if ($key == "dfDireccion") 	    $Direccion = str_replace("'", "&#39;", strtoupper($value));
				if ($key == "dfNombre") 	 	$Nombre    = $value;
				if ($key == "dfNombreFantasia") $NombreFan = $value;
				if ($key == "dfWeb") 		 	$Web   	   = $value;
			}
			
			$result = mssql_query("vm_per_u $Cod_Per, $Cod_TipPer, $doc_id, '$RutClt','$AppPat', '$AppMat', '$NomPer',
											'$Nombre', '$NombreFan', $Cod_Pro, $Cod_Esp, $nSex, '$Web', '$Gro_Per'", $db)
								  or die ("No se pudo actualizar datos del Cliente");
		}
	}
	
	mssql_close ($db);
	
	if ($ret == 0)
		header("Location: editar.php?accion=close&clt=".$RutClt."&xis=1&suc=".$suc); 
	else
		header("Location: escritorio_edtclt.php?clt=".$RutClt."&suc=".$suc); 
	
?>
