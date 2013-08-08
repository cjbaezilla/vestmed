<?php 
	ini_set('display_errors', '0');
	session_start();
	include("config.php");

    $doc_id = 1;
	$Cod_Clt = intval($_SESSION['CodClt']);
	$Cod_Per = intval($_SESSION['CodPer']);
	
	$result = mssql_query("vm_per_s $Cod_Per");
	if ($row = mssql_fetch_array($result)) {
		$Cod_TipPer = $row['Cod_TipPer'];
		$doc_id     = $row['Cod_TipDoc'];
		$RutUsr     = $row['Num_Doc'];
		//$AppPat     = $row['Pat_Per'];
		//$AppMat     = $row['Mat_Per'];
		//$NombreUsr  = $row['Nom_Per'];
		//$Cod_Pro    = $row['Cod_Pro'];
		//$Cod_Esp    = $row['Cod_Esp'];
		//$nSex       = $row['Sex'];
		//$Cod_Clt = $row['Cod_Clt'];
		
		foreach ($_POST as $key => $value) {
			//echo $key." --> ".$value."<BR>";
                        if ($key == "dfAppPat") 	 $AppPat    = str_replace("\'", "''", utf8_decode (strtoupper($value)));
                        if ($key == "dfAppMat") 	 $AppMat    = str_replace("\'", "''", utf8_decode (strtoupper($value)));
                        if ($key == "dfNomUsr") 	 $NombreUsr = str_replace("\'", "''", utf8_decode (strtoupper($value)));
                        if ($key == "dfDireccion") 	 $Direccion = str_replace("\'", "''", utf8_decode (strtoupper($value)));
			if ($key == "dfTelefonoSuc")     $FonoSuc  = $value;
			if ($key == "dfTelefonoUsr")     $FonoCtt  = $value;
			if ($key == "dfFaxSuc") 	 $FaxSuc   = $value;
			if ($key == "dfMovilUsr") 	 $MovilUsr = $value;
			if ($key == "dfemail") 		 $email    = $value;
			if ($key == "codcmn") 		 $Cod_Cmn  = intval($value);
			if ($key == "codcdd") 		 $Cod_Cdd  = intval($value);
			if ($key == "codpro") 		 $Cod_Pro   = intval($value);
			if ($key == "codesp") 		 $Cod_Esp   = intval($value);
			if ($key == "rbSexo") 		 $nSex   	= intval($value);
			//if ($key == "dfPassword") 	 $clave	    = encriptar($value,30);
		}
		
		$result = mssql_query("vm_per_u $Cod_Per, $Cod_TipPer, $doc_id, '$RutUsr', '$AppPat', '$AppMat', '$NombreUsr',
										'$Nombre', '$NombreF', $Cod_Pro, $Cod_Esp, $nSex, '$Web', NULL", $db)
							  or die ("No se pudo actualizar datos del Usuario");

		$result = mssql_query("vm_usrweb_ctt_s $Cod_Per, $Cod_Clt");
			
		if ($row = mssql_fetch_array($result)) {
			$Cod_Clt    = $row['Cod_Clt'];
			$Cgo_Ctt    = $row['Cgo_Ctt'];
			$Cod_TipCtt = $row['Cod_TipCtt'];
			$Cod_Suc    = $row['Cod_Suc'];
			$Nom_Suc    = $row['Nom_Suc'];

			//$query = "vm_suc_u $Cod_Suc, $Cod_Clt, '$Nom_Suc', '$Direccion', $Cod_Cmn, $Cod_Cdd, '$FonoSuc', '$FaxSuc'";
			$result = mssql_query("vm_suc_u $Cod_Suc, $Cod_Clt, '$Nom_Suc', '$Direccion', $Cod_Cmn, $Cod_Cdd, '$FonoSuc', '$FaxSuc'", $db)
							  or die ("No se pudo actualizar datos de la Sucursal");
			
			//$query = "vm_suc_s $Cod_Clt";
			$result = mssql_query("vm_suc_s $Cod_Clt", $db)
                                                or die ("No se pudo leer los datos de la sucursal<Br>");
			if($row = mssql_fetch_array($result)) {
				$Cod_Suc = $row['Cod_Suc'];
				
				//$query = "vm_ctt_u $Cod_Clt, $Cod_Suc, $Cod_Per, '$FonoCtt', '$MovilUsr', '$email', '$Cgo_Ctt', $Cod_TipCtt";
				$result = mssql_query("vm_ctt_u $Cod_Clt, $Cod_Suc, $Cod_Per, '$FonoCtt', '$MovilUsr', '$email', '$Cgo_Ctt', $Cod_TipCtt", $db)
								  or die ("No se pudo actualizar datos del Contacto");
								  
				header("Location: micuenta.php?idmsg=5"); 
			}
			else
				header("Location: micuenta.php?idmsg=6"); 
		}
	}
	else
		header("Location: micuenta.php?idmsg=4"); 
?>
